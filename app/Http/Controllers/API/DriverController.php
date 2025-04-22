<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Driver::with('user');
        
        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $drivers = $query->orderBy('id')->paginate(15);
        
        return response()->json($drivers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'license_number' => 'required|string|max:50|unique:drivers',
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive,on_leave',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Create user account
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
            
            // Assign driver role
            $user->assignRole('driver');
            
            // Create driver profile
            $driver = Driver::create([
                'user_id' => $user->id,
                'license_number' => $validated['license_number'],
                'phone_number' => $validated['phone_number'],
                'address' => $validated['address'] ?? null,
                'status' => $validated['status'],
            ]);
            
            DB::commit();
            
            return response()->json([
                'message' => 'Driver created successfully',
                'driver' => $driver->load('user'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to create driver',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $driver = Driver::with([
            'user', 
            'schedules.route', 
            'schedules.vehicle',
            'incidents' => function($query) {
                $query->latest()->take(5);
            }
        ])->findOrFail($id);
        
        // Get last known location
        $lastLocation = $driver->locations()->with('vehicle')->latest('recorded_at')->first();
        
        return response()->json([
            'driver' => $driver,
            'last_location' => $lastLocation,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $driver = Driver::with('user')->findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $driver->user_id,
            'password' => 'nullable|string|min:8',
            'license_number' => 'sometimes|required|string|max:50|unique:drivers,license_number,' . $id,
            'phone_number' => 'sometimes|required|string|max:20',
            'address' => 'nullable|string',
            'status' => 'sometimes|required|in:active,inactive,on_leave',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Update user information
            $userUpdates = [];
            if (isset($validated['name'])) {
                $userUpdates['name'] = $validated['name'];
            }
            if (isset($validated['email'])) {
                $userUpdates['email'] = $validated['email'];
            }
            if (isset($validated['password'])) {
                $userUpdates['password'] = Hash::make($validated['password']);
            }
            
            if (!empty($userUpdates)) {
                $driver->user->update($userUpdates);
            }
            
            // Update driver information
            $driverUpdates = [];
            if (isset($validated['license_number'])) {
                $driverUpdates['license_number'] = $validated['license_number'];
            }
            if (isset($validated['phone_number'])) {
                $driverUpdates['phone_number'] = $validated['phone_number'];
            }
            if (array_key_exists('address', $validated)) {
                $driverUpdates['address'] = $validated['address'];
            }
            if (isset($validated['status'])) {
                $driverUpdates['status'] = $validated['status'];
            }
            
            if (!empty($driverUpdates)) {
                $driver->update($driverUpdates);
            }
            
            DB::commit();
            
            return response()->json([
                'message' => 'Driver updated successfully',
                'driver' => $driver->load('user'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to update driver',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $driver = Driver::with('user')->findOrFail($id);
        
        // Check if driver has schedules
        if ($driver->schedules()->exists()) {
            return response()->json([
                'message' => 'Cannot delete driver as they have assigned schedules',
            ], 409);
        }
        
        DB::beginTransaction();
        
        try {
            // Delete driver record
            $driver->delete();
            
            // Delete user account
            $driver->user->delete();
            
            DB::commit();
            
            return response()->json([
                'message' => 'Driver deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to delete driver',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
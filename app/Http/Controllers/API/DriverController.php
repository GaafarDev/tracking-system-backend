<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Models\Driver;
use App\Models\User;
use App\Models\Vendor;
use App\Notifications\DriverAccountCreated;

class DriverController extends Controller
{
    // Remove the constructor and add the admin middleware to routes instead

    public function index(Request $request)
    {
        $query = Driver::with(['user', 'vendor']);
        
        // Apply search filter if provided
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->orWhere('license_number', 'like', "%{$search}%")
            ->orWhere('phone_number', 'like', "%{$search}%");
        }
        
        // Apply status filter if provided
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Apply vendor filter if provided
        if ($request->has('vendor_id') && $request->vendor_id !== 'all') {
            $query->where('vendor_id', $request->vendor_id);
        }
        
        $drivers = $query->paginate(10)
            ->withQueryString();
        
        // Get vendors for filter dropdown
        $vendors = Vendor::select('id', 'name')->get();
        
        return Inertia::render('Drivers/Index', [
            'drivers' => $drivers,
            'vendors' => $vendors,
            'filters' => $request->only(['search', 'status', 'vendor_id']),
        ]);
    }

    public function create()
    {
        $vendors = Vendor::where('status', 'active')->get();
        
        return Inertia::render('Drivers/Create', [
            'vendors' => $vendors,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'license_number' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,on_leave',
        ]);

        // Generate a random password
        $password = Str::random(12);

        // Create user account first with driver role
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($password),
            'role' => 'driver',
        ]);

        // Create driver record linked to user AND vendor
        $driver = Driver::create([
            'vendor_id' => $validated['vendor_id'],
            'user_id' => $user->id,
            'license_number' => $validated['license_number'],
            'phone_number' => $validated['phone_number'],
            'address' => $validated['address'],
            'status' => $validated['status'],
        ]);

        // Send email notification with credentials
        try {
            $user->notify(new DriverAccountCreated($driver, $password));
            Log::info("Driver account created successfully. Email sent to: {$user->email}");
            $message = 'Driver created successfully. Login credentials have been sent via email.';
        } catch (\Exception $e) {
            Log::error('Failed to send driver account creation email: ' . $e->getMessage());
            Log::info("Generated password for {$user->email}: {$password}");
            
            $message = "Driver created successfully. ⚠️ Email delivery failed. Login credentials: Email: {$user->email}, Password: {$password}";
        }

        return redirect()->route('drivers.index')
            ->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Driver $driver)
    {
        $driver->load([
            'user',
            'vendor',
            'schedules' => function($query) {
                $query->with(['route', 'vehicle'])->latest()->take(5);
            }
        ]);

        return Inertia::render('Drivers/Show', [
            'driver' => $driver
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Driver $driver)
    {
        $driver->load(['user', 'vendor']);
        $vendors = Vendor::where('status', 'active')->get();
        
        return Inertia::render('Drivers/Edit', [
            'driver' => $driver,
            'vendors' => $vendors,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Driver $driver)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $driver->user_id,
            'license_number' => 'required|string|max:255|unique:drivers,license_number,' . $driver->id,
            'phone_number' => 'required|string|max:255',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive,on_leave',
        ]);

        // Update user information
        $driver->user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update driver information INCLUDING vendor_id
        $driver->update([
            'vendor_id' => $request->vendor_id,
            'license_number' => $request->license_number,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'status' => $request->status,
        ]);

        return redirect()->route('drivers.show', $driver)
            ->with('success', 'Driver updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Driver $driver)
    {
        $user = $driver->user;
        $driver->delete();
        $user->delete();

        return redirect()->route('drivers.index')
            ->with('success', 'Driver deleted successfully.');
    }

    // API method for mobile app - Only drivers can access this
    public function me(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Only allow drivers to access this endpoint
            if (!$user->isDriver()) {
                return response()->json([
                    'message' => 'Access denied. Driver role required.'
                ], 403);
            }

            $driver = Driver::where('user_id', $user->id)->with(['user', 'vendor'])->first();
            
            if (!$driver) {
                return response()->json([
                    'message' => 'Driver profile not found'
                ], 404);
            }
            
            return response()->json($driver, 200);
        } catch (\Exception $e) {
            Log::error('Error fetching driver profile', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? 'null'
            ]);
            return response()->json([
                'message' => 'Error fetching driver profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Add this method for password reset functionality
    public function resetPassword(Driver $driver)
    {
        // Generate a new random password
        $newPassword = Str::random(12);
        
        // Update user password
        $driver->user->update([
            'password' => Hash::make($newPassword)
        ]);

        // Try to send email notification
        try {
            $driver->user->notify(new DriverAccountCreated($driver, $newPassword));
            Log::info("Password reset for driver. Email sent to: {$driver->user->email}");
            $message = 'Password reset successfully. New credentials have been sent via email.';
        } catch (\Exception $e) {
            Log::error('Failed to send password reset email: ' . $e->getMessage());
            Log::info("Generated password for {$driver->user->email}: {$newPassword}");
            
            // For development/backup - show the password when email fails
            $message = "Password reset successfully. ⚠️ Email delivery failed. New login credentials: Email: {$driver->user->email}, Password: {$newPassword}";
        }

        return redirect()->route('drivers.index')
            ->with('success', $message);
    }
}
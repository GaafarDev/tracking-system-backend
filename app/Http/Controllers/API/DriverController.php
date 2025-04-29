<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use App\Models\Driver;
use App\Models\User;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $query = Driver::with('user');
        
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
        
        $drivers = $query->paginate(10)
            ->withQueryString();
        
        return Inertia::render('Drivers/Index', [
            'drivers' => $drivers,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'license_number' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,on_leave',
        ]);

        // Create user account first
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Create driver record linked to user
        $driver = Driver::create([
            'user_id' => $user->id,
            'license_number' => $validated['license_number'],
            'phone_number' => $validated['phone_number'],
            'address' => $validated['address'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('drivers.index')
            ->with('success', 'Driver created successfully.');
    }

    public function create()
    {
        return Inertia::render('Drivers/Create');
    }

    public function show(Driver $driver)
    {
        $driver->load('user');
        
        return Inertia::render('Drivers/Show', [
            'driver' => $driver
        ]);
    }

    public function edit(Driver $driver)
    {
        $driver->load('user');
        
        return Inertia::render('Drivers/Edit', [
            'driver' => $driver
        ]);
    }

    public function update(Request $request, Driver $driver)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$driver->user_id,
            'license_number' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,on_leave',
        ]);

        // Update user info
        $driver->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Update driver info
        $driver->update([
            'license_number' => $validated['license_number'],
            'phone_number' => $validated['phone_number'],
            'address' => $validated['address'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('drivers.index')
            ->with('success', 'Driver updated successfully.');
    }

    public function destroy(Driver $driver)
    {
        // Check if driver is assigned to any schedules
        if ($driver->schedules()->exists()) {
            return back()->with('error', 'Cannot delete driver as they are assigned to schedules.');
        }
        
        // Get the user ID before deleting the driver
        $userId = $driver->user_id;
        
        // Delete the driver
        $driver->delete();
        
        // Delete the associated user
        User::find($userId)->delete();
        
        return redirect()->route('drivers.index')
            ->with('success', 'Driver deleted successfully.');
    }
}
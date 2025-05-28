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
use App\Notifications\DriverAccountCreated;

class DriverController extends Controller
{
    // Remove the constructor and add the admin middleware to routes instead

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
            'role' => 'driver', // Explicitly set as driver
        ]);

        // Create driver record linked to user
        $driver = Driver::create([
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
            
            // For development/backup - show the password when email fails
            $message = "Driver created successfully. ⚠️ Email delivery failed. Login credentials: Email: {$user->email}, Password: {$password}";
        }

        return redirect()->route('drivers.index')
            ->with('success', $message);
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

            $driver = Driver::where('user_id', $user->id)->with('user')->first();
            
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
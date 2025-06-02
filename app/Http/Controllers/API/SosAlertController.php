<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use App\Models\SosAlert;
use App\Models\Notification;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Route;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class SosAlertController extends Controller
{
    public function send(Request $request)
    {
        Log::info('=== SOS ALERT START ===', [
            'user_id' => $request->user()->id ?? 'null'
        ]);

        try {
            $validated = $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'message' => 'nullable|string|max:500',
            ]);
            
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Check if user is a driver
            if (!$user->isDriver()) {
                return response()->json([
                    'message' => 'Access denied. Driver role required.'
                ], 403);
            }

            $driver = Driver::where('user_id', $user->id)->first();
            if (!$driver) {
                return response()->json([
                    'message' => 'Driver profile not found'
                ], 404);
            }
            
            // Check if driver already has an active SOS alert
            $existingAlert = SosAlert::where('driver_id', $driver->id)
                ->where('status', 'active')
                ->first();
                
            if ($existingAlert) {
                return response()->json([
                    'message' => 'You already have an active SOS alert',
                    'sos_alert' => $existingAlert,
                ], 400);
            }
            
            // Create SOS alert
            $sosAlert = SosAlert::create([
                'driver_id' => $driver->id,
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'message' => $validated['message'] ?? '',
                'status' => 'active',
            ]);
            
            Log::info('SOS alert created successfully', ['sos_alert_id' => $sosAlert->id]);
            
            return response()->json([
                'message' => 'SOS alert sent successfully',
                'sos_alert' => $sosAlert,
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('SOS validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('SOS alert failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Failed to send SOS alert',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function getActiveSosAlert(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user || !$user->isDriver()) {
                return response()->json([
                    'message' => 'Access denied. Driver role required.'
                ], 403);
            }

            $driver = Driver::where('user_id', $user->id)->first();
            if (!$driver) {
                return response()->json([
                    'message' => 'Driver not found',
                    'sos_alert' => null
                ], 404);
            }
            
            $activeAlert = SosAlert::where('driver_id', $driver->id)
                ->where('status', 'active')
                ->first();
                
            return response()->json([
                'sos_alert' => $activeAlert
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Error checking active SOS alert', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Error checking active SOS alert',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function cancelSosAlert(Request $request, $id)
    {
        try {
            $user = $request->user();
            if (!$user || !$user->isDriver()) {
                return response()->json([
                    'message' => 'Access denied. Driver role required.'
                ], 403);
            }

            $driver = Driver::where('user_id', $user->id)->first();
            if (!$driver) {
                return response()->json([
                    'message' => 'Driver not found'
                ], 404);
            }
            
            $sosAlert = SosAlert::where('id', $id)
                ->where('driver_id', $driver->id)
                ->where('status', 'active')
                ->first();
                
            if (!$sosAlert) {
                return response()->json([
                    'message' => 'Active SOS alert not found'
                ], 404);
            }
                
            $sosAlert->update([
                'status' => 'resolved',
                'resolved_at' => now(),
            ]);
            
            Log::info('SOS alert cancelled successfully', ['sos_alert_id' => $sosAlert->id]);
            
            return response()->json([
                'message' => 'SOS alert cancelled successfully'
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Failed to cancel SOS alert', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Failed to cancel SOS alert',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    // Admin methods for web interface
    public function index(Request $request)
    {
        $query = SosAlert::with(['driver.user']);
        
        // Apply filters
        if ($request->filled('search')) {
            $query->whereHas('driver.user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('driver_id') && $request->driver_id !== 'all') {
            $query->where('driver_id', $request->driver_id);
        }
        
        $sosAlerts = $query->orderBy('created_at', 'desc')->paginate(10);
        $drivers = Driver::with('user')->get();
        
        return Inertia::render('SOS/Index', [
            'sosAlerts' => $sosAlerts,
            'drivers' => $drivers,
            'filters' => $request->only(['search', 'status', 'driver_id', 'from_date', 'to_date'])
        ]);
    }

    public function show(SosAlert $sosAlert)
    {
        $sosAlert->load('driver.user');
        
        return Inertia::render('SOS/Show', [
            'sosAlert' => $sosAlert,
        ]);
    }

    public function respond(Request $request, SosAlert $sosAlert)
    {
        // Only update if currently active
        if ($sosAlert->status === 'active') {
            $sosAlert->update([
                'status' => 'responded',
                'responded_at' => now(),
            ]);
            
            // Notify the driver
            Notification::create([
                'user_id' => $sosAlert->driver->user_id,
                'title' => 'SOS Alert Update',
                'message' => 'Your SOS alert has been responded to by the support team.',
                'type' => 'sos',
            ]);
        }
        
        return redirect()->back()->with('success', 'SOS alert marked as responded.');
    }
    
    public function resolve(Request $request, SosAlert $sosAlert)
    {
        // Only update if not already resolved
        if ($sosAlert->status !== 'resolved') {
            $sosAlert->update([
                'status' => 'resolved',
                'resolved_at' => now(),
            ]);
            
            // Notify the driver
            Notification::create([
                'user_id' => $sosAlert->driver->user_id,
                'title' => 'SOS Alert Update',
                'message' => 'Your SOS alert has been resolved by the support team.',
                'type' => 'sos',
            ]);
        }
        
        return redirect()->back()->with('success', 'SOS alert marked as resolved.');
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SosAlert $sosAlert)
    {
        $sosAlert->load(['driver.user']);
        
        return Inertia::render('SOS/Edit', [
            'sosAlert' => $sosAlert
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SosAlert $sosAlert)
    {
        $request->validate([
            'status' => 'required|in:active,responded,resolved',
            'message' => 'nullable|string',
        ]);

        $sosAlert->update($request->only(['status', 'message']));

        // Notify the driver if status changed
        if ($request->status !== $sosAlert->getOriginal('status')) {
            Notification::create([
                'user_id' => $sosAlert->driver->user_id,
                'title' => 'SOS Alert Update',
                'message' => "Your SOS alert status has been updated to: {$request->status}",
                'type' => 'sos',
            ]);
        }

        return redirect()->route('sos.show', $sosAlert)
            ->with('success', 'SOS alert updated successfully.');
    }
}
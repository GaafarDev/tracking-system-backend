<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use App\Models\SosAlert;
use App\Models\Notification;
use App\Models\Driver;
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
        $query = SosAlert::with('driver.user');
        
        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                  ->orWhereHas('driver.user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('driver', function($driverQuery) use ($search) {
                      $driverQuery->where('license_number', 'like', "%{$search}%")
                                  ->orWhere('phone_number', 'like', "%{$search}%");
                  });
            });
        }
        
        // Apply status filter
        if ($request->has('status') && $request->status !== 'all' && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // Apply driver filter
        if ($request->has('driver_id') && $request->driver_id !== 'all' && !empty($request->driver_id)) {
            $query->where('driver_id', $request->driver_id);
        }
        
        // Apply date range filters
        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        $sosAlerts = $query->orderByRaw("CASE 
                WHEN status = 'active' THEN 1 
                WHEN status = 'responded' THEN 2 
                WHEN status = 'resolved' THEN 3 
                ELSE 4 
            END")
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();
        
        // Get all drivers for the filter dropdown
        $drivers = Driver::with('user')->get();
        
        return Inertia::render('SOS/Index', [
            'sosAlerts' => $sosAlerts,
            'drivers' => $drivers,
            'filters' => $request->only(['search', 'status', 'driver_id', 'from_date', 'to_date']),
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
}
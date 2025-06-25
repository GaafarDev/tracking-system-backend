<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use App\Models\Incident;
use App\Models\Driver;
use App\Models\Notification;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IncidentController extends Controller
{
    public function report(Request $request)
    {
        Log::info('=== INCIDENT REPORT START ===', [
            'user_id' => $request->user()->id ?? 'null',
            'has_photo' => $request->hasFile('photo') ? 'YES' : 'NO',
            'all_files' => array_keys($request->allFiles()),
            'content_type' => $request->header('Content-Type')
        ]);

        try {
            // Validation
            $validated = $request->validate([
                'type' => 'required|string|in:accident,breakdown,road_obstruction,weather,other',
                'description' => 'required|string|max:1000',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:10240', // 10MB max
            ]);
            
            $user = $request->user();
            if (!$user) {
                Log::error('No authenticated user found');
                return response()->json([
                    'message' => 'User not authenticated',
                ], 401);
            }

            // Check if user is a driver
            if (!$user->isDriver()) {
                Log::error('User is not a driver', ['user_id' => $user->id]);
                return response()->json([
                    'message' => 'Access denied. Driver role required.',
                ], 403);
            }

            $driver = Driver::where('user_id', $user->id)->first();
            if (!$driver) {
                Log::error('No driver found for user', ['user_id' => $user->id]);
                return response()->json([
                    'message' => 'Driver profile not found',
                ], 404);
            }
            
            // Handle photo upload
            $photoPath = null;
            if ($request->hasFile('photo')) {
                try {
                    $file = $request->file('photo');
                    Log::info('Photo file details:', [
                        'original_name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType()
                    ]);
                    
                    $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $photoPath = $file->storeAs('incident_photos', $filename, 'public');
                    Log::info('Photo uploaded successfully: ' . $photoPath);
                } catch (\Exception $e) {
                    Log::error('Photo upload failed: ' . $e->getMessage());
                    // Continue without photo
                }
            } else {
                Log::info('No photo file in request');
            }
            
            // Create incident record
            $incident = Incident::create([
                'driver_id' => $driver->id,
                'type' => $validated['type'],
                'description' => $validated['description'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'photo_path' => $photoPath,
                'status' => 'reported',
            ]);
            
            Log::info('Incident created successfully', [
                'incident_id' => $incident->id,
                'has_photo' => $photoPath ? 'YES' : 'NO',
                'photo_path' => $photoPath
            ]);
            
            return response()->json([
                'message' => 'Incident reported successfully',
                'incident' => $incident,
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Incident report failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to report incident',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    // Add a method to get incidents for the authenticated driver
    public function getIncidents(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user || !$user->isDriver()) {
                return response()->json([
                    'message' => 'Access denied. Driver role required.',
                ], 403);
            }

            $driver = Driver::where('user_id', $user->id)->first();
            if (!$driver) {
                return response()->json([
                    'message' => 'Driver profile not found',
                ], 404);
            }

            $incidents = Incident::where('driver_id', $driver->id)
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'data' => $incidents->items(),
                'pagination' => [
                    'current_page' => $incidents->currentPage(),
                    'last_page' => $incidents->lastPage(),
                    'per_page' => $incidents->perPage(),
                    'total' => $incidents->total()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get incidents', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Failed to get incidents',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    // Rest of your existing methods...
    public function index(Request $request)
    {
        $query = Incident::with('driver.user');
        
        // Apply status filter
        if ($request->has('status') && $request->status !== 'all' && $request->status !== '') {
            if ($request->status === 'active') {
                $query->whereIn('status', ['reported', 'in_progress']);
            } else {
                $query->where('status', $request->status);
            }
        }
        
        // Apply type filter
        if ($request->has('type') && $request->type !== 'all' && $request->type !== '') {
            $query->where('type', $request->type);
        }
        
        // Apply date range filter
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        // Apply search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhereHas('driver.user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $incidents = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();
        
        return Inertia::render('Incidents/Index', [
            'incidents' => $incidents,
            'filters' => $request->only(['status', 'type', 'search', 'from_date', 'to_date']),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Incident $incident)
    {
        // Make sure you're loading the incident with all necessary relationships
        $incident->load(['driver.user']);
        
        // Debug: Log the photo_path to see if it exists
        \Log::info('Incident photo_path: ' . $incident->photo_path);
        
        return Inertia::render('Incidents/Show', [
            'incident' => $incident
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Incident $incident)
    {
        $incident->load(['driver.user']);
        
        return Inertia::render('Incidents/Edit', [
            'incident' => $incident
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Incident $incident)
    {
        $request->validate([
            'type' => 'required|in:accident,breakdown,road_obstruction,weather,other',
            'description' => 'required|string',
            'status' => 'required|in:reported,in_progress,resolved',
            'severity' => 'required|in:low,medium,high,critical',
        ]);

        $incident->update($request->only(['type', 'description', 'status', 'severity']));

        return redirect()->route('incidents.show', $incident)
            ->with('success', 'Incident updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Incident $incident)
    {
        $incident->delete();

        return redirect()->route('incidents.index')
            ->with('success', 'Incident deleted successfully.');
    }
    
    public function resolve(Request $request, Incident $incident)
    {
        // Only update if not already resolved or closed
        if (!in_array($incident->status, ['resolved', 'closed'])) {
            $incident->update([
                'status' => 'resolved',
                'resolved_at' => now(),
                'resolution_notes' => $request->input('resolution_notes', 'Incident marked as resolved.')
            ]);
            
            // Optionally notify the driver
            if ($incident->driver && $incident->driver->user) {
                Notification::create([
                    'user_id' => $incident->driver->user_id,
                    'title' => 'Incident Update',
                    'message' => 'Your reported incident has been resolved by the support team.',
                    'type' => 'incident',
                ]);
            }
        }
        
        // Return JSON response instead of redirect for AJAX calls
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Incident marked as resolved.'
            ]);
        }
        
        return redirect()->back()->with('success', 'Incident marked as resolved.');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'description' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
        ]);

        $photoPath = null;

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            
            // Generate unique filename
            $filename = time() . '_' . Str::random(10) . '.' . $photo->getClientOriginalExtension();
            
            // Store in public/storage/incident_photos directory
            $photoPath = $photo->storeAs('incident_photos', $filename, 'public');
            
            \Log::info('Photo uploaded successfully: ' . $photoPath);
        } else {
            \Log::info('No photo received in request');
            \Log::info('Request files: ' . json_encode($request->allFiles()));
        }

        // Get the authenticated driver
        $driver = $request->user()->driver;
        
        if (!$driver) {
            return response()->json(['error' => 'Driver not found'], 404);
        }

        // Create incident
        $incident = Incident::create([
            'driver_id' => $driver->id,
            'type' => $request->type,
            'description' => $request->description,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'photo_path' => $photoPath, // This should not be null if photo uploaded
            'status' => 'reported',
        ]);

        \Log::info('Incident created with photo_path: ' . $incident->photo_path);

        return response()->json([
            'message' => 'Incident reported successfully',
            'incident' => $incident,
        ], 201);
    }
}
<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use App\Models\Incident;
use App\Models\Driver;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class IncidentController extends Controller
{
    public function report(Request $request)
    {
        Log::info('=== INCIDENT REPORT START ===', [
            'user_id' => $request->user()->id ?? 'null',
            'request_data' => $request->except(['photo']) // Don't log photo data
        ]);

        try {
            // Simplified validation - make photo optional and smaller
            $validated = $request->validate([
                'type' => 'required|string|in:accident,breakdown,road_obstruction,weather,other',
                'description' => 'required|string|max:1000',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'photo' => 'nullable|image|max:2048', // Reduced to 2MB
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
            
            // Handle photo upload with better error handling
            $photoPath = null;
            if ($request->hasFile('photo')) {
                try {
                    $file = $request->file('photo');
                    
                    // Check file size before processing
                    if ($file->getSize() > 2048 * 1024) { // 2MB in bytes
                        Log::warning('Photo too large, skipping upload', ['size' => $file->getSize()]);
                    } else {
                        $photoPath = $file->store('incident_photos', 'public');
                        Log::info('Photo uploaded successfully', ['path' => $photoPath]);
                    }
                } catch (\Exception $e) {
                    Log::error('Photo upload failed', ['error' => $e->getMessage()]);
                    // Continue without photo - don't fail the entire request
                }
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
            
            Log::info('Incident created successfully', ['incident_id' => $incident->id]);
            
            return response()->json([
                'message' => 'Incident reported successfully',
                'incident' => $incident,
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Incident report failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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

    public function show(Incident $incident)
    {
        $incident->load('driver.user');
        
        return Inertia::render('Incidents/Show', [
            'incident' => $incident,
        ]);
    }

    public function update(Request $request, Incident $incident)
    {
        $validated = $request->validate([
            'status' => 'required|in:reported,in_progress,resolved,closed',
            'resolution_notes' => 'nullable|string',
        ]);
        
        // If status changed to resolved, update resolved_at timestamp
        if ($validated['status'] === 'resolved' && $incident->status !== 'resolved') {
            $validated['resolved_at'] = now();
        }
        
        $incident->update($validated);
        
        return redirect()->route('incidents.index')
            ->with('success', 'Incident updated successfully.');
    }

    public function destroy(Incident $incident)
    {
        // Delete associated photo if exists
        if ($incident->photo_path) {
            Storage::disk('public')->delete($incident->photo_path);
        }
        
        $incident->delete();
        
        return redirect()->route('incidents.index')
            ->with('success', 'Incident deleted successfully.');
    }
}
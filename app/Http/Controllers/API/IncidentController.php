<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use App\Models\Incident;
use App\Models\Driver;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        $query = Incident::with('driver.user');
        
        // Apply filters
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('type') && $request->type !== 'all') {
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
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
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

    public function create()
    {
        return Inertia::render('Incidents/Create');
    }

    public function store(Request $request)
    {
        // Implementation will be added later
        return redirect()->route('incidents.index');
    }

    public function show(Incident $incident)
    {
        $incident->load('driver.user');
        
        return Inertia::render('Incidents/Show', [
            'incident' => $incident,
        ]);
    }

    public function edit(Incident $incident)
    {
        $incident->load('driver.user');
        
        return Inertia::render('Incidents/Edit', [
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
        $incident->delete();
        
        return redirect()->route('incidents.index')
            ->with('success', 'Incident deleted successfully.');
    }

    public function report(Request $request)
    {
        Log::info('Incident report request received', [
            'user_id' => $request->user()->id ?? 'null',
            'request_data' => $request->all()
        ]);

        try {
            $validated = $request->validate([
                'type' => 'required|string',
                'description' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'photo' => 'nullable|image|max:5120', // 5MB max
            ]);
            
            $user = $request->user();
            if (!$user) {
                Log::error('No authenticated user found');
                return response()->json([
                    'message' => 'User not authenticated',
                ], 401);
            }

            $driver = Driver::where('user_id', $user->id)->first();
            if (!$driver) {
                Log::error('No driver found for user', ['user_id' => $user->id]);
                return response()->json([
                    'message' => 'Driver profile not found',
                ], 404);
            }
            
            $photoPath = null;
            if ($request->hasFile('photo')) {
                try {
                    $photoPath = $request->file('photo')->store('incident_photos', 'public');
                    Log::info('Photo uploaded successfully', ['path' => $photoPath]);
                } catch (\Exception $e) {
                    Log::error('Photo upload failed', ['error' => $e->getMessage()]);
                    // Continue without photo if upload fails
                }
            }
            
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
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
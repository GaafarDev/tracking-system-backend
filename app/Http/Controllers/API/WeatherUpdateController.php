<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WeatherUpdate;
use App\Models\Notification;
use App\Models\User;
use App\Models\Driver;

class WeatherUpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = WeatherUpdate::query();
        
        // Filter by condition if provided
        if ($request->has('condition')) {
            $query->where('condition', $request->condition);
        }
        
        // Filter by location if provided
        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }
        
        // Only show active weather updates
        $query->where('valid_until', '>', now());
        
        $weatherUpdates = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return response()->json($weatherUpdates);
    }

    /**
     * Get latest weather updates.
     */
    public function latest()
    {
        $latestUpdates = WeatherUpdate::where('valid_until', '>', now())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        return response()->json($latestUpdates);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'location' => 'required|string|max:255',
            'condition' => 'required|in:clear,rain,storm,flood,fog,other',
            'description' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'temperature' => 'nullable|numeric',
            'affects_routes' => 'boolean',
            'valid_until' => 'required|date|after:now',
        ]);
        
        $weatherUpdate = WeatherUpdate::create($validated);
        
        // If the weather affects routes, notify all drivers
        if ($validated['affects_routes']) {
            $this->notifyDrivers($weatherUpdate);
        }
        
        return response()->json([
            'message' => 'Weather update created successfully',
            'weather_update' => $weatherUpdate,
        ], 201);
    }
    
    /**
     * Notify drivers about severe weather.
     */
    private function notifyDrivers($weatherUpdate)
    {
        $drivers = Driver::with('user')->get();
        
        foreach ($drivers as $driver) {
            Notification::create([
                'user_id' => $driver->user_id,
                'title' => 'Weather Alert',
                'message' => "Weather alert for {$weatherUpdate->location}: {$weatherUpdate->condition}. {$weatherUpdate->description}",
                'type' => 'weather',
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $weatherUpdate = WeatherUpdate::findOrFail($id);
        
        return response()->json($weatherUpdate);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $weatherUpdate = WeatherUpdate::findOrFail($id);
        
        $validated = $request->validate([
            'location' => 'sometimes|required|string|max:255',
            'condition' => 'sometimes|required|in:clear,rain,storm,flood,fog,other',
            'description' => 'sometimes|required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'temperature' => 'nullable|numeric',
            'affects_routes' => 'sometimes|boolean',
            'valid_until' => 'sometimes|required|date|after:now',
        ]);
        
        $oldAffectsRoutes = $weatherUpdate->affects_routes;
        $weatherUpdate->update($validated);
        
        // If weather now affects routes and it didn't before, notify drivers
        if (isset($validated['affects_routes']) && $validated['affects_routes'] && !$oldAffectsRoutes) {
            $this->notifyDrivers($weatherUpdate);
        }
        
        return response()->json([
            'message' => 'Weather update modified successfully',
            'weather_update' => $weatherUpdate,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $weatherUpdate = WeatherUpdate::findOrFail($id);
        $weatherUpdate->delete();
        
        return response()->json([
            'message' => 'Weather update deleted successfully',
        ]);
    }
}
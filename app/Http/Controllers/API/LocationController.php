<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Driver;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::with(['driver', 'vehicle'])
            ->orderBy('recorded_at', 'desc')
            ->paginate(50);
            
        return response()->json($locations);
    }

    public function updateLocation(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'speed' => 'nullable|numeric',
            'heading' => 'nullable|numeric',
            'vehicle_id' => 'required|exists:vehicles,id',
        ]);
        
        // Get driver ID from authenticated user
        $user = $request->user();
        $driver = Driver::where('user_id', $user->id)->firstOrFail();
        
        // Create location record
        $location = Location::create([
            'driver_id' => $driver->id,
            'vehicle_id' => $validated['vehicle_id'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'speed' => $validated['speed'] ?? null,
            'heading' => $validated['heading'] ?? null,
            'recorded_at' => now(),
        ]);
        
        return response()->json([
            'message' => 'Location updated successfully',
            'location' => $location,
        ]);
    }
    
    /**
     * Get latest locations for all active drivers.
     */
    public function getLatestLocations()
    {
        // Get the latest location for each driver
        $latestLocations = Location::with(['driver.user', 'vehicle'])
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('locations')
                    ->groupBy('driver_id');
            })
            ->where('recorded_at', '>=', now()->subMinutes(30))
            ->get();
            
        return response()->json($latestLocations);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $location = Location::with(['driver', 'vehicle'])->findOrFail($id);
        return response()->json($location);
    }
}
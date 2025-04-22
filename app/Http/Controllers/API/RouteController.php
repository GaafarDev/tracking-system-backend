<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Route;

class RouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $routes = Route::orderBy('name')->paginate(15);
        
        return response()->json($routes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'waypoints' => 'nullable|array',
            'waypoints.*.lat' => 'required_with:waypoints|numeric',
            'waypoints.*.lng' => 'required_with:waypoints|numeric',
            'stops' => 'nullable|array',
            'stops.*.name' => 'required_with:stops|string',
            'stops.*.lat' => 'required_with:stops|numeric',
            'stops.*.lng' => 'required_with:stops|numeric',
            'distance_km' => 'nullable|numeric|min:0',
            'estimated_duration_minutes' => 'nullable|integer|min:1',
        ]);
        
        $route = Route::create($validated);
        
        return response()->json([
            'message' => 'Route created successfully',
            'route' => $route,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $route = Route::with('schedules.driver', 'schedules.vehicle')->findOrFail($id);
        
        return response()->json($route);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $route = Route::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'waypoints' => 'nullable|array',
            'waypoints.*.lat' => 'required_with:waypoints|numeric',
            'waypoints.*.lng' => 'required_with:waypoints|numeric',
            'stops' => 'nullable|array',
            'stops.*.name' => 'required_with:stops|string',
            'stops.*.lat' => 'required_with:stops|numeric',
            'stops.*.lng' => 'required_with:stops|numeric',
            'distance_km' => 'nullable|numeric|min:0',
            'estimated_duration_minutes' => 'nullable|integer|min:1',
        ]);
        
        $route->update($validated);
        
        return response()->json([
            'message' => 'Route updated successfully',
            'route' => $route,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $route = Route::findOrFail($id);
        
        // Check if route is used in schedules
        if ($route->schedules()->exists()) {
            return response()->json([
                'message' => 'Cannot delete route as it is used in schedules',
            ], 409);
        }
        
        $route->delete();
        
        return response()->json([
            'message' => 'Route deleted successfully',
        ]);
    }
}
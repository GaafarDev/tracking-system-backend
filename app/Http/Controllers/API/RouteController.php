<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Route;

class RouteController extends Controller
{
    public function index(Request $request)
    {
        $query = Route::query();
        
        // Apply search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('start_location', 'like', "%{$search}%")
                  ->orWhere('end_location', 'like', "%{$search}%");
            });
        }
        
        // Apply distance filter
        if ($request->has('distance') && $request->distance !== 'all') {
            switch ($request->distance) {
                case 'short':
                    $query->where('distance_km', '<', 10);
                    break;
                case 'medium':
                    $query->whereBetween('distance_km', [10, 50]);
                    break;
                case 'long':
                    $query->where('distance_km', '>', 50);
                    break;
            }
        }
        
        // Apply stops filter
        if ($request->has('stops') && $request->stops !== 'all') {
            switch ($request->stops) {
                case 'few':
                    $query->whereRaw('JSON_LENGTH(COALESCE(stops, "[]")) BETWEEN 1 AND 3');
                    break;
                case 'medium':
                    $query->whereRaw('JSON_LENGTH(COALESCE(stops, "[]")) BETWEEN 4 AND 7');
                    break;
                case 'many':
                    $query->whereRaw('JSON_LENGTH(COALESCE(stops, "[]")) >= 8');
                    break;
                case 'none':
                    $query->whereRaw('JSON_LENGTH(COALESCE(stops, "[]")) = 0');
                    break;
            }
        }
        
        $routes = $query->orderBy('name')->paginate(20)->withQueryString();
        
        return Inertia::render('Routes/Index', [
            'routes' => $routes,
            'filters' => $request->only(['search', 'distance', 'stops']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Routes/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_location' => 'required|string|max:255',
            'end_location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'waypoints' => 'nullable|array',
            'stops' => 'nullable|array',
            'distance_km' => 'nullable|numeric|min:0',
            'estimated_duration_minutes' => 'nullable|integer|min:1',
        ]);
        
        // No need to manually encode JSON - model casting handles it
        $route = Route::create($validated);
        
        return redirect()->route('routes.index')
            ->with('success', 'Route created successfully.');
    }

    public function show(Route $route)
    {
        // Model casting automatically handles JSON decoding
        return Inertia::render('Routes/Show', [
            'route' => $route,
        ]);
    }

    public function edit(Route $route)
    {
        // Model casting automatically handles JSON decoding
        return Inertia::render('Routes/Edit', [
            'route' => $route,
        ]);
    }

    public function update(Request $request, Route $route)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_location' => 'required|string|max:255',
            'end_location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'waypoints' => 'nullable|array',
            'stops' => 'nullable|array',
            'distance_km' => 'nullable|numeric|min:0',
            'estimated_duration_minutes' => 'nullable|integer|min:1',
        ]);
        
        // No need to manually encode JSON - model casting handles it
        $route->update($validated);
        
        return redirect()->route('routes.index')
            ->with('success', 'Route updated successfully.');
    }

    public function destroy(Route $route)
    {
        // Check if route is being used in any schedules
        if ($route->schedules()->exists()) {
            return back()->with('error', 'Cannot delete route as it is assigned to schedules.');
        }
        
        $route->delete();
        
        return redirect()->route('routes.index')
            ->with('success', 'Route deleted successfully.');
    }
}
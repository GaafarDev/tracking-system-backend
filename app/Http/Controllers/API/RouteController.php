<?php

namespace App\Http\Controllers\API; // Update this line
use App\Http\Controllers\Controller;


use App\Models\Route;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RouteController extends Controller
{
    public function index(Request $request)
    {
        $query = Route::query();
        
        // Apply search if provided
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }
        
        $routes = $query->orderBy('name')
            ->paginate(15)
            ->withQueryString();
        
        return Inertia::render('Routes/Index', [
            'routes' => $routes,
            'filters' => $request->only(['search']),
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
        
        return redirect()->route('routes.index')
            ->with('success', 'Route created successfully.');
    }

    public function show(Route $route)
    {
        $route->load('schedules.driver.user', 'schedules.vehicle');
        
        return Inertia::render('Routes/Show', [
            'route' => $route,
        ]);
    }

    public function edit(Route $route)
    {
        return Inertia::render('Routes/Edit', [
            'route' => $route,
        ]);
    }

    public function update(Request $request, Route $route)
    {
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
        
        return redirect()->route('routes.index')
            ->with('success', 'Route updated successfully.');
    }

    public function destroy(Route $route)
    {
        // Check if route is used in schedules
        if ($route->schedules()->exists()) {
            return back()->with('error', 'Cannot delete route as it is used in schedules.');
        }
        
        $route->delete();
        
        return redirect()->route('routes.index')
            ->with('success', 'Route deleted successfully.');
    }
}
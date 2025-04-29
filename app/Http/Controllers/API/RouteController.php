<?php

namespace App\Http\Controllers\API;
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
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Get routes with pagination
        $routes = $query->orderBy('name')
            ->paginate(10)
            ->withQueryString();
        
        // Debug information
        \Log::info('Routes query executed', [
            'count' => $routes->count(),
            'total' => $routes->total(),
            'search' => $request->search ?? 'none'
        ]);
        
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
            'stops' => 'nullable|array',
            'distance_km' => 'nullable|numeric',
            'estimated_duration_minutes' => 'nullable|integer',
        ]);
        
        $route = Route::create($validated);
        
        return redirect()->route('routes.index')
            ->with('success', 'Route created successfully.');
    }

    public function show(Route $route)
    {
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'waypoints' => 'nullable|array',
            'stops' => 'nullable|array',
            'distance_km' => 'nullable|numeric',
            'estimated_duration_minutes' => 'nullable|integer',
        ]);
        
        $route->update($validated);
        
        return redirect()->route('routes.index')
            ->with('success', 'Route updated successfully.');
    }

    public function destroy(Route $route)
    {
        // Check if route is being used in schedules
        if ($route->schedules()->exists()) {
            return back()->with('error', 'Cannot delete route as it is assigned to schedules.');
        }
        
        $route->delete();
        
        return redirect()->route('routes.index')
            ->with('success', 'Route deleted successfully.');
    }
}
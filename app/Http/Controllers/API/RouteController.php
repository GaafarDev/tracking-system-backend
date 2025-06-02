<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Route;
use App\Models\Vendor;

class RouteController extends Controller
{
    public function index(Request $request)
    {
        $query = Route::with('vendor');
        
        // Apply vendor filter
        if ($request->has('vendor_id') && $request->vendor_id !== 'all') {
            $query->where('vendor_id', $request->vendor_id);
        }
        
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
        $vendors = Vendor::where('status', 'active')->get();
        
        return Inertia::render('Routes/Index', [
            'routes' => $routes,
            'vendors' => $vendors,
            'filters' => $request->only(['search', 'distance', 'stops', 'vendor_id']),
        ]);
    }

    public function create()
    {
        $vendors = Vendor::where('status', 'active')->get();
        
        return Inertia::render('Routes/Create', [
            'vendors' => $vendors,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'name' => 'required|string|max:255',
            'start_location' => 'required|string|max:255',
            'end_location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'waypoints' => 'nullable|array',
            'stops' => 'nullable|array',
            'stops.*.name' => 'required_with:stops|string|max:255',
            'stops.*.lat' => 'required_with:stops|numeric|between:-90,90',
            'stops.*.lng' => 'required_with:stops|numeric|between:-180,180',
            'distance_km' => 'nullable|numeric|min:0',
            'estimated_duration_minutes' => 'nullable|integer|min:1',
        ]);
        
        $route = Route::create($validated);
        
        return redirect()->route('routes.index')
            ->with('success', 'Route created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Route $route)
    {
        $route->load(['schedules' => function($query) {
            $query->with(['driver.user', 'vehicle'])->where('is_active', true);
        }]);

        return Inertia::render('Routes/Show', [
            'route' => $route
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Route $route)
    {
        $vendors = Vendor::where('status', 'active')->get();
        
        return Inertia::render('Routes/Edit', [
            'route' => $route,
            'vendors' => $vendors,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Route $route)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'name' => 'required|string|max:255',
            'start_location' => 'required|string|max:255',
            'end_location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'distance_km' => 'nullable|numeric|min:0',
            'estimated_duration_minutes' => 'nullable|integer|min:1',
            'stops' => 'nullable|array',
            'stops.*.name' => 'required_with:stops|string|max:255',
            'stops.*.lat' => 'required_with:stops|numeric|between:-90,90',
            'stops.*.lng' => 'required_with:stops|numeric|between:-180,180',
        ]);

        $route->update($validated);

        return redirect()->route('routes.show', $route)
            ->with('success', 'Route updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Route $route)
    {
        $route->delete();

        return redirect()->route('routes.index')
            ->with('success', 'Route deleted successfully.');
    }
}
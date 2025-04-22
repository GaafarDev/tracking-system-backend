<?php

namespace App\Http\Controllers\API; // Update this line
use App\Http\Controllers\Controller;


use App\Models\Vehicle;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::query();
        
        // Apply filters
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        
        // Apply search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }
        
        $vehicles = $query->paginate(10)
            ->withQueryString();
        
        return Inertia::render('Vehicles/Index', [
            'vehicles' => $vehicles,
            'filters' => $request->only(['status', 'type', 'search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Vehicles/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|unique:vehicles,plate_number',
            'model' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'type' => 'required|in:bus,van,car,boat,other',
            'status' => 'required|in:active,maintenance,inactive',
        ]);
        
        $vehicle = Vehicle::create($validated);
        
        return redirect()->route('vehicles.index')
            ->with('success', 'Vehicle created successfully.');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['schedules.driver.user', 'schedules.route']);
        
        // Get last known location
        $lastLocation = $vehicle->locations()->with('driver.user')->latest('recorded_at')->first();
        
        return Inertia::render('Vehicles/Show', [
            'vehicle' => $vehicle,
            'lastLocation' => $lastLocation,
        ]);
    }

    public function edit(Vehicle $vehicle)
    {
        return Inertia::render('Vehicles/Edit', [
            'vehicle' => $vehicle,
        ]);
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|unique:vehicles,plate_number,' . $vehicle->id,
            'model' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'type' => 'required|in:bus,van,car,boat,other',
            'status' => 'required|in:active,maintenance,inactive',
        ]);
        
        $vehicle->update($validated);
        
        return redirect()->route('vehicles.index')
            ->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle)
    {
        // Check if vehicle is being used in schedules
        if ($vehicle->schedules()->exists()) {
            return back()->with('error', 'Cannot delete vehicle as it is assigned to schedules.');
        }
        
        $vehicle->delete();
        
        return redirect()->route('vehicles.index')
            ->with('success', 'Vehicle deleted successfully.');
    }
}
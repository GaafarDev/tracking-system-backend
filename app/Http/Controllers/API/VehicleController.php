<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use App\Models\Vehicle;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::with('vendor');
        
        // Apply vendor filter
        if ($request->has('vendor_id') && $request->vendor_id !== 'all') {
            $query->where('vendor_id', $request->vendor_id);
        }
        
        // Apply existing filters
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
        
        $vehicles = $query->paginate(10)->withQueryString();
        $vendors = Vendor::where('status', 'active')->get();
        
        return Inertia::render('Vehicles/Index', [
            'vehicles' => $vehicles,
            'vendors' => $vendors,
            'filters' => $request->only(['status', 'type', 'search', 'vendor_id']),
        ]);
    }

    public function create()
    {
        $vendors = Vendor::where('status', 'active')->get();
        
        return Inertia::render('Vehicles/Create', [
            'vendors' => $vendors,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
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

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['schedules' => function($query) {
            $query->with(['driver.user', 'route'])->latest()->take(5);
        }]);

        return Inertia::render('Vehicles/Show', [
            'vehicle' => $vehicle
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        $vendors = Vendor::where('status', 'active')->get();
        
        return Inertia::render('Vehicles/Edit', [
            'vehicle' => $vehicle,
            'vendors' => $vendors,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'plate_number' => 'required|string|max:255|unique:vehicles,plate_number,' . $vehicle->id,
            'model' => 'required|string|max:255',
            'type' => 'required|in:bus,van,car,truck,boat',
            'capacity' => 'required|integer|min:1',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'nullable|string|max:255',
            'fuel_type' => 'nullable|in:petrol,diesel,electric,hybrid,cng',
            'status' => 'required|in:active,maintenance,inactive',
            'engine_number' => 'nullable|string|max:255',
            'chassis_number' => 'nullable|string|max:255',
        ]);

        $vehicle->update($request->all());

        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', 'Vehicle updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehicle deleted successfully.');
    }
}
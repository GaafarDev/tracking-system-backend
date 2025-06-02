<?php
// app/Http/Controllers/API/VendorController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::query();
        
        // Apply search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_registration_number', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Apply status filter
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        $vendors = $query->withCount(['drivers', 'vehicles', 'routes'])
            ->paginate(10)
            ->withQueryString();
        
        return Inertia::render('Vendors/Index', [
            'vendors' => $vendors,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Vendors/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_registration_number' => 'required|string|max:255|unique:vendors',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:vendors',
            'phone_number' => 'required|string|max:255',
            'address' => 'required|string',
            'status' => 'required|in:active,inactive,suspended',
            'contract_start_date' => 'required|date',
            'contract_end_date' => 'nullable|date|after:contract_start_date',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $vendor = Vendor::create($validated);

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor created successfully.');
    }

    public function show(Vendor $vendor)
    {
        $vendor->load(['drivers.user', 'vehicles', 'routes']);
        
        $statistics = [
            'total_drivers' => $vendor->drivers()->count(),
            'active_drivers' => $vendor->drivers()->where('status', 'active')->count(),
            'total_vehicles' => $vendor->vehicles()->count(),
            'active_vehicles' => $vendor->vehicles()->where('status', 'active')->count(),
            'total_routes' => $vendor->routes()->count(),
            'open_incidents' => $vendor->getOpenIncidentsCount(),
            'active_sos_alerts' => $vendor->getActiveSosAlertsCount(),
        ];

        return Inertia::render('Vendors/Show', [
            'vendor' => $vendor,
            'statistics' => $statistics,
        ]);
    }

    public function edit(Vendor $vendor)
    {
        return Inertia::render('Vendors/Edit', [
            'vendor' => $vendor
        ]);
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_registration_number' => 'required|string|max:255|unique:vendors,company_registration_number,' . $vendor->id,
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:vendors,email,' . $vendor->id,
            'phone_number' => 'required|string|max:255',
            'address' => 'required|string',
            'status' => 'required|in:active,inactive,suspended',
            'contract_start_date' => 'required|date',
            'contract_end_date' => 'nullable|date|after:contract_start_date',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $vendor->update($validated);

        return redirect()->route('vendors.show', $vendor)
            ->with('success', 'Vendor updated successfully.');
    }

    public function destroy(Vendor $vendor)
    {
        // Check if vendor has associated data
        if ($vendor->drivers()->count() > 0 || $vendor->vehicles()->count() > 0 || $vendor->routes()->count() > 0) {
            return redirect()->route('vendors.index')
                ->with('error', 'Cannot delete vendor with associated drivers, vehicles, or routes.');
        }

        $vendor->delete();

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor deleted successfully.');
    }
}
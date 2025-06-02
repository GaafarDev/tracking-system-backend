<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Incident;
use App\Models\SosAlert;
use App\Models\Location;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Remove the constructor and add the admin middleware to routes instead

    public function index()
    {
        // Get active drivers with latest locations
        $latestLocations = Location::with(['driver.user', 'vehicle'])
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('locations')
                    ->groupBy('driver_id');
            })
            ->where('recorded_at', '>=', now()->subMinutes(30)) // Only recent locations
            ->orderBy('recorded_at', 'desc')
            ->get();
            
        // Count open incidents (reported + in_progress)
        $openIncidents = Incident::whereIn('status', ['reported', 'in_progress'])->count();
        
        // Count active SOS alerts
        $activeSosAlerts = SosAlert::where('status', 'active')->count();
        
        return Inertia::render('Dashboard', [
            'activeDrivers' => $latestLocations,
            'openIncidentsCount' => $openIncidents,
            'activeSosAlertsCount' => $activeSosAlerts,
            'activeDriversCount' => $latestLocations->count(), // Make sure this is passed
        ]);
    }
    
    /**
     * Get the latest dashboard statistics via API
     */
    public function stats()
    {
        // Get active drivers from recent locations grouped by vendor
        $activeDriversByVendor = Location::select('drivers.vendor_id', DB::raw('COUNT(DISTINCT locations.driver_id) as active_count'))
            ->join('drivers', 'locations.driver_id', '=', 'drivers.id')
            ->where('locations.recorded_at', '>=', now()->subMinutes(30))
            ->groupBy('drivers.vendor_id')
            ->with('vendor')
            ->get();

        // Get active vehicles from recent locations grouped by vendor
        $activeVehiclesByVendor = Location::select('vehicles.vendor_id', DB::raw('COUNT(DISTINCT locations.vehicle_id) as active_count'))
            ->join('vehicles', 'locations.vehicle_id', '=', 'vehicles.id')
            ->where('locations.recorded_at', '>=', now()->subMinutes(30))
            ->groupBy('vehicles.vendor_id')
            ->get();

        // Overall stats
        $stats = [
            'vendors' => [
                'total' => Vendor::count(),
                'active' => Vendor::where('status', 'active')->count(),
                'inactive' => Vendor::where('status', 'inactive')->count(),
                'suspended' => Vendor::where('status', 'suspended')->count(),
            ],
            'admins' => [
                'total' => User::where('role', 'admin')->count(),
            ],
            'drivers' => [
                'total' => Driver::count(),
                'active' => Driver::where('status', 'active')->count(),
                'inactive' => Driver::where('status', 'inactive')->count(),
                'on_leave' => Driver::where('status', 'on_leave')->count(),
            ],
            'vehicles' => [
                'total' => Vehicle::count(),
                'active' => Vehicle::where('status', 'active')->count(),
                'maintenance' => Vehicle::where('status', 'maintenance')->count(),
                'inactive' => Vehicle::where('status', 'inactive')->count(),
            ],
            'routes' => [
                'total' => Route::count(),
            ],
            'schedules' => [
                'total' => Schedule::count(),
                'active' => Schedule::where('is_active', true)->count(),
            ],
            'incidents' => [
                'total' => Incident::count(),
                'active' => Incident::whereIn('status', ['reported', 'in_progress'])->count(),
                'resolved' => Incident::where('status', 'resolved')->count(),
            ],
            'sos_alerts' => [
                'total' => SosAlert::count(),
                'active' => SosAlert::where('status', 'active')->count(),
            ],
            // Enhanced stats with vendor breakdown
            'vendor_breakdown' => [
                'drivers_by_vendor' => Driver::select('vendor_id', DB::raw('count(*) as total'))
                    ->with('vendor:id,name')
                    ->groupBy('vendor_id')
                    ->get(),
                'vehicles_by_vendor' => Vehicle::select('vendor_id', DB::raw('count(*) as total'))
                    ->with('vendor:id,name')
                    ->groupBy('vendor_id')
                    ->get(),
                'routes_by_vendor' => Route::select('vendor_id', DB::raw('count(*) as total'))
                    ->with('vendor:id,name')
                    ->groupBy('vendor_id')
                    ->get(),
                'incidents_by_vendor' => Incident::join('drivers', 'incidents.driver_id', '=', 'drivers.id')
                    ->select('drivers.vendor_id', DB::raw('count(*) as total'))
                    ->groupBy('drivers.vendor_id')
                    ->get(),
            ],
            // Keep existing fields for backwards compatibility
            'activeDriversCount' => Location::select('driver_id')
                ->where('recorded_at', '>=', now()->subMinutes(30))
                ->distinct()
                ->count(),
            'activeVehiclesCount' => Location::select('vehicle_id')
                ->where('recorded_at', '>=', now()->subMinutes(30))
                ->distinct()
                ->count(),
            'openIncidentsCount' => Incident::whereIn('status', ['reported', 'in_progress'])->count(),
            'activeSosAlertsCount' => SosAlert::where('status', 'active')->count(),
        ];
        
        return response()->json($stats);
    }
}
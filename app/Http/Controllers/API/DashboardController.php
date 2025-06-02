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
    public function stats(Request $request)
    {
        $vendorId = $request->get('vendor_id');
        
        // Get active drivers from recent locations grouped by vendor
        $activeDriversByVendorQuery = Location::select('drivers.vendor_id', DB::raw('COUNT(DISTINCT locations.driver_id) as active_count'))
            ->join('drivers', 'locations.driver_id', '=', 'drivers.id')
            ->where('locations.recorded_at', '>=', now()->subMinutes(30));
        
        if ($vendorId) {
            $activeDriversByVendorQuery->where('drivers.vendor_id', $vendorId);
        }
        
        $activeDriversByVendor = $activeDriversByVendorQuery->groupBy('drivers.vendor_id')
            ->with('vendor')
            ->get();

        // Get active vehicles from recent locations grouped by vendor
        $activeVehiclesByVendorQuery = Location::select('vehicles.vendor_id', DB::raw('COUNT(DISTINCT locations.vehicle_id) as active_count'))
            ->join('vehicles', 'locations.vehicle_id', '=', 'vehicles.id')
            ->where('locations.recorded_at', '>=', now()->subMinutes(30));
            
        if ($vendorId) {
            $activeVehiclesByVendorQuery->where('vehicles.vendor_id', $vendorId);
        }
        
        $activeVehiclesByVendor = $activeVehiclesByVendorQuery->groupBy('vehicles.vendor_id')
            ->get();

        // Apply vendor filtering to other stats
        $vendorsQuery = Vendor::query();
        $driversQuery = Driver::query();
        $vehiclesQuery = Vehicle::query();
        $routesQuery = Route::query();
        $schedulesQuery = Schedule::query();
        $incidentsQuery = Incident::query();
        $sosAlertsQuery = SosAlert::query();
        
        if ($vendorId) {
            $vendorsQuery->where('id', $vendorId);
            $driversQuery->where('vendor_id', $vendorId);
            $vehiclesQuery->where('vendor_id', $vendorId);
            $routesQuery->where('vendor_id', $vendorId);
            $schedulesQuery->where('vendor_id', $vendorId);
            $incidentsQuery->whereHas('driver', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            });
            $sosAlertsQuery->whereHas('driver', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            });
        }

        // Overall stats
        $stats = [
            'vendors' => [
                'total' => $vendorsQuery->count(),
                'active' => (clone $vendorsQuery)->where('status', 'active')->count(),
                'inactive' => (clone $vendorsQuery)->where('status', 'inactive')->count(),
                'suspended' => (clone $vendorsQuery)->where('status', 'suspended')->count(),
            ],
            'admins' => [
                'total' => User::where('role', 'admin')->count(),
            ],
            'drivers' => [
                'total' => $driversQuery->count(),
                'active' => (clone $driversQuery)->where('status', 'active')->count(),
                'inactive' => (clone $driversQuery)->where('status', 'inactive')->count(),
                'on_leave' => (clone $driversQuery)->where('status', 'on_leave')->count(),
            ],
            'vehicles' => [
                'total' => $vehiclesQuery->count(),
                'active' => (clone $vehiclesQuery)->where('status', 'active')->count(),
                'maintenance' => (clone $vehiclesQuery)->where('status', 'maintenance')->count(),
                'inactive' => (clone $vehiclesQuery)->where('status', 'inactive')->count(),
            ],
            'routes' => [
                'total' => $routesQuery->count(),
            ],
            'schedules' => [
                'total' => $schedulesQuery->count(),
                'active' => (clone $schedulesQuery)->where('is_active', true)->count(),
            ],
            'incidents' => [
                'total' => $incidentsQuery->count(),
                'active' => (clone $incidentsQuery)->whereIn('status', ['reported', 'in_progress'])->count(),
                'resolved' => (clone $incidentsQuery)->where('status', 'resolved')->count(),
            ],
            'sos_alerts' => [
                'total' => $sosAlertsQuery->count(),
                'active' => (clone $sosAlertsQuery)->where('status', 'active')->count(),
            ],
            // Enhanced stats with vendor breakdown
            'vendor_breakdown' => [
                'drivers_by_vendor' => Driver::select('vendor_id', DB::raw('count(*) as total'))
                    ->with('vendor:id,name')
                    ->when($vendorId, function($q) use ($vendorId) {
                        return $q->where('vendor_id', $vendorId);
                    })
                    ->groupBy('vendor_id')
                    ->get(),
                'vehicles_by_vendor' => Vehicle::select('vendor_id', DB::raw('count(*) as total'))
                    ->with('vendor:id,name')
                    ->when($vendorId, function($q) use ($vendorId) {
                        return $q->where('vendor_id', $vendorId);
                    })
                    ->groupBy('vendor_id')
                    ->get(),
                'routes_by_vendor' => Route::select('vendor_id', DB::raw('count(*) as total'))
                    ->with('vendor:id,name')
                    ->when($vendorId, function($q) use ($vendorId) {
                        return $q->where('vendor_id', $vendorId);
                    })
                    ->groupBy('vendor_id')
                    ->get(),
                'incidents_by_vendor' => Incident::join('drivers', 'incidents.driver_id', '=', 'drivers.id')
                    ->select('drivers.vendor_id', DB::raw('count(*) as total'))
                    ->when($vendorId, function($q) use ($vendorId) {
                        return $q->where('drivers.vendor_id', $vendorId);
                    })
                    ->groupBy('drivers.vendor_id')
                    ->get(),
            ],
            // Keep existing fields for backwards compatibility with vendor filtering
            'activeDriversCount' => Location::select('driver_id')
                ->when($vendorId, function($q) use ($vendorId) {
                    return $q->whereHas('driver', function($dq) use ($vendorId) {
                        $dq->where('vendor_id', $vendorId);
                    });
                })
                ->where('recorded_at', '>=', now()->subMinutes(30))
                ->distinct()
                ->count(),
            'activeVehiclesCount' => Location::select('vehicle_id')
                ->when($vendorId, function($q) use ($vendorId) {
                    return $q->whereHas('vehicle', function($vq) use ($vendorId) {
                        $vq->where('vendor_id', $vendorId);
                    });
                })
                ->where('recorded_at', '>=', now()->subMinutes(30))
                ->distinct()
                ->count(),
            'openIncidentsCount' => Incident::whereIn('status', ['reported', 'in_progress'])
                ->when($vendorId, function($q) use ($vendorId) {
                    return $q->whereHas('driver', function($dq) use ($vendorId) {
                        $dq->where('vendor_id', $vendorId);
                    });
                })
                ->count(),
            'activeSosAlertsCount' => SosAlert::where('status', 'active')
                ->when($vendorId, function($q) use ($vendorId) {
                    return $q->whereHas('driver', function($dq) use ($vendorId) {
                        $dq->where('vendor_id', $vendorId);
                    });
                })
                ->count(),
        ];
        
        return response()->json($stats);
    }
}
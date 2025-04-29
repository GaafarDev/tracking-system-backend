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
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        // Get active drivers with latest locations
        $latestLocations = Location::with(['driver.user', 'vehicle'])
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('locations')
                    ->groupBy('driver_id');
            })
            ->orderBy('recorded_at', 'desc')
            ->get();
            
        // Count open incidents (reported + in_progress)
        $openIncidents = Incident::whereIn('status', ['reported', 'in_progress'])->count();
        
        // Count active SOS alerts
        $activeSosAlerts = SosAlert::where('status', 'active')->count();
        
        return Inertia::render('Dashboard', [
            'activeDrivers' => $latestLocations,
            'openIncidentsCount' => $openIncidents,
            'activeSosAlertsCount' => $activeSosAlerts
        ]);
    }
    
    /**
     * Get the latest dashboard statistics via API
     */
    public function stats()
    {
        // Force fresh count without caching
        $stats = [
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
            // Add these fields explicitly for the dashboard
            'openIncidentsCount' => Incident::whereIn('status', ['reported', 'in_progress'])->count(),
            'activeSosAlertsCount' => SosAlert::where('status', 'active')->count(),
        ];
        
        return response()->json($stats);
    }
}
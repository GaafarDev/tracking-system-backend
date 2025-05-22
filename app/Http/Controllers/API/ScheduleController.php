<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use App\Models\Schedule;
use App\Models\Driver;
use App\Models\Route;
use App\Models\Vehicle;
use App\Models\Notification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        // Check if this is an API request from mobile app
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->handleApiRequest($request);
        }
        
        // Handle web request (existing code)
        $query = Schedule::with(['route', 'driver.user', 'vehicle']);
        
        // Filter by driver if provided
        if ($request->has('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }
        
        // Filter by route if provided
        if ($request->has('route_id')) {
            $query->where('route_id', $request->route_id);
        }
        
        // Filter by day of week if provided
        if ($request->has('day_of_week')) {
            $query->where('day_of_week', $request->day_of_week);
        }
        
        // Filter by active status if provided
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }
        
        $schedules = $query->orderBy('day_of_week')
            ->orderBy('departure_time')
            ->paginate(20);
        
        $drivers = Driver::with('user')->get();
        $routes = Route::all();
        $vehicles = Vehicle::where('status', 'active')->get();
        
        return Inertia::render('Schedules/Index', [
            'schedules' => $schedules,
            'drivers' => $drivers,
            'routes' => $routes,
            'vehicles' => $vehicles,
            'filters' => $request->only(['driver_id', 'route_id', 'day_of_week', 'is_active']),
        ]);
    }

    private function handleApiRequest(Request $request)
    {
        try {
            $user = $request->user();
            $driver = Driver::where('user_id', $user->id)->first();
            
            if (!$driver) {
                return response()->json([
                    'message' => 'Driver not found',
                    'data' => []
                ], 404);
            }
            
            $query = Schedule::with(['route', 'vehicle'])
                ->where('driver_id', $driver->id);
            
            // Filter by day of week if provided
            if ($request->has('day_of_week')) {
                $query->where('day_of_week', $request->day_of_week);
            }
            
            // Only active schedules for mobile app
            $query->where('is_active', true);
            
            $schedules = $query->orderBy('day_of_week')
                ->orderBy('departure_time')
                ->get();
            
            return response()->json([
                'data' => $schedules
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching schedules',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function create()
    {
        $drivers = Driver::with('user')->where('status', 'active')->get();
        $routes = Route::all();
        $vehicles = Vehicle::where('status', 'active')->get();
        
        return Inertia::render('Schedules/Create', [
            'drivers' => $drivers,
            'routes' => $routes,
            'vehicles' => $vehicles,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'driver_id' => 'required|exists:drivers,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'departure_time' => 'required|date_format:H:i',
            'arrival_time' => 'required|date_format:H:i|after:departure_time',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'is_active' => 'boolean',
        ]);
        
        // Check for schedule conflicts
        $this->checkScheduleConflicts($validated);
        
        $schedule = Schedule::create($validated);
        
        // Notify the driver
        $this->notifyDriver($schedule, 'assigned');
        
        return redirect()->route('schedules.index')
            ->with('success', 'Schedule created successfully.');
    }
    
    private function checkScheduleConflicts($data)
    {
        // Check if driver is already scheduled at the same time
        $driverConflict = Schedule::where('driver_id', $data['driver_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->where(function($query) use ($data) {
                $query->whereBetween('departure_time', [$data['departure_time'], $data['arrival_time']])
                    ->orWhereBetween('arrival_time', [$data['departure_time'], $data['arrival_time']]);
            })
            ->exists();
            
        if ($driverConflict) {
            return back()->with('error', 'Driver is already scheduled during this time period');
        }
        
        // Check if vehicle is already scheduled at the same time
        $vehicleConflict = Schedule::where('vehicle_id', $data['vehicle_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->where(function($query) use ($data) {
                $query->whereBetween('departure_time', [$data['departure_time'], $data['arrival_time']])
                    ->orWhereBetween('arrival_time', [$data['departure_time'], $data['arrival_time']]);
            })
            ->exists();
            
        if ($vehicleConflict) {
            return back()->with('error', 'Vehicle is already scheduled during this time period');
        }
    }
    
    private function notifyDriver($schedule, $action)
    {
        $driver = Driver::findOrFail($schedule->driver_id);
        
        $dayCapitalized = ucfirst($schedule->day_of_week);
        
        Notification::create([
            'user_id' => $driver->user_id,
            'title' => 'Schedule Update',
            'message' => "You have been {$action} to a schedule on {$dayCapitalized} from {$schedule->departure_time} to {$schedule->arrival_time}.",
            'type' => 'schedule',
        ]);
    }

    public function show(Schedule $schedule)
    {
        $schedule->load(['route', 'driver.user', 'vehicle']);
        
        return Inertia::render('Schedules/Show', [
            'schedule' => $schedule,
        ]);
    }

    public function edit(Schedule $schedule)
    {
        $drivers = Driver::with('user')->where('status', 'active')->get();
        $routes = Route::all();
        $vehicles = Vehicle::where('status', 'active')->get();
        
        return Inertia::render('Schedules/Edit', [
            'schedule' => $schedule,
            'drivers' => $drivers,
            'routes' => $routes,
            'vehicles' => $vehicles,
        ]);
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'route_id' => 'sometimes|required|exists:routes,id',
            'driver_id' => 'sometimes|required|exists:drivers,id',
            'vehicle_id' => 'sometimes|required|exists:vehicles,id',
            'departure_time' => 'sometimes|required|date_format:H:i',
            'arrival_time' => 'sometimes|required|date_format:H:i|after:departure_time',
            'day_of_week' => 'sometimes|required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'is_active' => 'sometimes|boolean',
        ]);
        
        $oldDriverId = $schedule->driver_id;
        $schedule->update($validated);
        
        // If driver changed, notify both the old and new driver
        if (isset($validated['driver_id']) && $oldDriverId != $validated['driver_id']) {
            // Notify the old driver about removal
            Notification::create([
                'user_id' => Driver::findOrFail($oldDriverId)->user_id,
                'title' => 'Schedule Update',
                'message' => "You have been removed from a schedule on " . ucfirst($schedule->day_of_week) . ".",
                'type' => 'schedule',
            ]);
            
            // Notify the new driver about assignment
            $this->notifyDriver($schedule, 'assigned');
        } 
        // If schedule details changed but not the driver, notify the same driver
        elseif (isset($validated['day_of_week']) || isset($validated['departure_time']) || isset($validated['arrival_time'])) {
            $this->notifyDriver($schedule, 'updated');
        }
        
        return redirect()->route('schedules.index')
            ->with('success', 'Schedule updated successfully.');
    }

    public function destroy(Schedule $schedule)
    {
        $driver = Driver::findOrFail($schedule->driver_id);
        
        // Notify the driver about removal
        Notification::create([
            'user_id' => $driver->user_id,
            'title' => 'Schedule Update',
            'message' => "A schedule on " . ucfirst($schedule->day_of_week) . " from {$schedule->departure_time} to {$schedule->arrival_time} has been cancelled.",
            'type' => 'schedule',
        ]);
        
        $schedule->delete();
        
        return redirect()->route('schedules.index')
            ->with('success', 'Schedule deleted successfully.');
    }
}
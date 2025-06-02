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
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        // Check if this is an API request from mobile app
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->handleApiRequest($request);
        }
        
        // Handle web request (existing code for admin dashboard)
        return $this->handleWebRequest($request);
    }

    private function handleApiRequest(Request $request)
    {
        Log::info('=== SCHEDULE API REQUEST ===', [
            'user_id' => $request->user()->id ?? 'null',
            'request_data' => $request->all()
        ]);

        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'message' => 'User not authenticated',
                    'data' => []
                ], 401);
            }

            // Check if user is a driver
            if (!$user->isDriver()) {
                return response()->json([
                    'message' => 'Access denied. Driver role required.',
                    'data' => []
                ], 403);
            }
            
            $driver = Driver::where('user_id', $user->id)->first();
            if (!$driver) {
                Log::error('Driver not found for user', ['user_id' => $user->id]);
                return response()->json([
                    'message' => 'Driver profile not found',
                    'data' => []
                ], 404);
            }
            
            Log::info('Driver found', ['driver_id' => $driver->id]);

            // Build query for driver's schedules
            $query = Schedule::with(['route', 'vehicle'])
                ->where('driver_id', $driver->id);
            
            // Filter by day of week if provided
            if ($request->has('day_of_week') && !empty($request->day_of_week)) {
                $query->where('day_of_week', strtolower($request->day_of_week));
            }
            
            // Filter by active status - simplified logic
            $isActive = $request->get('is_active');
            if ($isActive !== null) {
                if (in_array($isActive, [true, 'true', '1', 1])) {
                    $query->where('is_active', true);
                } elseif (in_array($isActive, [false, 'false', '0', 0])) {
                    $query->where('is_active', false);
                }
            } else {
                // Default: only show active schedules for mobile app
                $query->where('is_active', true);
            }
            
            $schedules = $query->orderByRaw("
                CASE day_of_week
                    WHEN 'monday' THEN 1
                    WHEN 'tuesday' THEN 2
                    WHEN 'wednesday' THEN 3
                    WHEN 'thursday' THEN 4
                    WHEN 'friday' THEN 5
                    WHEN 'saturday' THEN 6
                    WHEN 'sunday' THEN 7
                    ELSE 8
                END
            ")
            ->orderBy('departure_time')
            ->get();
            
            Log::info('Schedules found', ['count' => $schedules->count()]);
            
            return response()->json([
                'data' => $schedules,
                'message' => 'Schedules retrieved successfully'
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Error fetching schedules', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Error fetching schedules',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                'data' => []
            ], 500);
        }
    }

    private function handleWebRequest(Request $request)
    {
        $query = Schedule::with(['route', 'driver.user', 'vehicle']);
        
        // Filter by driver if provided
        if ($request->has('driver_id') && $request->driver_id !== 'all') {
            $query->where('driver_id', $request->driver_id);
        }
        
        // Filter by route if provided
        if ($request->has('route_id') && $request->route_id !== 'all') {
            $query->where('route_id', $request->route_id);
        }
        
        // Filter by day of week if provided
        if ($request->has('day_of_week') && $request->day_of_week !== 'all') {
            $query->where('day_of_week', $request->day_of_week);
        }
        
        // Filter by active status if provided
        if ($request->has('is_active') && $request->is_active !== '') {
            $isActive = $request->is_active;
            if (in_array($isActive, [true, 'true', '1', 1])) {
                $query->where('is_active', true);
            } elseif (in_array($isActive, [false, 'false', '0', 0])) {
                $query->where('is_active', false);
            }
        }
        
        // Add search functionality for web requests
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('route', function($routeQuery) use ($search) {
                    $routeQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('driver.user', function($driverQuery) use ($search) {
                    $driverQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('vehicle', function($vehicleQuery) use ($search) {
                    $vehicleQuery->where('plate_number', 'like', "%{$search}%");
                });
            });
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
            'filters' => $request->only(['driver_id', 'route_id', 'day_of_week', 'is_active', 'search']),
        ]);
    }

    // Add a specific endpoint for getting today's schedules
    public function getTodaySchedules(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user || !$user->isDriver()) {
                return response()->json([
                    'message' => 'Access denied. Driver role required.',
                    'data' => []
                ], 403);
            }

            $driver = Driver::where('user_id', $user->id)->first();
            if (!$driver) {
                return response()->json([
                    'message' => 'Driver profile not found',
                    'data' => []
                ], 404);
            }

            // Get today's day of week
            $today = strtolower(now()->format('l')); // 'monday', 'tuesday', etc.

            $todaySchedules = Schedule::with(['route', 'vehicle'])
                ->where('driver_id', $driver->id)
                ->where('day_of_week', $today)
                ->where('is_active', true)
                ->orderBy('departure_time')
                ->get();

            return response()->json([
                'data' => $todaySchedules,
                'today' => $today,
                'message' => 'Today\'s schedules retrieved successfully'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching today\'s schedules', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Error fetching today\'s schedules',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                'data' => []
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
            ->where('is_active', true)
            ->where(function($query) use ($data) {
                $query->whereBetween('departure_time', [$data['departure_time'], $data['arrival_time']])
                    ->orWhereBetween('arrival_time', [$data['departure_time'], $data['arrival_time']])
                    ->orWhere(function($q) use ($data) {
                        $q->where('departure_time', '<=', $data['departure_time'])
                          ->where('arrival_time', '>=', $data['arrival_time']);
                    });
            })
            ->exists();
            
        if ($driverConflict) {
            abort(422, 'Driver is already scheduled during this time period');
        }
        
        // Check if vehicle is already scheduled at the same time
        $vehicleConflict = Schedule::where('vehicle_id', $data['vehicle_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->where('is_active', true)
            ->where(function($query) use ($data) {
                $query->whereBetween('departure_time', [$data['departure_time'], $data['arrival_time']])
                    ->orWhereBetween('arrival_time', [$data['departure_time'], $data['arrival_time']])
                    ->orWhere(function($q) use ($data) {
                        $q->where('departure_time', '<=', $data['departure_time'])
                          ->where('arrival_time', '>=', $data['arrival_time']);
                    });
            })
            ->exists();
            
        if ($vehicleConflict) {
            abort(422, 'Vehicle is already scheduled during this time period');
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

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Driver;
use App\Models\Notification;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
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
        
        return response()->json($schedules);
    }

    /**
     * Store a newly created resource in storage.
     */
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
        
        return response()->json([
            'message' => 'Schedule created successfully',
            'schedule' => $schedule,
        ], 201);
    }
    
    /**
     * Check for schedule conflicts.
     */
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
            abort(422, 'Driver is already scheduled during this time period');
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
            abort(422, 'Vehicle is already scheduled during this time period');
        }
    }
    
    /**
     * Notify driver about schedule changes.
     */
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $schedule = Schedule::with(['route', 'driver.user', 'vehicle'])->findOrFail($id);
        
        return response()->json($schedule);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $schedule = Schedule::findOrFail($id);
        
        $validated = $request->validate([
            'route_id' => 'sometimes|required|exists:routes,id',
            'driver_id' => 'sometimes|required|exists:drivers,id',
            'vehicle_id' => 'sometimes|required|exists:vehicles,id',
            'departure_time' => 'sometimes|required|date_format:H:i',
            'arrival_time' => 'sometimes|required|date_format:H:i|after:departure_time',
            'day_of_week' => 'sometimes|required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'is_active' => 'sometimes|boolean',
        ]);
        
        // If any timing or resource assignment changed, check for conflicts
        if (isset($validated['driver_id']) || isset($validated['vehicle_id']) || 
            isset($validated['day_of_week']) || isset($validated['departure_time']) || 
            isset($validated['arrival_time'])) {
            
            // Merge existing data with updates for conflict check
            $checkData = array_merge([
                'driver_id' => $schedule->driver_id,
                'vehicle_id' => $schedule->vehicle_id,
                'day_of_week' => $schedule->day_of_week,
                'departure_time' => $schedule->departure_time,
                'arrival_time' => $schedule->arrival_time,
            ], $validated);
            
            $this->checkScheduleConflicts($checkData);
        }
        
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
        
        return response()->json([
            'message' => 'Schedule updated successfully',
            'schedule' => $schedule,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $schedule = Schedule::findOrFail($id);
        $driver = Driver::findOrFail($schedule->driver_id);
        
        // Notify the driver about removal
        Notification::create([
            'user_id' => $driver->user_id,
            'title' => 'Schedule Update',
            'message' => "A schedule on " . ucfirst($schedule->day_of_week) . " from {$schedule->departure_time} to {$schedule->arrival_time} has been cancelled.",
            'type' => 'schedule',
        ]);
        
        $schedule->delete();
        
        return response()->json([
            'message' => 'Schedule deleted successfully',
        ]);
    }
}
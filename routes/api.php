<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DriverController;
use App\Http\Controllers\API\VehicleController;
use App\Http\Controllers\API\RouteController;
use App\Http\Controllers\API\LocationController;
use App\Http\Controllers\API\ScheduleController;
use App\Http\Controllers\API\IncidentController;
use App\Http\Controllers\API\SosAlertController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\WeatherUpdateController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Drivers
    Route::apiResource('drivers', DriverController::class);
    
    // Vehicles
    Route::apiResource('vehicles', VehicleController::class);
    
    // Routes
    Route::apiResource('routes', RouteController::class);
    
    // Locations
    Route::post('/locations/update', [LocationController::class, 'updateLocation']);
    Route::get('/locations/latest', [LocationController::class, 'getLatestLocations']);
    Route::apiResource('locations', LocationController::class);
    
    // Schedules
    Route::apiResource('schedules', ScheduleController::class);
    
    // Incidents
    Route::post('/incidents/report', [IncidentController::class, 'report']);
    Route::apiResource('incidents', IncidentController::class);
    
    // SOS Alerts
    Route::post('/sos/send', [SosAlertController::class, 'send']);
    Route::post('/sos/{sosAlert}/respond', [SosAlertController::class, 'respond']);
    Route::post('/sos/{sosAlert}/resolve', [SosAlertController::class, 'resolve']);
    Route::apiResource('sos', SosAlertController::class, ['names' => 'sos-alerts']);
    
    // Notifications
    Route::get('/notifications/unread', [NotificationController::class, 'unread']);
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::apiResource('notifications', NotificationController::class);
    
    // Weather Updates
    Route::get('/weather/latest', [WeatherUpdateController::class, 'latest']);
    Route::apiResource('weather', WeatherUpdateController::class, ['names' => 'weather-updates']);
});
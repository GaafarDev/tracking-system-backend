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
use App\Http\Controllers\API\DashboardController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Add CORS preflight handling
Route::options('{any}', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
})->where('any', '.*');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Drivers (mobile app endpoints)
    Route::get('/drivers/me', [DriverController::class, 'me']);
    
    // Admin-only driver management
    Route::middleware('admin')->group(function () {
        Route::apiResource('drivers', DriverController::class)->except(['show']);
    });
    
    // Vehicles (admin only)
    Route::middleware('admin')->group(function () {
        Route::apiResource('vehicles', VehicleController::class);
    });
    
    // Routes (admin only)
    Route::middleware('admin')->group(function () {
        Route::apiResource('routes', RouteController::class);
    });
    
    // Locations
    Route::post('/locations/update', [LocationController::class, 'updateLocation']);
    Route::get('/locations/latest', [LocationController::class, 'getLatestLocations']);
    Route::apiResource('locations', LocationController::class);
    
    // Schedules
    Route::get('/schedules', [ScheduleController::class, 'index']); // Works for both web and API
    Route::get('/schedules/today', [ScheduleController::class, 'getTodaySchedules']); // New endpoint for mobile
    
    // Admin-only schedule management
    Route::middleware('admin')->group(function () {
        Route::post('/schedules', [ScheduleController::class, 'store']);
        Route::put('/schedules/{schedule}', [ScheduleController::class, 'update']);
        Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy']);
        Route::get('/schedules/{schedule}', [ScheduleController::class, 'show']);
    });
    
    // Incidents
    Route::post('/incidents/report', [IncidentController::class, 'report']); // Mobile app
    Route::get('/incidents', [IncidentController::class, 'getIncidents']); // Mobile app - get driver's incidents
    
    // Admin-only incident management
    Route::middleware('admin')->group(function () {
        Route::get('/incidents/all', [IncidentController::class, 'index']); // Admin view all incidents
        Route::get('/incidents/{incident}', [IncidentController::class, 'show']);
        Route::put('/incidents/{incident}', [IncidentController::class, 'update']);
        Route::delete('/incidents/{incident}', [IncidentController::class, 'destroy']);
    });
    
    // SOS Alerts
    Route::post('/sos/send', [SosAlertController::class, 'send']); // Mobile app
    Route::get('/sos/active', [SosAlertController::class, 'getActiveSosAlert']); // Mobile app
    Route::post('/sos/{id}/cancel', [SosAlertController::class, 'cancelSosAlert']); // Mobile app
    
    // Admin-only SOS management
    Route::middleware('admin')->group(function () {
        Route::get('/sos', [SosAlertController::class, 'index']);
        Route::get('/sos/{sosAlert}', [SosAlertController::class, 'show']);
        Route::post('/sos/{sosAlert}/respond', [SosAlertController::class, 'respond']);
        Route::post('/sos/{sosAlert}/resolve', [SosAlertController::class, 'resolve']);
    });
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread', [NotificationController::class, 'unread']);
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::get('/notifications/{notification}', [NotificationController::class, 'show']);
    
    // Weather Updates
    Route::get('/weather/latest', [WeatherUpdateController::class, 'latest']);
    
    // Admin-only weather management
    Route::middleware('admin')->group(function () {
        Route::apiResource('weather', WeatherUpdateController::class);
    });

    // Dashboard (admin only)
    Route::middleware('admin')->group(function () {
        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    });
});
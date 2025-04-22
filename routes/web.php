<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\SosAlertController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Driver routes - using resource controller
    Route::resource('drivers', DriverController::class);
    
    // Vehicle routes - using resource controller
    Route::resource('vehicles', VehicleController::class);
    
    // Route routes - using resource controller
    Route::resource('routes', RouteController::class);
    
    // Schedule routes - using resource controller
    Route::resource('schedules', ScheduleController::class);
    
    // Incident routes - using resource controller
    Route::resource('incidents', IncidentController::class);
    
    // SOS Alert routes
    Route::get('/sos', [SosAlertController::class, 'index'])->name('sos.index');
    Route::get('/sos/{sosAlert}', [SosAlertController::class, 'show'])->name('sos.show');
    Route::post('/sos/{sosAlert}/respond', [SosAlertController::class, 'respond'])->name('sos.respond');
    Route::post('/sos/{sosAlert}/resolve', [SosAlertController::class, 'resolve'])->name('sos.resolve');
});
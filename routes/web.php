<?php
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\DriverController;
use App\Http\Controllers\API\VehicleController;
use App\Http\Controllers\API\RouteController;
use App\Http\Controllers\API\ScheduleController;
use App\Http\Controllers\API\IncidentController;
use App\Http\Controllers\API\SosAlertController;

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
    Route::get('/dashboard', [App\Http\Controllers\API\DashboardController::class, 'index'])->name('dashboard');
    
    // Resource controllers
    Route::resource('drivers', DriverController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('routes', RouteController::class);
    Route::resource('schedules', ScheduleController::class);
    Route::resource('incidents', IncidentController::class);
    
    
    // SOS Alert routes
    Route::get('/sos', [SosAlertController::class, 'index'])->name('sos.index');
    Route::get('/sos/{sosAlert}', [SosAlertController::class, 'show'])->name('sos.show');
    Route::post('/sos/{sosAlert}/respond', [SosAlertController::class, 'respond'])->name('sos.respond');
    Route::post('/sos/{sosAlert}/resolve', [SosAlertController::class, 'resolve'])->name('sos.resolve');
});
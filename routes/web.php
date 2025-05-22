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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

// These debug routes are for testing only - remove after testing is complete
Route::get('/debug/test-incident', function() {
    try {
        $incident = \App\Models\Incident::create([
            'driver_id' => 1, // make sure this driver exists
            'type' => 'test',
            'description' => 'Test incident',
            'latitude' => 3.1319,
            'longitude' => 101.6841,
            'status' => 'reported',
        ]);
        
        return response()->json([
            'message' => 'Test incident created',
            'incident' => $incident
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

Route::get('/debug/test-driver', function() {
    $drivers = \App\Models\Driver::with('user')->get();
    $users = \App\Models\User::all();
    
    return response()->json([
        'drivers_count' => $drivers->count(),
        'users_count' => $users->count(),
        'drivers' => $drivers,
        'users' => $users
    ]);
});

Route::get('/debug/test-auth', function(Request $request) {
    return response()->json([
        'authenticated' => Auth::check(),
        'user' => Auth::user(),
        'session_token' => session()->token()
    ]);
})->middleware('web');

Route::get('/debug/check-database', function() {
    try {
        $stats = [
            'drivers_total' => \App\Models\Driver::count(),
            'users_total' => \App\Models\User::count(),
            'incidents_total' => \App\Models\Incident::count(),
            'sos_alerts_total' => \App\Models\SosAlert::count(),
            'locations_total' => \App\Models\Location::count(),
            'schedules_total' => \App\Models\Schedule::count(),
        ];
        
        return response()->json($stats);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Database connection failed',
            'message' => $e->getMessage()
        ], 500);
    }
});

Route::get('/debug/create-test-user', function() {
    try {
        // Check if test user exists
        $user = \App\Models\User::where('email', 'test@example.com')->first();
        
        if (!$user) {
            $user = \App\Models\User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'email_verified_at' => now(),
            ]);
            
            // Create driver profile
            $driver = \App\Models\Driver::create([
                'user_id' => $user->id,
                'license_number' => 'TEST123',
                'phone_number' => '+60123456789',
                'address' => 'Test Address',
                'status' => 'active',
            ]);
            
            return response()->json([
                'message' => 'Test user and driver created',
                'user' => $user,
                'driver' => $driver
            ]);
        } else {
            return response()->json([
                'message' => 'Test user already exists',
                'user' => $user,
                'driver' => $user->driver
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to create test user',
            'message' => $e->getMessage()
        ], 500);
    }
});
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
use App\Http\Controllers\API\VendorController;
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

// Admin-only routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    \App\Http\Middleware\AdminMiddleware::class
])->group(function () {
    // Vendor management routes
    Route::resource('vendors', VendorController::class);
    
    // Dashboard - Admin only
    Route::get('/dashboard', [App\Http\Controllers\API\DashboardController::class, 'index'])->name('dashboard');
    
    // Existing resource controllers - Admin only
    Route::resource('drivers', DriverController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('routes', RouteController::class);
    Route::resource('schedules', ScheduleController::class);
    Route::resource('incidents', IncidentController::class);
    
    // Add this route for password reset
    Route::post('/drivers/{driver}/reset-password', [DriverController::class, 'resetPassword'])
        ->name('drivers.reset-password');
    
    // SOS Alert routes - Admin only
    Route::resource('sos', SosAlertController::class, ['except' => ['create', 'store']]);
    Route::post('/sos/{sosAlert}/respond', [SosAlertController::class, 'respond'])->name('sos.respond');
    Route::post('/sos/{sosAlert}/resolve', [SosAlertController::class, 'resolve'])->name('sos.resolve');
});

// Debug routes - these should be removed in production
Route::prefix('debug')->group(function () {
    Route::get('/test-incident', function() {
        try {
            $incident = \App\Models\Incident::create([
                'driver_id' => 1,
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

    Route::get('/test-driver', function() {
        $drivers = \App\Models\Driver::with('user')->get();
        $users = \App\Models\User::all();
        
        return response()->json([
            'drivers_count' => $drivers->count(),
            'users_count' => $users->count(),
            'drivers' => $drivers,
            'users' => $users
        ]);
    });

    Route::get('/test-auth', function(Request $request) {
        return response()->json([
            'authenticated' => Auth::check(),
            'user' => Auth::user(),
            'session_token' => session()->token()
        ]);
    })->middleware('web');

    Route::get('/check-database', function() {
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

    Route::get('/create-test-user', function() {
        try {
            $user = \App\Models\User::where('email', 'test@example.com')->first();
            
            if (!$user) {
                $user = \App\Models\User::create([
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                    'email_verified_at' => now(),
                ]);
                
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
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    });

    Route::get('/recent-locations', function() {
        try {
            $recentLocations = \App\Models\Location::with(['driver.user', 'vehicle'])
                ->where('recorded_at', '>=', now()->subMinutes(10))
                ->orderBy('recorded_at', 'desc')
                ->get();
                
            return response()->json([
                'total_recent_locations' => $recentLocations->count(),
                'recent_locations' => $recentLocations,
                'all_locations_count' => \App\Models\Location::count(),
                'last_5_locations' => \App\Models\Location::with(['driver.user', 'vehicle'])
                    ->orderBy('recorded_at', 'desc')
                    ->take(5)
                    ->get()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get recent locations',
                'message' => $e->getMessage()
            ], 500);
        }
    });

    Route::get('/dashboard-data', function() {
        try {
            $latestLocations = \App\Models\Location::with(['driver.user', 'vehicle'])
                ->whereIn('id', function ($query) {
                    $query->selectRaw('MAX(id)')
                        ->from('locations')
                        ->groupBy('driver_id');
                })
                ->where('recorded_at', '>=', now()->subMinutes(30))
                ->orderBy('recorded_at', 'desc')
                ->get();
                
            $openIncidents = \App\Models\Incident::whereIn('status', ['reported', 'in_progress'])->count();
            $activeSosAlerts = \App\Models\SosAlert::where('status', 'active')->count();
            
            return response()->json([
                'activeDrivers' => $latestLocations,
                'activeDriversCount' => $latestLocations->count(),
                'openIncidentsCount' => $openIncidents,
                'activeSosAlertsCount' => $activeSosAlerts,
                'debug_info' => [
                    'total_locations_in_db' => \App\Models\Location::count(),
                    'locations_last_30_min' => \App\Models\Location::where('recorded_at', '>=', now()->subMinutes(30))->count(),
                    'current_time' => now(),
                    'cutoff_time' => now()->subMinutes(30)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get dashboard data',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    });
});
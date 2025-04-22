<?php

namespace App\Http\Controllers\API; // Update this line
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Driver;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::with('user')->paginate(10);
        
        return Inertia::render('Drivers/Index', [
            'drivers' => $drivers
        ]);
    }

    public function create()
    {
        return Inertia::render('Drivers/Create');
    }

    public function store(Request $request)
    {
        // Implementation to come
        return redirect()->route('drivers.index');
    }

    public function show(Driver $driver)
    {
        return Inertia::render('Drivers/Show', [
            'driver' => $driver->load('user')
        ]);
    }

    public function edit(Driver $driver)
    {
        return Inertia::render('Drivers/Edit', [
            'driver' => $driver->load('user')
        ]);
    }

    public function update(Request $request, Driver $driver)
    {
        // Implementation to come
        return redirect()->route('drivers.index');
    }

    public function destroy(Driver $driver)
    {
        // Implementation to come
        return redirect()->route('drivers.index');
    }
}
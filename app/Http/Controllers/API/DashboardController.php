<?php

namespace App\Http\Controllers\API; // Update this line
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'activeDrivers' => []  // You'll populate this with real data later
        ]);
    }
}
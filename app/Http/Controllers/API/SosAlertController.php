<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SosAlert;
use App\Models\Notification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SosAlertController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sosAlerts = SosAlert::with('driver.user')
            ->orderByRaw("CASE 
                WHEN status = 'active' THEN 1 
                WHEN status = 'responded' THEN 2 
                WHEN status = 'resolved' THEN 3 
                ELSE 4 
            END")
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return Inertia::render('Sos/Index', [
            'sosAlerts' => $sosAlerts,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(SosAlert $sosAlert)
    {
        $sosAlert->load('driver.user');
        
        return Inertia::render('Sos/Show', [
            'sosAlert' => $sosAlert,
        ]);
    }

    /**
     * Mark SOS alert as responded.
     */
    public function respond(Request $request, SosAlert $sosAlert)
    {
        // Only update if currently active
        if ($sosAlert->status === 'active') {
            $sosAlert->update([
                'status' => 'responded',
                'responded_at' => now(),
            ]);
            
            // Notify the driver
            Notification::create([
                'user_id' => $sosAlert->driver->user_id,
                'title' => 'SOS Alert Update',
                'message' => 'Your SOS alert has been responded to by the support team.',
                'type' => 'sos',
            ]);
        }
        
        return redirect()->back();
    }
    
    /**
     * Mark SOS alert as resolved.
     */
    public function resolve(Request $request, SosAlert $sosAlert)
    {
        // Only update if not already resolved
        if ($sosAlert->status !== 'resolved') {
            $sosAlert->update([
                'status' => 'resolved',
                'resolved_at' => now(),
            ]);
            
            // Notify the driver
            Notification::create([
                'user_id' => $sosAlert->driver->user_id,
                'title' => 'SOS Alert Update',
                'message' => 'Your SOS alert has been resolved by the support team.',
                'type' => 'sos',
            ]);
        }
        
        return redirect()->back();
    }
}
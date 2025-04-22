<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return response()->json($notifications);
    }

    /**
     * Display unread notifications.
     */
    public function unread(Request $request)
    {
        $user = $request->user();
        
        $unreadNotifications = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($unreadNotifications);
    }
    
    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, string $id)
    {
        $user = $request->user();
        
        $notification = Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();
            
        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        
        return response()->json([
            'message' => 'Notification marked as read',
            'notification' => $notification,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $user = $request->user();
        
        $notification = Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();
            
        return response()->json($notification);
    }
}
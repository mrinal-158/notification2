<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class NotifyController extends Controller
{
    public function all(Request $request){
        $notifications = auth()->user()
            ->notifications()                   // relationship query
            ->orderBy('created_at', 'desc')     // sort by latest
            ->get()
            ->map(function($notification){
                return [
                    'message'    => $notification->data['message'] ?? null,   // extract message
                    'created_at' => $notification->created_at->toDateTimeString(),
                ];
            });

        return response()->json([
            'notifications' => $notifications,
        ]);
    }

    public function unread()
    {
        $notifications = auth()->user()
            ->unreadNotifications()                   // relationship query
            ->orderBy('created_at', 'desc')     // sort by latest
            ->get()
            ->map(function($notification){
                return [
                    'message'    => $notification->data['message'] ?? null,   // extract message
                    'created_at' => $notification->created_at->toDateTimeString(),
                ];
            });

        return response()->json([
            'notifications' => $notifications,
        ]);
    }

    public function read(Request $request){
        $notifications = auth()->user()
            ->readNotifications()                   // relationship query
            ->orderBy('created_at', 'desc')     // sort by latest
            ->get()
            ->map(function($notification){
                return [
                    'message'    => $notification->data['message'] ?? null,   // extract message
                    'created_at' => $notification->created_at->toDateTimeString(),
                ];
            });

        return response()->json([
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead(Request $request, $id){
        $notification = auth()->user()
            ->unreadNotifications
            ->where('id', $id)
            ->first();
        
        if(!$notification){
            return response()->json([
                'message' => 'Notification already read or not found!',
            ]);
        }

        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read',
            'notification' => $notification,
        ]);
    }

    public function markAllAsRead(Request $request){
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json([
            'message' => 'All notifications marked as read!',
        ]);
    }
}

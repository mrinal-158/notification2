<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifyController extends Controller
{
    public function showNotification(Request $request){
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
}

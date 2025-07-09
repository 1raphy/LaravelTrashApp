<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NotificationNasabah;
use App\Models\Notifications;
use Illuminate\Support\Facades\Log;

class OperatorNotificationController extends Controller
{
    public function index(Request $request)
    {
        try {
            Log::info("OperatorNotificationController@index called", [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
            ]);

            $notifications = Notifications::with(['user', 'setoranSampah.jenisSampah', 'penarikanSaldo'])
                ->whereIn('type', ['registrasi', 'setoran', 'penarikan'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($notifications, 200);
        } catch (\Exception $e) {
            Log::error("Error fetching operator notifications: {$e->getMessage()}");
            return response()->json(['message' => 'Failed to fetch notifications'], 500);
        }
    }
}

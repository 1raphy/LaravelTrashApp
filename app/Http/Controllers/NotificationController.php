<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NotificationNasabah;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $userId = $request->query('user_id');
            Log::info("NotificationController@index called", [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'query' => $request->query(),
            ]);

            $notifications = NotificationNasabah::with('setoranSampah.jenisSampah')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($notifications, 200);
        } catch (\Exception $e) {
            Log::error("Error fetching notifications: {$e->getMessage()}");
            return response()->json(['message' => 'Failed to fetch notifications'], 500);
        }
    }
}

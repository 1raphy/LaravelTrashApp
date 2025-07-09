<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\JenisSampah;
use Illuminate\Http\Request;
use App\Models\SetoranSampah;
use App\Models\PenarikanSaldo;
use Illuminate\Support\Facades\Log;

class OperatorStatsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $today = now()->startOfDay();
            $stats = [
                'setoran_baru' => SetoranSampah::where('created_at', '>=', $today)->count(),
                'penarikan_pending' => PenarikanSaldo::where('status', 'pending')->count(),
                'total_nasabah' => User::where('role', 'nasabah')->count(),
                'kategori_sampah' => JenisSampah::count(),
            ];

            Log::info("Operator stats fetched", ['stats' => $stats]);

            return response()->json($stats, 200);
        } catch (\Exception $e) {
            Log::error("Error fetching operator stats: {$e->getMessage()}", [
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Failed to fetch stats'], 500);
        }
    }
}

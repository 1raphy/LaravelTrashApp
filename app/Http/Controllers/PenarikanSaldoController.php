<?php

namespace App\Http\Controllers;

use App\Models\NotificationNasabah;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PenarikanSaldo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PenarikanSaldoController extends Controller
{
    // public function index(Request $request)
    // {
    //     Log::info('PenarikanSaldoController@index called', ['request' => $request->all()]);

    //     $userId = $request->user()->id;
    //     $penarikans = PenarikanSaldo::where('user_id', $userId)
    //                 ->with(['user'])
    //                 ->get();

    //     return response()->json($penarikans);
    // }

    public function index(Request $request)
    {
        try {
            $userId = $request->query('user_id');
            Log::info("PenarikanSaldoController@index called", [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'query' => $request->query(),
            ]);

            $penarikan = PenarikanSaldo::where('user_id', $userId)->get();
            return response()->json($penarikan, 200);
        } catch (\Exception $e) {
            Log::error("Error fetching penarikan saldo: {$e->getMessage()}");
            return response()->json(['message' => 'Failed to fetch penarikan saldo'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'jumlah' => 'required|numeric|min:0',
            ]);

            $user = User::findOrFail($request->user_id);
            if ($user->deposit_balance < $request->jumlah) {
                return response()->json(['message' => 'Saldo tidak cukup'], 400);
            }

            $penarikan = PenarikanSaldo::create([
                'user_id' => $request->user_id,
                'jumlah' => $request->jumlah,
                'status' => 'pending',
                'created_at' => now(),
            ]);

            $user->deposit_balance -= $request->jumlah;
            $user->save();

            NotificationNasabah::create([
                'user_id' => $request->user_id,
                'type' => 'penarikan',
                'message' => "Penarikan saldo #{$penarikan->id} sebesar Rp {$request->jumlah} oleh nasabah ID {$request->user_id}",
                'status' => 'pending',
                'penarikan_saldo_id' => $penarikan->id,
                'created_at' => now(),
            ]);

            Log::info("Penarikan saldo created and notification created: user_id={$request->user_id}, jumlah={$request->jumlah}, new_balance={$user->deposit_balance}");

            return response()->json($penarikan, 201);
        } catch (\Exception $e) {
            Log::error("Error creating penarikan saldo: {$e->getMessage()}");
            return response()->json(['message' => 'Failed to create penarikan saldo'], 500);
        }
    }

    // public function store(Request $request)
    // {
    //     Log::info('PenarikanSaldoController@store called', ['request' => $request->all()]);

    //     $validated = $request->validate([
    //         'jumlah' => 'required|numeric|min:0',
    //         'user_id' => 'required|integer|exists:users,id',
    //     ]);

    //     if ($validated['user_id'] !== $request->user()->id) {
    //         return response()->json(['message' => 'Unauthorized'], 403);
    //     }

    //     $user = User::findOrFail($validated['user_id']);
    //     if ($user->deposit_balance < $validated['jumlah']) {
    //         return response()->json(['message' => 'Saldo deposit tidak mencukupi'], 400);
    //     }

    //     $user->deposit_balance -= $validated['jumlah'];
    //     $user->save();

    //     $penarikan = PenarikanSaldo::create($validated);
        
    //     Log::info('Penarikan created and balance updated', [
    //         'user_id' => $user->id,
    //         'penarikan_id' => $penarikan->id,
    //         'jumlah' => $penarikan->jumlah,
    //         'new_balance' => $user->deposit_balance,
    //     ]);

    //     return response()->json(['data' => $penarikan], 201);
    // }
}

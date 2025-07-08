<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PenarikanSaldo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PenarikanSaldoController extends Controller
{
    public function index(Request $request)
    {
        Log::info('PenarikanSaldoController@index called', ['request' => $request->all()]);

        $userId = $request->user()->id;
        $penarikans = PenarikanSaldo::where('user_id', $userId)
                    ->with(['user'])
                    ->get();

        return response()->json($penarikans);
        // Atau: return response()->json(['data' => $penarikans]);
    }

    public function store(Request $request)
    {
        Log::info('PenarikanSaldoController@store called', ['request' => $request->all()]);

        $validated = $request->validate([
            'jumlah' => 'required|numeric|min:0',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validated['user_id'] !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($validated['user_id']);
        if ($user->deposit_balance < $validated['jumlah']) {
            return response()->json(['message' => 'Saldo deposit tidak mencukupi'], 400);
        }

        $user->deposit_balance -= $validated['jumlah'];
        $user->save();

        $penarikan = PenarikanSaldo::create($validated);
        
        Log::info('Penarikan created and balance updated', [
            'user_id' => $user->id,
            'penarikan_id' => $penarikan->id,
            'jumlah' => $penarikan->jumlah,
            'new_balance' => $user->deposit_balance,
        ]);

        return response()->json(['data' => $penarikan], 201);
    }
}

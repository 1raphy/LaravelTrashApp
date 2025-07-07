<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PenarikanSaldo;
use Illuminate\Support\Facades\Auth;

class PenarikanSaldoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:10000'
        ]);

        $user = Auth::user();
        
        if ($user->deposit_balance < $request->jumlah) {
            return response()->json([
                'message' => 'Saldo tidak cukup'
            ], 400);
        }

        $penarikan = PenarikanSaldo::create([
            'user_id' => Auth::id(),
            'jumlah' => $request->jumlah
        ]);

        $user->deposit_balance -= $request->jumlah;
        $user->save();

        return response()->json([
            'message' => 'Penarikan saldo berhasil diajukan',
            'data' => $penarikan
        ], 201);
    }
}

<?php

namespace App\Http\Controllers;

use Log;
use Illuminate\Http\Request;
use App\Models\PenarikanSaldo;
use Illuminate\Support\Facades\Auth;

class PenarikanSaldoController extends Controller
{
    public function index(Request $request)
    {
        // Debug untuk memastikan metode index dijalankan
        \Log::info('PenarikanSaldoController@index called', ['request' => $request->all()]);

        // Ambil user_id dari pengguna yang terautentikasi
        $userId = $request->user()->id; // Asumsi autentikasi Sanctum
        $penarikans = PenarikanSaldo::where('user_id', $userId)
                    ->with(['user'])
                    ->get();

        // Kembalikan response JSON tanpa validasi
        return response()->json($penarikans);
        // Atau: return response()->json(['data' => $penarikans]);
    }

    public function store(Request $request)
    {
        // Debug untuk memastikan metode store tidak dijalankan untuk GET
        Log::info('PenarikanSaldoController@store called', ['request' => $request->all()]);

        // Validasi hanya untuk POST
        $validated = $request->validate([
            'jumlah' => 'required|numeric|min:0',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $penarikan = PenarikanSaldo::create($validated);
        return response()->json(['data' => $penarikan], 201);
    }
}

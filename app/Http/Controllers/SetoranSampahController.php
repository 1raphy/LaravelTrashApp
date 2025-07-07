<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\JenisSampah;
use Illuminate\Http\Request;
use App\Models\SetoranSampah;
use Illuminate\Support\Facades\Auth;

class SetoranSampahController extends Controller
{
    public function index()
    {
        $setoran = SetoranSampah::with(['user', 'jenisSampah'])->get();
        return response()->json($setoran);
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_sampah_id' => 'required|exists:jenis_sampah,id',
            'berat_kg' => 'required|numeric|min:0.1',
        ]);

        $jenisSampah = JenisSampah::find($request->jenis_sampah_id);
        $totalHarga = $request->berat_kg * $jenisSampah->harga_per_kg;

        $setoran = SetoranSampah::create([
            'user_id' => Auth::id(),
            'jenis_sampah_id' => $request->jenis_sampah_id,
            'berat_kg' => $request->berat_kg,
            'total_harga' => $totalHarga,
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Setoran sampah berhasil diajukan',
            'data' => $setoran
        ], 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak'
        ]);

        $setoran = SetoranSampah::findOrFail($id);
        
        if ($request->status === 'disetujui') {
            $user = User::find($setoran->user_id);
            $user->deposit_balance += $setoran->total_harga;
            $user->save();
        }

        $setoran->status = $request->status;
        $setoran->save();

        return response()->json([
            'message' => 'Status setoran berhasil diperbarui',
            'data' => $setoran
        ]);
    }
}

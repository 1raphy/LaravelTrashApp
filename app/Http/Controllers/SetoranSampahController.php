<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\JenisSampah;
use Illuminate\Http\Request;
use App\Models\SetoranSampah;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SetoranSampahController extends Controller
{
    public function index()
    {
        $setoran = SetoranSampah::with(['user', 'jenisSampah'])->get();
        return response()->json($setoran);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_sampah_id' => 'required|exists:jenis_sampah,id',
            'berat_kg' => 'required|numeric|min:0.1',
            'metode_penjemputan' => 'required|in:Antar Sendiri,Dijemput di Rumah,Titik Kumpul Terdekat',
            'alamat_penjemputan' => 'required_if:metode_penjemputan,Dijemput di Rumah',
            'catatan_tambahan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $jenisSampah = JenisSampah::find($request->jenis_sampah_id);
        $beratKg = $request->berat_kg;
        $totalHarga = $beratKg * $jenisSampah->harga_per_kg;

        $setoran = SetoranSampah::create([
            'user_id' => Auth::id(),
            'jenis_sampah_id' => $request->jenis_sampah_id,
            'berat_kg' => $beratKg,
            'total_harga' => $totalHarga,
            'metode_penjemputan' => $request->metode_penjemputan,
            'alamat_penjemputan' => $request->alamat_penjemputan,
            'catatan_tambahan' => $request->catatan_tambahan,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Setoran sampah berhasil dicatat',
            'data' => $setoran,
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

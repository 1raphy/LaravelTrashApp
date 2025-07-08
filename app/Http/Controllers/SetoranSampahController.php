<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\JenisSampah;
use Illuminate\Http\Request;
use App\Models\SetoranSampah;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SetoranSampahController extends Controller
{
    // public function index()
    // {
    //     $setoran = SetoranSampah::with(['user', 'jenisSampah'])->get();
    //     return response()->json($setoran);
    // }

    public function index(Request $request)
    {
        Log::info('SetoranSampahController@index called', [
            'method' => $request->method(),
            'url' => $request->url(),
            'query' => $request->query(),
        ]);

        $query = SetoranSampah::with(['user', 'jenisSampah']);
        if ($request->user()->role === 'operator') {
            $query->whereIn('status', ['pending', 'disetujui', 'ditolak']);
        } else {
            $query->where('user_id', $request->user()->id);
        }

        $setorans = $query->get();
        return response()->json($setorans);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_sampah_id' => 'required|exists:jenis_sampah,id',
            'berat_kg' => 'required|numeric|min:0.1',
            'metode_penjemputan' => 'required|in:Antar Sendiri,Dijemput di Rumah',
            'alamat_penjemputan' => 'required_if:metode_penjemputan,Dijemput di Rumah',
            'catatan_tambahan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                ],
                422,
            );
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

        return response()->json(
            [
                'message' => 'Setoran sampah berhasil dicatat',
                'data' => $setoran,
            ],
            201,
        );
    }

    public function updateStatus(Request $request, $id)
    {
        Log::info('SetoranSampahController@updateStatus called', [
            'id' => $id,
            'request' => $request->all(),
        ]);

        // Hanya operator yang dapat memperbarui status
        if ($request->user()->role !== 'operator') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:disetujui,ditolak',
        ]);

        $setoran = SetoranSampah::findOrFail($id);

        // Jika status berubah menjadi 'disetujui', tambahkan total_harga ke deposit_balance
        if ($validated['status'] === 'disetujui' && $setoran->status !== 'disetujui') {
            $user = User::findOrFail($setoran->user_id);
            $user->deposit_balance += $setoran->total_harga;
            $user->save();
            Log::info('Deposit balance updated', [
                'user_id' => $user->id,
                'new_balance' => $user->deposit_balance,
                'setoran_id' => $setoran->id,
            ]);
        }

        $setoran->status = $validated['status'];
        $setoran->save();

        return response()->json(['data' => $setoran]);
    }
}

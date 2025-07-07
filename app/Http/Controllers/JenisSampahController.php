<?php

namespace App\Http\Controllers;

use App\Models\JenisSampah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JenisSampahController extends Controller
{
    public function index()
    {
        $jenisSampah = JenisSampah::all();
        return response()->json([
            'status' => 'success',
            'data' => $jenisSampah
        ], 200);
    }

    // Menyimpan jenis sampah baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_sampah' => 'required|string|max:255',
            'harga_per_kg' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $jenisSampah = JenisSampah::create([
            'nama_sampah' => $request->nama_sampah,
            'harga_per_kg' => $request->harga_per_kg
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Jenis sampah berhasil ditambahkan',
            'data' => $jenisSampah
        ], 201);
    }

    // Menampilkan detail jenis sampah
    public function show($id)
    {
        $jenisSampah = JenisSampah::find($id);

        if (!$jenisSampah) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jenis sampah tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $jenisSampah
        ], 200);
    }

    // Memperbarui jenis sampah
    public function update(Request $request, $id)
    {
        $jenisSampah = JenisSampah::find($id);

        if (!$jenisSampah) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jenis sampah tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_sampah' => 'required|string|max:255',
            'harga_per_kg' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $jenisSampah->update([
            'nama_sampah' => $request->nama_sampah,
            'harga_per_kg' => $request->harga_per_kg
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Jenis sampah berhasil diperbarui',
            'data' => $jenisSampah
        ], 200);
    }

    // Menghapus jenis sampah
    public function destroy($id)
    {
        $jenisSampah = JenisSampah::find($id);

        if (!$jenisSampah) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jenis sampah tidak ditemukan'
            ], 404);
        }

        $jenisSampah->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Jenis sampah berhasil dihapus'
        ], 200);
    }
}

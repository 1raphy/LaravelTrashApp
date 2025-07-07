<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JenisSampahController;
use App\Http\Controllers\SetoranSampahController;
use App\Http\Controllers\PenarikanSaldoController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Endpoint untuk mengelola setoran sampah
    Route::get('/setoran-sampah', [SetoranSampahController::class, 'index']);
    Route::post('/setoran-sampah', [SetoranSampahController::class, 'store']);
    Route::put('/setoran-sampah/{id}/status', [SetoranSampahController::class, 'updateStatus']);

    // Endpoint untuk penarikan saldo
    Route::post('/penarikan-saldo', [PenarikanSaldoController::class, 'store']);

    // Endpoint untuk mengelola jenis sampah (kecuali index)
    Route::post('/jenis-sampah', [JenisSampahController::class, 'store']);
    Route::get('/jenis-sampah/{id}', [JenisSampahController::class, 'show']);
    Route::put('/jenis-sampah/{id}', [JenisSampahController::class, 'update']);
    Route::delete('/jenis-sampah/{id}', [JenisSampahController::class, 'destroy']);
});

// Endpoint publik untuk mendapatkan daftar jenis sampah
Route::get('/jenis-sampah', [JenisSampahController::class, 'index']);
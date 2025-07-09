<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JenisSampahController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SetoranSampahController;
use App\Http\Controllers\PenarikanSaldoController;
use App\Http\Controllers\OperatorNotificationController;
use App\Http\Controllers\OperatorStatsController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::get('/users/{id}/total-setoran', [UserController::class, 'getTotalSetoran']);

    // Endpoint untuk mengelola setoran sampah
    Route::get('/setoran-sampah', [SetoranSampahController::class, 'index']);
    Route::post('/setoran-sampah', [SetoranSampahController::class, 'store']);
    Route::put('/setoran-sampah/{id}/status', [SetoranSampahController::class, 'updateStatus']);

    // Endpoint untuk penarikan saldo
    Route::resource('penarikan-saldo', PenarikanSaldoController::class)->only(['index', 'store']);

    // Endpoint untuk mengelola jenis sampah (kecuali index)
    Route::post('/jenis-sampah', [JenisSampahController::class, 'store']);
    Route::get('/jenis-sampah/{id}', [JenisSampahController::class, 'show']);
    Route::put('/jenis-sampah/{id}', [JenisSampahController::class, 'update']);
    Route::delete('/jenis-sampah/{id}', [JenisSampahController::class, 'destroy']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/operator-notifications', [OperatorNotificationController::class, 'index']);
    Route::get('/operator-stats', [OperatorStatsController::class, 'index']);
});

// Endpoint publik untuk mendapatkan daftar jenis sampah
Route::get('/jenis-sampah-index', [JenisSampahController::class, 'index']);
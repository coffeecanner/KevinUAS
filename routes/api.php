<?php
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\DaftarUlangController;
use App\Http\Controllers\PengurusanController;

Route::middleware('auth:sanctum')->group(function () {

    // ================= PENDAFTARAN =================
    Route::get('/pendaftaran', [PendaftaranController::class, 'index']);
    Route::get('/pendaftaran/search', [PendaftaranController::class, 'search']);
    Route::post('/pendaftaran', [PendaftaranController::class, 'store']);
    Route::get('/pendaftaran/{id}', [PendaftaranController::class, 'show']);
    Route::put('/pendaftaran/{id}', [PendaftaranController::class, 'update']);
    Route::delete('/pendaftaran/{id}', [PendaftaranController::class, 'destroy']);

    // ================= DAFTAR ULANG =================
    Route::get('/daftar-ulang', [DaftarUlangController::class, 'index']);
    Route::get('/daftar-ulang/search', [DaftarUlangController::class, 'search']);
    Route::post('/daftar-ulang', [DaftarUlangController::class, 'store']);
    Route::get('/daftar-ulang/{id}', [DaftarUlangController::class, 'show']);
    Route::put('/daftar-ulang/{id}', [DaftarUlangController::class, 'update']);
    Route::delete('/daftar-ulang/{id}', [DaftarUlangController::class, 'destroy']);

    // ================= PENGURUSAN =================
    Route::get('/pengurusan', [PengurusanController::class, 'index']);
    Route::get('/pengurusan/search', [PengurusanController::class, 'search']);

    Route::get('/pengurusan/current', [PengurusanController::class, 'current']);
    Route::put('/pengurusan/{id}/selesai-next', [PengurusanController::class, 'selesaiDanNext']);

    Route::post('/pengurusan', [PengurusanController::class, 'store']);
    Route::put('/pengurusan/{id}', [PengurusanController::class, 'update']);
    Route::delete('/pengurusan/{id}', [PengurusanController::class, 'destroy']);
});

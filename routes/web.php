<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\DaftarUlangController;
use App\Http\Controllers\PengurusanController;
use App\Http\Controllers\DashboardController;

// Authentication (public)
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);

// Protected routes - require authentication
Route::middleware('auth')->group(function(){
	Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
	Route::get('/user/profile', [App\Http\Controllers\AuthController::class, 'profile']);

	// Dashboard
	Route::get('/', [DashboardController::class, 'index']);
	Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
    Route::get('/pengurusan', [PengurusanController::class, 'index']);
Route::get('/pengurusan/current', [PengurusanController::class, 'current']);
Route::post('/pengurusan/panggil', [PengurusanController::class, 'panggilNext']);
Route::put('/pengurusan/{id}/selesai', [PengurusanController::class, 'selesai']);

	// Simple JSON endpoints for the passport process modules
	// JSON API endpoints (prefixed with /api)
	Route::get('/api/pendaftaran', [PendaftaranController::class, 'index']);
	Route::get('/api/pendaftaran/search', [PendaftaranController::class, 'search']);
	Route::post('/api/pendaftaran', [PendaftaranController::class, 'store']);
	Route::get('/api/pendaftaran/{id}', [PendaftaranController::class, 'show']);
	Route::put('/api/pendaftaran/{id}', [PendaftaranController::class, 'update']);
	Route::delete('/api/pendaftaran/{id}', [PendaftaranController::class, 'destroy']);

	Route::get('/api/daftar-ulang', [DaftarUlangController::class, 'index']);
	Route::get('/api/daftar-ulang/search', [DaftarUlangController::class, 'search']);
	Route::post('/api/daftar-ulang', [DaftarUlangController::class, 'store']);
	Route::get('/api/daftar-ulang/{id}', [DaftarUlangController::class, 'show']);
	Route::put('/api/daftar-ulang/{id}', [DaftarUlangController::class, 'update']);
	Route::delete('/api/daftar-ulang/{id}', [DaftarUlangController::class, 'destroy']);

	Route::get('/api/pengurusan', [PengurusanController::class, 'index']);
    Route::get('/api/pengurusan/current', [PengurusanController::class, 'current']);
Route::put('/api/pengurusan/{id}/selesai-next', [PengurusanController::class, 'selesaiDanNext']);

	Route::get('/api/pengurusan/search', [PengurusanController::class, 'search']);
	Route::post('/api/pengurusan', [PengurusanController::class, 'store']);
	Route::get('/api/pengurusan/{id}', [PengurusanController::class, 'show']);
	Route::put('/api/pengurusan/{id}', [PengurusanController::class, 'update']);
	Route::delete('/api/pengurusan/{id}', [PengurusanController::class, 'destroy']);

	// UI routes (Blade views) - direct paths
	Route::get('/pendaftaran', function(){ return view('pendaftaran.index'); });
	Route::get('/daftar-ulang', function(){ return view('daftar_ulang.index'); });
	Route::get('/pengurusan', function(){ return view('pengurusan.index'); });
});

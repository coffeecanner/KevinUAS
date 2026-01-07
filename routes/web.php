<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\DaftarUlangController;
use App\Http\Controllers\PengurusanController;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index']);
// Authentication
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
Route::get('/user/profile', [App\Http\Controllers\AuthController::class, 'profile'])->middleware('auth');

// Simple JSON endpoints for the passport process modules
Route::get('/pendaftaran', [PendaftaranController::class, 'index']);
Route::get('/pendaftaran/search', [PendaftaranController::class, 'search']);
Route::post('/pendaftaran', [PendaftaranController::class, 'store']);
Route::get('/pendaftaran/{id}', [PendaftaranController::class, 'show']);
Route::put('/pendaftaran/{id}', [PendaftaranController::class, 'update']);
Route::delete('/pendaftaran/{id}', [PendaftaranController::class, 'destroy']);

Route::get('/daftar-ulang', [DaftarUlangController::class, 'index']);
Route::post('/daftar-ulang', [DaftarUlangController::class, 'store']);
Route::get('/daftar-ulang/{id}', [DaftarUlangController::class, 'show']);
Route::put('/daftar-ulang/{id}', [DaftarUlangController::class, 'update']);
Route::delete('/daftar-ulang/{id}', [DaftarUlangController::class, 'destroy']);

Route::get('/pengurusan', [PengurusanController::class, 'index']);
Route::post('/pengurusan', [PengurusanController::class, 'store']);
Route::get('/pengurusan/{id}', [PengurusanController::class, 'show']);
Route::delete('/pengurusan/{id}', [PengurusanController::class, 'destroy']);

// UI routes (Blade views)
Route::get('/ui/pendaftaran', function(){ return view('pendaftaran.index'); });
Route::get('/ui/daftar-ulang', function(){ return view('daftar_ulang.index'); });
Route::get('/ui/pengurusan', function(){ return view('pengurusan.index'); });

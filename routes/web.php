<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PenyewaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

// Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

//registr
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

// Dashboard Admin
Route::get('/admin/dashboard', function () {
    if (Auth::user()->role != 'admin') {
        abort(403);
    }

    return view('admin.dashboard');
})->middleware('auth');

// Dashboard Pemilik
Route::get('/pemilik/dashboard', function () {
    if (Auth::user()->role != 'pemilik') {
        abort(403);
    }

    return view('pemilik.dashboard');
})->middleware('auth');

// Penyewa Routes
Route::middleware(['auth', 'role:penyewa'])->prefix('penyewa')->group(function () {
    Route::get('/dashboard', [PenyewaController::class, 'dashboard']);
    Route::get('/cari-kos', [PenyewaController::class, 'cariKos']);
    Route::get('/detail-kos/{kosId}', [PenyewaController::class, 'detailKos']);

    // Pengajuan Sewa
    Route::get('/pengajuan-sewa/{kamarId}', [PenyewaController::class, 'formPengajuanSewa']);
    Route::post('/pengajuan-sewa', [PenyewaController::class, 'ajukanSewa']);

    // Pembayaran
    Route::get('/pembayaran', [PenyewaController::class, 'daftarPembayaran']);
    Route::get('/pembayaran/{sewaId}', [PenyewaController::class, 'formPembayaran']);
    Route::post('/pembayaran', [PenyewaController::class, 'bayar']);

    // Riwayat Sewa
    Route::get('/riwayat-sewa', [PenyewaController::class, 'riwayatSewa']);

    // Ulasan
    Route::get('/ulasan/{sewaId}', [PenyewaController::class, 'formUlasan']);
    Route::post('/ulasan', [PenyewaController::class, 'simpanUlasan']);
});

<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KelolaKamarController;
use App\Http\Controllers\KelolaPenyewaanController;
use App\Http\Controllers\PemilikController;
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

// Pemilik Routes
Route::middleware(['auth', 'role:pemilik'])->prefix('pemilik')->name('pemilik.')->group(function () {
    Route::get('/dashboard', [PemilikController::class, 'dashboard'])->name('dashboard');

    // Kelola Kamar Kos
    Route::get('/kelola-kamar', [KelolaKamarController::class, 'index'])->name('kelola-kamar.index');
    Route::get('/kelola-kamar/create', [KelolaKamarController::class, 'create'])->name('kelola-kamar.create');
    Route::post('/kelola-kamar', [KelolaKamarController::class, 'store'])->name('kelola-kamar.store');
    Route::get('/kelola-kamar/{kamar}/edit', [KelolaKamarController::class, 'edit'])->name('kelola-kamar.edit');
    Route::put('/kelola-kamar/{kamar}', [KelolaKamarController::class, 'update'])->name('kelola-kamar.update');
    Route::delete('/kelola-kamar/{kamar}', [KelolaKamarController::class, 'destroy'])->name('kelola-kamar.destroy');
    Route::patch('/kelola-kamar/{kamar}/toggle-status', [KelolaKamarController::class, 'toggleStatus'])->name('kelola-kamar.toggle-status');

    // Daftar & Kelola Penyewaan
    Route::get('/penyewaan', [KelolaPenyewaanController::class, 'index'])->name('penyewaan.index');
    Route::patch('/penyewaan/{sewa}/setujui', [KelolaPenyewaanController::class, 'setujui'])->name('penyewaan.setujui');
    Route::patch('/penyewaan/{sewa}/tolak', [KelolaPenyewaanController::class, 'tolak'])->name('penyewaan.tolak');
    Route::patch('/penyewaan/{sewa}/selesai', [KelolaPenyewaanController::class, 'selesai'])->name('penyewaan.selesai');

    // Quick Store Kos (jika pemilik baru belum punya kos)
    Route::post('/kos/quick-store', [PemilikController::class, 'quickStoreKos'])->name('kos.quick-store');
});

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

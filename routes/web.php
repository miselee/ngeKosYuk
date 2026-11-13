<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KelolaKamarController;
use App\Http\Controllers\KelolaPembayaranController;
use App\Http\Controllers\KelolaPenyewaanController;
use App\Http\Controllers\PemilikController;
use App\Http\Controllers\PenyewaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

    Route::get('/kos', [AdminController::class, 'kos'])->name('kos.index');
    Route::delete('/kos/{kos}', [AdminController::class, 'destroyKos'])->name('kos.destroy');

    Route::get('/transaksi', [AdminController::class, 'transaksi'])->name('transaksi.index');
    Route::get('/pembayaran/{pembayaran}/kwitansi', [AdminController::class, 'kwitansi'])->name('pembayaran.kwitansi');
    Route::delete('/ulasan/{ulasan}', [AdminController::class, 'destroyUlasan'])->name('ulasan.destroy');
});

Route::middleware(['auth', 'role:pemilik'])->prefix('pemilik')->name('pemilik.')->group(function () {
    Route::get('/dashboard', [PemilikController::class, 'dashboard'])->name('dashboard');

    Route::get('/kelola-kamar', [KelolaKamarController::class, 'index'])->name('kelola-kamar.index');
    Route::get('/kelola-kamar/create', [KelolaKamarController::class, 'create'])->name('kelola-kamar.create');
    Route::post('/kelola-kamar', [KelolaKamarController::class, 'store'])->name('kelola-kamar.store');
    Route::get('/kelola-kamar/{kamar}/edit', [KelolaKamarController::class, 'edit'])->name('kelola-kamar.edit');
    Route::put('/kelola-kamar/{kamar}', [KelolaKamarController::class, 'update'])->name('kelola-kamar.update');
    Route::delete('/kelola-kamar/{kamar}', [KelolaKamarController::class, 'destroy'])->name('kelola-kamar.destroy');
    Route::patch('/kelola-kamar/{kamar}/toggle-status', [KelolaKamarController::class, 'toggleStatus'])->name('kelola-kamar.toggle-status');

    Route::get('/penyewaan', [KelolaPenyewaanController::class, 'index'])->name('penyewaan.index');
    Route::patch('/penyewaan/{sewa}/setujui', [KelolaPenyewaanController::class, 'setujui'])->name('penyewaan.setujui');
    Route::patch('/penyewaan/{sewa}/tolak', [KelolaPenyewaanController::class, 'tolak'])->name('penyewaan.tolak');
    Route::patch('/penyewaan/{sewa}/selesai', [KelolaPenyewaanController::class, 'selesai'])->name('penyewaan.selesai');

    Route::get('/pembayaran', [KelolaPembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('/pembayaran/{pembayaran}/kwitansi', [KelolaPembayaranController::class, 'kwitansi'])->name('pembayaran.kwitansi');
    Route::patch('/pembayaran/{pembayaran}/verifikasi', [KelolaPembayaranController::class, 'verifikasi'])->name('pembayaran.verifikasi');
    Route::patch('/pembayaran/{pembayaran}/tolak', [KelolaPembayaranController::class, 'tolak'])->name('pembayaran.tolak');

    Route::post('/kos/quick-store', [PemilikController::class, 'quickStoreKos'])->name('kos.quick-store');
});

Route::middleware(['auth', 'role:penyewa'])->prefix('penyewa')->group(function () {
    Route::get('/dashboard', [PenyewaController::class, 'dashboard']);
    Route::get('/cari-kos', [PenyewaController::class, 'cariKos']);
    Route::get('/detail-kos/{kosId}', [PenyewaController::class, 'detailKos']);

    Route::get('/pengajuan-sewa/{kamarId}', [PenyewaController::class, 'formPengajuanSewa']);
    Route::post('/pengajuan-sewa', [PenyewaController::class, 'ajukanSewa']);

    Route::get('/pembayaran', [PenyewaController::class, 'daftarPembayaran'])->name('penyewa.pembayaran.index');
    Route::get('/pembayaran/{pembayaranId}/kwitansi', [PenyewaController::class, 'kwitansiPembayaran'])->name('penyewa.pembayaran.kwitansi');
    Route::get('/pembayaran/{sewaId}', [PenyewaController::class, 'formPembayaran'])->name('penyewa.pembayaran.create');
    Route::post('/pembayaran', [PenyewaController::class, 'bayar'])->name('penyewa.pembayaran.store');

    Route::get('/riwayat-sewa', [PenyewaController::class, 'riwayatSewa']);

    Route::get('/ulasan/{sewaId}', [PenyewaController::class, 'formUlasan']);
    Route::post('/ulasan', [PenyewaController::class, 'simpanUlasan']);
});

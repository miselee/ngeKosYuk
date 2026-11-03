<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;

Route::redirect('/', '/login');

// Login
Route::get('/login', [AuthController::class, 'showLogin']);
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

// Dashboard Penyewa
Route::get('/penyewa/dashboard', function () {
    if (Auth::user()->role != 'penyewa') {
        abort(403);
    }

    return view('penyewa.dashboard');
})->middleware('auth');
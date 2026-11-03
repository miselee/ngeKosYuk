
@extends('layouts.app')

@section('title', 'Register - ngeKosYuk')

@section('content')

<style>
    body {
        background: linear-gradient(120deg, #dff3fc, #edf5fa);
        color: #263747;
        font-family: Arial, sans-serif;
    }

    .auth-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        padding: 40px 0;
    }

    .auth-container {
        background: white;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 15px 45px rgba(40, 105, 145, 0.12);
    }

    .auth-navbar {
        padding: 16px 25px;
        border-bottom: 1px solid #edf1f5;
    }

    .brand {
        color: #238dc5;
        font-size: 25px;
        font-weight: 800;
        text-decoration: none;
    }

    .auth-form-section {
        padding: 35px 45px;
        background: white;
    }

    .auth-image-section {
        min-height: 750px;
        position: relative;
        background: #dceffc;
    }

    .auth-image {
        width: 100%;
        height: 100%;
        min-height: 750px;
        object-fit: cover;
    }

    .form-control, .form-select {
        padding: 11px 13px;
        border: 1px solid #e0e8ee;
        border-radius: 9px;
        font-size: 14px;
    }

    .form-control:focus, .form-select:focus {
        border-color: #2696d2;
        box-shadow: 0 0 0 3px rgba(38, 150, 210, .12);
    }

    .btn-blue {
        background: #2696d2;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 12px;
        font-weight: 600;
    }

    .btn-blue:hover {
        background: #187db5;
        color: white;
    }

    .auth-link {
        color: #2696d2;
        text-decoration: none;
        font-weight: 600;
    }

    .auth-tabs {
        display: flex;
        gap: 30px;
        border-bottom: 1px solid #e5eaf0;
        margin-bottom: 22px;
    }

    .auth-tabs a {
        color: #687786;
        text-decoration: none;
        padding-bottom: 12px;
    }

    .auth-tabs a.active {
        color: #2696d2;
        border-bottom: 2px solid #2696d2;
        font-weight: 700;
    }

    .image-caption {
        position: absolute;
        bottom: 25px;
        left: 25px;
        right: 25px;
        background: white;
        border-radius: 12px;
        padding: 18px;
        box-shadow: 0 5px 20px rgba(0,0,0,.08);
    }

    @media (max-width: 991px) {
        .auth-form-section {
            padding: 35px 25px;
        }
    }
</style>

<div class="auth-wrapper">
    <div class="container">
        <div class="auth-container">

            <nav class="auth-navbar d-flex justify-content-between align-items-center">
                <a href="/" class="brand">
                    <i class="bi bi-house-door-fill"></i> ngeKosYuk
                </a>
            </nav>

            <div class="row g-0">

                <div class="col-lg-5">
                    <div class="auth-form-section h-100">

                        <h2 class="fw-bold mb-2">Create Account</h2>

                        <p class="text-secondary small mb-4">
                            Daftar sekarang dan mulai temukan
                            tempat tinggal yang nyaman.
                        </p>

                        <div class="auth-tabs">
                            <a href="{{ url('/register') }}" class="active">
                                Register
                            </a>
                            <a href="{{ url('/login') }}">
                                Login
                            </a>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger small">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ url('/register') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">
                                    Nama Lengkap
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    placeholder="Masukkan nama lengkap"
                                    value="{{ old('name') }}"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">
                                    Nomor HP
                                </label>
                                <input
                                    type="text"
                                    name="nomor_hp"
                                    class="form-control"
                                    placeholder="Contoh: 081234567890"
                                    value="{{ old('nomor_hp') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">
                                    Email
                                </label>
                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    placeholder="Masukkan email"
                                    value="{{ old('email') }}"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">
                                    Daftar Sebagai
                                </label>
                                <select name="role" class="form-select" required>
                                    <option value="" disabled
                                        {{ old('role') ? '' : 'selected' }}>
                                        Pilih jenis akun
                                    </option>

                                    <option value="pemilik"
                                        {{ old('role') == 'pemilik' ? 'selected' : '' }}>
                                        Pemilik Kos
                                    </option>

                                    <option value="penyewa"
                                        {{ old('role') == 'penyewa' ? 'selected' : '' }}>
                                        Penyewa
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">
                                    Password
                                </label>
                                <input
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    placeholder="Minimal 8 karakter"
                                    required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-semibold">
                                    Konfirmasi Password
                                </label>
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    class="form-control"
                                    placeholder="Masukkan ulang password"
                                    required>
                            </div>

                            <button type="submit" class="btn btn-blue w-100">
                                CREATE ACCOUNT
                            </button>
                        </form>

                        <p class="text-center text-secondary small mt-4">
                            Sudah punya akun?
                            <a href="{{ url('/login') }}" class="auth-link">
                                Sign In
                            </a>
                        </p>

                        <div class="text-center">
                            <a href="/" class="text-secondary small text-decoration-none">
                                <i class="bi bi-arrow-left"></i>
                                Kembali ke Beranda
                            </a>
                        </div>

                    </div>
                </div>

                <div class="col-lg-7 d-none d-lg-block">
                    <div class="auth-image-section h-100">

                        <img
                            src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=1200"
                            alt="Hunian nyaman"
                            class="auth-image">

                        <div class="image-caption d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:55px;height:55px;background:#e0f2fc;flex-shrink:0;">
                                <i class="bi bi-house-heart-fill fs-3"
                                   style="color:#2696d2;"></i>
                            </div>

                            <div>
                                <h6 class="fw-bold mb-1">
                                    Mulai Perjalananmu Bersama ngeKosYuk
                                </h6>
                                <p class="text-secondary small mb-0">
                                    Temukan kos yang sesuai dengan kebutuhanmu.
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

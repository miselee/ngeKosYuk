@extends('layouts.app')

@section('title', 'Daftar Akun')

@push('styles')
<style>
    .auth-shell {
        min-height: 100vh;
        display: grid;
        grid-template-columns: 1fr;
        background-color: var(--nk-bg);
    }

    @media (min-width: 992px) {
        .auth-shell {
            grid-template-columns: 0.95fr 1.05fr;
        }
    }

    .auth-visual {
        position: relative;
        background-color: #0F172A;
        color: #FFFFFF;
        padding: 3rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        overflow: hidden;
    }

    .auth-visual-img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.3;
    }

    .auth-visual-content {
        position: relative;
        z-index: 2;
    }

    .auth-form-pane {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2.5rem 1.5rem;
        background-color: var(--nk-surface);
    }

    .auth-form-box {
        width: 100%;
        max-width: 440px;
    }

    .role-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
    }

    .role-option {
        position: relative;
    }

    .role-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .role-label {
        display: block;
        border: 1px solid var(--nk-border-strong);
        border-radius: 8px;
        padding: 0.75rem 0.9rem;
        background-color: var(--nk-surface);
        transition: border-color 0.15s ease, background-color 0.15s ease;
    }

    .role-option input[type="radio"]:checked + .role-label {
        border-color: var(--nk-blue);
        background-color: var(--nk-blue-soft);
        box-shadow: inset 0 0 0 1px var(--nk-blue);
    }

    .role-option input[type="radio"]:focus-visible + .role-label {
        outline: 2px solid var(--nk-blue);
        outline-offset: 2px;
    }

    .role-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--nk-ink);
        display: block;
    }

    .role-desc {
        font-size: 0.75rem;
        color: var(--nk-muted);
        display: block;
        margin-top: 0.15rem;
    }
</style>
@endpush

@section('content')
<div class="auth-shell">
    <div class="auth-visual d-none d-lg-flex">
        <img src="https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80"
             alt="Suasana hunian kos nyaman"
             width="1200"
             height="900"
             class="auth-visual-img">

        <div class="auth-visual-content d-flex align-items-center gap-3">
            <img src="{{ asset('images/logo/logo-ngekosyuk.jpeg') }}"
                 alt="Logo ngeKosYuk"
                 width="42"
                 height="42"
                 class="rounded-2 border border-secondary">
            <span class="fw-bold fs-5">ngeKosYuk</span>
        </div>

        <div class="auth-visual-content my-auto py-5" style="max-width: 440px;">
            <h1 class="fw-bold mb-3" style="font-size: 2rem; letter-spacing: -0.03em; line-height: 1.2;">
                Mulai sewa atau kelola properti kos dalam hitungan menit.
            </h1>
            <p class="mb-0" style="color: #CBD5E1; font-size: 0.92rem;">
                Pilih peran Anda saat mendaftar: cari kos sesuai anggaran sebagai Penyewa, atau kelola ketersediaan kamar sebagai Pemilik Kos.
            </p>
        </div>

        <div class="auth-visual-content small" style="color: #CBD5E1;">
            Data kontrak sewa dan histori transaksi tersimpan rapi dalam satu akun.
        </div>
    </div>

    <div class="auth-form-pane">
        <div class="auth-form-box">
            <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('images/logo/logo-ngekosyuk.jpeg') }}"
                         alt="Logo ngeKosYuk"
                         width="34"
                         height="34"
                         class="nk-brand-logo">
                    <span class="fw-bold fs-5">ngeKosYuk</span>
                </div>
                <button type="button"
                        class="nk-theme-toggle"
                        data-nk-theme-toggle
                        aria-pressed="false"
                        aria-label="Aktifkan mode gelap"
                        title="Ganti mode gelap atau terang">
                    <i class="bi bi-moon-stars" data-nk-theme-icon aria-hidden="true"></i>
                    <span data-nk-theme-label>Gelap</span>
                </button>
            </div>

            <h2 class="fw-bold mb-1" style="font-size: 1.45rem; letter-spacing: -0.025em;">Buat Akun Baru</h2>
            <p class="text-secondary small mb-4">Lengkapi identitas singkat di bawah ini untuk mulai menggunakan ngeKosYuk.</p>

            @if(session('error'))
                <div class="alert alert-warning py-2 px-3 small mb-3" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger py-2 px-3 small mb-3" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ url('/register') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label d-block">Daftar Sebagai</label>
                    <div class="role-grid">
                        <div class="role-option">
                            <input type="radio"
                                   name="role"
                                   id="role_penyewa"
                                   value="penyewa"
                                   {{ old('role', 'penyewa') === 'penyewa' ? 'checked' : '' }}
                                   required>
                            <label for="role_penyewa" class="role-label">
                                <span class="role-title">Penyewa Kos</span>
                                <span class="role-desc">Cari &amp; ajukan sewa kamar</span>
                            </label>
                        </div>
                        <div class="role-option">
                            <input type="radio"
                                   name="role"
                                   id="role_pemilik"
                                   value="pemilik"
                                   {{ old('role') === 'pemilik' ? 'checked' : '' }}>
                            <label for="role_pemilik" class="role-label">
                                <span class="role-title">Pemilik Kos</span>
                                <span class="role-desc">Kelola kamar &amp; tagihan</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text"
                           id="name"
                           name="name"
                           class="form-control"
                           placeholder="Contoh: Budi Santoso"
                           value="{{ old('name') }}"
                           required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label for="email" class="form-label">Alamat Email</label>
                        <input type="email"
                               id="email"
                               name="email"
                               class="form-control"
                               placeholder="nama@email.com"
                               value="{{ old('email') }}"
                               required>
                    </div>
                    <div class="col-sm-6">
                        <label for="nomor_hp" class="form-label">Nomor WhatsApp / HP</label>
                        <input type="text"
                               id="nomor_hp"
                               name="nomor_hp"
                               class="form-control font-mono"
                               placeholder="081234567890"
                               value="{{ old('nomor_hp') }}">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <label for="password" class="form-label">Kata Sandi</label>
                        <input type="password"
                               id="password"
                               name="password"
                               class="form-control"
                               placeholder="Min. 6 karakter"
                               required>
                    </div>
                    <div class="col-sm-6">
                        <label for="password_confirmation" class="form-label">Ulangi Kata Sandi</label>
                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               class="form-control"
                               placeholder="Ketik ulang sandi"
                               required>
                    </div>
                </div>

                <button type="submit" class="btn-nk-primary w-100 py-2 mb-3">
                    Buat Akun
                </button>
            </form>

            <p class="text-center text-secondary small mb-0">
                Sudah memiliki akun?
                <a href="{{ url('/login') }}" class="fw-semibold text-decoration-none" style="color: var(--nk-blue);">
                    Masuk di sini
                </a>
            </p>
        </div>
    </div>
</div>
@endsection

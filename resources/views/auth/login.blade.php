@extends('layouts.app')

@section('title', 'Masuk Akun')

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
            grid-template-columns: 1.05fr 0.95fr;
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
        opacity: 0.32;
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
        max-width: 400px;
    }

    .pw-toggle-btn {
        background: transparent;
        border: 1px solid var(--nk-border-strong);
        border-left: none;
        border-radius: 0 8px 8px 0;
        padding: 0 0.85rem;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--nk-muted);
    }

    .pw-toggle-btn:hover {
        color: var(--nk-ink);
        background-color: var(--nk-surface-subtle);
    }
</style>
@endpush

@section('content')
<div class="auth-shell">
    <div class="auth-visual d-none d-lg-flex">
        <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80"
             alt="Interior kamar kos modern"
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

        <div class="auth-visual-content my-auto py-5" style="max-width: 460px;">
            <h1 class="fw-bold mb-3" style="font-size: 2.1rem; letter-spacing: -0.03em; line-height: 1.2;">
                Kelola dan temukan hunian kos tanpa perantara rumit.
            </h1>
            <p class="mb-0" style="color: #CBD5E1; font-size: 0.95rem;">
                Satu tempat untuk cek ketersediaan kamar, ajukan kontrak sewa, hingga cetak kwitansi pembayaran bulanan secara transparan.
            </p>
        </div>

        <div class="auth-visual-content d-flex gap-4 pt-3 border-top border-secondary border-opacity-50 small" style="color: #CBD5E1;">
            <div>
                <strong class="text-white d-block">Penyewa Kos</strong>
                Cari kamar &amp; pantau tagihan
            </div>
            <div>
                <strong class="text-white d-block">Pemilik Kos</strong>
                Kelola unit &amp; konfirmasi sewa
            </div>
        </div>
    </div>

    <div class="auth-form-pane">
        <div class="auth-form-box">
            <div class="d-flex align-items-center justify-content-between gap-2 mb-4">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('images/logo/logo-ngekosyuk.jpeg') }}"
                         alt="Logo ngeKosYuk"
                         width="36"
                         height="36"
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

            <h2 class="fw-bold mb-1" style="font-size: 1.5rem; letter-spacing: -0.025em;">Masuk ke Akun Anda</h2>
            <p class="text-secondary small mb-4">Gunakan email terdaftar untuk mengakses panel penyewa atau pemilik kos.</p>

            @if(session('success'))
                <div class="alert alert-success py-2 px-3 small mb-3" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-warning py-2 px-3 small mb-3" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger py-2 px-3 small mb-3" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ url('/login') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Alamat Email</label>
                    <input type="email"
                           id="email"
                           name="email"
                           class="form-control"
                           placeholder="nama@email.com"
                           value="{{ old('email') }}"
                           required
                           autofocus>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <div class="input-group">
                        <input type="password"
                               id="password"
                               name="password"
                               class="form-control"
                               style="border-right: none;"
                               placeholder="Masukkan kata sandi"
                               required>
                        <button type="button"
                                class="pw-toggle-btn"
                                id="btnTogglePassword"
                                aria-label="Tampilkan atau sembunyikan kata sandi">
                            Lihat
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-nk-primary w-100 py-2 mb-3">
                    Masuk Sekarang
                </button>
            </form>

            <p class="text-center text-secondary small mb-0">
                Belum punya akun?
                <a href="{{ url('/register') }}" class="fw-semibold text-decoration-none" style="color: var(--nk-blue);">
                    Daftar akun baru
                </a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const pwInput = document.getElementById('password');
    const toggleBtn = document.getElementById('btnTogglePassword');
    if (pwInput && toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            const isHidden = pwInput.type === 'password';
            pwInput.type = isHidden ? 'text' : 'password';
            toggleBtn.textContent = isHidden ? 'Sembunyikan' : 'Lihat';
        });
    }
});
</script>
@endpush

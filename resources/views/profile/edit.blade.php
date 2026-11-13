@extends('layouts.app')

@section('title', 'Profil Saya')

@php
    $roleLabel = ['admin' => 'Admin', 'pemilik' => 'Pemilik Kos', 'penyewa' => 'Penyewa'][$user->role] ?? ucfirst($user->role);
    $initials = collect(explode(' ', trim($user->name)))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
@endphp

@push('styles')
<style>
    .profile-avatar {
        width: 64px;
        height: 64px;
        border-radius: 12px;
        background-color: var(--nk-blue-soft);
        color: var(--nk-blue);
        border: 1px solid var(--nk-blue-border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.35rem;
        letter-spacing: -0.02em;
        flex-shrink: 0;
    }

    .profile-meta dt {
        font-size: 0.76rem;
        font-weight: 600;
        color: var(--nk-muted);
        margin-bottom: 0.15rem;
    }

    .profile-meta dd {
        font-size: 0.88rem;
        margin-bottom: 0.9rem;
    }

    .profile-meta dd:last-child {
        margin-bottom: 0;
    }

    .pw-wrap {
        position: relative;
    }

    .pw-wrap .form-control {
        padding-right: 5.5rem;
    }

    .pw-peek {
        position: absolute;
        top: 50%;
        right: 0.4rem;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        font-size: 0.76rem;
        font-weight: 600;
        color: var(--nk-muted);
        padding: 0.3rem 0.5rem;
        border-radius: 5px;
    }

    .pw-peek:hover {
        color: var(--nk-blue);
        background-color: var(--nk-blue-soft);
    }
</style>
@endpush

@section('content')
@include($user->role . '.partials.navbar')

<div class="container py-4" style="max-width: 980px;">
    @php
        $dashboardUrl = match($user->role) {
            'admin' => route('admin.dashboard'),
            'pemilik' => route('pemilik.dashboard'),
            default => url('/penyewa/dashboard'),
        };
    @endphp
    <div class="nk-page-header">
        <div>
            <h1 class="nk-page-title">Profil Saya</h1>
            <p class="nk-page-subtitle">Perbarui nama, email, nomor WhatsApp, dan kata sandi akun Anda.</p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="nk-badge nk-badge-success">Akun Aktif &middot; {{ $roleLabel }}</span>
            <a href="{{ $dashboardUrl }}" class="btn-nk-secondary">
                Kembali ke Dashboard
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="nk-card p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="profile-avatar" aria-hidden="true">{{ $initials ?: '?' }}</div>
                    <div class="min-w-0">
                        <strong class="d-block text-truncate">{{ $user->name }}</strong>
                        <span class="nk-badge nk-badge-info mt-1">{{ $roleLabel }}</span>
                    </div>
                </div>

                <dl class="profile-meta mb-0 pt-3 border-top">
                    <dt>Email</dt>
                    <dd class="text-break">{{ $user->email }}</dd>
                    <dt>Nomor WhatsApp</dt>
                    <dd class="font-mono">{{ $user->nomor_hp ?: 'Belum diisi' }}</dd>
                    <dt>Bergabung sejak</dt>
                    <dd class="font-mono">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</dd>
                </dl>
            </div>
        </div>

        <div class="col-lg-8 d-flex flex-column gap-4">
            <form action="{{ route('profile.update') }}" method="POST" class="nk-card">
                @csrf
                @method('PUT')

                <div class="nk-card-header">
                    <h2 class="nk-card-title">Data Diri</h2>
                </div>

                <div class="p-4">
                    @if(session('success'))
                        <div class="alert alert-success py-2 px-3 small mb-3" role="alert">{{ session('success') }}</div>
                    @endif

                    @if($errors->hasAny(['name', 'email', 'nomor_hp']))
                        <div class="alert alert-danger py-2 px-3 small mb-3" role="alert">
                            {{ $errors->first('name') ?: ($errors->first('email') ?: $errors->first('nomor_hp')) }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-7">
                            <label for="email" class="form-label">Alamat Email</label>
                            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="col-sm-5">
                            <label for="nomor_hp" class="form-label">Nomor WhatsApp</label>
                            <input type="text" id="nomor_hp" name="nomor_hp" class="form-control font-mono @error('nomor_hp') is-invalid @enderror"
                                   placeholder="081234567890" value="{{ old('nomor_hp', $user->nomor_hp) }}">
                        </div>
                    </div>
                    @if($user->role === 'pemilik')
                        <p class="small text-secondary mt-2 mb-0">Nomor ini dipakai penyewa untuk menghubungi Anda lewat tombol WhatsApp di halaman detail kos.</p>
                    @endif
                </div>

                <div class="px-4 py-3 border-top d-flex justify-content-end">
                    <button type="submit" class="btn-nk-primary">Simpan Data Diri</button>
                </div>
            </form>

            <form action="{{ route('profile.password') }}" method="POST" class="nk-card">
                @csrf
                @method('PUT')

                <div class="nk-card-header">
                    <h2 class="nk-card-title">Ganti Kata Sandi</h2>
                </div>

                <div class="p-4">
                    @if(session('success_password'))
                        <div class="alert alert-success py-2 px-3 small mb-3" role="alert">{{ session('success_password') }}</div>
                    @endif

                    @if($errors->hasAny(['current_password', 'password']))
                        <div class="alert alert-danger py-2 px-3 small mb-3" role="alert">
                            {{ $errors->first('current_password') ?: $errors->first('password') }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Kata Sandi Saat Ini</label>
                        <div class="pw-wrap">
                            <input type="password" id="current_password" name="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror" required autocomplete="current-password">
                            <button type="button" class="pw-peek" data-target="current_password" aria-label="Tampilkan kata sandi saat ini">Lihat</button>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="password" class="form-label">Kata Sandi Baru</label>
                            <div class="pw-wrap">
                                <input type="password" id="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror" minlength="8" required autocomplete="new-password">
                                <button type="button" class="pw-peek" data-target="password" aria-label="Tampilkan kata sandi baru">Lihat</button>
                            </div>
                            <div class="form-text">Minimal 8 karakter.</div>
                        </div>
                        <div class="col-sm-6">
                            <label for="password_confirmation" class="form-label">Ulangi Kata Sandi Baru</label>
                            <div class="pw-wrap">
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                       class="form-control" minlength="8" required autocomplete="new-password">
                                <button type="button" class="pw-peek" data-target="password_confirmation" aria-label="Tampilkan ulangan kata sandi">Lihat</button>
                            </div>
                            <div class="form-text" id="pwMatchHint" aria-live="polite"></div>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-3 border-top d-flex justify-content-end">
                    <button type="submit" class="btn-nk-primary">Ganti Kata Sandi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.pw-peek').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const input = document.getElementById(btn.dataset.target);
            const hidden = input.type === 'password';
            input.type = hidden ? 'text' : 'password';
            btn.textContent = hidden ? 'Sembunyikan' : 'Lihat';
        });
    });

    const pw = document.getElementById('password');
    const confirm = document.getElementById('password_confirmation');
    const hint = document.getElementById('pwMatchHint');

    function checkMatch() {
        if (!confirm.value) {
            hint.textContent = '';
            return;
        }
        const match = pw.value === confirm.value;
        hint.textContent = match ? 'Kata sandi cocok.' : 'Kata sandi belum sama.';
        hint.style.color = match ? 'var(--nk-emerald-ink)' : 'var(--nk-rose-ink)';
    }

    pw.addEventListener('input', checkMatch);
    confirm.addEventListener('input', checkMatch);
});
</script>
@endpush


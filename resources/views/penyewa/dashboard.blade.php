@extends('layouts.app')

@section('title', 'Beranda Penyewa')

@push('styles')
<style>
    .search-band {
        background-color: var(--nk-surface);
        border: 1px solid var(--nk-border);
        border-radius: 12px;
        padding: 1.75rem;
    }

    .kos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
        gap: 1.25rem;
    }

    .kos-card {
        background-color: var(--nk-surface);
        border: 1px solid var(--nk-border);
        border-radius: 10px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .kos-card:hover {
        border-color: var(--nk-blue);
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.05);
    }

    .kos-thumb-wrap {
        position: relative;
        height: 175px;
        background-color: var(--nk-surface-subtle);
        border-bottom: 1px solid var(--nk-border);
        overflow: hidden;
    }

    .kos-thumb {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .kos-type-tag {
        position: absolute;
        top: 0.75rem;
        left: 0.75rem;
    }

    .kos-card-body {
        padding: 1.15rem;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .kos-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--nk-ink);
        margin: 0 0 0.25rem 0;
        letter-spacing: -0.01em;
    }

    .kos-address {
        font-size: 0.82rem;
        color: var(--nk-muted);
        margin-bottom: 0.85rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .kos-price-row {
        margin-top: auto;
        padding-top: 0.85rem;
        border-top: 1px solid var(--nk-border);
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 0.5rem;
    }

    .quick-chip {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--nk-muted);
        background-color: var(--nk-bg);
        border: 1px solid var(--nk-border);
        text-decoration: none;
        transition: border-color 0.15s ease, color 0.15s ease, background-color 0.15s ease;
    }

    .quick-chip:hover {
        border-color: var(--nk-blue);
        color: var(--nk-blue);
        background-color: var(--nk-blue-soft);
    }
</style>
@endpush

@section('content')
@include('penyewa.partials.navbar')

<div class="container py-4">
    {{-- Interactive Search & Quick Navigation Band --}}
    <div class="search-band mb-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <h1 class="nk-page-title mb-2">Halo, {{ Auth::user()->name }}. Cari kamar kos yang pas hari ini.</h1>
                <p class="nk-page-subtitle mb-3">
                    Bandingkan harga sewa bulanan, ketersediaan unit kamar, dan ajukan sewa langsung ke pemilik properti.
                </p>

                <form action="{{ url('/penyewa/cari-kos') }}" method="GET" class="row g-2">
                    <div class="col-sm-6">
                        <label for="lokasi_cepat" class="visually-hidden">Cari nama atau lokasi kos</label>
                        <input type="text"
                               id="lokasi_cepat"
                               name="lokasi"
                               class="form-control"
                               placeholder="Ketik nama daerah, jalan, atau nama kos...">
                    </div>
                    <div class="col-sm-3">
                        <label for="tipe_cepat" class="visually-hidden">Tipe Kos</label>
                        <select id="tipe_cepat" name="tipe_kos" class="form-select">
                            <option value="">Semua Tipe</option>
                            <option value="putra">Kos Putra</option>
                            <option value="putri">Kos Putri</option>
                            <option value="campur">Kos Campur</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <button type="submit" class="btn-nk-primary w-100">
                            Cari Kos
                        </button>
                    </div>
                </form>

                <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                    <span class="small text-secondary">Filter cepat:</span>
                    <a href="{{ url('/penyewa/cari-kos?tipe_kos=putra') }}" class="quick-chip">Kos Putra</a>
                    <a href="{{ url('/penyewa/cari-kos?tipe_kos=putri') }}" class="quick-chip">Kos Putri</a>
                    <a href="{{ url('/penyewa/cari-kos?tipe_kos=campur') }}" class="quick-chip">Kos Campur</a>
                    <a href="{{ url('/penyewa/cari-kos?harga_max=1000000') }}" class="quick-chip">Di bawah Rp 1 Juta</a>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="nk-card p-3" style="background-color: var(--nk-bg);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold small">Akses Cepat Penyewa</span>
                        <span class="nk-badge nk-badge-info">Akun Aktif</span>
                    </div>
                    <p class="small text-secondary mb-3">
                        Pantau status persetujuan pengajuan sewa kamar Anda atau unggah bukti pembayaran tagihan bulanan.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ url('/penyewa/riwayat-sewa') }}" class="btn-nk-secondary py-1 px-3 small">
                            Lihat Sewa Saya
                        </a>
                        <a href="{{ route('penyewa.pembayaran.index') }}" class="btn-nk-secondary py-1 px-3 small">
                            Riwayat &amp; Tagihan Pembayaran
                        </a>
                        <a href="{{ route('profile.edit') }}" class="btn-nk-secondary py-1 px-3 small">
                            Edit Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Rekomendasi Kos Section --}}
    <div class="d-flex flex-wrap justify-content-between align-items-end mb-3">
        <div>
            <h2 class="fw-bold mb-1" style="font-size: 1.25rem; letter-spacing: -0.02em;">Rekomendasi Kos</h2>
            <p class="text-secondary small mb-0">Daftar kos terbaru yang memiliki kamar siap huni.</p>
        </div>
        <a href="{{ url('/penyewa/cari-kos') }}" class="small fw-semibold text-decoration-none" style="color: var(--nk-blue);">
            Lihat semua kos
        </a>
    </div>

    <div class="kos-grid">
        @forelse($rekomendasiKos as $kos)
            @php
                $kamarTersedia = $kos->kamars->where('status', 'tersedia');
                $hargaTermurah = $kamarTersedia->min('harga_per_bulan') ?? $kos->kamars->min('harga_per_bulan');
                $fotoUrl = $kos->foto
                    ? (str_starts_with($kos->foto, 'http') ? $kos->foto : asset('storage/' . $kos->foto))
                    : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=600&q=80';
            @endphp
            <article class="kos-card">
                <div class="kos-thumb-wrap">
                    <img src="{{ $fotoUrl }}"
                         alt="Foto {{ $kos->nama_kos }}"
                         width="400"
                         height="175"
                         class="kos-thumb">
                    <div class="kos-type-tag">
                        @if($kos->tipe_kos === 'putra')
                            <span class="nk-badge nk-badge-info">Kos Putra</span>
                        @elseif($kos->tipe_kos === 'putri')
                            <span class="nk-badge nk-badge-teal">Kos Putri</span>
                        @else
                            <span class="nk-badge nk-badge-neutral">Kos Campur</span>
                        @endif
                    </div>
                </div>

                <div class="kos-card-body">
                    <h3 class="kos-title">{{ $kos->nama_kos }}</h3>
                    <p class="kos-address">{{ $kos->alamat }}</p>

                    <div class="d-flex align-items-center justify-content-between small text-secondary mb-2">
                        <span>Ketersediaan</span>
                        <strong class="text-dark font-mono">{{ $kamarTersedia->count() }} kamar kosong</strong>
                    </div>

                    <div class="kos-price-row">
                        <div>
                            <span class="d-block text-secondary" style="font-size: 0.72rem;">Mulai dari</span>
                            @if($hargaTermurah)
                                <strong class="font-mono" style="color: var(--nk-blue); font-size: 0.98rem;">
                                    Rp {{ number_format($hargaTermurah, 0, ',', '.') }}
                                </strong>
                                <span class="text-secondary" style="font-size: 0.75rem;">/ bln</span>
                            @else
                                <span class="text-secondary small">Belum ada kamar</span>
                            @endif
                        </div>

                        <a href="{{ url('/penyewa/detail-kos/' . $kos->id) }}" class="btn-nk-primary py-1 px-3 small">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="nk-card p-5 text-center" style="grid-column: 1 / -1;">
                <h3 class="h6 fw-bold mb-1">Belum Ada Kos Terdaftar</h3>
                <p class="text-secondary small mb-0">Saat ini belum ada data properti kos yang tersedia.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

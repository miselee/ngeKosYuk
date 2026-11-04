
@extends('layouts.app')

@section('title', ($kos->nama_kos ?? 'Detail Kos') . ' - ngeKosYuk')

@section('content')

@include('penyewa.partials.navbar')

<style>
    :root {
        --primary-blue: #2696D2;
        --dark-blue: #2468B8;
        --light-blue: #EAF4FF;
        --text-dark: #263747;
    }

    body {
        background: #f8fafe;
        color: var(--text-dark);
    }

    .detail-hero {
        width: 100%;
        height: 380px;
        object-fit: cover;
        border-radius: 0 0 18px 18px;
    }

    .detail-hero-placeholder {
        width: 100%;
        height: 380px;
        background: linear-gradient(135deg, var(--light-blue) 0%, #d4e8fc 100%);
        border-radius: 0 0 18px 18px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .detail-hero-placeholder i {
        font-size: 80px;
        color: var(--dark-blue);
        opacity: .35;
    }

    .detail-wrap {
        max-width: 960px;
        margin: -60px auto 0;
        padding: 0 20px 60px;
        position: relative;
        z-index: 2;
    }

    .detail-main-card {
        background: white;
        border: 1px solid #e5eaf0;
        border-radius: 16px;
        padding: 32px 30px 28px;
    }

    .kos-title {
        font-size: 26px;
        font-weight: 800;
        color: var(--dark-blue);
        margin-bottom: 6px;
    }

    .kos-meta {
        color: #6b7d8e;
        font-size: 14px;
    }

    .kos-meta i {
        color: var(--primary-blue);
    }

    .tipe-badge {
        display: inline-block;
        background: var(--light-blue);
        color: var(--dark-blue);
        border-radius: 20px;
        padding: 5px 16px;
        font-size: 13px;
        font-weight: 600;
    }

    .owner-info {
        background: var(--light-blue);
        border-radius: 12px;
        padding: 16px 20px;
        margin-top: 20px;
    }

    .owner-info .owner-label {
        font-size: 12px;
        color: #6b7d8e;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 4px;
    }

    .owner-info .owner-name {
        font-weight: 700;
        color: var(--text-dark);
        font-size: 15px;
    }

    .owner-info .owner-phone {
        color: var(--dark-blue);
        font-size: 14px;
    }

    .section-heading {
        font-size: 20px;
        font-weight: 800;
        color: var(--dark-blue);
        margin: 40px 0 18px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--light-blue);
    }

    .kamar-card {
        border: 1px solid #e5eaf0;
        border-radius: 14px;
        padding: 22px 24px;
        background: white;
        transition: .2s;
    }

    .kamar-card:hover {
        border-color: var(--primary-blue);
        box-shadow: 0 4px 16px rgba(38,150,210,.1);
    }

    .kamar-nomor {
        font-weight: 700;
        font-size: 17px;
        color: var(--text-dark);
    }

    .kamar-harga {
        color: var(--dark-blue);
        font-weight: 800;
        font-size: 18px;
    }

    .kamar-harga small {
        font-weight: 400;
        font-size: 13px;
        color: #6b7d8e;
    }

    .kamar-fasilitas {
        color: #5a6d7d;
        font-size: 14px;
        margin: 10px 0 16px;
        line-height: 1.6;
    }

    .btn-ajukan {
        background: var(--dark-blue);
        color: white;
        border: none;
        padding: 10px 22px;
        border-radius: 9px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        display: inline-block;
        transition: .2s;
    }

    .btn-ajukan:hover {
        background: var(--primary-blue);
        color: white;
    }

    .review-item {
        padding: 18px 0;
        border-bottom: 1px solid #eef2f6;
    }

    .review-item:last-child {
        border-bottom: none;
    }

    .review-stars i {
        color: #f0c040;
        font-size: 14px;
    }

    .review-stars i.empty {
        color: #d1d9e0;
    }

    .review-author {
        font-weight: 600;
        font-size: 15px;
        color: var(--text-dark);
    }

    .review-date {
        font-size: 12px;
        color: #94a3b0;
    }

    .review-text {
        font-size: 14px;
        color: #4a5b6a;
        margin-top: 6px;
        line-height: 1.55;
    }

    .avg-rating-box {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #fff8e6;
        border: 1px solid #f5e3a3;
        border-radius: 10px;
        padding: 8px 16px;
        margin-bottom: 14px;
    }

    .avg-rating-box .score {
        font-weight: 800;
        font-size: 20px;
        color: #c6930a;
    }

    .avg-rating-box .label {
        font-size: 13px;
        color: #8a7234;
    }

    .empty-block {
        text-align: center;
        padding: 40px 20px;
        color: #94a3b0;
    }

    .empty-block i {
        font-size: 40px;
        color: #c5d3de;
        margin-bottom: 10px;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--dark-blue);
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 20px;
    }

    .btn-back:hover {
        color: var(--primary-blue);
    }

    @media (max-width: 768px) {
        .detail-hero, .detail-hero-placeholder {
            height: 240px;
            border-radius: 0;
        }

        .detail-wrap {
            margin-top: -30px;
            padding: 0 14px 40px;
        }

        .detail-main-card {
            padding: 22px 18px 20px;
        }

        .kos-title {
            font-size: 22px;
        }
    }
</style>

@if($kos->foto)
    <img src="{{ str_starts_with($kos->foto, 'http') ? $kos->foto : asset('storage/' . $kos->foto) }}"
         alt="{{ $kos->nama_kos }}"
         class="detail-hero">
@else
    <div class="detail-hero-placeholder">
        <i class="bi bi-building"></i>
    </div>
@endif

<div class="detail-wrap">

    <a href="{{ url('/penyewa/cari-kos') }}" class="btn-back">
        <i class="bi bi-arrow-left"></i> Kembali ke pencarian
    </a>

    <div class="detail-main-card">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <h1 class="kos-title">{{ $kos->nama_kos }}</h1>
                <p class="kos-meta mb-2">
                    <i class="bi bi-geo-alt-fill"></i> {{ $kos->alamat }}
                </p>
                <span class="tipe-badge">
                    <i class="bi bi-person-fill"></i> {{ ucfirst($kos->tipe_kos) }}
                </span>
            </div>
        </div>

        @if($kos->deskripsi)
            <p class="mt-3" style="line-height:1.7; color:#4a5b6a;">
                {{ $kos->deskripsi }}
            </p>
        @endif

        @php
            $pemilikKos = $kos->pemilik ?? $kos->user;
            $nomorKontak = $pemilikKos->nomor_hp ?? $pemilikKos->no_telepon ?? null;
        @endphp
        <div class="owner-info">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="d-flex align-items-center justify-content-center"
                         style="width:44px;height:44px;border-radius:50%;background:var(--dark-blue);color:white;font-weight:700;font-size:18px;">
                        {{ strtoupper(substr($pemilikKos->name ?? 'P', 0, 1)) }}
                    </div>
                </div>
                <div class="col">
                    <div class="owner-label">Pemilik Kos</div>
                    <div class="owner-name">{{ $pemilikKos->name ?? 'Pemilik' }}</div>
                </div>
                @if($nomorKontak)
                    <div class="col-auto">
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $nomorKontak) }}" target="_blank" class="owner-phone text-decoration-none">
                            <i class="bi bi-whatsapp"></i> {{ $nomorKontak }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Kamar Tersedia --}}
    <h3 class="section-heading">
        <i class="bi bi-door-open"></i> Kamar Tersedia
    </h3>

    @php
        $kamarTersedia = ($kos->kamars ?? collect())->where('status', 'tersedia');
    @endphp

    @if($kamarTersedia->count())
        <div class="row g-3">
            @foreach($kamarTersedia as $kamar)
                <div class="col-md-6">
                    <div class="kamar-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="kamar-nomor">
                                    <i class="bi bi-hash"></i> Kamar {{ $kamar->nomor_kamar }}
                                </div>
                            </div>
                            <div class="kamar-harga">
                                Rp {{ number_format($kamar->harga_per_bulan, 0, ',', '.') }}
                                <small>/bulan</small>
                            </div>
                        </div>

                        @if($kamar->fasilitas)
                            <div class="kamar-fasilitas">
                                <i class="bi bi-check2-circle text-success"></i>
                                {{ $kamar->fasilitas }}
                            </div>
                        @endif

                        <a href="{{ url('/penyewa/pengajuan-sewa/' . $kamar->id) }}"
                           class="btn-ajukan">
                            <i class="bi bi-pencil-square"></i> Ajukan Sewa
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-block">
            <i class="bi bi-door-closed d-block"></i>
            <p class="mb-0">Tidak ada kamar tersedia saat ini.</p>
        </div>
    @endif

    {{-- Ulasan --}}
    <h3 class="section-heading">
        <i class="bi bi-chat-quote"></i> Ulasan Penghuni
    </h3>

    @php
        $ulasans = $kos->ulasans ?? collect();
        $avgRating = $ulasans->count() ? round($ulasans->avg('rating'), 1) : 0;
    @endphp

    @if($ulasans->count())
        <div class="avg-rating-box">
            <i class="bi bi-star-fill" style="color:#f0c040;font-size:18px;"></i>
            <span class="score">{{ $avgRating }}</span>
            <span class="label">dari {{ $ulasans->count() }} ulasan</span>
        </div>

        @foreach($ulasans as $ulasan)
            <div class="review-item">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="review-author">{{ $ulasan->user->name ?? 'Anonim' }}</span>
                    <span class="review-date">{{ \Carbon\Carbon::parse($ulasan->created_at)->translatedFormat('d M Y') }}</span>
                </div>
                <div class="review-stars mb-1">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi bi-star-fill {{ $i <= $ulasan->rating ? '' : 'empty' }}"></i>
                    @endfor
                </div>
                <p class="review-text mb-0">{{ $ulasan->komentar }}</p>
            </div>
        @endforeach
    @else
        <div class="empty-block">
            <i class="bi bi-chat-left-dots d-block"></i>
            <p class="mb-0">Belum ada ulasan untuk kos ini.</p>
        </div>
    @endif

</div>

@endsection

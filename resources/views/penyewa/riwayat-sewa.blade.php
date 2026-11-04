
@extends('layouts.app')

@section('title', 'Riwayat Sewa - ngeKosYuk')

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

    .riwayat-page {
        max-width: 860px;
        margin: 0 auto;
        padding: 36px 20px 60px;
    }

    .page-header {
        margin-bottom: 28px;
    }

    .page-title {
        font-size: 24px;
        font-weight: 800;
        color: var(--dark-blue);
        margin-bottom: 4px;
    }

    .page-subtitle {
        color: #6b7d8e;
        font-size: 14px;
    }

    .sewa-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .sewa-item {
        background: white;
        border: 1px solid #e5eaf0;
        border-radius: 14px;
        padding: 22px 24px;
        transition: .2s;
    }

    .sewa-item:hover {
        border-color: #c5d8ef;
        box-shadow: 0 3px 12px rgba(38,104,184,.06);
    }

    .sewa-kos-name {
        font-weight: 700;
        font-size: 16px;
        color: var(--text-dark);
    }

    .sewa-kamar {
        font-size: 13px;
        color: #6b7d8e;
        margin-top: 2px;
    }

    .sewa-period {
        font-size: 13px;
        color: #5a6d7d;
        margin-top: 8px;
    }

    .sewa-period i {
        color: var(--primary-blue);
    }

    .sewa-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .badge-status {
        font-size: 12px;
        padding: 5px 14px;
        border-radius: 20px;
        font-weight: 600;
    }

    .btn-action {
        font-size: 13px;
        padding: 7px 18px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: .2s;
    }

    .btn-bayar {
        background: var(--dark-blue);
        color: white;
        border: none;
    }

    .btn-bayar:hover {
        background: var(--primary-blue);
        color: white;
    }

    .btn-ulasan {
        background: transparent;
        color: var(--dark-blue);
        border: 1.5px solid var(--dark-blue);
    }

    .btn-ulasan:hover {
        background: var(--dark-blue);
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 70px 20px;
        background: white;
        border: 1px dashed #c5d8ef;
        border-radius: 16px;
    }

    .empty-state i {
        font-size: 52px;
        color: #b0c8e2;
    }

    .empty-state h5 {
        color: var(--dark-blue);
        font-weight: 700;
        margin-top: 16px;
    }

    .empty-state p {
        color: #7b8794;
    }

    .empty-state .btn-cari {
        background: var(--dark-blue);
        color: white;
        padding: 10px 24px;
        border-radius: 9px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        display: inline-block;
        margin-top: 10px;
    }

    .empty-state .btn-cari:hover {
        background: var(--primary-blue);
        color: white;
    }

    @media (max-width: 768px) {
        .riwayat-page {
            padding: 24px 14px 40px;
        }

        .sewa-item {
            padding: 18px 16px;
        }

        .sewa-row {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 12px !important;
        }
    }
</style>

<div class="riwayat-page">

    <div class="page-header">
        <h1 class="page-title">Riwayat Sewa</h1>
        <p class="page-subtitle">Daftar pengajuan dan riwayat sewa kos Anda.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle-fill"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($sewas->count() > 0)
        <div class="sewa-list">
            @foreach($sewas as $sewa)
                <div class="sewa-item">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 sewa-row">
                        <div class="flex-grow-1">
                            <div class="sewa-kos-name">
                                <i class="bi bi-building" style="color:var(--primary-blue);"></i>
                                {{ $sewa->kamar->kos->nama_kos ?? '-' }}
                            </div>
                            <div class="sewa-kamar">
                                Kamar #{{ $sewa->kamar->nomor_kamar ?? '-' }}
                            </div>
                            <div class="sewa-period">
                                <i class="bi bi-calendar3"></i>
                                {{ \Carbon\Carbon::parse($sewa->tanggal_mulai)->format('d M Y') }}
                                &mdash;
                                {{ \Carbon\Carbon::parse($sewa->tanggal_selesai)->format('d M Y') }}
                            </div>
                            @if($sewa->catatan)
                                <div class="text-muted small mt-1">
                                    <i class="bi bi-chat-left-text"></i> Catatan: {{ $sewa->catatan }}
                                </div>
                            @endif
                        </div>

                        <div class="sewa-actions">
                            @if($sewa->status === 'pending')
                                <span class="badge-status bg-warning text-dark">
                                    <i class="bi bi-hourglass-split"></i> Menunggu Persetujuan
                                </span>
                            @elseif($sewa->status === 'disetujui')
                                <span class="badge-status bg-success text-white">
                                    <i class="bi bi-check-circle"></i> Disetujui
                                </span>
                                <a href="{{ url('/penyewa/pembayaran/' . $sewa->id) }}"
                                   class="btn-action btn-bayar">
                                    <i class="bi bi-credit-card"></i> Bayar
                                </a>
                            @elseif($sewa->status === 'ditolak')
                                <span class="badge-status bg-danger text-white">
                                    <i class="bi bi-x-circle"></i> Ditolak
                                </span>
                            @elseif($sewa->status === 'selesai')
                                <span class="badge-status bg-secondary text-white">
                                    <i class="bi bi-check-all"></i> Selesai
                                </span>
                                @if(!$sewa->ulasan)
                                    <a href="{{ url('/penyewa/ulasan/' . $sewa->id) }}"
                                       class="btn-action btn-ulasan">
                                        <i class="bi bi-star"></i> Beri Ulasan
                                    </a>
                                @else
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-star-fill text-warning"></i> Sudah Diulas
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $sewas->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-journal-x d-block"></i>
            <h5>Belum ada riwayat sewa</h5>
            <p>Anda belum pernah mengajukan sewa kos. Mulai cari kos impian Anda!</p>
            <a href="{{ url('/penyewa/cari-kos') }}" class="btn-cari">
                <i class="bi bi-search"></i> Cari Kos
            </a>
        </div>
    @endif

</div>

@endsection

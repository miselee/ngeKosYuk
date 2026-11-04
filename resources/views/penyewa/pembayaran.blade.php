
@extends('layouts.app')

@section('title', 'Riwayat Pembayaran - ngeKosYuk')

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

    .pembayaran-page {
        max-width: 860px;
        margin: 0 auto;
        padding: 36px 20px 60px;
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
        margin-bottom: 28px;
    }

    .payment-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .payment-item {
        background: white;
        border: 1px solid #e5eaf0;
        border-radius: 14px;
        padding: 22px 24px;
        transition: .2s;
    }

    .payment-item:hover {
        border-color: #c5d8ef;
        box-shadow: 0 3px 12px rgba(38,104,184,.06);
    }

    .payment-kos {
        font-weight: 700;
        font-size: 16px;
        color: var(--text-dark);
    }

    .payment-kamar {
        font-size: 13px;
        color: #6b7d8e;
        margin-top: 2px;
    }

    .payment-amount {
        font-weight: 800;
        font-size: 19px;
        color: var(--dark-blue);
    }

    .payment-detail-row {
        font-size: 13px;
        color: #5a6d7d;
        margin-top: 10px;
        display: flex;
        flex-wrap: wrap;
        gap: 18px;
    }

    .payment-detail-row span i {
        color: var(--primary-blue);
        margin-right: 3px;
    }

    .badge-status {
        font-size: 12px;
        padding: 5px 14px;
        border-radius: 20px;
        font-weight: 600;
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

    @media (max-width: 768px) {
        .pembayaran-page {
            padding: 24px 14px 40px;
        }

        .payment-item {
            padding: 18px 16px;
        }

        .payment-row {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 12px !important;
        }
    }
</style>

<div class="pembayaran-page">

    <h1 class="page-title">Riwayat Pembayaran</h1>
    <p class="page-subtitle">Semua catatan pembayaran sewa kos Anda.</p>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($pembayarans->count() > 0)
        <div class="payment-list">
            @foreach($pembayarans as $bayar)
                <div class="payment-item">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 payment-row">
                        <div class="flex-grow-1">
                            <div class="payment-kos">
                                <i class="bi bi-building" style="color:var(--primary-blue);"></i>
                                {{ $bayar->sewa->kamar->kos->nama_kos ?? '-' }}
                            </div>
                            <div class="payment-kamar">
                                Kamar #{{ $bayar->sewa->kamar->nomor_kamar ?? '-' }}
                            </div>

                            <div class="payment-detail-row">
                                <span>
                                    <i class="bi bi-wallet2"></i>
                                    @if($bayar->metode_pembayaran === 'transfer_bank')
                                        Transfer Bank
                                    @elseif($bayar->metode_pembayaran === 'e_wallet')
                                        E-Wallet
                                    @elseif($bayar->metode_pembayaran === 'tunai')
                                        Tunai
                                    @else
                                        {{ ucwords(str_replace('_', ' ', $bayar->metode_pembayaran)) }}
                                    @endif
                                </span>
                                <span>
                                    <i class="bi bi-calendar-event"></i>
                                    {{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d M Y') }}
                                </span>
                                @if($bayar->bukti_pembayaran)
                                    <span>
                                        <a href="{{ asset('storage/' . $bayar->bukti_pembayaran) }}" target="_blank" class="text-decoration-none" style="color:var(--dark-blue);">
                                            <i class="bi bi-image"></i> Lihat Bukti
                                        </a>
                                    </span>
                                @endif
                                @if($bayar->keterangan)
                                    <span class="text-muted fst-italic">
                                        "{{ $bayar->keterangan }}"
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="text-end">
                            <div class="payment-amount">
                                Rp {{ number_format($bayar->jumlah, 0, ',', '.') }}
                            </div>
                            <div class="mt-2">
                                @if($bayar->status === 'pending')
                                    <span class="badge-status bg-warning text-dark">
                                        <i class="bi bi-hourglass-split"></i> Menunggu Verifikasi
                                    </span>
                                @elseif($bayar->status === 'diverifikasi')
                                    <span class="badge-status bg-success text-white">
                                        <i class="bi bi-check-circle"></i> Diverifikasi
                                    </span>
                                @elseif($bayar->status === 'ditolak')
                                    <span class="badge-status bg-danger text-white">
                                        <i class="bi bi-x-circle"></i> Ditolak
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $pembayarans->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-receipt-cutoff d-block"></i>
            <h5>Belum ada pembayaran</h5>
            <p>Riwayat pembayaran Anda akan muncul di sini setelah melakukan pembayaran.</p>
        </div>
    @endif

</div>

@endsection

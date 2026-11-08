@extends('layouts.app')

@section('title', 'Kwitansi ' . $pembayaran->kode_invoice)

@push('styles')
<style>
    .invoice-sheet {
        background-color: var(--nk-surface);
        border: 1px solid var(--nk-border-strong);
        border-radius: 10px;
        padding: 2.25rem;
        max-width: 780px;
        margin: 0 auto;
    }

    @media print {
        .no-print, .nk-navbar {
            display: none !important;
        }
        body {
            background-color: #FFFFFF !important;
        }
        .invoice-sheet {
            border: 1px solid #000000;
            box-shadow: none;
            padding: 1.5rem;
            max-width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3 no-print" style="max-width: 780px; margin: 0 auto;">
        @if(auth()->user()->role === 'pemilik')
            <a href="{{ route('pemilik.pembayaran.index') }}" class="btn-nk-secondary py-1 px-3 small">
                &larr; Kembali ke Pembayaran
            </a>
        @else
            <a href="{{ route('penyewa.pembayaran.index') }}" class="btn-nk-secondary py-1 px-3 small">
                &larr; Kembali ke Riwayat Pembayaran
            </a>
        @endif

        <button type="button" onclick="window.print()" class="btn-nk-primary py-1 px-3 small">
            Cetak Kwitansi / Simpan PDF
        </button>
    </div>

    <div class="invoice-sheet">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 pb-4 mb-4 border-bottom">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('images/logo/logo-ngekosyuk.jpeg') }}"
                     alt="Logo ngeKosYuk"
                     width="48"
                     height="48"
                     class="rounded-2 border">
                <div>
                    <h1 class="h5 fw-bold mb-0">KWITANSI PEMBAYARAN KOS</h1>
                    <span class="small text-secondary">Bukti Transaksi Resmi ngeKosYuk</span>
                </div>
            </div>

            <div class="text-sm-end">
                <div class="font-mono fw-bold fs-6">{{ $pembayaran->kode_invoice }}</div>
                <div class="small text-secondary mb-2">
                    Tanggal: {{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d M Y') }}
                </div>
                @if($pembayaran->status === 'diverifikasi')
                    <span class="nk-badge nk-badge-success">LUNAS / TERVERIFIKASI</span>
                @elseif($pembayaran->status === 'pending')
                    <span class="nk-badge nk-badge-warning">MENUNGGU VERIFIKASI</span>
                @else
                    <span class="nk-badge nk-badge-danger">DITOLAK</span>
                @endif
            </div>
        </div>

        <div class="row g-4 mb-4 small">
            <div class="col-sm-6">
                <span class="text-secondary d-block mb-1">Diterima Dari (Penyewa):</span>
                <strong class="d-block fs-6">{{ $pembayaran->sewa->penyewa->name }}</strong>
                <span class="text-secondary d-block">{{ $pembayaran->sewa->penyewa->email }}</span>
                <span class="text-secondary font-mono">{{ $pembayaran->sewa->penyewa->nomor_hp ?? '-' }}</span>
            </div>
            <div class="col-sm-6 text-sm-end">
                <span class="text-secondary d-block mb-1">Properti Kos &amp; Pengelola:</span>
                <strong class="d-block fs-6">{{ $pembayaran->sewa->kamar->kos->nama_kos }}</strong>
                <span class="text-secondary d-block">{{ $pembayaran->sewa->kamar->kos->alamat }}</span>
                <span class="text-secondary">Pemilik: {{ $pembayaran->sewa->kamar->kos->pemilik->name ?? '-' }}</span>
            </div>
        </div>

        <div class="table-responsive mb-4">
            <table class="nk-table border rounded-2">
                <thead>
                    <tr>
                        <th>Deskripsi Tagihan</th>
                        <th>Periode Kontrak</th>
                        <th>Metode</th>
                        <th class="text-end">Jumlah Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="fw-semibold">Sewa Kamar #{{ $pembayaran->sewa->kamar->nomor_kamar }}</div>
                            <div class="small text-secondary">{{ $pembayaran->sewa->kamar->kos->nama_kos }}</div>
                        </td>
                        <td class="font-mono small">
                            {{ \Carbon\Carbon::parse($pembayaran->sewa->tanggal_mulai)->format('d M Y') }} s/d
                            {{ \Carbon\Carbon::parse($pembayaran->sewa->tanggal_selesai)->format('d M Y') }}
                        </td>
                        <td>{{ $pembayaran->label_metode }}</td>
                        <td class="text-end font-mono fw-bold">
                            Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center p-3 rounded-2 mb-4" style="background-color: var(--nk-bg); border: 1px solid var(--nk-border);">
            <span class="fw-semibold">Total Pembayaran Diterima</span>
            <strong class="font-mono fs-4" style="color: var(--nk-blue);">
                Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}
            </strong>
        </div>

        @if($pembayaran->keterangan)
            <div class="small text-secondary mb-4">
                <strong>Keterangan Transaksi:</strong> {{ $pembayaran->keterangan }}
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-end pt-3 border-top small text-secondary">
            <div>
                Dicetak melalui sistem manajemen hunian <strong>ngeKosYuk</strong>.
            </div>
            <div class="text-end">
                <span class="d-block mb-4">Pengelola Properti,</span>
                <strong class="text-dark">{{ $pembayaran->sewa->kamar->kos->pemilik->name ?? 'Pemilik Kos' }}</strong>
            </div>
        </div>
    </div>
</div>
@endsection

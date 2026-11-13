@extends('layouts.app')

@section('title', 'Pembayaran & Tagihan')

@section('content')
@include('penyewa.partials.navbar')

<div class="container py-4">
    <div class="nk-page-header">
        <div>
            <h1 class="nk-page-title">Riwayat Pembayaran</h1>
            <p class="nk-page-subtitle">Pantau tagihan sewa aktif, status verifikasi pembayaran, dan unduh bukti kwitansi resmi.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ url('/penyewa/riwayat-sewa') }}" class="btn-nk-secondary">
                Lihat Sewa Saya
            </a>
            <a href="{{ url('/penyewa/cari-kos') }}" class="btn-nk-primary">
                Cari Kos
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2 px-3 small mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    {{-- Metric Ledger Strip --}}
    <div class="nk-metric-strip mb-4">
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Total Dibayarkan (Lunas)</span>
            <div class="nk-metric-value" style="color: var(--nk-teal);">
                Rp {{ number_format($totalNominalLunas ?? 0, 0, ',', '.') }}
            </div>
            <div class="nk-metric-sub">{{ $totalDiverifikasi ?? 0 }} transaksi diverifikasi</div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Menunggu Verifikasi</span>
            <div class="nk-metric-value">
                {{ $totalPending ?? 0 }}
            </div>
            <div class="nk-metric-sub">Sedang diperiksa oleh pemilik kos</div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Kontrak Sewa Aktif</span>
            <div class="nk-metric-value" style="color: var(--nk-blue);">
                {{ isset($sewaAktif) ? $sewaAktif->count() : 0 }}
            </div>
            <div class="nk-metric-sub">Kamar yang sedang Anda tempati</div>
        </div>
    </div>

    {{-- Active Rental Contracts Requiring Payment --}}
    @if(isset($sewaAktif) && $sewaAktif->count() > 0)
        <div class="nk-card mb-4">
            <div class="nk-card-header">
                <h2 class="nk-card-title">Tagihan Kontrak Sewa Aktif</h2>
                <span class="small text-secondary">Pilih kontrak untuk mengirim pembayaran baru</span>
            </div>
            <div class="p-3 d-flex flex-column gap-2">
                @foreach($sewaAktif as $kontrak)
                    @php
                        $start = \Carbon\Carbon::parse($kontrak->tanggal_mulai);
                        $end = \Carbon\Carbon::parse($kontrak->tanggal_selesai);
                        $durasiBulan = max(1, (int) round($start->diffInDays($end) / 30));
                        $estimasiTotal = $durasiBulan * $kontrak->kamar->harga_per_bulan;
                        $sudahLunas = $kontrak->pembayarans->where('status', 'diverifikasi')->sum('jumlah');
                        $sisaTagihan = max(0, $estimasiTotal - $sudahLunas);
                    @endphp
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 p-3 rounded-2 border" style="background-color: var(--nk-bg);">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <strong class="small">{{ $kontrak->kamar->kos->nama_kos }}</strong>
                                <span class="font-mono small text-secondary">Kamar #{{ $kontrak->kamar->nomor_kamar }}</span>
                            </div>
                            <span class="small text-secondary">
                                Periode {{ $start->format('d M Y') }} s/d {{ $end->format('d M Y') }} ({{ $durasiBulan }} bln)
                            </span>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <div class="text-end">
                                <span class="d-block text-secondary" style="font-size: 0.72rem;">Sisa Tagihan Kontrak</span>
                                <strong class="font-mono" style="color: {{ $sisaTagihan > 0 ? 'var(--nk-blue)' : 'var(--nk-teal)' }};">
                                    Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                                </strong>
                            </div>
                            <a href="{{ route('penyewa.pembayaran.create', $kontrak->id) }}" class="btn-nk-primary py-1 px-3 small">
                                Bayar Sekarang
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Payment History Table --}}
    <div class="nk-card overflow-hidden">
        <div class="nk-card-header">
            <h2 class="nk-card-title">Daftar Transaksi Pembayaran</h2>
            <span class="small text-secondary font-mono">{{ $pembayarans->total() }} transaksi</span>
        </div>

        @if($pembayarans->count() > 0)
            <div class="table-responsive">
                <table class="nk-table">
                    <thead>
                        <tr>
                            <th>No. Invoice</th>
                            <th>Properti &amp; Kamar</th>
                            <th>Tanggal Bayar</th>
                            <th>Metode</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pembayarans as $bayar)
                            <tr>
                                <td class="font-mono fw-semibold">{{ $bayar->kode_invoice }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $bayar->sewa->kamar->kos->nama_kos }}</div>
                                    <div class="small text-secondary font-mono">Kamar #{{ $bayar->sewa->kamar->nomor_kamar }}</div>
                                </td>
                                <td class="font-mono small">
                                    {{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d M Y') }}
                                </td>
                                <td>
                                    <span class="nk-badge nk-badge-neutral">{{ $bayar->label_metode }}</span>
                                </td>
                                <td class="font-mono fw-bold">
                                    Rp {{ number_format($bayar->jumlah, 0, ',', '.') }}
                                </td>
                                <td>
                                    @if($bayar->status === 'diverifikasi')
                                        <span class="nk-badge nk-badge-success">Diverifikasi</span>
                                    @elseif($bayar->status === 'pending')
                                        <span class="nk-badge nk-badge-warning">Menunggu Konfirmasi</span>
                                    @else
                                        <span class="nk-badge nk-badge-danger">Ditolak</span>
                                    @endif

                                    @if($bayar->keterangan)
                                        <div class="small text-secondary mt-1" style="font-size: 0.74rem;">
                                            {{ $bayar->keterangan }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('penyewa.pembayaran.kwitansi', $bayar->id) }}"
                                           class="btn-nk-secondary py-1 px-2 small">
                                            Kwitansi
                                        </a>
                                        @if($bayar->status === 'ditolak')
                                            <a href="{{ route('penyewa.pembayaran.create', $bayar->sewa_id) }}"
                                               class="btn-nk-primary py-1 px-2 small">
                                                Kirim Ulang
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($pembayarans->hasPages())
                <div class="p-3 border-top">
                    {{ $pembayarans->links() }}
                </div>
            @endif
        @else
            <div class="p-5 text-center">
                <h3 class="h6 fw-bold mb-1">Belum Ada Transaksi Pembayaran</h3>
                <p class="text-secondary small mb-3">Transaksi pembayaran kos Anda akan tercatat secara otomatis di halaman ini.</p>
                <a href="{{ url('/penyewa/riwayat-sewa') }}" class="btn-nk-secondary small">
                    Cek Kontrak Sewa Saya
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

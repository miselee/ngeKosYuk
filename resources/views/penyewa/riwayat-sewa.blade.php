@extends('layouts.app')

@section('title', 'Sewa Saya')

@section('content')
@include('penyewa.partials.navbar')

<div class="container py-4">
    <div class="nk-page-header">
        <div>
            <h1 class="nk-page-title">Riwayat &amp; Kontrak Sewa Saya</h1>
            <p class="nk-page-subtitle">Pantau status pengajuan sewa kamar, lakukan pembayaran tagihan, atau tulis ulasan properti kos.</p>
        </div>
        <a href="{{ url('/penyewa/cari-kos') }}" class="btn-nk-primary">
            Cari Kos Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2 px-3 small mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info py-2 px-3 small mb-4" role="alert">
            {{ session('info') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger py-2 px-3 small mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="d-flex flex-column gap-3">
        @forelse($sewas as $sewa)
            @php
                $sudahUlasan = \App\Models\Ulasan::where('user_id', Auth::id())
                    ->where('sewa_id', $sewa->id)
                    ->exists();
                $totalLunas = $sewa->pembayarans->where('status', 'diverifikasi')->sum('jumlah');
            @endphp
            <div class="nk-card p-4">
                <div class="row align-items-center g-3">
                    <div class="col-lg-5">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            @if($sewa->status === 'pending')
                                <span class="nk-badge nk-badge-warning">Menunggu Konfirmasi</span>
                            @elseif($sewa->status === 'disetujui')
                                <span class="nk-badge nk-badge-success">Disetujui</span>
                            @elseif($sewa->status === 'ditolak')
                                <span class="nk-badge nk-badge-danger">Ditolak</span>
                            @else
                                <span class="nk-badge nk-badge-neutral">Selesai</span>
                            @endif

                            <span class="font-mono small text-secondary">
                                Kamar #{{ $sewa->kamar->nomor_kamar }}
                            </span>
                        </div>

                        <h2 class="h6 fw-bold mb-1">{{ $sewa->kamar->kos->nama_kos }}</h2>
                        <p class="small text-secondary mb-0">{{ $sewa->kamar->kos->alamat }}</p>
                    </div>

                    <div class="col-lg-4">
                        <div class="small">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-secondary">Periode Sewa</span>
                                <strong class="font-mono">
                                    {{ \Carbon\Carbon::parse($sewa->tanggal_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($sewa->tanggal_selesai)->format('d M Y') }}
                                </strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-secondary">Tarif Kamar</span>
                                <strong class="font-mono">Rp {{ number_format($sewa->kamar->harga_per_bulan, 0, ',', '.') }}/bln</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-secondary">Total Terverifikasi</span>
                                <strong class="font-mono" style="color: var(--nk-teal);">Rp {{ number_format($totalLunas, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 text-lg-end d-flex flex-wrap justify-content-lg-end gap-2">
                        @if($sewa->status === 'disetujui')
                            <a href="{{ route('penyewa.pembayaran.create', $sewa->id) }}" class="btn-nk-primary py-1 px-3 small">
                                Bayar Tagihan
                            </a>
                        @endif

                        @if(in_array($sewa->status, ['disetujui', 'selesai']) && !$sudahUlasan)
                            <a href="{{ url('/penyewa/ulasan/' . $sewa->id) }}" class="btn-nk-secondary py-1 px-3 small">
                                Beri Ulasan
                            </a>
                        @endif

                        <a href="{{ url('/penyewa/detail-kos/' . $sewa->kamar->kos_id) }}" class="btn-nk-secondary py-1 px-3 small">
                            Detail Kos
                        </a>
                    </div>
                </div>

                @if($sewa->catatan)
                    <div class="mt-3 pt-2 border-top small text-secondary">
                        <strong>Catatan:</strong> {{ $sewa->catatan }}
                    </div>
                @endif
            </div>
        @empty
            <div class="nk-card p-5 text-center">
                <h2 class="h6 fw-bold mb-1">Belum Ada Riwayat Sewa</h2>
                <p class="text-secondary small mb-3">Anda belum pernah mengajukan sewa kamar kos.</p>
                <a href="{{ url('/penyewa/cari-kos') }}" class="btn-nk-primary small">
                    Mulai Cari Kos
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Konfirmasi Pembayaran - ngeKosYuk')

@section('content')
@include('pemilik.partials.navbar')

<style>
    :root {
        --primary-blue: #2696D2;
        --dark-blue: #2468B8;
        --light-blue: #EAF4FF;
        --text-dark: #263747;
    }

    body {
        background-color: #f7f9fc;
        color: var(--text-dark);
        font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", sans-serif;
    }

    .page-header-box {
        background: white;
        border-bottom: 1px solid #e7ecf3;
        padding: 24px 0;
        margin-bottom: 24px;
    }

    .content-box {
        background: white;
        border-radius: 14px;
        border: 1px solid #e7ecf3;
        box-shadow: 0 4px 12px rgba(27, 79, 140, 0.04);
        padding: 24px;
    }

    .stat-card-mini {
        background: white;
        border-radius: 12px;
        padding: 16px 20px;
        border: 1px solid #e7ecf3;
        box-shadow: 0 2px 8px rgba(27, 79, 140, 0.03);
    }

    .filter-card {
        background: #fbfcfe;
        border: 1px solid #e7ecf3;
        border-radius: 12px;
        padding: 18px;
        margin-bottom: 20px;
    }

    .btn-brand-primary {
        background: #2468B8;
        color: white;
        font-weight: 600;
        border: none;
        padding: 9px 18px;
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .btn-brand-primary:hover {
        background: #1b4f8c;
        color: white;
    }

    .stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .stat-pill:hover {
        transform: translateY(-1px);
    }

    .stat-pill-all {
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }

    .stat-pill-pending {
        background-color: #fff8e6;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .stat-pill-diverifikasi {
        background-color: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .stat-pill-ditolak {
        background-color: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .stat-pill.active {
        box-shadow: 0 0 0 2px var(--dark-blue);
    }

    .badge-pending {
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .badge-diverifikasi {
        background-color: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .badge-ditolak {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .bukti-thumb {
        width: 44px;
        height: 44px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        border: 1px solid #cbd5e1;
        transition: transform 0.15s ease;
    }

    .bukti-thumb:hover {
        transform: scale(1.08);
        border-color: var(--dark-blue);
    }
</style>

{{-- Page Header --}}
<div class="page-header-box">
    <div class="container-fluid px-3 px-lg-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 small">
                <li class="breadcrumb-item"><a href="{{ route('pemilik.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Konfirmasi Pembayaran</li>
            </ol>
        </nav>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h3 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-credit-card-2-front-fill text-primary me-2"></i>Konfirmasi Pembayaran
                </h3>
                <p class="text-muted small mb-0">Verifikasi bukti transfer dari penyewa, konfirmasi pembayaran masuk, atau tolak bukti yang tidak sesuai.</p>
            </div>
            @if($totalPending > 0)
                <div>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-2 fs-6">
                        <i class="bi bi-hourglass-split me-1"></i> {{ $totalPending }} Pembayaran Perlu Diverifikasi
                    </span>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Main Container --}}
<div class="container-fluid px-3 px-lg-4 pb-5">
    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Mini KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card-mini d-flex align-items-center gap-3">
                <div class="rounded-3 bg-primary-subtle text-primary p-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="bi bi-receipt fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small fw-medium d-block">Total Transaksi</span>
                    <h5 class="fw-bold mb-0 text-dark">{{ $totalPembayaran }}</h5>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-mini d-flex align-items-center gap-3">
                <div class="rounded-3 bg-warning-subtle text-warning-emphasis p-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="bi bi-clock-history fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small fw-medium d-block">Menunggu Verifikasi</span>
                    <h5 class="fw-bold mb-0 text-warning">{{ $totalPending }}</h5>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-mini d-flex align-items-center gap-3">
                <div class="rounded-3 bg-success-subtle text-success p-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="bi bi-check-circle fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small fw-medium d-block">Diverifikasi</span>
                    <h5 class="fw-bold mb-0 text-success">{{ $totalDiverifikasi }}</h5>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-mini d-flex align-items-center gap-3">
                <div class="rounded-3 bg-info-subtle text-info-emphasis p-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="bi bi-cash-stack fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small fw-medium d-block">Dana Masuk (Verified)</span>
                    <h5 class="fw-bold mb-0 text-primary">Rp {{ number_format($totalNominalMasuk, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
    </div>

    {{-- Content Box --}}
    <div class="content-box">
        {{-- Status Filter Tabs / Pills --}}
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3 pb-3 border-bottom">
            <span class="text-muted small me-2">Status:</span>
            <a href="{{ route('pemilik.pembayaran.index', array_merge(request()->except('status', 'page'), [])) }}" 
               class="stat-pill stat-pill-all {{ !request('status') ? 'active' : '' }}">
                Semua ({{ $totalPembayaran }})
            </a>
            <a href="{{ route('pemilik.pembayaran.index', array_merge(request()->except('status', 'page'), ['status' => 'pending'])) }}" 
               class="stat-pill stat-pill-pending {{ request('status') === 'pending' ? 'active' : '' }}">
                <i class="bi bi-hourglass-split"></i> Menunggu Verifikasi ({{ $totalPending }})
            </a>
            <a href="{{ route('pemilik.pembayaran.index', array_merge(request()->except('status', 'page'), ['status' => 'diverifikasi'])) }}" 
               class="stat-pill stat-pill-diverifikasi {{ request('status') === 'diverifikasi' ? 'active' : '' }}">
                <i class="bi bi-check2-circle"></i> Diverifikasi ({{ $totalDiverifikasi }})
            </a>
            <a href="{{ route('pemilik.pembayaran.index', array_merge(request()->except('status', 'page'), ['status' => 'ditolak'])) }}" 
               class="stat-pill stat-pill-ditolak {{ request('status') === 'ditolak' ? 'active' : '' }}">
                <i class="bi bi-x-circle"></i> Ditolak ({{ $totalDitolak }})
            </a>
        </div>

        {{-- Filter & Search Form --}}
        <form method="GET" action="{{ route('pemilik.pembayaran.index') }}" class="filter-card">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-5">
                    <label class="form-label small fw-semibold text-muted mb-1">Cari Transaksi</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0" placeholder="Nama penyewa, no kamar, atau catatan...">
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1">Pilih Lokasi Kos</label>
                    <select name="kos_id" class="form-select">
                        <option value="">Semua Lokasi Kos</option>
                        @foreach($daftarKos as $kos)
                            <option value="{{ $kos->id }}" {{ request('kos_id') == $kos->id ? 'selected' : '' }}>
                                {{ $kos->nama_kos }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-brand-primary w-100">
                        <i class="bi bi-funnel-fill me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['q', 'kos_id', 'status']))
                        <a href="{{ route('pemilik.pembayaran.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>

        {{-- Table --}}
        @if($pembayarans->isEmpty())
            <div class="text-center py-5">
                <div class="rounded-circle bg-light d-inline-flex p-4 mb-3 text-muted">
                    <i class="bi bi-credit-card display-4"></i>
                </div>
                @if(request()->hasAny(['q', 'kos_id', 'status']))
                    <h5 class="fw-bold text-dark">Tidak Ada Data Pembayaran yang Cocok</h5>
                    <p class="text-muted small mb-3">Coba ubah kata kunci pencarian atau reset filter.</p>
                    <a href="{{ route('pemilik.pembayaran.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                    </a>
                @else
                    <h5 class="fw-bold text-dark">Belum Ada Catatan Pembayaran Masuk</h5>
                    <p class="text-muted small mb-0">Ketika penyewa mengirimkan konfirmasi pembayaran beserta bukti transfer, datanya akan tampil di sini.</p>
                @endif
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr class="text-muted small">
                            <th style="width: 50px;">#</th>
                            <th>Penyewa</th>
                            <th>Kamar & Kos</th>
                            <th>Tanggal Bayar</th>
                            <th>Nominal</th>
                            <th>Metode</th>
                            <th>Bukti</th>
                            <th>Status</th>
                            <th class="text-end" style="width: 190px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pembayarans as $index => $bayar)
                            @php
                                $penyewa = $bayar->sewa->penyewa ?? null;
                                $kamar = $bayar->sewa->kamar ?? null;
                                $kos = $bayar->sewa->kos ?? null;
                                $rawHp = $penyewa->nomor_hp ?? '';
                                $cleanHp = preg_replace('/[^0-9]/', '', $rawHp);
                                $waNumber = str_starts_with($cleanHp, '0') ? '62' . substr($cleanHp, 1) : $cleanHp;

                                $buktiUrl = null;
                                if ($bayar->bukti_pembayaran) {
                                    $buktiUrl = str_starts_with($bayar->bukti_pembayaran, 'http')
                                        ? $bayar->bukti_pembayaran
                                        : asset('storage/' . $bayar->bukti_pembayaran);
                                }
                            @endphp
                            <tr>
                                <td class="text-muted small">
                                    {{ $pembayarans->firstItem() + $index }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; font-size: 14px;">
                                            {{ strtoupper(substr($penyewa->name ?? 'P', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block">{{ $penyewa->name ?? 'Penyewa' }}</span>
                                            <span class="text-muted" style="font-size: 11px;">
                                                <i class="bi bi-envelope me-1"></i>{{ $penyewa->email ?? '-' }}
                                            </span>
                                            @if($rawHp)
                                                <div class="d-flex align-items-center gap-1 mt-0.5">
                                                    <span class="text-muted" style="font-size: 11px;">
                                                        <i class="bi bi-telephone me-1"></i>{{ $rawHp }}
                                                    </span>
                                                    <a href="https://wa.me/{{ $waNumber }}" target="_blank" class="badge bg-success-subtle text-success text-decoration-none px-1.5 py-0.5" style="font-size: 10px;" title="Chat WhatsApp">
                                                        <i class="bi bi-whatsapp"></i> WA
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary px-2 py-1 rounded-2">
                                        {{ $kamar->nomor_kamar ?? 'Kamar -' }}
                                    </span>
                                    <span class="fw-semibold text-dark small d-block mt-1">{{ $kos->nama_kos ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="fw-medium text-dark d-block small">
                                        {{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d M Y') }}
                                    </span>
                                    <span class="text-muted" style="font-size: 11px;">
                                        {{ \Carbon\Carbon::parse($bayar->created_at)->diffForHumans() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary fs-6 d-block">
                                        Rp {{ number_format($bayar->jumlah, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    @if($bayar->metode_pembayaran === 'transfer_bank')
                                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1">
                                            <i class="bi bi-bank me-1"></i> Transfer Bank
                                        </span>
                                    @elseif($bayar->metode_pembayaran === 'e_wallet')
                                        <span class="badge bg-purple-subtle text-primary border px-2 py-1" style="background:#f3e8ff;">
                                            <i class="bi bi-phone me-1"></i> E-Wallet
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                            <i class="bi bi-cash me-1"></i> Tunai
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($buktiUrl)
                                        <img src="{{ $buktiUrl }}" alt="Bukti Transfer" class="bukti-thumb" data-bs-toggle="modal" data-bs-target="#modalBukti{{ $bayar->id }}" title="Klik untuk memperbesar bukti">
                                    @else
                                        <span class="text-muted small fst-italic">Tanpa Berkas</span>
                                    @endif
                                </td>
                                <td>
                                    @if($bayar->status === 'pending')
                                        <span class="badge badge-pending rounded-pill px-2.5 py-1">
                                            <i class="bi bi-hourglass-split me-1"></i> Menunggu
                                        </span>
                                    @elseif($bayar->status === 'diverifikasi')
                                        <span class="badge badge-diverifikasi rounded-pill px-2.5 py-1">
                                            <i class="bi bi-check-circle-fill me-1"></i> Diverifikasi
                                        </span>
                                    @elseif($bayar->status === 'ditolak')
                                        <span class="badge badge-ditolak rounded-pill px-2.5 py-1">
                                            <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end align-items-center gap-1">
                                        {{-- Tombol Detail --}}
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $bayar->id }}" title="Lihat Detail Transaksi">
                                            <i class="bi bi-eye"></i> Detail
                                        </button>

                                        {{-- Aksi Verifikasi / Tolak --}}
                                        @if($bayar->status === 'pending')
                                            {{-- Verifikasi Button --}}
                                            <form action="{{ route('pemilik.pembayaran.verifikasi', $bayar) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success" title="Verifikasi Pembayaran" onclick="return confirm('Apakah Anda yakin ingin memverifikasi pembayaran sebesar Rp {{ number_format($bayar->jumlah, 0, ',', '.') }}?')">
                                                    <i class="bi bi-check-lg"></i> Terima
                                                </button>
                                            </form>

                                            {{-- Tolak Button --}}
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalTolak{{ $bayar->id }}" title="Tolak Pembayaran">
                                                <i class="bi bi-x-lg"></i> Tolak
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Modal Preview Bukti Pembayaran --}}
                                    @if($buktiUrl)
                                        <div class="modal fade text-start" id="modalBukti{{ $bayar->id }}" tabindex="-1" aria-labelledby="modalBuktiLabel{{ $bayar->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header border-bottom">
                                                        <h5 class="modal-title fw-bold" id="modalBuktiLabel{{ $bayar->id }}">
                                                            <i class="bi bi-image text-primary me-2"></i>Bukti Transfer - {{ $penyewa->name ?? 'Penyewa' }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-center p-3">
                                                        <img src="{{ $buktiUrl }}" alt="Bukti Transfer" class="img-fluid rounded-3 border mb-3" style="max-height: 480px; width: auto; object-fit: contain;">
                                                        <div class="p-2.5 bg-light rounded-3 text-start small">
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="text-muted">Nominal:</span>
                                                                <strong class="text-primary">Rp {{ number_format($bayar->jumlah, 0, ',', '.') }}</strong>
                                                            </div>
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="text-muted">Metode:</span>
                                                                <span>{{ str_replace('_', ' ', ucfirst($bayar->metode_pembayaran)) }}</span>
                                                            </div>
                                                            <div class="d-flex justify-content-between">
                                                                <span class="text-muted">Tanggal:</span>
                                                                <span>{{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d F Y') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top bg-light justify-content-between">
                                                        <a href="{{ $buktiUrl }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-3">
                                                            <i class="bi bi-box-arrow-up-right me-1"></i> Buka Ukuran Penuh
                                                        </a>
                                                        <button type="button" class="btn btn-secondary btn-sm rounded-3 px-3" data-bs-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Modal Detail Transaksi --}}
                                    <div class="modal fade text-start" id="modalDetail{{ $bayar->id }}" tabindex="-1" aria-labelledby="modalDetailLabel{{ $bayar->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header border-bottom">
                                                    <h5 class="modal-title fw-bold" id="modalDetailLabel{{ $bayar->id }}">
                                                        <i class="bi bi-receipt text-primary me-2"></i>Rincian Pembayaran #{{ $bayar->id }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    {{-- Info Pembayaran --}}
                                                    <div class="p-3 bg-light rounded-3 mb-3">
                                                        <h6 class="fw-bold text-dark mb-2">Informasi Transaksi</h6>
                                                        <div class="row g-2 small">
                                                            <div class="col-5 text-muted">Nominal Pembayaran:</div>
                                                            <div class="col-7 fw-bold text-primary fs-6">Rp {{ number_format($bayar->jumlah, 0, ',', '.') }}</div>
                                                            <div class="col-5 text-muted">Metode Bayar:</div>
                                                            <div class="col-7 fw-semibold">{{ str_replace('_', ' ', ucfirst($bayar->metode_pembayaran)) }}</div>
                                                            <div class="col-5 text-muted">Tanggal Bayar:</div>
                                                            <div class="col-7">{{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d F Y') }}</div>
                                                            <div class="col-5 text-muted">Status Saat Ini:</div>
                                                            <div class="col-7">
                                                                <span class="badge badge-{{ $bayar->status }} px-2 py-1 rounded-pill">
                                                                    {{ ucfirst($bayar->status) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Info Penyewa & Kamar --}}
                                                    <div class="p-3 bg-light rounded-3 mb-3">
                                                        <h6 class="fw-bold text-dark mb-2">Penyewa & Unit Kamar</h6>
                                                        <div class="row g-2 small">
                                                            <div class="col-5 text-muted">Nama Penyewa:</div>
                                                            <div class="col-7 fw-semibold text-dark">{{ $penyewa->name ?? '-' }}</div>
                                                            <div class="col-5 text-muted">Nomor HP:</div>
                                                            <div class="col-7">{{ $penyewa->nomor_hp ?? 'Belum ada nomor HP' }}</div>
                                                            <div class="col-5 text-muted">Kos:</div>
                                                            <div class="col-7">{{ $kos->nama_kos ?? '-' }}</div>
                                                            <div class="col-5 text-muted">Nomor Kamar:</div>
                                                            <div class="col-7 fw-bold text-primary">{{ $kamar->nomor_kamar ?? '-' }}</div>
                                                            <div class="col-5 text-muted">Tarif Sewa Kamar:</div>
                                                            <div class="col-7">Rp {{ number_format($kamar->harga_per_bulan ?? 0, 0, ',', '.') }} / bulan</div>
                                                        </div>
                                                    </div>

                                                    {{-- Keterangan / Catatan --}}
                                                    @if($bayar->keterangan)
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-semibold text-muted mb-1">Keterangan / Catatan Transaksi:</label>
                                                            <div class="p-2.5 bg-white border rounded-3 small text-secondary" style="white-space: pre-line;">
                                                                {{ $bayar->keterangan }}
                                                            </div>
                                                        </div>
                                                    @endif

                                                    {{-- Bukti Mini --}}
                                                    @if($buktiUrl)
                                                        <div>
                                                            <label class="form-label small fw-semibold text-muted mb-1">Bukti Transfer:</label>
                                                            <div class="text-center p-2 bg-light border rounded-3">
                                                                <img src="{{ $buktiUrl }}" alt="Bukti Transfer" class="img-fluid rounded" style="max-height: 220px; object-fit: contain;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer border-top bg-light">
                                                    <button type="button" class="btn btn-secondary btn-sm rounded-3 px-3" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Modal Tolak Pembayaran --}}
                                    @if($bayar->status === 'pending')
                                        <div class="modal fade text-start" id="modalTolak{{ $bayar->id }}" tabindex="-1" aria-labelledby="modalTolakLabel{{ $bayar->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                                <div class="modal-content border-0 shadow">
                                                    <form action="{{ route('pemilik.pembayaran.tolak', $bayar) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="modal-header border-0 pb-0">
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body pt-0 px-4 pb-3">
                                                            <div class="rounded-circle bg-danger-subtle text-danger d-inline-flex p-3 mb-2">
                                                                <i class="bi bi-x-circle-fill fs-3"></i>
                                                            </div>
                                                            <h6 class="fw-bold mb-1">Tolak Pembayaran?</h6>
                                                            <p class="text-muted small mb-3">
                                                                Pembayaran sebesar <strong>Rp {{ number_format($bayar->jumlah, 0, ',', '.') }}</strong> dari <strong>{{ $penyewa->name ?? 'Penyewa' }}</strong> akan ditolak.
                                                            </p>
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-semibold text-muted">Alasan Penolakan (Opsional)</label>
                                                                <textarea name="alasan_penolakan" class="form-control form-control-sm" rows="2" placeholder="Contoh: Bukti transfer tidak terbaca / dana belum masuk rekening."></textarea>
                                                            </div>
                                                            <div class="d-flex gap-2 justify-content-end">
                                                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-sm btn-danger">Tolak Pembayaran</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 pt-3 border-top">
                <span class="text-muted small mb-2 mb-md-0">
                    Menampilkan {{ $pembayarans->firstItem() }} sampai {{ $pembayarans->lastItem() }} dari total {{ $pembayarans->total() }} transaksi
                </span>
                <div>
                    {{ $pembayarans->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>

@endsection


@extends('layouts.app')

@section('title', 'Daftar Penyewaan - ngeKosYuk')

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

    .stat-pill-disetujui {
        background-color: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .stat-pill-ditolak {
        background-color: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .stat-pill-selesai {
        background-color: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .stat-pill.active {
        box-shadow: 0 0 0 2px var(--dark-blue);
    }

    .badge-pending {
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .badge-disetujui {
        background-color: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .badge-ditolak {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .badge-selesai {
        background-color: #e0e7ff;
        color: #3730a3;
        border: 1px solid #c7d2fe;
    }
</style>

{{-- Page Header --}}
<div class="page-header-box">
    <div class="container-fluid px-3 px-lg-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 small">
                <li class="breadcrumb-item"><a href="{{ route('pemilik.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Daftar Penyewaan</li>
            </ol>
        </nav>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h3 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-file-earmark-text-fill text-primary me-2"></i>Daftar Penyewaan Kos
                </h3>
                <p class="text-muted small mb-0">Kelola dan pantau permohonan sewa masuk dari penyewa, setujui kontrak, dan kelola masa huni.</p>
            </div>
            @if($totalPending > 0)
                <div>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-2 fs-6">
                        <i class="bi bi-bell-fill me-1"></i> {{ $totalPending }} Pengajuan Perlu Ditinjau
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

    {{-- Content Box --}}
    <div class="content-box">
        {{-- Status Filter Tabs / Pills --}}
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3 pb-3 border-bottom">
            <span class="text-muted small me-2">Status:</span>
            <a href="{{ route('pemilik.penyewaan.index', array_merge(request()->except('status', 'page'), [])) }}" 
               class="stat-pill stat-pill-all {{ !request('status') ? 'active' : '' }}">
                Semua ({{ $totalSewa }})
            </a>
            <a href="{{ route('pemilik.penyewaan.index', array_merge(request()->except('status', 'page'), ['status' => 'pending'])) }}" 
               class="stat-pill stat-pill-pending {{ request('status') === 'pending' ? 'active' : '' }}">
                <i class="bi bi-hourglass-split"></i> Menunggu ({{ $totalPending }})
            </a>
            <a href="{{ route('pemilik.penyewaan.index', array_merge(request()->except('status', 'page'), ['status' => 'disetujui'])) }}" 
               class="stat-pill stat-pill-disetujui {{ request('status') === 'disetujui' ? 'active' : '' }}">
                <i class="bi bi-check2-circle"></i> Disetujui ({{ $totalDisetujui }})
            </a>
            <a href="{{ route('pemilik.penyewaan.index', array_merge(request()->except('status', 'page'), ['status' => 'selesai'])) }}" 
               class="stat-pill stat-pill-selesai {{ request('status') === 'selesai' ? 'active' : '' }}">
                <i class="bi bi-flag-fill"></i> Selesai ({{ $totalSelesai }})
            </a>
            <a href="{{ route('pemilik.penyewaan.index', array_merge(request()->except('status', 'page'), ['status' => 'ditolak'])) }}" 
               class="stat-pill stat-pill-ditolak {{ request('status') === 'ditolak' ? 'active' : '' }}">
                <i class="bi bi-x-circle"></i> Ditolak ({{ $totalDitolak }})
            </a>
        </div>

        {{-- Filter & Search Form --}}
        <form method="GET" action="{{ route('pemilik.penyewaan.index') }}" class="filter-card">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-5">
                    <label class="form-label small fw-semibold text-muted mb-1">Cari Penyewa / Kamar</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0" placeholder="Nama penyewa, no HP, atau no kamar...">
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
                        <a href="{{ route('pemilik.penyewaan.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>

        {{-- Table --}}
        @if($sewas->isEmpty())
            <div class="text-center py-5">
                <div class="rounded-circle bg-light d-inline-flex p-4 mb-3 text-muted">
                    <i class="bi bi-inbox display-4"></i>
                </div>
                @if(request()->hasAny(['q', 'kos_id', 'status']))
                    <h5 class="fw-bold text-dark">Tidak Ada Data Penyewaan yang Cocok</h5>
                    <p class="text-muted small mb-3">Coba ubah kata kunci pencarian atau reset filter.</p>
                    <a href="{{ route('pemilik.penyewaan.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                    </a>
                @else
                    <h5 class="fw-bold text-dark">Belum Ada Pengajuan Sewa Masuk</h5>
                    <p class="text-muted small mb-0">Pengajuan sewa dari calon penyewa akan otomatis muncul di halaman ini.</p>
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
                            <th>Periode Sewa</th>
                            <th>Tarif Sewa</th>
                            <th>Status</th>
                            <th class="text-end" style="width: 220px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sewas as $index => $sewa)
                            @php
                                $tglMulai = \Carbon\Carbon::parse($sewa->tanggal_mulai);
                                $tglSelesai = \Carbon\Carbon::parse($sewa->tanggal_selesai);
                                $durasiBulan = max(1, round($tglMulai->diffInDays($tglSelesai) / 30));
                                $rawHp = $sewa->penyewa->nomor_hp ?? '';
                                $cleanHp = preg_replace('/[^0-9]/', '', $rawHp);
                                if (str_starts_with($cleanHp, '0')) {
                                    $waNumber = '62' . substr($cleanHp, 1);
                                } else {
                                    $waNumber = $cleanHp;
                                }
                            @endphp
                            <tr>
                                <td class="text-muted small">
                                    {{ $sewas->firstItem() + $index }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; font-size: 14px;">
                                            {{ strtoupper(substr($sewa->penyewa->name ?? 'P', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block">{{ $sewa->penyewa->name ?? 'Penyewa' }}</span>
                                            <span class="text-muted" style="font-size: 11px;">
                                                <i class="bi bi-envelope me-1"></i>{{ $sewa->penyewa->email ?? '-' }}
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
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="badge bg-primary px-2 py-1 rounded-2">
                                            {{ $sewa->kamar->nomor_kamar ?? 'Kamar -' }}
                                        </span>
                                    </div>
                                    <span class="fw-semibold text-dark small d-block mt-1">{{ $sewa->kos->nama_kos ?? '-' }}</span>
                                    <span class="text-muted" style="font-size: 11px;">Tipe: {{ ucfirst($sewa->kos->tipe_kos ?? '-') }}</span>
                                </td>
                                <td>
                                    <span class="fw-medium text-dark d-block small">
                                        {{ $tglMulai->format('d M Y') }} - {{ $tglSelesai->format('d M Y') }}
                                    </span>
                                    <span class="badge bg-light text-muted border" style="font-size: 11px;">
                                        <i class="bi bi-clock-history me-1"></i>&plusmn; {{ $durasiBulan }} Bulan
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary">
                                        Rp {{ number_format($sewa->kamar->harga_per_bulan ?? 0, 0, ',', '.') }}
                                    </span>
                                    <span class="text-muted d-block" style="font-size: 11px;">per bulan</span>
                                </td>
                                <td>
                                    @if($sewa->status === 'pending')
                                        <span class="badge badge-pending rounded-pill px-2.5 py-1">
                                            <i class="bi bi-hourglass-split me-1"></i> Menunggu
                                        </span>
                                    @elseif($sewa->status === 'disetujui')
                                        <span class="badge badge-disetujui rounded-pill px-2.5 py-1">
                                            <i class="bi bi-check-circle-fill me-1"></i> Disetujui
                                        </span>
                                    @elseif($sewa->status === 'ditolak')
                                        <span class="badge badge-ditolak rounded-pill px-2.5 py-1">
                                            <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                                        </span>
                                    @elseif($sewa->status === 'selesai')
                                        <span class="badge badge-selesai rounded-pill px-2.5 py-1">
                                            <i class="bi bi-flag-fill me-1"></i> Selesai
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end align-items-center gap-1">
                                        {{-- Tombol Detail --}}
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $sewa->id }}" title="Lihat Detail">
                                            <i class="bi bi-eye"></i> Detail
                                        </button>

                                        {{-- Aksi Sesuai Status --}}
                                        @if($sewa->status === 'pending')
                                            {{-- Setujui --}}
                                            <form action="{{ route('pemilik.penyewaan.setujui', $sewa) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success" title="Setujui Pengajuan" onclick="return confirm('Apakah Anda yakin ingin menyetujui pengajuan sewa ini? Kamar akan otomatis ditandai Terisi.')">
                                                    <i class="bi bi-check-lg"></i> Setujui
                                                </button>
                                            </form>

                                            {{-- Tolak Modal Trigger --}}
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalTolak{{ $sewa->id }}" title="Tolak Pengajuan">
                                                <i class="bi bi-x-lg"></i> Tolak
                                            </button>
                                        @elseif($sewa->status === 'disetujui')
                                            {{-- Tandai Selesai --}}
                                            <form action="{{ route('pemilik.penyewaan.selesai', $sewa) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-primary" title="Tandai Masa Sewa Selesai" onclick="return confirm('Tandai masa sewa ini telah selesai? Kamar akan otomatis dikembalikan berstatus Tersedia.')">
                                                    <i class="bi bi-check2-all"></i> Selesai
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    {{-- Modal Detail Penyewaan --}}
                                    <div class="modal fade text-start" id="modalDetail{{ $sewa->id }}" tabindex="-1" aria-labelledby="modalDetailLabel{{ $sewa->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header border-bottom">
                                                    <h5 class="modal-title fw-bold" id="modalDetailLabel{{ $sewa->id }}">
                                                        <i class="bi bi-file-text text-primary me-2"></i>Rincian Penyewaan #{{ $sewa->id }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    {{-- Info Penyewa --}}
                                                    <div class="p-3 bg-light rounded-3 mb-3">
                                                        <h6 class="fw-bold text-dark mb-2">Informasi Penyewa</h6>
                                                        <div class="row g-2 small">
                                                            <div class="col-4 text-muted">Nama:</div>
                                                            <div class="col-8 fw-semibold text-dark">{{ $sewa->penyewa->name ?? '-' }}</div>
                                                            <div class="col-4 text-muted">Email:</div>
                                                            <div class="col-8">{{ $sewa->penyewa->email ?? '-' }}</div>
                                                            <div class="col-4 text-muted">Nomor HP:</div>
                                                            <div class="col-8">{{ $sewa->penyewa->nomor_hp ?? 'Belum ada nomor HP' }}</div>
                                                        </div>
                                                    </div>

                                                    {{-- Info Kamar & Kos --}}
                                                    <div class="p-3 bg-light rounded-3 mb-3">
                                                        <h6 class="fw-bold text-dark mb-2">Unit Kamar & Kos</h6>
                                                        <div class="row g-2 small">
                                                            <div class="col-4 text-muted">Kos:</div>
                                                            <div class="col-8 fw-semibold text-dark">{{ $sewa->kos->nama_kos ?? '-' }} ({{ ucfirst($sewa->kos->tipe_kos ?? '-') }})</div>
                                                            <div class="col-4 text-muted">Nomor Kamar:</div>
                                                            <div class="col-8 fw-bold text-primary">{{ $sewa->kamar->nomor_kamar ?? '-' }}</div>
                                                            <div class="col-4 text-muted">Tarif:</div>
                                                            <div class="col-8">Rp {{ number_format($sewa->kamar->harga_per_bulan ?? 0, 0, ',', '.') }} / bulan</div>
                                                            <div class="col-4 text-muted">Fasilitas:</div>
                                                            <div class="col-8">{{ $sewa->kamar->fasilitas ?? 'Tidak ada fasilitas khusus' }}</div>
                                                        </div>
                                                    </div>

                                                    {{-- Info Masa Sewa --}}
                                                    <div class="p-3 bg-light rounded-3 mb-3">
                                                        <h6 class="fw-bold text-dark mb-2">Jadwal Sewa</h6>
                                                        <div class="row g-2 small">
                                                            <div class="col-4 text-muted">Mulai Sewa:</div>
                                                            <div class="col-8 fw-semibold">{{ $tglMulai->format('d F Y') }}</div>
                                                            <div class="col-4 text-muted">Selesai Sewa:</div>
                                                            <div class="col-8 fw-semibold">{{ $tglSelesai->format('d F Y') }}</div>
                                                            <div class="col-4 text-muted">Status:</div>
                                                            <div class="col-8">
                                                                <span class="badge badge-{{ $sewa->status }} px-2 py-1 rounded-pill">
                                                                    {{ ucfirst($sewa->status) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Catatan Penyewa --}}
                                                    @if($sewa->catatan)
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-semibold text-muted mb-1">Catatan dari Penyewa / Riwayat:</label>
                                                            <div class="p-2.5 bg-white border rounded-3 small text-secondary" style="white-space: pre-line;">
                                                                {{ $sewa->catatan }}
                                                            </div>
                                                        </div>
                                                    @endif

                                                    {{-- Status Pembayaran --}}
                                                    <div>
                                                        <label class="form-label small fw-semibold text-muted mb-1">Status Pembayaran Terkait:</label>
                                                        @if($sewa->pembayarans->isEmpty())
                                                            <div class="alert alert-light border small text-muted mb-0 py-2">
                                                                Belum ada catatan pembayaran dari penyewa.
                                                            </div>
                                                        @else
                                                            <div class="list-group list-group-flush border rounded-3">
                                                                @foreach($sewa->pembayarans as $bayar)
                                                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3 small">
                                                                        <div>
                                                                            <span class="fw-semibold text-dark">Rp {{ number_format($bayar->jumlah, 0, ',', '.') }}</span>
                                                                            <span class="text-muted d-block" style="font-size: 11px;">
                                                                                {{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d M Y') }} &bull; {{ str_replace('_', ' ', ucfirst($bayar->metode_pembayaran)) }}
                                                                            </span>
                                                                        </div>
                                                                        <span class="badge {{ $bayar->status === 'diverifikasi' ? 'bg-success' : ($bayar->status === 'ditolak' ? 'bg-danger' : 'bg-warning text-dark') }} rounded-pill">
                                                                            {{ ucfirst($bayar->status) }}
                                                                        </span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top bg-light">
                                                    <button type="button" class="btn btn-secondary btn-sm rounded-3 px-3" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Modal Tolak Pengajuan --}}
                                    @if($sewa->status === 'pending')
                                        <div class="modal fade text-start" id="modalTolak{{ $sewa->id }}" tabindex="-1" aria-labelledby="modalTolakLabel{{ $sewa->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                                <div class="modal-content border-0 shadow">
                                                    <form action="{{ route('pemilik.penyewaan.tolak', $sewa) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="modal-header border-0 pb-0">
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body pt-0 px-4 pb-3">
                                                            <div class="rounded-circle bg-danger-subtle text-danger d-inline-flex p-3 mb-2">
                                                                <i class="bi bi-x-circle-fill fs-3"></i>
                                                            </div>
                                                            <h6 class="fw-bold mb-1">Tolak Pengajuan Sewa?</h6>
                                                            <p class="text-muted small mb-3">
                                                                Pengajuan dari <strong>{{ $sewa->penyewa->name ?? 'Penyewa' }}</strong> untuk Kamar <strong>{{ $sewa->kamar->nomor_kamar ?? '-' }}</strong> akan ditolak.
                                                            </p>
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-semibold text-muted">Alasan Penolakan (Opsional)</label>
                                                                <textarea name="alasan_penolakan" class="form-control form-control-sm" rows="2" placeholder="Contoh: Kamar sedang perbaikan, dll."></textarea>
                                                            </div>
                                                            <div class="d-flex gap-2 justify-content-end">
                                                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-sm btn-danger">Tolak Pengajuan</button>
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
                    Menampilkan {{ $sewas->firstItem() }} sampai {{ $sewas->lastItem() }} dari total {{ $sewas->total() }} pengajuan
                </span>
                <div>
                    {{ $sewas->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>

@endsection


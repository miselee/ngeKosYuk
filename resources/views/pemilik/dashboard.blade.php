@extends('layouts.app')

@section('title', 'Dashboard Pemilik - ngeKosYuk')

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

    .dashboard-header {
        background: linear-gradient(135deg, #2468B8 0%, #1b4f8c 100%);
        color: white;
        padding: 36px 0 60px;
        position: relative;
    }

    .main-container {
        margin-top: -38px;
        padding-bottom: 60px;
    }

    .stat-card {
        background: white;
        border-radius: 14px;
        padding: 22px;
        border: 1px solid #e7ecf3;
        box-shadow: 0 4px 12px rgba(27, 79, 140, 0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(27, 79, 140, 0.08);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .icon-blue { background: #eaf4ff; color: #2468B8; }
    .icon-green { background: #e8f8f0; color: #198754; }
    .icon-amber { background: #fff8e6; color: #d97706; }
    .icon-purple { background: #f3e8ff; color: #7c3aed; }

    .content-card {
        background: white;
        border-radius: 14px;
        border: 1px solid #e7ecf3;
        box-shadow: 0 4px 12px rgba(27, 79, 140, 0.04);
        padding: 24px;
    }

    .btn-brand-primary {
        background: #2468B8;
        color: white;
        font-weight: 600;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .btn-brand-primary:hover {
        background: #1b4f8c;
        color: white;
    }

    .btn-brand-outline {
        background: white;
        color: #2468B8;
        border: 1.5px solid #2468B8;
        font-weight: 600;
        padding: 9px 18px;
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .btn-brand-outline:hover {
        background: #eaf4ff;
        color: #1b4f8c;
    }

    .badge-tersedia {
        background-color: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
        font-weight: 600;
    }

    .badge-terisi {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
        font-weight: 600;
    }

    .kos-mini-card {
        border: 1px solid #e7ecf3;
        border-radius: 12px;
        padding: 16px;
        background: #fbfcfe;
        transition: all 0.2s ease;
    }

    .kos-mini-card:hover {
        border-color: #2468B8;
        background: white;
    }
</style>

{{-- Header Banner --}}
<div class="dashboard-header">
    <div class="container-fluid px-3 px-lg-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-white text-primary fw-semibold px-2.5 py-1 mb-2">Panel Pemilik</span>
                <h2 class="fw-bold mb-1">Halo, {{ Auth::user()->name }}! 👋</h2>
                <p class="text-white-50 mb-0">Kelola kamar kos Anda, pantau tingkat hunian, dan atur ketersediaan properti.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('pemilik.penyewaan.index') }}" class="btn btn-light fw-bold text-dark px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
                    <i class="bi bi-file-earmark-text-fill text-primary"></i> Daftar Penyewaan
                    @if(isset($sewaPendingCount) && $sewaPendingCount > 0)
                        <span class="badge bg-danger rounded-pill ms-1">{{ $sewaPendingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('pemilik.pembayaran.index') }}" class="btn btn-outline-light px-3 py-2 rounded-3 d-flex align-items-center gap-1.5">
                    <i class="bi bi-credit-card-2-front"></i> Pembayaran
                    @if(isset($pembayaranPendingCount) && $pembayaranPendingCount > 0)
                        <span class="badge bg-warning text-dark rounded-pill ms-1">{{ $pembayaranPendingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('pemilik.kelola-kamar.create') }}" class="btn btn-outline-light px-3 py-2 rounded-3 d-flex align-items-center gap-1.5">
                    <i class="bi bi-plus-circle-fill"></i> Tambah Kamar
                </a>
                <button type="button" class="btn btn-outline-light px-3 py-2 rounded-3 d-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalTambahKos">
                    <i class="bi bi-building-add"></i> Tambah Kos
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Main Container --}}
<div class="container-fluid px-3 px-lg-4 main-container">
    {{-- Alert Flash Message --}}
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

    {{-- Pending Sewa Alert Banner --}}
    @if(isset($sewaPendingCount) && $sewaPendingCount > 0)
        <div class="alert alert-warning border-0 shadow-sm rounded-3 mb-4 p-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
            <div class="d-flex align-items-center gap-2.5">
                <div class="rounded-circle bg-warning-subtle text-warning-emphasis p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-bell-fill fs-5"></i>
                </div>
                <div>
                    <strong class="text-dark d-block">Ada {{ $sewaPendingCount }} Pengajuan Sewa Baru Menunggu Persetujuan!</strong>
                    <span class="text-muted small">Calon penyewa sedang menantikan respon persetujuan kamar dari Anda.</span>
                </div>
            </div>
            <a href="{{ route('pemilik.penyewaan.index', ['status' => 'pending']) }}" class="btn btn-warning btn-sm fw-bold px-3 py-1.5 rounded-pill text-nowrap">
                Tinjau Pengajuan &rarr;
            </a>
        </div>
    @endif

    {{-- Pending Pembayaran Alert Banner --}}
    @if(isset($pembayaranPendingCount) && $pembayaranPendingCount > 0)
        <div class="alert alert-info border-0 shadow-sm rounded-3 mb-4 p-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
            <div class="d-flex align-items-center gap-2.5">
                <div class="rounded-circle bg-info-subtle text-info-emphasis p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-credit-card-2-front-fill fs-5"></i>
                </div>
                <div>
                    <strong class="text-dark d-block">Ada {{ $pembayaranPendingCount }} Bukti Pembayaran Baru Menunggu Verifikasi!</strong>
                    <span class="text-muted small">Penyewa telah mengirim bukti transfer pembayaran kos yang perlu Anda cek.</span>
                </div>
            </div>
            <a href="{{ route('pemilik.pembayaran.index', ['status' => 'pending']) }}" class="btn btn-info text-white btn-sm fw-bold px-3 py-1.5 rounded-pill text-nowrap">
                Verifikasi Pembayaran &rarr;
            </a>
        </div>
    @endif

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card d-flex align-items-center gap-3">
                <div class="stat-icon icon-blue">
                    <i class="bi bi-buildings"></i>
                </div>
                <div>
                    <span class="text-muted small fw-medium d-block">Total Kos</span>
                    <h3 class="fw-bold mb-0 text-dark">{{ $totalKos }}</h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card d-flex align-items-center gap-3">
                <div class="stat-icon icon-purple">
                    <i class="bi bi-door-open"></i>
                </div>
                <div>
                    <span class="text-muted small fw-medium d-block">Total Kamar</span>
                    <h3 class="fw-bold mb-0 text-dark">{{ $totalKamar }}</h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card d-flex align-items-center gap-3">
                <div class="stat-icon icon-green">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <div>
                    <span class="text-muted small fw-medium d-block">Kamar Tersedia</span>
                    <h3 class="fw-bold mb-0 text-success">{{ $kamarTersedia }}</h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card d-flex align-items-center gap-3">
                <div class="stat-icon icon-amber">
                    <i class="bi bi-person-fill-check"></i>
                </div>
                <div>
                    <span class="text-muted small fw-medium d-block">Kamar Terisi</span>
                    <h3 class="fw-bold mb-0 text-warning">{{ $kamarTerisi }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Section --}}
    <div class="row g-4">
        {{-- Left: Kamar Kos Terbaru --}}
        <div class="col-lg-8">
            <div class="content-card">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div>
                        <h5 class="fw-bold mb-1">Daftar Kamar Terbaru</h5>
                        <p class="text-muted small mb-0">Ringkasan status ketersediaan kamar yang Anda kelola.</p>
                    </div>
                    <a href="{{ route('pemilik.kelola-kamar.index') }}" class="btn btn-sm btn-brand-outline d-flex align-items-center gap-1">
                        <span>Kelola Semua Kamar</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                @if($kamarTerbaru->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-door-closed display-4 text-muted opacity-50 mb-3 d-block"></i>
                        <h6 class="fw-bold text-dark">Belum Ada Kamar Terdaftar</h6>
                        <p class="text-muted small mb-3">Mulai tambahkan kamar pertama untuk disewakan kepada pencari kos.</p>
                        @if($daftarKos->isNotEmpty())
                            <a href="{{ route('pemilik.kelola-kamar.create') }}" class="btn btn-brand-primary btn-sm">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Kamar Sekarang
                            </a>
                        @else
                            <button type="button" class="btn btn-brand-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKos">
                                <i class="bi bi-building-add me-1"></i> Buat Kos Terlebih Dahulu
                            </button>
                        @endif
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-muted small">
                                    <th>Nomor Kamar</th>
                                    <th>Kos</th>
                                    <th>Harga / Bulan</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kamarTerbaru as $kamar)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle bg-light text-primary p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                                    <i class="bi bi-door-closed-fill"></i>
                                                </div>
                                                <div>
                                                    <span class="fw-bold text-dark d-block">{{ $kamar->nomor_kamar }}</span>
                                                    <span class="text-muted" style="font-size: 11px;">
                                                        {{ Str::limit($kamar->fasilitas ?? 'Tanpa info fasilitas', 28) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-medium text-secondary">{{ $kamar->kos->nama_kos ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-primary">Rp {{ number_format($kamar->harga_per_bulan, 0, ',', '.') }}</span>
                                        </td>
                                        <td>
                                            @if($kamar->status === 'tersedia')
                                                <span class="badge badge-tersedia rounded-pill px-2.5 py-1">
                                                    <i class="bi bi-check-circle-fill me-1"></i> Tersedia
                                                </span>
                                            @else
                                                <span class="badge badge-terisi rounded-pill px-2.5 py-1">
                                                    <i class="bi bi-dash-circle-fill me-1"></i> Terisi
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('pemilik.kelola-kamar.edit', $kamar) }}" class="btn btn-outline-secondary" title="Edit Kamar">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <form action="{{ route('pemilik.kelola-kamar.toggle-status', $kamar) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-outline-primary" title="Ubah Status (Tersedia/Terisi)">
                                                        <i class="bi bi-arrow-repeat"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right: Properti Kos yang Dimiliki --}}
        <div class="col-lg-4">
            <div class="content-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div>
                        <h5 class="fw-bold mb-1">Properti Kos Anda</h5>
                        <p class="text-muted small mb-0">{{ $daftarKos->count() }} lokasi terdaftar</p>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5" data-bs-toggle="modal" data-bs-target="#modalTambahKos" title="Tambah Kos Baru">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>

                @if($daftarKos->isEmpty())
                    <div class="text-center py-4">
                        <i class="bi bi-building-slash display-5 text-muted opacity-50 mb-2 d-block"></i>
                        <p class="text-muted small">Anda belum mendaftarkan bangunan kos.</p>
                        <button type="button" class="btn btn-sm btn-brand-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKos">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Kos Baru
                        </button>
                    </div>
                @else
                    <div class="d-flex flex-column gap-3">
                        @foreach($daftarKos as $kos)
                            <div class="kos-mini-card">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold text-dark mb-0">{{ $kos->nama_kos }}</h6>
                                    <span class="badge bg-secondary-subtle text-secondary text-capitalize px-2 py-0.5" style="font-size: 11px;">
                                        {{ $kos->tipe_kos }}
                                    </span>
                                </div>
                                <p class="text-muted small mb-2 text-truncate" style="font-size: 12px;">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $kos->alamat }}
                                </p>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <span class="small text-muted">
                                        <i class="bi bi-door-open me-1"></i><strong>{{ $kos->kamars->count() }}</strong> Kamar
                                    </span>
                                    <a href="{{ route('pemilik.kelola-kamar.index', ['kos_id' => $kos->id]) }}" class="small fw-semibold text-primary text-decoration-none">
                                        Kelola Kamar &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Kos Cepat --}}
<div class="modal fade" id="modalTambahKos" tabindex="-1" aria-labelledby="modalTambahKosLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('pemilik.kos.quick-store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold" id="modalTambahKosLabel">
                        <i class="bi bi-building-add text-primary me-2"></i>Daftarkan Kos Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Nama Kos <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kos" class="form-control rounded-3" placeholder="Contoh: Kos Melati Asri" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Tipe Kos <span class="text-danger">*</span></label>
                        <select name="tipe_kos" class="form-select rounded-3" required>
                            <option value="campur">Campur (Putra & Putri)</option>
                            <option value="putra">Khusus Putra</option>
                            <option value="putri">Khusus Putri</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea name="alamat" rows="2" class="form-control rounded-3" placeholder="Jl. Raya No. ..., Kota" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Deskripsi Singkat (Opsional)</label>
                        <textarea name="deskripsi" rows="2" class="form-control rounded-3" placeholder="Kos dekat kampus, lingkungan nyaman dan aman..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-brand-primary rounded-3 px-4">Simpan Kos</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

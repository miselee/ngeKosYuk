@extends('layouts.app')

@section('title', 'Kelola Kamar Kos - ngeKosYuk')

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

    .filter-card {
        background: #fbfcfe;
        border: 1px solid #e7ecf3;
        border-radius: 12px;
        padding: 18px;
        margin-bottom: 20px;
    }

    .badge-tersedia {
        background-color: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
        font-weight: 600;
        font-size: 12px;
    }

    .badge-terisi {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
        font-weight: 600;
        font-size: 12px;
    }

    .facility-badge {
        background-color: #f1f5f9;
        color: #475569;
        font-size: 11px;
        font-weight: 500;
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-block;
        margin-right: 4px;
        margin-bottom: 3px;
    }

    .stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 600;
    }

    .stat-pill-all {
        background-color: #eaf4ff;
        color: #2468B8;
        border: 1px solid #cfe2ff;
    }

    .stat-pill-tersedia {
        background-color: #e8f8f0;
        color: #198754;
        border: 1px solid #c3e6cb;
    }

    .stat-pill-terisi {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }
</style>

{{-- Header --}}
<div class="page-header-box">
    <div class="container-fluid px-3 px-lg-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 small">
                <li class="breadcrumb-item"><a href="{{ route('pemilik.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Kelola Kamar</li>
            </ol>
        </nav>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h3 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-door-open-fill text-primary me-2"></i>Kelola Kamar Kos
                </h3>
                <p class="text-muted small mb-0">Pantau ketersediaan, perbarui informasi fasilitas, tarif, dan tambah unit kamar baru.</p>
            </div>
            <div class="d-flex gap-2">
                @if($daftarKos->isNotEmpty())
                    <a href="{{ route('pemilik.kelola-kamar.create') }}" class="btn btn-brand-primary d-flex align-items-center gap-2 shadow-sm">
                        <i class="bi bi-plus-circle-fill"></i>
                        <span>Tambah Kamar Baru</span>
                    </a>
                @else
                    <button type="button" class="btn btn-brand-primary d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKos">
                        <i class="bi bi-building-add"></i>
                        <span>Buat Kos Terlebih Dahulu</span>
                    </button>
                @endif
            </div>
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
        {{-- Summary Pills --}}
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3 pb-3 border-bottom">
            <span class="text-muted small me-2">Ringkasan:</span>
            <span class="stat-pill stat-pill-all">
                <i class="bi bi-door-closed"></i> Total: {{ $totalKamar }} Kamar
            </span>
            <span class="stat-pill stat-pill-tersedia">
                <i class="bi bi-check2-circle"></i> Tersedia: {{ $totalTersedia }}
            </span>
            <span class="stat-pill stat-pill-terisi">
                <i class="bi bi-person-fill-check"></i> Terisi: {{ $totalTerisi }}
            </span>
        </div>

        {{-- Filter & Search Form --}}
        <form method="GET" action="{{ route('pemilik.kelola-kamar.index') }}" class="filter-card">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1">Cari Kamar</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0" placeholder="Nomor kamar atau fasilitas...">
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Pilih Kos</label>
                    <select name="kos_id" class="form-select">
                        <option value="">Semua Lokasi Kos</option>
                        @foreach($daftarKos as $kos)
                            <option value="{{ $kos->id }}" {{ request('kos_id') == $kos->id ? 'selected' : '' }}>
                                {{ $kos->nama_kos }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Status Ketersediaan</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="tersedia" {{ request('status') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="terisi" {{ request('status') === 'terisi' ? 'selected' : '' }}>Terisi</option>
                    </select>
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-brand-primary w-100">
                        <i class="bi bi-funnel-fill me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['q', 'kos_id', 'status']))
                        <a href="{{ route('pemilik.kelola-kamar.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>

        {{-- Kamar Table --}}
        @if($kamars->isEmpty())
            <div class="text-center py-5">
                <div class="rounded-circle bg-light d-inline-flex p-4 mb-3 text-muted">
                    <i class="bi bi-door-closed display-4"></i>
                </div>
                @if(request()->hasAny(['q', 'kos_id', 'status']))
                    <h5 class="fw-bold text-dark">Tidak Ada Kamar yang Cocok</h5>
                    <p class="text-muted small mb-3">Coba ubah kata kunci pencarian atau bersihkan filter yang aktif.</p>
                    <a href="{{ route('pemilik.kelola-kamar.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                    </a>
                @elseif($daftarKos->isEmpty())
                    <h5 class="fw-bold text-dark">Anda Belum Memiliki Kos</h5>
                    <p class="text-muted small mb-3">Sebelum mengelola kamar, Anda harus mendaftarkan bangunan kos terlebih dahulu.</p>
                    <button type="button" class="btn btn-brand-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKos">
                        <i class="bi bi-building-add me-1"></i> Daftarkan Kos Baru
                    </button>
                @else
                    <h5 class="fw-bold text-dark">Belum Ada Kamar Terdaftar</h5>
                    <p class="text-muted small mb-3">Mulai tambahkan kamar pertama untuk mulai menyewakan properti Anda.</p>
                    <a href="{{ route('pemilik.kelola-kamar.create') }}" class="btn btn-brand-primary btn-sm">
                        <i class="bi bi-plus-circle-fill me-1"></i> Tambah Kamar Sekarang
                    </a>
                @endif
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr class="text-muted small">
                            <th style="width: 50px;">#</th>
                            <th>Nomor Kamar</th>
                            <th>Nama Kos</th>
                            <th>Tarif / Bulan</th>
                            <th>Fasilitas</th>
                            <th>Status</th>
                            <th class="text-end" style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kamars as $index => $kamar)
                            <tr>
                                <td class="text-muted small">
                                    {{ $kamars->firstItem() + $index }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="rounded-3 bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                            <i class="bi bi-door-closed-fill fs-5"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block">{{ $kamar->nomor_kamar }}</span>
                                            <span class="text-muted" style="font-size: 11px;">ID: #{{ $kamar->id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-secondary d-block">{{ $kamar->kos->nama_kos ?? '-' }}</span>
                                    <span class="text-muted" style="font-size: 11px;">
                                        <i class="bi bi-tag-fill me-1"></i>Tipe: {{ ucfirst($kamar->kos->tipe_kos ?? '-') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary">Rp {{ number_format($kamar->harga_per_bulan, 0, ',', '.') }}</span>
                                    <span class="text-muted small d-block">per bulan</span>
                                </td>
                                <td style="max-width: 280px;">
                                    @if($kamar->fasilitas)
                                        @php
                                            $fasilitasArray = array_map('trim', explode(',', $kamar->fasilitas));
                                        @endphp
                                        <div>
                                            @foreach(array_slice($fasilitasArray, 0, 4) as $item)
                                                <span class="facility-badge">{{ $item }}</span>
                                            @endforeach
                                            @if(count($fasilitasArray) > 4)
                                                <span class="facility-badge text-primary" title="{{ $kamar->fasilitas }}">
                                                    +{{ count($fasilitasArray) - 4 }} lainnya
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small fst-italic">Belum diisi</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @if($kamar->status === 'tersedia')
                                            <span class="badge badge-tersedia rounded-pill px-2.5 py-1">
                                                <i class="bi bi-check-circle-fill me-1"></i> Tersedia
                                            </span>
                                        @else
                                            <span class="badge badge-terisi rounded-pill px-2.5 py-1">
                                                <i class="bi bi-dash-circle-fill me-1"></i> Terisi
                                            </span>
                                        @endif

                                        {{-- Toggle Status Button --}}
                                        <form action="{{ route('pemilik.kelola-kamar.toggle-status', $kamar) }}" method="POST" class="m-0">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-link p-0 text-decoration-none text-muted" style="font-size: 11px;" title="Klik untuk mengubah status">
                                                <i class="bi bi-arrow-repeat text-primary me-0.5"></i>
                                                Tandai {{ $kamar->status === 'tersedia' ? 'Terisi' : 'Tersedia' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('pemilik.kelola-kamar.edit', $kamar) }}" class="btn btn-outline-primary" title="Edit Data Kamar">
                                            <i class="bi bi-pencil-square me-1"></i> Edit
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" title="Hapus Kamar" data-bs-toggle="modal" data-bs-target="#modalHapus{{ $kamar->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    {{-- Modal Konfirmasi Hapus --}}
                                    <div class="modal fade text-start" id="modalHapus{{ $kamar->id }}" tabindex="-1" aria-labelledby="modalHapusLabel{{ $kamar->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-sm">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header border-0 pb-0">
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center pt-0 px-4 pb-4">
                                                    <div class="rounded-circle bg-danger-subtle text-danger d-inline-flex p-3 mb-3">
                                                        <i class="bi bi-exclamation-triangle-fill fs-3"></i>
                                                    </div>
                                                    <h6 class="fw-bold mb-2">Hapus Kamar {{ $kamar->nomor_kamar }}?</h6>
                                                    <p class="text-muted small mb-3">
                                                        Kamar dari kos <strong>{{ $kamar->kos->nama_kos ?? '-' }}</strong> akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.
                                                    </p>
                                                    <div class="d-flex gap-2 justify-content-center">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal">Batal</button>
                                                        <form action="{{ route('pemilik.kelola-kamar.destroy', $kamar) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger px-3">Hapus</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 pt-3 border-top">
                <span class="text-muted small mb-2 mb-md-0">
                    Menampilkan {{ $kamars->firstItem() }} sampai {{ $kamars->lastItem() }} dari total {{ $kamars->total() }} kamar
                </span>
                <div>
                    {{ $kamars->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Modal Tambah Kos Cepat (Jika Belum Punya Kos) --}}
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


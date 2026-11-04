
@extends('layouts.app')

@section('title', 'Cari Kos - ngeKosYuk')

@section('content')

@include('penyewa.partials.navbar')

<style>
    .search-page {
        padding: 30px 24px;
        color: #263747;
    }

    .search-navbar {
        padding: 20px 5%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .search-navbar a {
        color: #2166c5;
        text-decoration: none;
        font-size: 18px;
        margin-left: 25px;
    }

    .search-navbar .brand {
        font-size: 21px;
        font-weight: 600;
        margin-left: 0;
    }

    .filter-box {
        border: 1px solid #b8d1f5;
        border-radius: 9px;
        padding: 25px;
        height: 100%;
    }

    .filter-title {
        font-weight: 600;
        margin-bottom: 8px;
    }

    .filter-input {
        border: 1px solid #4d8be0;
        border-radius: 8px;
        padding: 8px 12px;
        width: 100%;
        outline: none;
    }

    .filter-section {
        margin-bottom: 22px;
    }

    .filter-option {
        border: 1px solid #4d8be0;
        border-radius: 8px;
        padding: 8px 12px;
    }

    .filter-option label {
        display: block;
        margin-bottom: 5px;
    }

    .filter-option input {
        accent-color: #2166c5;
    }

    .btn-filter {
        background: #2868bd;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 8px 35px;
    }

    .btn-filter:hover {
        background: #174f9b;
        color: white;
    }

    .result-title {
        color: #2868bd;
        font-size: 28px;
        font-weight: 700;
    }

    .kos-card {
        border: 1px solid #3979d0;
        border-radius: 9px;
        overflow: hidden;
        height: 100%;
        background: white;
    }

    .kos-image {
        width: 100%;
        height: 215px;
        object-fit: cover;
    }

    .kos-image-wrapper {
        position: relative;
    }

    .kos-content {
        padding: 12px 14px;
    }

    .kos-name {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
        color: #263747;
    }

    .kos-type {
        color: white;
        font-size: 12px;
        padding: 3px 15px;
        border-radius: 15px;
    }

    .putri {
        background: #2868bd;
    }

    .putra {
        background: #ed861e;
    }

    .campur {
        background: #1dbb42;
    }

    .kos-address {
        font-size: 12px;
        color: #706565;
        margin-top: 10px;
    }

    .empty-state {
        border: 1px dashed #b8d1f5;
        border-radius: 12px;
        padding: 65px 20px;
        background: #fafdff;
    }

    .empty-state i {
        font-size: 55px;
        color: #8aaedb;
    }

    .empty-state h4 {
        color: #2868bd;
        font-weight: 700;
        margin-top: 15px;
    }

    .empty-state p {
        color: #7b8794;
        margin-bottom: 0;
    }

    @media (max-width: 768px) {
        .search-navbar {
            justify-content: center;
        }

        .search-navbar a {
            margin: 5px 8px;
            font-size: 14px;
        }

        .search-page {
            padding: 20px 14px;
        }
    }
</style>

<div class="search-page">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="filter-box">
                <form action="{{ url('/penyewa/cari-kos') }}" method="GET">
                    <div class="filter-section">
                        <div class="filter-title">
                            Lokasi / Nama Kos:
                        </div>
                        <input type="text"
                               name="lokasi"
                               class="filter-input"
                               value="{{ request('lokasi') }}"
                               placeholder="Cari lokasi atau nama kos...">
                    </div>

                    <div class="filter-section">
                        <div class="filter-title"> Rentang Harga (Rp/bulan) </div>
                        <div class="d-flex align-items-center gap-2">
                            <input type="number"
                                   name="harga_min"
                                   class="filter-input"
                                   value="{{ request('harga_min') }}"
                                   placeholder="Min">
                            <span>-</span>
                            <input type="number"
                                   name="harga_max"
                                   class="filter-input"
                                   value="{{ request('harga_max') }}"
                                   placeholder="Max">
                        </div>
                    </div>

                    <div class="filter-section">
                        <div class="filter-title">
                            Tipe Kos (Penghuni):
                        </div>
                        <div class="filter-option">
                            <label>
                                <input type="radio"
                                       name="tipe_kos"
                                       value=""
                                       {{ !request('tipe_kos') ? 'checked' : '' }}>
                                Semua Tipe
                            </label>
                            <label>
                                <input type="radio"
                                       name="tipe_kos"
                                       value="putri"
                                       {{ request('tipe_kos') === 'putri' ? 'checked' : '' }}>
                                Khusus Putri
                            </label>
                            <label>
                                <input type="radio"
                                       name="tipe_kos"
                                       value="putra"
                                       {{ request('tipe_kos') === 'putra' ? 'checked' : '' }}>
                                Khusus Putra
                            </label>
                            <label class="mb-0">
                                <input type="radio"
                                       name="tipe_kos"
                                       value="campur"
                                       {{ request('tipe_kos') === 'campur' ? 'checked' : '' }}>
                                Campur
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-filter flex-grow-1">
                            <i class="bi bi-funnel-fill"></i> Terapkan Filter
                        </button>
                        @if(request()->hasAny(['lokasi', 'harga_min', 'harga_max', 'tipe_kos', 'sort']))
                            <a href="{{ url('/penyewa/cari-kos') }}" class="btn btn-outline-secondary">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <h2 class="result-title mb-0">
                    Hasil Pencarian
                    <small class="text-muted fs-6 fw-normal">({{ ($daftarKos ?? $kos)->total() ?? 0 }} kos)</small>
                </h2>
                <form action="{{ url('/penyewa/cari-kos') }}" method="GET" class="d-inline-block">
                    @if(request('lokasi')) <input type="hidden" name="lokasi" value="{{ request('lokasi') }}"> @endif
                    @if(request('harga_min')) <input type="hidden" name="harga_min" value="{{ request('harga_min') }}"> @endif
                    @if(request('harga_max')) <input type="hidden" name="harga_max" value="{{ request('harga_max') }}"> @endif
                    @if(request('tipe_kos')) <input type="hidden" name="tipe_kos" value="{{ request('tipe_kos') }}"> @endif
                    <select name="sort" class="form-select" style="width: 220px; border-color:#4d8be0;" onchange="this.form.submit()">
                        <option value="" {{ !request('sort') ? 'selected' : '' }}>Terbaru</option>
                        <option value="termurah" {{ request('sort') === 'termurah' ? 'selected' : '' }}>Harga terendah</option>
                        <option value="termahal" {{ request('sort') === 'termahal' ? 'selected' : '' }}>Harga tertinggi</option>
                    </select>
                </form>
            </div>

            <div class="row g-4">
                @forelse(($kos ?? $daftarKos ?? collect()) as $item)
                    <div class="col-md-6">
                        <div class="kos-card">
                            <div class="kos-image-wrapper">
                                @if($item->foto)
                                    <img src="{{ str_starts_with($item->foto, 'http') ? $item->foto : asset('storage/' . $item->foto) }}"
                                         class="kos-image"
                                         alt="{{ $item->nama_kos }}">
                                @else
                                    <div class="kos-image d-flex align-items-center justify-content-center bg-light">
                                        <i class="bi bi-building fs-1 text-secondary opacity-50"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="kos-content">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="kos-name text-truncate" title="{{ $item->nama_kos }}">
                                        {{ $item->nama_kos }}
                                    </h5>
                                    <span class="kos-type {{ strtolower($item->tipe_kos) === 'putri' ? 'putri' : (strtolower($item->tipe_kos) === 'putra' ? 'putra' : 'campur') }}">
                                        {{ ucfirst($item->tipe_kos) }}
                                    </span>
                                </div>

                                <p class="kos-address text-truncate mb-2">
                                    <i class="bi bi-geo-alt-fill text-danger"></i>
                                    {{ $item->alamat }}
                                </p>

                                @php
                                    $minHarga = $item->kamars->min('harga_per_bulan');
                                    $sisaKamar = $item->kamars->where('status', 'tersedia')->count();
                                @endphp

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        @if($minHarga)
                                            <div class="fw-bold" style="color:var(--dark-blue);font-size:15px;">
                                                Rp {{ number_format($minHarga, 0, ',', '.') }}
                                                <small class="text-muted fw-normal" style="font-size:12px;">/bln</small>
                                            </div>
                                        @endif
                                        <small class="text-muted" style="font-size:12px;">
                                            <i class="bi bi-door-open"></i> {{ $sisaKamar }} kamar tersedia
                                        </small>
                                    </div>

                                    @if($item->ulasans && $item->ulasans->count() > 0)
                                        <div class="small fw-bold text-warning">
                                            <i class="bi bi-star-fill"></i> {{ round($item->ulasans->avg('rating'), 1) }}
                                            <span class="text-muted fw-normal">({{ $item->ulasans->count() }})</span>
                                        </div>
                                    @endif
                                </div>

                                <a href="{{ url('/penyewa/detail-kos/' . $item->id) }}"
                                   class="btn btn-primary w-100" style="background:var(--dark-blue); border:none;">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="empty-state text-center">
                            <i class="bi bi-house-x"></i>
                            <h4>Tidak Ada Kos yang Ditemukan</h4>
                            <p>Coba ubah kata kunci lokasi atau filter rentang harga Anda.</p>
                            <a href="{{ url('/penyewa/cari-kos') }}" class="btn btn-outline-primary btn-sm mt-2">
                                Lihat Semua Kos
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            @if(isset($daftarKos) && method_exists($daftarKos, 'links'))
                <div class="mt-4">
                    {{ $daftarKos->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

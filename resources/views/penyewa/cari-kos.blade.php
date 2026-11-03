
@extends('layouts.app')

@section('title', 'Cari Kos - ngeKosYuk')

@section('content')

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

<nav class="search-navbar">

    <a href="{{ url('/penyewa/dashboard') }}" class="brand">
        NgeKosYuk
    </a>

    <div>
        <a href="{{ url('/penyewa/dashboard') }}">Beranda</a>
        <a href="{{ url('/penyewa/cari-kos') }}">Cari Kos</a>
        <a href="#">Riwayat Sewa</a>
        <a href="#">Riwayat Pembayaran</a>

        <form action="{{ url('/logout') }}"
              method="POST"
              style="display:inline;">
            @csrf

            <button type="submit"
                    class="btn btn-link text-primary text-decoration-none"
                    style="font-size:18px;">
                Log Out
            </button>
        </form>
    </div>

</nav>

<div class="search-page">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="filter-box">
                <form action="#" method="GET">
                    <div class="filter-section">
                        <div class="filter-title">
                            Lokasi:
                        </div>
                        <input type="text"
                               name="lokasi"
                               class="filter-input"
                               placeholder="Cari lokasi...">
                    </div>

                    <div class="filter-section">
                        <div class="filter-title"> Harga </div>
                        <div class="d-flex align-items-center gap-2">
                            <input type="number"
                                   name="harga_min"
                                   class="filter-input"
                                   placeholder="Min: 100000">
                            <span>-</span>
                            <input type="number"
                                   name="harga_max"
                                   class="filter-input"
                                   placeholder="Max: 7000000">
                        </div>
                    </div>

                    <div class="filter-section">
                        <div class="filter-title">
                            Periode:
                        </div>
                        <div class="filter-option">
                            <label>
                                <input type="radio"
                                       name="periode"
                                       value="Tahunan">
                                Tahunan
                            </label>
                            <label class="mb-0">
                                <input type="radio"
                                       name="periode"
                                       value="Bulanan">
                                Bulanan
                            </label>
                        </div>
                    </div>

                    <div class="filter-section">
                        <div class="filter-title">
                            Penghuni:
                        </div>
                        <div class="filter-option">
                            <label>
                                <input type="radio"
                                       name="penghuni"
                                       value="Putri">
                                Putri
                            </label>
                            <label>
                                <input type="radio"
                                       name="penghuni"
                                       value="Putra">
                                Putra
                            </label>

                            <label class="mb-0">
                                <input type="radio"
                                       name="penghuni"
                                       value="Campur">
                                Campur
                            </label>
                        </div>
                    </div>


                    <div class="text-end">
                        <button type="submit" class="btn-filter">
                            Terapkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="d-flex justify-content-between
                        align-items-center flex-wrap gap-3 mb-4">

                <h2 class="result-title mb-0">
                    Hasil Pencarian
                </h2>
                <select class="form-select"
                        style="width: 285px; border-color:#4d8be0;">
                    <option value="rating"> Rating tertinggi </option>
                    <option value="termurah">Harga terendah</option>
                    <option value="termahal">Harga tertinggi</option>
                </select>
            </div>

            <div class="row g-4">
                @forelse(($kos ?? collect()) as $item)
                    <div class="col-md-6">
                        <div class="kos-card">
                            <div class="kos-image-wrapper">
                                @if($item->foto)
                                    <img src="{{ asset('storage/' . $item->foto) }}"
                                         class="kos-image"
                                         alt="{{ $item->nama_kos }}">
                                @else
                                    <div class="kos-image d-flex
                                                align-items-center
                                                justify-content-center
                                                bg-light">
                                        <i class="bi bi-house fs-1
                                                  text-secondary"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="kos-content">
                                <div class="d-flex justify-content-between
                                            align-items-center mb-2">
                                    <h5 class="kos-name">
                                        {{ $item->nama_kos }}
                                    </h5>
                                    <span class="kos-type
                                        {{ strtolower($item->tipe_kos) === 'putri' ? 'putri' : (strtolower($item->tipe_kos) === 'putra' ? 'putra' : 'campur') }}">

                                        {{ ucfirst($item->tipe_kos) }}
                                    </span>
                                </div>

                                <p class="kos-address">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    {{ $item->alamat }}
                                </p>
                                <a href="{{ url('/penyewa/detail-kos/' . $item->id) }}"
                                   class="btn btn-primary w-100">
                                    Lihat Detail
                                </a>

                            </div>
                        </div>
                    </div>

                @empty

                    <div class="col-12">
                        <div class="empty-state text-center">
                            <i class="bi bi-house-x"></i>
                            <h4>
                                Belum Ada Kos Tersedia
                            </h4>
                            <p>
                                Saat ini belum ada kos yang terdaftar.
                                Silakan cek kembali nanti setelah
                                pemilik menambahkan kos.
                            </p>
                        </div>
                    </div>

                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

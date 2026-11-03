
@extends('layouts.app')

@section('title', 'Beranda - ngeKosYuk')

@section('content')

<style>
    body {
        background: #fff;
        font-family: Arial, sans-serif;
        color: #263747;
    }

    
    .navbar-home {
        background: #fff;
        padding: 15px 5%;
        box-shadow: 0 2px 8px rgba(0,0,0,.05);
    }

    .navbar-brand {
        color: #2674c9 !important;
        font-size: 21px;
        font-weight: 600;
    }

    .navbar-home .nav-link {
        color: #2165bd;
        font-size: 17px;
        margin-left: 15px;
    }

    .navbar-home .nav-link:hover {
        color: #123e7c;
    }

    
    .hero {
        height: 440px;
        background:
            linear-gradient(rgba(0,0,0,.25), rgba(0,0,0,.25)),
            url('https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=1600')
            center/cover no-repeat;
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    .hero h1 {
        color: white;
        font-size: 48px;
        font-weight: 700;
        text-shadow: 0 3px 8px rgba(0,0,0,.3);
        margin-bottom: 35px;
    }

    .search-box {
        max-width: 420px;
        margin: auto;
        display: flex;
        background: white;
        border: 1px solid #2674c9;
        border-radius: 8px;
        overflow: hidden;
    }

    .search-box input {
        border: none;
        padding: 12px 15px;
        outline: none;
        flex: 1;
    }

    .search-box button {
        border: none;
        background: white;
        padding: 0 15px;
        color: #2674c9;
    }

    .recommendation {
        padding: 48px 3%;
    }

    .section-title {
        color: #2674c9;
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 18px;
    }

    .kos-card {
        border: 1px solid #3979d2;
        border-radius: 9px;
        overflow: hidden;
        height: 100%;
        transition: .2s;
    }

    .kos-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 5px 15px rgba(0,0,0,.1);
    }

    .kos-image {
        height: 215px;
        width: 100%;
        object-fit: cover;
    }

    .kos-info {
        padding: 12px 14px 18px;
    }

    .kos-name {
        font-size: 19px;
        font-weight: 600;
        color: #222;
    }

    .rating {
        position: absolute;
        top: 12px;
        right: 12px;
        background: #fff5a8;
        color: #2674c9;
        border-radius: 8px;
        padding: 3px 9px;
    }

    .kos-type {
        font-size: 12px;
        padding: 4px 12px;
        border-radius: 15px;
        color: white;
    }

    .putri { background: #2674c9; }
    .putra { background: #f28b20; }
    .campur { background: #20b83c; }

    .price {
        color: #2674c9;
        font-size: 19px;
        font-weight: 500;
    }

    .price-month {
        color: #16852d;
        font-size: 14px;
    }

    .location {
        color: #6c6262;
        font-size: 12px;
        margin-top: 8px;
    }

    .favorite {
        position: absolute;
        bottom: 12px;
        left: 14px;
        color: white;
        font-size: 23px;
        cursor: pointer;
        text-shadow: 0 2px 5px #555;
    }

    @media(max-width: 768px) {
        .hero h1 { font-size: 32px; }
        .navbar-home .nav-link { margin-left: 0; }
    }
</style>

<nav class="navbar navbar-expand-lg navbar-home">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">NgeKosYuk</a>

        <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse" data-bs-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Cari Kos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Daftar Kos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Riwayat Sewa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Riwayat Pembayaran</a>
                </li>
                <li class="nav-item">
                    <form action="{{ url('/logout') }}" method="POST">
                        @csrf
                        <button class="nav-link border-0 bg-transparent"
                            type="submit">Log Out</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<section class="hero">
    <div>
        <h1>Cari Kosan<br>Impianmu Di Sini</h1>

        <form action="#" method="GET" class="search-box">
            <input type="text" name="search" placeholder="Cari lokasi atau nama kos...">
            <button type="submit">
                <i class="bi bi-search fs-5"></i>
            </button>
        </form>
    </div>
</section>

<section class="recommendation">
    <h2 class="section-title">Rekomendasi Kos</h2>

    <div class="row g-4">

        @php
            $kosList = [
                [
                    'nama' => 'Asrama Melati 2',
                    'foto' => 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=600',
                    'rating' => '4.7',
                    'tipe' => 'Putri',
                    'class' => 'putri',
                    'tahun' => '7.000.000',
                    'bulan' => '800.000',
                    'alamat' => 'Jl. Pulau Morotai No. 68, Jagabaya III, Way Halim, Bandar Lampung'
                ],
                [
                    'nama' => 'Asrama Apadeh',
                    'foto' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=600',
                    'rating' => '4.7',
                    'tipe' => 'Putra',
                    'class' => 'putra',
                    'tahun' => '7.000.000',
                    'bulan' => '800.000',
                    'alamat' => 'Jl. Pulau Morotai No. 68, Jagabaya III, Way Halim, Bandar Lampung'
                ],
                [
                    'nama' => 'Asrama Apadeh',
                    'foto' => 'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?w=600',
                    'rating' => '4.7',
                    'tipe' => 'Campur',
                    'class' => 'campur',
                    'tahun' => '7.000.000',
                    'bulan' => '700.000',
                    'alamat' => 'Jl. Pulau Morotai No. 68, Jagabaya III, Way Halim, Bandar Lampung'
                ],
                [
                    'nama' => 'Asrama Hijau',
                    'foto' => 'https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?w=600',
                    'rating' => '4.7',
                    'tipe' => 'Putra',
                    'class' => 'putra',
                    'tahun' => '7.000.000',
                    'bulan' => '800.000',
                    'alamat' => 'Jl. Pulau Morotai No. 68, Jagabaya III, Way Halim, Bandar Lampung'
                ],
            ];
        @endphp

        @foreach($kosList as $kos)
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="kos-card">
                <div class="position-relative">
                    <img src="{{ $kos['foto'] }}" class="kos-image" alt="{{ $kos['nama'] }}">

                    <span class="rating">
                        <i class="bi bi-star"></i> {{ $kos['rating'] }}
                    </span>

                    <span class="favorite">
                        <i class="bi bi-heart-fill"></i>
                    </span>
                </div>

                <div class="kos-info">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="kos-name">{{ $kos['nama'] }}</span>
                        <span class="kos-type {{ $kos['class'] }}">{{ $kos['tipe'] }}</span>
                    </div>

                    <div class="price">
                        Rp {{ $kos['tahun'] }}
                        <small class="badge rounded-pill bg-info-subtle text-info">Tahunan</small>
                    </div>

                    <div class="price-month">
                        Rp {{ $kos['bulan'] }}
                        <small class="badge rounded-pill bg-success-subtle text-success">Bulanan</small>
                    </div>

                    <div class="location">
                        <i class="bi bi-geo-alt-fill"></i>
                        {{ $kos['alamat'] }}
                    </div>
                </div>
            </div>
        </div>
        @endforeach

    </div>
</section>

@endsection

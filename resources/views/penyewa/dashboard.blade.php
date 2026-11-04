
@extends('layouts.app')

@section('content')

@include('penyewa.partials.navbar')

<style>
    :root {
        --primary-blue: #2696D2;
        --dark-blue: #2468B8;
        --light-blue: #EAF4FF;
        --text-dark: #263747;
    }

    body {
        background: #fff;
        color: var(--text-dark);
    }
    
    .hero {
        min-height: 390px;
        display: flex;
        align-items: center;
        padding: 60px 6%;
        background:
            linear-gradient(90deg, rgba(200,244,255,.95), rgba(200,244,255,.35)),
            url('https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=1600')
            center/cover;
    }

    .hero-content {
        max-width: 520px;
    }

    .hero h1 {
        color: #2468B8;
        font-size: 42px;
        font-weight: 800;
        margin-bottom: 20px;
        text-shadow: 1px 2px 3px rgba(0,0,0,.15);
    }

    .hero p {
        font-size: 20px;
        color: #172B3A;
        margin-bottom: 25px;
    }

    .btn-primary-custom {
        background: var(--dark-blue);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 8px;
        text-decoration: none;
        display: inline-block;
        font-weight: 600;
    }

    .btn-primary-custom:hover {
        background: var(--primary-blue);
        color: white;
    }

    .section-title {
        color: var(--dark-blue);
        font-weight: 800;
        margin-bottom: 30px;
    }

    .recommendation-section {
        padding: 65px 6%;
    }

    .kos-card {
        border: 1px solid #e5eaf0;
        border-radius: 13px;
        overflow: hidden;
        height: 100%;
        transition: .25s;
        background: white;
    }

    .kos-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 22px rgba(0,0,0,.09);
    }

    .kos-image {
        width: 100%;
        height: 190px;
        object-fit: cover;
    }

    .kos-card-body {
        padding: 17px;
    }

    .kos-name {
        color: var(--text-dark);
        font-weight: 700;
        font-size: 17px;
    }

    .kos-address {
        color: #777;
        font-size: 13px;
        margin: 8px 0;
    }

    .kos-type {
        display: inline-block;
        background: var(--light-blue);
        color: var(--dark-blue);
        border-radius: 20px;
        padding: 5px 12px;
        font-size: 12px;
    }

    .about-section {
        padding: 55px 6%;
    }

    .about-description {
        font-size: 16px;
        line-height: 1.6;
    }

    .about-highlight {
        background: var(--light-blue);
        border-radius: 15px;
        padding: 22px;
        display: flex;
        align-items: center;
        gap: 22px;
        margin-top: 20px;
    }

    .about-highlight i {
        font-size: 48px;
        color: var(--dark-blue);
    }

    .about-image {
        width: 100%;
        max-height: 300px;
        object-fit: contain;
    }

    .values-section {
        padding: 60px 6%;
    }

    .value-card {
        border: 1px solid #cbdcf3;
        border-radius: 15px;
        padding: 28px 15px;
        text-align: center;
        height: 100%;
        color: var(--dark-blue);
    }

    .value-card i {
        font-size: 42px;
        margin-bottom: 15px;
    }

    .value-card h6 {
        font-size: 17px;
        margin: 0;
    }

    .vision-section {
        padding: 45px 6% 80px;
    }

    .vision-card {
        border: 1px solid #cbdcf3;
        border-radius: 15px;
        padding: 30px;
        height: 100%;
        text-align: center;
    }

    .vision-card h4 {
        color: var(--dark-blue);
        font-weight: 800;
        margin-bottom: 18px;
    }

    .vision-card p,
    .vision-card li {
        font-size: 16px;
        line-height: 1.5;
    }

    .vision-card ol {
        text-align: center;
        padding-left: 25px;
    }

    .footer {
        background: #2468B8;
        color: white;
        padding: 25px 6%;
        text-align: center;
    }

    @media (max-width: 768px) {
        .hero {
            min-height: 350px;
            padding: 40px 7%;
        }

        .hero h1 {
            font-size: 32px;
        }

        .hero p {
            font-size: 17px;
        }

        .navbar .nav-link {
            margin-left: 0;
        }
    }
</style>

<section class="hero">
    <div class="hero-content">
        <h1>Selamat Datang di ngeKosYuk</h1>

        <p>
            Kami hadir untuk memudahkan kamu menemukan kos
            yang nyaman, aman, dan sesuai dengan kebutuhanmu.
        </p>

        <a href="{{ url('/penyewa/cari-kos') }}"
            class="btn-primary-custom">
            Cari Kos Sekarang
        </a>
    </div>
</section>

<section class="recommendation-section">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title mb-2">Rekomendasi Kos</h2>
                <p class="text-muted mb-0">
                    Temukan tempat tinggal yang nyaman untukmu.
                </p>
            </div>

            <a href="{{ url('/penyewa/cari-kos') }}"
                class="text-decoration-none fw-semibold">
                Lihat Semua <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="row g-4">
            @forelse(($kos ?? collect()) as $item)
                <div class="col-md-6 col-lg-3">
                    <div class="kos-card">
                        <img
                            src="{{ $item->foto ?? 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=700' }}"
                            class="kos-image"
                            alt="Foto Kos">

                        <div class="kos-card-body">
                            <span class="kos-type">
                                {{ $item->tipe_kos ?? 'Tipe kos' }}
                            </span>

                            <h5 class="kos-name mt-3">
                                {{ $item->nama_kos ?? 'Nama Kos' }}
                            </h5>

                            <p class="kos-address">
                                <i class="bi bi-geo-alt"></i>
                                {{ $item->alamat ?? 'Alamat belum tersedia' }}
                            </p>

                            <a href="{{ url('/penyewa/detail-kos/' . $item->id) }}"
                                class="btn-primary-custom w-100 text-center">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5 bg-light rounded-4">
                        <i class="bi bi-house-door"
                            style="font-size: 48px; color: #2696D2;"></i>

                        <h5 class="mt-3 fw-bold">
                            Belum Ada Kos Tersedia
                        </h5>

                        <p class="text-muted">
                            Kos yang tersedia akan ditampilkan di sini.
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>


<section class="about-section">
    <div class="container-fluid">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <h2 class="section-title mb-3">Siapa Kami?</h2>

                <p class="about-description">
                    ngeKosYuk adalah platform sistem informasi sewa kos
                    yang dirancang untuk menghubungkan penyewa dengan
                    pemilik kos secara mudah, aman, dan transparan.
                    Kami percaya bahwa mencari tempat tinggal bukan
                    sekadar mencari kamar, tetapi juga menemukan
                    kenyamanan dan rasa aman untuk beraktivitas
                    dan berkembang.
                </p>

                <div class="about-highlight">
                    <i class="bi bi-house-heart-fill"></i>

                    <div>
                        <h5 class="fw-bold"
                            style="color: #2468B8;">
                            Lebih dari sekadar kos
                        </h5>

                        <p class="mb-0">
                            ngeKosYuk bukan hanya tempat untuk mencari kos,
                            tetapi juga solusi untuk kehidupan yang lebih baik.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 text-center">
                <img
                    src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=700"
                    class="about-image"
                    alt="Ilustrasi Rumah">
            </div>
        </div>
    </div>
</section>

<section class="values-section">
    <div class="container">
        <h2 class="section-title text-center mb-5">
            Nilai Kami
        </h2>

        <div class="row g-4 justify-content-center">
            <div class="col-6 col-md-3">
                <div class="value-card">
                    <i class="bi bi-people-fill"></i>
                    <h6>Peduli<br>Pengguna</h6>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="value-card">
                    <i class="bi bi-shield-fill-check"></i>
                    <h6>Aman &<br>Terpercaya</h6>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="value-card">
                    <i class="bi bi-hand-thumbs-up-fill"></i>
                    <h6>Mudah<br>Digunakan</h6>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="value-card">
                    <i class="bi bi-eye-fill"></i>
                    <h6>Transparan</h6>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="vision-section">
    <div class="container">
        <h2 class="section-title text-center mb-5">
            Visi & Misi
        </h2>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="vision-card">
                    <h4>Visi</h4>

                    <p>
                        Menjadi platform sewa kos terpercaya dan terdepan
                        di Indonesia yang memberikan kemudahan, kenyamanan,
                        dan keamanan bagi seluruh penggunanya.
                    </p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="vision-card">
                    <h4>Misi</h4>

                    <ol>
                        <li>
                            Mempermudah pencarian kos dengan informasi
                            yang lengkap dan akurat.
                        </li>
                        <li>
                            Membangun koneksi yang baik antara penyewa
                            dan pemilik kos.
                        </li>
                        <li>
                            Memberikan pengalaman sewa kos yang aman,
                            nyaman, dan transparan.
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</section>


<footer class="footer">
    <small>
        &copy; {{ date('Y') }} ngeKosYuk. All Rights Reserved.
    </small>
</footer>

@endsection

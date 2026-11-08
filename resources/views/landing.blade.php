@extends('layouts.app')

@section('title', 'Beranda')

@push('styles')
<style>
    .landing-hero {
        background: var(--nk-surface);
        border: 1px solid var(--nk-border);
        border-radius: 12px;
        padding: 3rem 2.5rem;
        margin-bottom: 2rem;
    }

    .landing-hero h1 {
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -0.04em;
        color: var(--nk-ink);
        max-width: 650px;
    }

    .landing-hero p {
        color: var(--nk-muted);
        max-width: 550px;
        line-height: 1.7;
    }

    .hero-image {
        width: 100%;
        height: 280px;
        object-fit: cover;
        border-radius: 10px;
    }

    .feature-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
        margin-bottom: 2.5rem;
    }

    .feature-card {
        background: var(--nk-surface);
        border: 1px solid var(--nk-border);
        border-radius: 10px;
        padding: 1.5rem;
    }

    .feature-icon {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--nk-blue-soft);
        color: var(--nk-blue);
        border-radius: 9px;
        font-size: 1.3rem;
        margin-bottom: 1rem;
    }

    .kos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
        gap: 1.25rem;
    }

    .kos-card {
        background: var(--nk-surface);
        border: 1px solid var(--nk-border);
        border-radius: 10px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .kos-card:hover {
        border-color: var(--nk-blue);
        box-shadow: 0 6px 16px rgba(15, 23, 42, .05);
    }

    .kos-thumb-wrap {
        height: 175px;
        position: relative;
        overflow: hidden;
        background: var(--nk-surface-subtle);
    }

    .kos-thumb {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .kos-card-body {
        padding: 1.15rem;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .kos-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--nk-ink);
        margin-bottom: .25rem;
    }

    .kos-address {
        font-size: .82rem;
        color: var(--nk-muted);
        margin-bottom: .85rem;
    }

    .kos-price-row {
        margin-top: auto;
        padding-top: .85rem;
        border-top: 1px solid var(--nk-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .5rem;
    }

    .landing-cta {
        background: var(--nk-blue-soft);
        border: 1px solid var(--nk-border);
        border-radius: 12px;
        padding: 2.5rem;
        text-align: center;
    }

    .public-navbar {
        background: var(--nk-surface);
        border-bottom: 1px solid var(--nk-border);
    }

    .public-navbar .navbar-brand {
        color: var(--nk-blue);
        font-weight: 800;
        font-size: 1.25rem;
    }

    .public-navbar .nav-link {
        color: var(--nk-muted);
        font-size: .9rem;
        font-weight: 500;
    }

    .public-navbar .nav-link:hover {
        color: var(--nk-blue);
    }

    @media (max-width: 768px) {
        .landing-hero {
            padding: 1.5rem;
        }

        .hero-image {
            height: 210px;
        }
    }
</style>
@endpush

@section('content')

@include('partials.navbar-public')

<div class="container py-4">
    <section class="landing-hero" id="beranda">
        <div class="row align-items-center g-4">

            <div class="col-lg-7">
                <span class="nk-badge nk-badge-info mb-3"> TEMUKAN KOS IMPIANMU </span>
                <h1 class="mb-3"> Temukan Tempat Tinggal Nyaman, Semudah Itu. </h1>
                <p class="mb-4">
                    Cari dan temukan kos sesuai kebutuhanmu.
                    Bandingkan harga, lihat fasilitas kamar,
                    dan ajukan penyewaan dengan mudah melalui ngeKosYuk.
                </p>

                <div class="d-flex flex-wrap gap-2">
                    <a href="#rekomendasi"
                       class="btn-nk-primary px-4 py-2 text-decoration-none">
                        <i class="bi bi-search me-2"></i>
                        Mulai Cari Kos
                    </a>

                    <a href="{{ url('/register') }}"
                       class="btn-nk-secondary px-4 py-2 text-decoration-none">
                        Buat Akun
                    </a>
                </div>
            </div>

            <div class="col-lg-5">
                <img
                    src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=900&q=85"
                    alt="Interior kamar kos nyaman"
                    class="hero-image">
            </div>

        </div>
    </section>

    <section id="keunggulan" class="mb-5">

        <div class="mb-3">
            <h2 class="fw-bold mb-1" style="font-size:1.25rem;">Mengapa ngeKosYuk?</h2>
            <p class="text-secondary small mb-0"> Temukan kemudahan dalam mencari dan menyewa kamar kos.</p>
        </div>

        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon"> 
                    <i class="bi bi-search"></i> 
                </div>
                <h3 class="h6 fw-bold">Pencarian Mudah</h3>
                <p class="small text-secondary mb-0">
                    Cari kos berdasarkan lokasi, tipe, dan harga
                    sesuai kebutuhan.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="bi bi-house-check"></i>
                </div>
                <h3 class="h6 fw-bold">Informasi Lengkap</h3>
                <p class="small text-secondary mb-0">
                    Lihat detail kos, fasilitas, harga,
                    dan ketersediaan kamar.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="bi bi-credit-card"></i>
                </div>
                <h3 class="h6 fw-bold">Proses Praktis</h3>
                <p class="small text-secondary mb-0">
                    Ajukan penyewaan dan lakukan pembayaran
                    melalui satu platform.
                </p>
            </div>

        </div>
    </section>

    <section id="rekomendasi">

        <div class="d-flex flex-wrap justify-content-between
                    align-items-end mb-3">
            <div>
                <h2 class="fw-bold mb-1" style="font-size:1.25rem;"> Rekomendasi Kos </h2>
                <p class="text-secondary small mb-0"> Temukan kos yang sesuai dengan kebutuhanmu. </p>
            </div>

            <a href="{{ route('kos.cari') }}"
               class="small fw-semibold text-decoration-none"
               style="color:var(--nk-blue);">
                Cari kos lainnya &rarr;
            </a>

        </div>

        <div class="kos-grid">

            @forelse($rekomendasiKos as $kos)

                @php
                    $kamarTersedia = $kos->kamars->where('status', 'tersedia');

                    $hargaTermurah = $kamarTersedia->min('harga_per_bulan');

                    $fotoUrl = $kos->foto
                        ? (str_starts_with($kos->foto, 'http')
                            ? $kos->foto
                            : asset('storage/' . $kos->foto))
                        : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=600&q=80';
                @endphp

                <article class="kos-card">

                    <div class="kos-thumb-wrap">
                        <img src="{{ $fotoUrl }}"
                             alt="Foto {{ $kos->nama_kos }}"
                             class="kos-thumb">

                        <div class="position-absolute top-0 start-0 m-3">
                            @if($kos->tipe_kos === 'putra')
                                <span class="nk-badge nk-badge-info">
                                    Kos Putra
                                </span>
                            @elseif($kos->tipe_kos === 'putri')
                                <span class="nk-badge nk-badge-teal">
                                    Kos Putri
                                </span>
                            @else
                                <span class="nk-badge nk-badge-neutral">
                                    Kos Campur
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="kos-card-body">

                        <h3 class="kos-title">
                            {{ $kos->nama_kos }}
                        </h3>

                        <p class="kos-address">
                            <i class="bi bi-geo-alt me-1"></i>
                            {{ $kos->alamat }}
                        </p>

                        <div class="d-flex justify-content-between
                                    align-items-center small text-secondary mb-2">
                            <span>Ketersediaan</span>

                            <strong class="text-dark">
                                {{ $kamarTersedia->count() }} kamar kosong
                            </strong>
                        </div>

                        <div class="kos-price-row">

                            <div>
                                <span class="d-block text-secondary"
                                      style="font-size:.72rem;">
                                    Mulai dari
                                </span>

                                @if($hargaTermurah)
                                    <strong style="color:var(--nk-blue);">
                                        Rp {{ number_format($hargaTermurah, 0, ',', '.') }}
                                    </strong>

                                    <span class="text-secondary small">
                                        / bln
                                    </span>
                                @else
                                    <span class="text-secondary small">
                                        Belum ada kamar
                                    </span>
                                @endif
                            </div>

                            <a href="{{ route('kos.detail', $kos->id) }}"
                               class="btn-nk-primary py-1 px-3 small text-decoration-none">
                                Lihat Detail
                            </a>

                        </div>
                    </div>
                </article>

            @empty

                <div class="nk-card p-5 text-center"
                     style="grid-column:1/-1;">

                    <i class="bi bi-house-x fs-1 text-secondary"></i>

                    <h3 class="h6 fw-bold mt-2">
                        Belum Ada Kos Terdaftar
                    </h3>

                    <p class="text-secondary small mb-0">
                        Saat ini belum ada data kos yang tersedia.
                    </p>

                </div>

            @endforelse

        </div>

        @if(method_exists($rekomendasiKos, 'links'))
            <div class="mt-4">
                {{ $rekomendasiKos->links() }}
            </div>
        @endif

    </section>

    <section class="landing-cta mt-5">

        <h2 class="fw-bold mb-2" style="font-size:1.5rem;"> Siap Menemukan Kos Impianmu? </h2>
        <p class="text-secondary mb-4">
            Daftar sekarang dan temukan tempat tinggal
            yang sesuai dengan kebutuhanmu.
        </p>

        <a href="{{ url('/register') }}"
           class="btn-nk-primary px-4 py-2 text-decoration-none">
            Daftar Sekarang
            <i class="bi bi-arrow-right ms-2"></i>
        </a>

    </section>

</div>

@endsection

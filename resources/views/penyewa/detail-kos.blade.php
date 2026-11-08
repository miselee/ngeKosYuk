@extends('layouts.app')

@section('title', $kos->nama_kos)

@push('styles')
<style>
    .kos-hero-img {
        width: 100%;
        height: 300px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid var(--nk-border);
        background-color: var(--nk-surface-subtle);
    }

    .room-row-card {
        background-color: var(--nk-surface);
        border: 1px solid var(--nk-border);
        border-radius: 10px;
        padding: 1.15rem 1.25rem;
        transition: border-color 0.15s ease;
    }

    .room-row-card:hover {
        border-color: var(--nk-border-strong);
    }

    .facility-tag {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        font-size: 0.75rem;
        color: var(--nk-muted);
        background-color: var(--nk-bg);
        border: 1px solid var(--nk-border);
        border-radius: 5px;
    }
</style>
@endpush

@section('content')
@include('penyewa.partials.navbar')

<div class="container py-4">
    <div class="mb-3">
        <a href="{{ url('/penyewa/cari-kos') }}" class="small text-decoration-none fw-medium" style="color: var(--nk-muted);">
            &larr; Kembali ke Pencarian Kos
        </a>
    </div>

    {{-- Property Overview --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            @php
                $fotoKosUrl = $kos->foto
                    ? (str_starts_with($kos->foto, 'http') ? $kos->foto : asset('storage/' . $kos->foto))
                    : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=900&q=80';
            @endphp
            <img src="{{ $fotoKosUrl }}"
                 alt="Foto {{ $kos->nama_kos }}"
                 width="800"
                 height="300"
                 class="kos-hero-img">
        </div>

        <div class="col-lg-5">
            <div class="nk-card p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        @if($kos->tipe_kos === 'putra')
                            <span class="nk-badge nk-badge-info">Kos Putra</span>
                        @elseif($kos->tipe_kos === 'putri')
                            <span class="nk-badge nk-badge-teal">Kos Putri</span>
                        @else
                            <span class="nk-badge nk-badge-neutral">Kos Campur</span>
                        @endif

                        @if($rataRating)
                            <span class="nk-badge nk-badge-warning font-mono">
                                &#9733; {{ number_format($rataRating, 1) }} / 5.0
                            </span>
                        @endif
                    </div>

                    <h1 class="nk-page-title mb-2">{{ $kos->nama_kos }}</h1>
                    <p class="small text-secondary mb-3">{{ $kos->alamat }}</p>

                    <div class="pt-3 border-top">
                        <span class="small fw-semibold d-block mb-1">Deskripsi &amp; Peraturan Kos</span>
                        <p class="small text-secondary mb-0">
                            {{ $kos->deskripsi ?: 'Hunian kos bersih dan terawat. Hubungi pemilik untuk detail peraturan jam bertamu dan fasilitas bersama.' }}
                        </p>
                    </div>
                </div>

                <div class="pt-3 mt-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <span class="d-block text-secondary" style="font-size: 0.75rem;">Dikelola oleh</span>
                        <strong class="small">{{ $kos->pemilik->name ?? 'Pemilik Kos' }}</strong>
                        @if($kos->pemilik && $kos->pemilik->nomor_hp)
                            <span class="d-block font-mono small text-secondary">{{ $kos->pemilik->nomor_hp }}</span>
                        @endif
                    </div>

                    @if($kos->pemilik && $kos->pemilik->nomor_hp)
                        @php
                            $waNumber = preg_replace('/[^0-9]/', '', $kos->pemilik->nomor_hp);
                            if (str_starts_with($waNumber, '0')) {
                                $waNumber = '62' . substr($waNumber, 1);
                            }
                        @endphp
                        <a href="https://wa.me/{{ $waNumber }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn-nk-teal py-1 px-3 small">
                            Tanya via WhatsApp
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Room Units List --}}
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-end mb-3">
            <div>
                <h2 class="h5 fw-bold mb-0">Daftar Unit Kamar ({{ $kos->kamars->count() }})</h2>
                <p class="small text-secondary mb-0">Pilih kamar berstatus tersedia untuk mengajukan kontrak sewa.</p>
            </div>
        </div>

        <div class="d-flex flex-column gap-3">
            @forelse($kos->kamars as $kamar)
                <div class="room-row-card">
                    <div class="row align-items-center g-3">
                        <div class="col-md-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="font-mono fw-bold fs-6">Kamar {{ $kamar->nomor_kamar }}</span>
                                @if($kamar->status === 'tersedia')
                                    <span class="nk-badge nk-badge-success">Tersedia</span>
                                @else
                                    <span class="nk-badge nk-badge-danger">Terisi</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-5">
                            @if($kamar->fasilitas)
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach(explode(',', $kamar->fasilitas) as $fas)
                                        <span class="facility-tag">{{ trim($fas) }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="small text-secondary">Fasilitas standar kamar</span>
                            @endif
                        </div>

                        <div class="col-md-4 text-md-end d-flex flex-md-row flex-column justify-content-md-end align-items-md-center gap-3">
                            <div>
                                <strong class="font-mono" style="color: var(--nk-blue); font-size: 1rem;">
                                    Rp {{ number_format($kamar->harga_per_bulan, 0, ',', '.') }}
                                </strong>
                                <span class="small text-secondary">/ bulan</span>
                            </div>

                            @if($kamar->status === 'tersedia')
                                <a href="{{ url('/penyewa/pengajuan-sewa/' . $kamar->id) }}" class="btn-nk-primary py-1 px-3 small">
                                    Ajukan Sewa
                                </a>
                            @else
                                <button type="button" class="btn-nk-secondary py-1 px-3 small opacity-50" disabled>
                                    Tidak Tersedia
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="nk-card p-4 text-center text-secondary small">
                    Belum ada unit kamar yang didaftarkan pada properti kos ini.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Tenant Reviews --}}
    <div>
        <h2 class="h5 fw-bold mb-3">Ulasan Penghuni ({{ $kos->ulasans->count() }})</h2>
        <div class="row g-3">
            @forelse($kos->ulasans as $ulasan)
                <div class="col-md-6">
                    <div class="nk-card p-3 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong class="small">{{ $ulasan->penyewa->name ?? 'Penyewa' }}</strong>
                            <span class="nk-badge nk-badge-warning font-mono">
                                {{ $ulasan->rating }} / 5
                            </span>
                        </div>
                        <p class="small text-secondary mb-1">{{ $ulasan->komentar ?: 'Tidak ada komentar tertulis.' }}</p>
                        <span class="text-secondary" style="font-size: 0.72rem;">
                            {{ $ulasan->created_at ? $ulasan->created_at->format('d M Y') : '' }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="nk-card p-4 text-center text-secondary small">
                        Belum ada ulasan untuk properti kos ini.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

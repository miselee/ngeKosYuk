@extends('layouts.app')

@section('title', 'Cari Kos')

@push('styles')
<style>
    .filter-panel {
        background-color: var(--nk-surface);
        border: 1px solid var(--nk-border);
        border-radius: 10px;
        padding: 1.25rem;
        position: sticky;
        top: 4.5rem;
    }

    .tipe-selector {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.45rem;
    }

    .tipe-radio {
        position: relative;
    }

    .tipe-radio input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .tipe-radio label {
        display: block;
        text-align: center;
        padding: 0.45rem 0.5rem;
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--nk-muted);
        background-color: var(--nk-bg);
        border: 1px solid var(--nk-border);
        border-radius: 6px;
        transition: all 0.15s ease;
    }

    .tipe-radio input[type="radio"]:checked + label {
        background-color: var(--nk-blue-soft);
        color: var(--nk-blue);
        border-color: var(--nk-blue);
        font-weight: 600;
    }

    .price-preset-btn {
        font-size: 0.74rem;
        padding: 0.25rem 0.55rem;
        border-radius: 5px;
        border: 1px solid var(--nk-border);
        background-color: var(--nk-bg);
        color: var(--nk-muted);
    }

    .price-preset-btn:hover {
        border-color: var(--nk-blue);
        color: var(--nk-blue);
    }

    .kos-result-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.15rem;
    }

    .kos-result-card {
        background-color: var(--nk-surface);
        border: 1px solid var(--nk-border);
        border-radius: 10px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: border-color 0.15s ease;
    }

    .kos-result-card:hover {
        border-color: var(--nk-blue);
    }

    .kos-result-img {
        width: 100%;
        height: 170px;
        object-fit: cover;
        border-bottom: 1px solid var(--nk-border);
        background-color: var(--nk-surface-subtle);
    }
</style>
@endpush

@section('content')
@include('penyewa.partials.navbar')

<div class="container py-4">
    <div class="nk-page-header">
        <div>
            <h1 class="nk-page-title">Eksplorasi &amp; Cari Kos</h1>
            <p class="nk-page-subtitle">Gunakan saringan lokasi, tipe penghuni, dan batas anggaran bulanan untuk menemukan kamar yang sesuai.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <label for="sort_select" class="small text-secondary fw-medium mb-0">Urutkan:</label>
            <select id="sort_select"
                    name="sort"
                    form="filterKosForm"
                    class="form-select form-select-sm"
                    style="width: auto;"
                    onchange="document.getElementById('filterKosForm').submit()">
                <option value="terbaru" {{ request('sort', 'terbaru') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                <option value="termurah" {{ request('sort') == 'termurah' ? 'selected' : '' }}>Harga Termurah</option>
                <option value="termahal" {{ request('sort') == 'termahal' ? 'selected' : '' }}>Harga Termahal</option>
            </select>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left Filter Sidebar --}}
        <div class="col-lg-3">
            <form id="filterKosForm" action="{{ url('/penyewa/cari-kos') }}" method="GET" class="filter-panel">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <span class="fw-bold small">Saringan Pencarian</span>
                    <a href="{{ url('/penyewa/cari-kos') }}" class="small text-decoration-none" style="color: var(--nk-muted); font-size: 0.78rem;">
                        Reset
                    </a>
                </div>

                <div class="mb-3">
                    <label for="lokasi" class="form-label">Lokasi atau Nama Kos</label>
                    <input type="text"
                           id="lokasi"
                           name="lokasi"
                           class="form-control"
                           placeholder="Contoh: Mawar, Malang..."
                           value="{{ request('lokasi') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label d-block">Tipe Kos</label>
                    @php $tipeAktif = request('tipe_kos', request('penghuni', '')); @endphp
                    <div class="tipe-selector">
                        <div class="tipe-radio">
                            <input type="radio" name="tipe_kos" id="tipe_semua" value="" {{ $tipeAktif == '' ? 'checked' : '' }}>
                            <label for="tipe_semua">Semua</label>
                        </div>
                        <div class="tipe-radio">
                            <input type="radio" name="tipe_kos" id="tipe_putra" value="putra" {{ $tipeAktif == 'putra' ? 'checked' : '' }}>
                            <label for="tipe_putra">Putra</label>
                        </div>
                        <div class="tipe-radio">
                            <input type="radio" name="tipe_kos" id="tipe_putri" value="putri" {{ $tipeAktif == 'putri' ? 'checked' : '' }}>
                            <label for="tipe_putri">Putri</label>
                        </div>
                        <div class="tipe-radio">
                            <input type="radio" name="tipe_kos" id="tipe_campur" value="campur" {{ $tipeAktif == 'campur' ? 'checked' : '' }}>
                            <label for="tipe_campur">Campur</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="harga_min" class="form-label">Harga Minimum (Rp)</label>
                    <input type="number"
                           id="harga_min"
                           name="harga_min"
                           class="form-control font-mono"
                           placeholder="0"
                           value="{{ request('harga_min') }}">
                </div>

                <div class="mb-3">
                    <label for="harga_max" class="form-label">Harga Maksimum (Rp)</label>
                    <input type="number"
                           id="harga_max"
                           name="harga_max"
                           class="form-control font-mono"
                           placeholder="2500000"
                           value="{{ request('harga_max') }}">
                    <div class="d-flex flex-wrap gap-1 mt-2">
                        <button type="button" class="price-preset-btn" data-max="800000">&le; 800rb</button>
                        <button type="button" class="price-preset-btn" data-max="1500000">&le; 1,5jt</button>
                        <button type="button" class="price-preset-btn" data-max="2500000">&le; 2,5jt</button>
                    </div>
                </div>

                <button type="submit" class="btn-nk-primary w-100 mt-2">
                    Terapkan Filter
                </button>
            </form>
        </div>

        {{-- Right Results Area --}}
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="small text-secondary">
                    Menampilkan <strong class="text-dark font-mono">{{ $daftarKos->count() }}</strong> properti kos
                </span>
            </div>

            <div class="kos-result-grid">
                @forelse($daftarKos as $itemKos)
                    @php
                        $kamarTersedia = $itemKos->kamars->where('status', 'tersedia');
                        $hargaMin = $kamarTersedia->min('harga_per_bulan') ?? $itemKos->kamars->min('harga_per_bulan');
                        $fotoUrl = $itemKos->foto
                            ? (str_starts_with($itemKos->foto, 'http') ? $itemKos->foto : asset('storage/' . $itemKos->foto))
                            : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=600&q=80';
                    @endphp
                    <article class="kos-result-card">
                        <img src="{{ $fotoUrl }}"
                             alt="Foto {{ $itemKos->nama_kos }}"
                             width="400"
                             height="170"
                             class="kos-result-img">

                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                                @if($itemKos->tipe_kos === 'putra')
                                    <span class="nk-badge nk-badge-info">Putra</span>
                                @elseif($itemKos->tipe_kos === 'putri')
                                    <span class="nk-badge nk-badge-teal">Putri</span>
                                @else
                                    <span class="nk-badge nk-badge-neutral">Campur</span>
                                @endif

                                <span class="small font-mono text-secondary">
                                    {{ $kamarTersedia->count() }} kamar tersedia
                                </span>
                            </div>

                            <h2 class="h6 fw-bold mb-1">{{ $itemKos->nama_kos }}</h2>
                            <p class="small text-secondary mb-3">{{ $itemKos->alamat }}</p>

                            <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="d-block text-secondary" style="font-size: 0.72rem;">Sewa per bulan</span>
                                    @if($hargaMin)
                                        <strong class="font-mono" style="color: var(--nk-blue);">
                                            Rp {{ number_format($hargaMin, 0, ',', '.') }}
                                        </strong>
                                    @else
                                        <span class="small text-secondary">Penuh</span>
                                    @endif
                                </div>

                                <a href="{{ url('/penyewa/detail-kos/' . $itemKos->id) }}" class="btn-nk-primary py-1 px-3 small">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="nk-card p-5 text-center" style="grid-column: 1 / -1;">
                        <h3 class="h6 fw-bold mb-1">Tidak Ada Kos yang Ditemukan</h3>
                        <p class="text-secondary small mb-3">
                            Coba longgarkan batas harga maksimum atau kata kunci lokasi pencarian Anda.
                        </p>
                        <a href="{{ url('/penyewa/cari-kos') }}" class="btn-nk-secondary small">
                            Tampilkan Semua Kos
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const maxInput = document.getElementById('harga_max');
    document.querySelectorAll('.price-preset-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (maxInput) {
                maxInput.value = btn.getAttribute('data-max');
            }
        });
    });
});
</script>
@endpush

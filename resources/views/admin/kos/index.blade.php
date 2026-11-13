@extends('layouts.app')

@section('title', 'Monitoring Properti & Kamar - Admin')

@section('content')
@include('admin.partials.navbar')

<div class="container py-4">
    <div class="nk-page-header">
        <div>
            <h1 class="nk-page-title">Monitoring Properti &amp; Kamar Kos</h1>
            <p class="nk-page-subtitle">Pantau seluruh properti kos yang didaftarkan oleh mitra pemilik beserta ketersediaan unit kamarnya.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.users.index') }}" class="btn-nk-secondary">
                Kelola Pengguna
            </a>
            <a href="{{ route('admin.transaksi.index') }}" class="btn-nk-primary">
                Audit Transaksi
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2 px-3 small mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger py-2 px-3 small mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="nk-metric-strip mb-4">
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Total Properti Kos</span>
            <div class="nk-metric-value">{{ $totalKos }}</div>
            <div class="nk-metric-sub">Seluruh mitra pemilik</div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Total Unit Kamar</span>
            <div class="nk-metric-value">{{ $totalKamar }}</div>
            <div class="nk-metric-sub">Terdaftar di platform</div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Kamar Tersedia</span>
            <div class="nk-metric-value" style="color: var(--nk-teal);">{{ $totalTersedia }}</div>
            <div class="nk-metric-sub">Siap disewa penghuni baru</div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Kamar Terisi</span>
            <div class="nk-metric-value" style="color: var(--nk-blue);">{{ $totalTerisi }}</div>
            <div class="nk-metric-sub">Sedang dihuni penyewa</div>
        </div>
    </div>

    <div class="nk-card p-3 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="nk-tabs">
                <a href="{{ route('admin.kos.index') }}"
                   class="nk-tab {{ !request('tipe_kos') ? 'active' : '' }}">
                    Semua Tipe
                </a>
                <a href="{{ route('admin.kos.index', ['tipe_kos' => 'putra']) }}"
                   class="nk-tab {{ request('tipe_kos') === 'putra' ? 'active' : '' }}">
                    Kos Putra
                </a>
                <a href="{{ route('admin.kos.index', ['tipe_kos' => 'putri']) }}"
                   class="nk-tab {{ request('tipe_kos') === 'putri' ? 'active' : '' }}">
                    Kos Putri
                </a>
                <a href="{{ route('admin.kos.index', ['tipe_kos' => 'campur']) }}"
                   class="nk-tab {{ request('tipe_kos') === 'campur' ? 'active' : '' }}">
                    Kos Campur
                </a>
            </div>

            <form action="{{ route('admin.kos.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                @if(request('tipe_kos'))
                    <input type="hidden" name="tipe_kos" value="{{ request('tipe_kos') }}">
                @endif
                <input type="text"
                       name="q"
                       class="form-control form-control-sm"
                       style="width: 240px;"
                       placeholder="Cari nama kos, lokasi, pemilik..."
                       aria-label="Cari properti kos"
                       value="{{ request('q') }}">
                <button type="submit" class="btn-nk-primary py-1 px-3 small">Cari</button>
                @if(request()->hasAny(['tipe_kos', 'q']))
                    <a href="{{ route('admin.kos.index') }}" class="btn-nk-secondary py-1 px-3 small">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <div class="nk-card overflow-hidden">
        @if($daftarKos->count() > 0)
            <div class="table-responsive">
                <table class="nk-table">
                    <thead>
                        <tr>
                            <th>Nama Properti &amp; Alamat</th>
                            <th>Mitra Pemilik</th>
                            <th>Tipe</th>
                            <th>Okupansi Kamar</th>
                            <th>Rentang Tarif / Bulan</th>
                            <th class="text-end">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($daftarKos as $kos)
                            @php
                                $tersediaCount = $kos->kamars->where('status', 'tersedia')->count();
                                $terisiCount = $kos->kamars->where('status', 'terisi')->count();
                                $minHarga = $kos->kamars->min('harga_per_bulan');
                                $maxHarga = $kos->kamars->max('harga_per_bulan');
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $kos->nama_kos }}</div>
                                    <div class="small text-secondary">{{ $kos->alamat }}</div>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $kos->pemilik->name ?? '-' }}</div>
                                    <div class="small text-secondary font-mono">{{ $kos->pemilik->nomor_hp ?? $kos->pemilik->email ?? '-' }}</div>
                                </td>
                                <td>
                                    @if($kos->tipe_kos === 'putra')
                                        <span class="nk-badge nk-badge-info">Putra</span>
                                    @elseif($kos->tipe_kos === 'putri')
                                        <span class="nk-badge nk-badge-teal">Putri</span>
                                    @else
                                        <span class="nk-badge nk-badge-neutral">Campur</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="font-mono small">
                                        <strong>{{ $kos->kamars_count }}</strong> unit total
                                    </div>
                                    <div class="small text-secondary">
                                        <span class="font-mono">{{ $tersediaCount }}</span> kosong &middot; <span class="font-mono">{{ $terisiCount }}</span> terisi
                                    </div>
                                </td>
                                <td class="font-mono small">
                                    @if($minHarga)
                                        Rp {{ number_format($minHarga, 0, ',', '.') }}
                                        @if($maxHarga && $maxHarga != $minHarga)
                                            s/d Rp {{ number_format($maxHarga, 0, ',', '.') }}
                                        @endif
                                    @else
                                        <span class="text-secondary">Belum ada kamar</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button"
                                                class="btn-nk-secondary py-1 px-2 small"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalDetailKos{{ $kos->id }}">
                                            Rincian Kamar
                                        </button>
                                        <form action="{{ route('admin.kos.destroy', $kos->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Hapus properti {{ $kos->nama_kos }} beserta seluruh unit kamarnya?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-nk-secondary py-1 px-2 small text-danger">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            @push('modals')
                            <div class="modal fade" id="modalDetailKos{{ $kos->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <div>
                                                <h2 class="modal-title h6 fw-bold mb-0">{{ $kos->nama_kos }}</h2>
                                                <span class="small text-secondary">Pemilik: {{ $kos->pemilik->name ?? '-' }} &middot; {{ $kos->alamat }}</span>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body p-0">
                                            @if($kos->kamars->count() > 0)
                                                <div class="table-responsive">
                                                    <table class="nk-table">
                                                        <thead>
                                                            <tr>
                                                                <th>No. Kamar</th>
                                                                <th>Fasilitas</th>
                                                                <th>Tarif / Bulan</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($kos->kamars as $km)
                                                                <tr>
                                                                    <td class="font-mono fw-semibold">Kamar {{ $km->nomor_kamar }}</td>
                                                                    <td class="small text-secondary">{{ $km->fasilitas ?: '-' }}</td>
                                                                    <td class="font-mono">Rp {{ number_format($km->harga_per_bulan, 0, ',', '.') }}</td>
                                                                    <td>
                                                                        @if($km->status === 'tersedia')
                                                                            <span class="nk-badge nk-badge-success">Tersedia</span>
                                                                        @else
                                                                            <span class="nk-badge nk-badge-danger">Terisi</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="p-4 text-center text-secondary small">
                                                    Properti kos ini belum memiliki unit kamar terdaftar.
                                                </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn-nk-secondary" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endpush
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($daftarKos->hasPages())
                <div class="p-3 border-top">
                    {{ $daftarKos->links() }}
                </div>
            @endif
        @else
            <div class="p-5 text-center text-secondary small">
                Tidak ada data properti kos yang sesuai dengan pencarian.
            </div>
        @endif
    </div>
</div>
@endsection


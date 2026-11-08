@extends('layouts.app')

@section('title', 'Kelola Kamar Kos')

@section('content')
@include('pemilik.partials.navbar')

<div class="container py-4">
    <div class="nk-page-header">
        <div>
            <h1 class="nk-page-title">Kelola Kamar Kos</h1>
            <p class="nk-page-subtitle">Atur daftar unit kamar, harga sewa bulanan, fasilitas, serta status ketersediaan kamar.</p>
        </div>
        <a href="{{ route('pemilik.kelola-kamar.create') }}" class="btn-nk-primary">
            + Tambah Kamar Baru
        </a>
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

    {{-- Metric Ledger Strip --}}
    <div class="nk-metric-strip mb-4">
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Total Unit Kamar</span>
            <div class="nk-metric-value">{{ $totalKamar }}</div>
            <div class="nk-metric-sub">Seluruh properti kos</div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Kamar Tersedia</span>
            <div class="nk-metric-value" style="color: var(--nk-teal);">{{ $totalTersedia }}</div>
            <div class="nk-metric-sub">Siap diajukan penyewa</div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Kamar Terisi</span>
            <div class="nk-metric-value" style="color: var(--nk-blue);">{{ $totalTerisi }}</div>
            <div class="nk-metric-sub">Sedang disewa aktif</div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="nk-card p-3 mb-4">
        <form action="{{ route('pemilik.kelola-kamar.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label for="kos_id" class="form-label">Filter Properti Kos</label>
                <select id="kos_id" name="kos_id" class="form-select">
                    <option value="">Semua Properti Kos</option>
                    @foreach($daftarKos as $kos)
                        <option value="{{ $kos->id }}" {{ request('kos_id') == $kos->id ? 'selected' : '' }}>
                            {{ $kos->nama_kos }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status Kamar</label>
                <select id="status" name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="tersedia" {{ request('status') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="terisi" {{ request('status') === 'terisi' ? 'selected' : '' }}>Terisi</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="q" class="form-label">Cari Nomor / Fasilitas</label>
                <input type="text" id="q" name="q" class="form-control" placeholder="Contoh: 101, AC..." value="{{ request('q') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn-nk-primary w-100">Filter</button>
                @if(request()->hasAny(['kos_id', 'status', 'q']))
                    <a href="{{ route('pemilik.kelola-kamar.index') }}" class="btn-nk-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Rooms Table --}}
    <div class="nk-card overflow-hidden">
        @if($kamars->count() > 0)
            <div class="table-responsive">
                <table class="nk-table">
                    <thead>
                        <tr>
                            <th>Nomor Kamar</th>
                            <th>Properti Kos</th>
                            <th>Fasilitas Kamar</th>
                            <th>Harga / Bulan</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kamars as $kamar)
                            <tr>
                                <td class="font-mono fw-bold">Kamar {{ $kamar->nomor_kamar }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $kamar->kos->nama_kos }}</div>
                                    <div class="small text-secondary">{{ ucfirst($kamar->kos->tipe_kos) }}</div>
                                </td>
                                <td class="small text-secondary">
                                    {{ $kamar->fasilitas ?: '-' }}
                                </td>
                                <td class="font-mono fw-semibold">
                                    Rp {{ number_format($kamar->harga_per_bulan, 0, ',', '.') }}
                                </td>
                                <td>
                                    <form action="{{ route('pemilik.kelola-kamar.toggle-status', $kamar->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="border-0 bg-transparent p-0"
                                                title="Klik untuk mengubah status ketersediaan kamar">
                                            @if($kamar->status === 'tersedia')
                                                <span class="nk-badge nk-badge-success">Tersedia</span>
                                            @else
                                                <span class="nk-badge nk-badge-danger">Terisi</span>
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('pemilik.kelola-kamar.edit', $kamar->id) }}" class="btn-nk-secondary py-1 px-2 small">
                                            Edit
                                        </a>
                                        <form action="{{ route('pemilik.kelola-kamar.destroy', $kamar->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Hapus Kamar {{ $kamar->nomor_kamar }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-nk-secondary py-1 px-2 small text-danger">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($kamars->hasPages())
                <div class="p-3 border-top">
                    {{ $kamars->links() }}
                </div>
            @endif
        @else
            <div class="p-5 text-center">
                <h2 class="h6 fw-bold mb-1">Belum Ada Unit Kamar Ditemukan</h2>
                <p class="text-secondary small mb-3">Tambahkan unit kamar baru untuk mulai menerima pengajuan sewa.</p>
                <a href="{{ route('pemilik.kelola-kamar.create') }}" class="btn-nk-primary small">
                    + Tambah Kamar Baru
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

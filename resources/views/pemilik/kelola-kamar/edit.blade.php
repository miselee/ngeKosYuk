@extends('layouts.app')

@section('title', 'Edit Kamar ' . $kamar->nomor_kamar)

@push('styles')
<style>
    .facility-chip-btn {
        font-size: 0.78rem;
        padding: 0.3rem 0.65rem;
        border-radius: 6px;
        border: 1px solid var(--nk-border-strong);
        background-color: var(--nk-bg);
        color: var(--nk-ink);
    }

    .facility-chip-btn:hover {
        border-color: var(--nk-blue);
        color: var(--nk-blue);
        background-color: var(--nk-blue-soft);
    }
</style>
@endpush

@section('content')
@include('pemilik.partials.navbar')

<div class="container py-4" style="max-width: 760px;">
    <div class="nk-page-header">
        <div>
            <h1 class="nk-page-title">Edit Kamar {{ $kamar->nomor_kamar }}</h1>
            <p class="nk-page-subtitle">Perbarui informasi properti, harga sewa, fasilitas, atau status ketersediaan unit kamar.</p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            @if($kamar->status === 'tersedia')
                <span class="nk-badge nk-badge-success">Tersedia</span>
            @else
                <span class="nk-badge nk-badge-info">Terisi</span>
            @endif
            <a href="{{ route('pemilik.kelola-kamar.index') }}" class="btn-nk-secondary">
                Kembali ke Kelola Kamar
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger py-2 px-3 small mb-4" role="alert">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pemilik.kelola-kamar.update', $kamar->id) }}" method="POST" class="nk-card p-4">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="kos_id" class="form-label">Properti Kos</label>
            <select id="kos_id" name="kos_id" class="form-select" required>
                @foreach($daftarKos as $kos)
                    <option value="{{ $kos->id }}" {{ old('kos_id', $kamar->kos_id) == $kos->id ? 'selected' : '' }}>
                        {{ $kos->nama_kos }} ({{ ucfirst($kos->tipe_kos) }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-sm-6">
                <label for="nomor_kamar" class="form-label">Nomor / Kode Kamar</label>
                <input type="text"
                       id="nomor_kamar"
                       name="nomor_kamar"
                       class="form-control font-mono"
                       value="{{ old('nomor_kamar', $kamar->nomor_kamar) }}"
                       required>
            </div>
            <div class="col-sm-6">
                <label for="status" class="form-label">Status Ketersediaan</label>
                <select id="status" name="status" class="form-select" required>
                    <option value="tersedia" {{ old('status', $kamar->status) === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="terisi" {{ old('status', $kamar->status) === 'terisi' ? 'selected' : '' }}>Terisi</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="harga_per_bulan" class="form-label">Harga Sewa per Bulan (Rp)</label>
            <input type="number"
                   id="harga_per_bulan"
                   name="harga_per_bulan"
                   class="form-control font-mono"
                   min="10000"
                   value="{{ old('harga_per_bulan', $kamar->harga_per_bulan) }}"
                   required>
        </div>

        <div class="mb-4">
            <label for="fasilitas" class="form-label">Fasilitas Kamar</label>
            <textarea id="fasilitas"
                      name="fasilitas"
                      rows="3"
                      class="form-control">{{ old('fasilitas', $kamar->fasilitas) }}</textarea>
            <div class="d-flex flex-wrap gap-1 mt-2">
                @foreach(['AC', 'Kamar Mandi Dalam', 'WiFi', 'Kasur', 'Lemari', 'Meja Belajar', 'Water Heater'] as $item)
                    <button type="button" class="facility-chip-btn" data-facility="{{ $item }}">+ {{ $item }}</button>
                @endforeach
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
            <a href="{{ route('pemilik.kelola-kamar.index') }}" class="btn-nk-secondary">Batal</a>
            <button type="submit" class="btn-nk-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const textarea = document.getElementById('fasilitas');
    document.querySelectorAll('.facility-chip-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const fac = btn.getAttribute('data-facility');
            const current = textarea.value.split(',').map(s => s.trim()).filter(Boolean);
            if (!current.includes(fac)) {
                current.push(fac);
                textarea.value = current.join(', ');
            }
        });
    });
});
</script>
@endpush

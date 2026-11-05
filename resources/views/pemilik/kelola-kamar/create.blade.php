@extends('layouts.app')

@section('title', 'Tambah Kamar Kos - ngeKosYuk')

@section('content')
@include('pemilik.partials.navbar')

<style>
    :root {
        --primary-blue: #2696D2;
        --dark-blue: #2468B8;
        --light-blue: #EAF4FF;
        --text-dark: #263747;
    }

    body {
        background-color: #f7f9fc;
        color: var(--text-dark);
        font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", sans-serif;
    }

    .form-container {
        max-width: 760px;
        margin: 30px auto 60px;
        padding: 0 15px;
    }

    .form-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e7ecf3;
        box-shadow: 0 4px 16px rgba(27, 79, 140, 0.05);
        padding: 32px;
    }

    .btn-brand-primary {
        background: #2468B8;
        color: white;
        font-weight: 600;
        border: none;
        padding: 10px 24px;
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .btn-brand-primary:hover {
        background: #1b4f8c;
        color: white;
    }

    .chip-btn {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        color: #475569;
        font-size: 12px;
        padding: 5px 12px;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.15s ease;
        user-select: none;
    }

    .chip-btn:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    .chip-btn.active {
        background: #2468B8;
        color: white;
        border-color: #2468B8;
    }
</style>

<div class="form-container">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-3 small">
            <li class="breadcrumb-item"><a href="{{ route('pemilik.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pemilik.kelola-kamar.index') }}" class="text-decoration-none text-muted">Kelola Kamar</a></li>
            <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Tambah Kamar</li>
        </ol>
    </nav>

    {{-- Error Banner --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                <strong class="text-danger">Terdapat kesalahan input:</strong>
            </div>
            <ul class="mb-0 ps-4 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="form-card">
        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
            <div class="rounded-circle bg-primary-subtle text-primary p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-door-open-fill fs-4"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-1 text-dark">Tambah Kamar Kos Baru</h4>
                <p class="text-muted small mb-0">Isi formulir di bawah ini untuk menambahkan unit kamar baru.</p>
            </div>
        </div>

        <form action="{{ route('pemilik.kelola-kamar.store') }}" method="POST">
            @csrf

            {{-- Pilihan Kos --}}
            <div class="mb-3">
                <label for="kos_id" class="form-label fw-semibold text-dark small">Pilih Lokasi Kos <span class="text-danger">*</span></label>
                <select name="kos_id" id="kos_id" class="form-select @error('kos_id') is-invalid @enderror" required>
                    <option value="" disabled {{ old('kos_id') ? '' : 'selected' }}>-- Pilih Kos --</option>
                    @foreach($daftarKos as $kos)
                        <option value="{{ $kos->id }}" {{ old('kos_id', request('kos_id')) == $kos->id ? 'selected' : '' }}>
                            {{ $kos->nama_kos }} (Tipe: {{ ucfirst($kos->tipe_kos) }})
                        </option>
                    @endforeach
                </select>
                @error('kos_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Nomor Kamar --}}
            <div class="mb-3">
                <label for="nomor_kamar" class="form-label fw-semibold text-dark small">Nomor / Nama Kamar <span class="text-danger">*</span></label>
                <input type="text" name="nomor_kamar" id="nomor_kamar" class="form-control @error('nomor_kamar') is-invalid @enderror" placeholder="Contoh: A-01, Kamar 102, Lt 2 - No 05" value="{{ old('nomor_kamar') }}" required>
                <div class="form-text small">Gunakan nomor atau kode yang mudah dikenali calon penyewa.</div>
                @error('nomor_kamar')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Harga per Bulan --}}
            <div class="mb-3">
                <label for="harga_per_bulan" class="form-label fw-semibold text-dark small">Tarif Sewa per Bulan <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light fw-bold text-secondary">Rp</span>
                    <input type="number" name="harga_per_bulan" id="harga_per_bulan" class="form-control @error('harga_per_bulan') is-invalid @enderror" placeholder="1000000" min="10000" step="50000" value="{{ old('harga_per_bulan') }}" required>
                    <span class="input-group-text bg-light text-muted small">/ bulan</span>
                </div>
                <div id="hargaPreview" class="form-text text-primary fw-semibold mt-1" style="font-size: 13px;"></div>
                @error('harga_per_bulan')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Status Awal --}}
            <div class="mb-4">
                <label class="form-label fw-semibold text-dark small d-block">Status Ketersediaan Awal <span class="text-danger">*</span></label>
                <div class="d-flex gap-3">
                    <div class="form-check form-check-inline border rounded-3 px-3 py-2 m-0 flex-fill">
                        <input class="form-check-input" type="radio" name="status" id="statusTersedia" value="tersedia" {{ old('status', 'tersedia') === 'tersedia' ? 'checked' : '' }}>
                        <label class="form-check-label fw-medium text-success" for="statusTersedia">
                            <i class="bi bi-check-circle-fill me-1"></i> Tersedia (Siap Huni)
                        </label>
                    </div>
                    <div class="form-check form-check-inline border rounded-3 px-3 py-2 m-0 flex-fill">
                        <input class="form-check-input" type="radio" name="status" id="statusTerisi" value="terisi" {{ old('status') === 'terisi' ? 'checked' : '' }}>
                        <label class="form-check-label fw-medium text-secondary" for="statusTerisi">
                            <i class="bi bi-dash-circle-fill me-1"></i> Terisi (Sudah Berpenghuni)
                        </label>
                    </div>
                </div>
                @error('status')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Fasilitas Kamar --}}
            <div class="mb-4">
                <label for="fasilitas" class="form-label fw-semibold text-dark small d-block mb-1">Fasilitas Kamar</label>
                <div class="mb-2">
                    <span class="text-muted small d-block mb-1">Klik untuk menambahkan fasilitas cepat:</span>
                    <div class="d-flex flex-wrap gap-1" id="presetChips">
                        <span class="chip-btn" data-val="Kasur"><i class="bi bi-plus"></i> Kasur</span>
                        <span class="chip-btn" data-val="Lemari"><i class="bi bi-plus"></i> Lemari</span>
                        <span class="chip-btn" data-val="AC"><i class="bi bi-plus"></i> AC</span>
                        <span class="chip-btn" data-val="Kamar Mandi Dalam"><i class="bi bi-plus"></i> Kamar Mandi Dalam</span>
                        <span class="chip-btn" data-val="WiFi"><i class="bi bi-plus"></i> WiFi</span>
                        <span class="chip-btn" data-val="Meja Belajar"><i class="bi bi-plus"></i> Meja Belajar</span>
                        <span class="chip-btn" data-val="Water Heater"><i class="bi bi-plus"></i> Water Heater</span>
                        <span class="chip-btn" data-val="TV"><i class="bi bi-plus"></i> TV</span>
                        <span class="chip-btn" data-val="Kipas Angin"><i class="bi bi-plus"></i> Kipas Angin</span>
                    </div>
                </div>
                <textarea name="fasilitas" id="fasilitas" rows="3" class="form-control @error('fasilitas') is-invalid @enderror" placeholder="Contoh: Kasur, Lemari, AC, Kamar Mandi Dalam, WiFi">{{ old('fasilitas') }}</textarea>
                <div class="form-text small">Pisahkan setiap fasilitas dengan tanda koma (,).</div>
                @error('fasilitas')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Buttons --}}
            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="{{ route('pemilik.kelola-kamar.index') }}" class="btn btn-outline-secondary px-4 rounded-3">Batal</a>
                <button type="submit" class="btn btn-brand-primary px-4 shadow-sm">
                    <i class="bi bi-save me-1"></i> Simpan Kamar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputHarga = document.getElementById('harga_per_bulan');
        const previewHarga = document.getElementById('hargaPreview');
        const textareaFasilitas = document.getElementById('fasilitas');
        const chips = document.querySelectorAll('#presetChips .chip-btn');

        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(angka);
        }

        function updatePreview() {
            const val = parseInt(inputHarga.value, 10);
            if (!isNaN(val) && val > 0) {
                previewHarga.textContent = 'Preview: ' + formatRupiah(val) + ' / bulan';
            } else {
                previewHarga.textContent = '';
            }
        }

        inputHarga.addEventListener('input', updatePreview);
        updatePreview();

        // Chip click handler
        chips.forEach(chip => {
            chip.addEventListener('click', function () {
                const item = this.getAttribute('data-val');
                let current = textareaFasilitas.value.split(',').map(s => s.trim()).filter(Boolean);

                const index = current.indexOf(item);
                if (index > -1) {
                    current.splice(index, 1);
                    this.classList.remove('active');
                } else {
                    current.push(item);
                    this.classList.add('active');
                }

                textareaFasilitas.value = current.join(', ');
            });
        });

        // Sync initial chips with textarea value
        function syncChips() {
            const current = textareaFasilitas.value.split(',').map(s => s.trim().toLowerCase());
            chips.forEach(chip => {
                const item = chip.getAttribute('data-val').toLowerCase();
                if (current.includes(item)) {
                    chip.classList.add('active');
                } else {
                    chip.classList.remove('active');
                }
            });
        }

        textareaFasilitas.addEventListener('input', syncChips);
        syncChips();
    });
</script>
@endsection


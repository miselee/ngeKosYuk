@extends('layouts.app')

@section('title', ($kos ? 'Edit' : 'Tambah') . ' Kos - ngeKosYuk')

@section('content')
@include('pemilik.partials.navbar')

<div class="container py-4" style="max-width: 800px;">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('pemilik.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pemilik.kelola-kos.index') }}">Kelola Kos</a></li>
            <li class="breadcrumb-item active">{{ $kos ? 'Edit Kos' : 'Tambah Kos' }}</li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 p-md-5">
            <h2 class="fw-bold mb-1">{{ $kos ? 'Edit Data Kos' : 'Tambah Kos Baru' }}</h2>
            <p class="text-muted mb-4">Lengkapi informasi kos yang akan ditampilkan kepada calon penyewa.</p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form action="{{ $kos ? route('pemilik.kelola-kos.update', $kos) : route('pemilik.kelola-kos.store') }}" method="POST">
                @csrf
                @if ($kos) @method('PUT') @endif

                <div class="mb-3">
                    <label for="nama_kos" class="form-label fw-semibold">Nama Kos <span class="text-danger">*</span></label>
                    <input id="nama_kos" name="nama_kos" type="text" class="form-control @error('nama_kos') is-invalid @enderror" value="{{ old('nama_kos', $kos?->nama_kos) }}" maxlength="255" required>
                    @error('nama_kos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="alamat" class="form-label fw-semibold">Alamat Lengkap <span class="text-danger">*</span></label>
                    <textarea id="alamat" name="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror" required>{{ old('alamat', $kos?->alamat) }}</textarea>
                    @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="tipe_kos" class="form-label fw-semibold">Tipe Kos <span class="text-danger">*</span></label>
                    <select id="tipe_kos" name="tipe_kos" class="form-select @error('tipe_kos') is-invalid @enderror" required>
                        <option value="">Pilih tipe kos</option>
                        @foreach (['putra' => 'Putra', 'putri' => 'Putri', 'campur' => 'Campur'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('tipe_kos', $kos?->tipe_kos) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('tipe_kos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $kos?->deskripsi) }}</textarea>
                    @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label for="foto" class="form-label fw-semibold">URL Foto</label>
                    <input id="foto" name="foto" type="url" class="form-control @error('foto') is-invalid @enderror" value="{{ old('foto', $kos?->foto) }}" maxlength="255" placeholder="https://...">
                    @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('pemilik.kelola-kos.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">{{ $kos ? 'Simpan Perubahan' : 'Tambah Kos' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

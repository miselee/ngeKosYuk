
@extends('layouts.app')

@section('title', 'Ajukan Sewa - ngeKosYuk')

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
        background: #f8fafe;
        color: var(--text-dark);
    }

    .form-page {
        max-width: 680px;
        margin: 0 auto;
        padding: 36px 20px 60px;
    }

    .page-title {
        font-size: 24px;
        font-weight: 800;
        color: var(--dark-blue);
        margin-bottom: 6px;
    }

    .page-subtitle {
        color: #6b7d8e;
        font-size: 14px;
        margin-bottom: 28px;
    }

    .summary-card {
        background: var(--light-blue);
        border: 1px solid #cbdcf3;
        border-radius: 14px;
        padding: 22px 24px;
        margin-bottom: 28px;
    }

    .summary-card .kos-name {
        font-weight: 700;
        font-size: 17px;
        color: var(--dark-blue);
    }

    .summary-card .detail-row {
        font-size: 14px;
        color: #4a5b6a;
        margin-top: 6px;
    }

    .summary-card .price {
        font-weight: 800;
        font-size: 20px;
        color: var(--dark-blue);
        margin-top: 10px;
    }

    .summary-card .price small {
        font-weight: 400;
        font-size: 13px;
        color: #6b7d8e;
    }

    .form-card {
        background: white;
        border: 1px solid #e5eaf0;
        border-radius: 14px;
        padding: 28px 26px;
    }

    .form-card label {
        font-weight: 600;
        font-size: 14px;
        color: var(--text-dark);
        margin-bottom: 6px;
    }

    .form-card .form-control {
        border: 1px solid #c7d4e1;
        border-radius: 9px;
        padding: 10px 14px;
        font-size: 14px;
    }

    .form-card .form-control:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 3px rgba(38,150,210,.12);
    }

    .btn-submit {
        background: var(--dark-blue);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 9px;
        font-weight: 700;
        font-size: 15px;
        transition: .2s;
    }

    .btn-submit:hover {
        background: var(--primary-blue);
        color: white;
    }

    .btn-back-link {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: var(--dark-blue);
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 22px;
    }

    .btn-back-link:hover {
        color: var(--primary-blue);
    }

    @media (max-width: 768px) {
        .form-page {
            padding: 24px 14px 40px;
        }

        .form-card {
            padding: 22px 18px;
        }
    }
</style>

<div class="form-page">

    <a href="{{ url('/penyewa/detail-kos/' . $kamar->kos->id) }}" class="btn-back-link">
        <i class="bi bi-arrow-left"></i> Kembali ke detail kos
    </a>

    <h1 class="page-title">Ajukan Sewa</h1>
    <p class="page-subtitle">Isi data berikut untuk mengajukan sewa kamar.</p>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="bi bi-exclamation-triangle-fill"></i> Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="summary-card">
        <div class="kos-name">
            <i class="bi bi-building"></i> {{ $kamar->kos->nama_kos }}
        </div>
        <div class="detail-row">
            <i class="bi bi-hash"></i> Kamar {{ $kamar->nomor_kamar }}
        </div>
        <div class="price">
            Rp {{ number_format($kamar->harga_per_bulan, 0, ',', '.') }}
            <small>/bulan</small>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ url('/penyewa/pengajuan-sewa') }}" method="POST">
            @csrf
            <input type="hidden" name="kamar_id" value="{{ $kamar->id }}">
            <input type="hidden" name="kos_id" value="{{ $kamar->kos->id }}">

            <div class="mb-3">
                <label for="tanggal_mulai">Tanggal Mulai Sewa</label>
                <input type="date"
                       id="tanggal_mulai"
                       name="tanggal_mulai"
                       class="form-control @error('tanggal_mulai') is-invalid @enderror"
                       value="{{ old('tanggal_mulai') }}"
                       required>
                @error('tanggal_mulai')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="tanggal_selesai">Tanggal Selesai Sewa</label>
                <input type="date"
                       id="tanggal_selesai"
                       name="tanggal_selesai"
                       class="form-control @error('tanggal_selesai') is-invalid @enderror"
                       value="{{ old('tanggal_selesai') }}"
                       required>
                @error('tanggal_selesai')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="catatan">Catatan <span class="text-muted fw-normal">(opsional)</span></label>
                <textarea id="catatan"
                          name="catatan"
                          class="form-control @error('catatan') is-invalid @enderror"
                          rows="3"
                          placeholder="Misalnya: saya butuh kasur tambahan, dst.">{{ old('catatan') }}</textarea>
                @error('catatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn-submit">
                    <i class="bi bi-send-fill"></i> Ajukan Sewa
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

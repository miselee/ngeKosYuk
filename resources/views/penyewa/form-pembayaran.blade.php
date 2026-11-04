
@extends('layouts.app')

@section('title', 'Pembayaran Sewa - ngeKosYuk')

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
        margin-top: 4px;
    }

    .summary-card .detail-row i {
        color: var(--primary-blue);
        margin-right: 3px;
    }

    .summary-card .total-due {
        font-weight: 800;
        font-size: 22px;
        color: var(--dark-blue);
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px dashed #b3cde8;
    }

    .summary-card .total-due small {
        font-weight: 400;
        font-size: 13px;
        color: #6b7d8e;
        display: block;
        margin-bottom: 2px;
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

    .form-card .form-control,
    .form-card .form-select {
        border: 1px solid #c7d4e1;
        border-radius: 9px;
        padding: 10px 14px;
        font-size: 14px;
    }

    .form-card .form-control:focus,
    .form-card .form-select:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 3px rgba(38,150,210,.12);
    }

    .upload-area {
        border: 2px dashed #c7d4e1;
        border-radius: 12px;
        padding: 28px 20px;
        text-align: center;
        cursor: pointer;
        transition: .2s;
        position: relative;
        background: #fcfcfd;
    }

    .upload-area:hover {
        border-color: var(--primary-blue);
        background: var(--light-blue);
    }

    .upload-area i {
        font-size: 32px;
        color: #8aaedb;
    }

    .upload-area p {
        font-size: 13px;
        color: #6b7d8e;
        margin: 8px 0 0;
    }

    .upload-area input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
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

    <a href="{{ url('/penyewa/riwayat-sewa') }}" class="btn-back-link">
        <i class="bi bi-arrow-left"></i> Kembali ke riwayat sewa
    </a>

    <h1 class="page-title">Pembayaran Sewa</h1>
    <p class="page-subtitle">Lengkapi form di bawah ini untuk memproses pembayaran.</p>

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
            <i class="bi bi-building"></i> {{ $sewa->kamar->kos->nama_kos }}
        </div>
        <div class="detail-row">
            <i class="bi bi-hash"></i> Kamar {{ $sewa->kamar->nomor_kamar }}
        </div>
        <div class="detail-row">
            <i class="bi bi-calendar3"></i>
            {{ \Carbon\Carbon::parse($sewa->tanggal_mulai)->format('d M Y') }}
            &mdash;
            {{ \Carbon\Carbon::parse($sewa->tanggal_selesai)->format('d M Y') }}
        </div>
        <div class="total-due">
            <small>Total yang harus dibayar</small>
            Rp {{ number_format($sewa->kamar->harga_per_bulan, 0, ',', '.') }}
        </div>
    </div>

    <div class="form-card">
        <form action="{{ url('/penyewa/pembayaran') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="sewa_id" value="{{ $sewa->id }}">
            <input type="hidden" name="tanggal_bayar" value="{{ date('Y-m-d') }}">

            <div class="mb-3">
                <label for="jumlah">Jumlah Pembayaran (Rp)</label>
                <input type="number"
                       id="jumlah"
                       name="jumlah"
                       class="form-control @error('jumlah') is-invalid @enderror"
                       value="{{ old('jumlah', $sewa->kamar->harga_per_bulan) }}"
                       min="1"
                       required>
                @error('jumlah')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="metode_pembayaran">Metode Pembayaran</label>
                <select id="metode_pembayaran"
                        name="metode_pembayaran"
                        class="form-select @error('metode_pembayaran') is-invalid @enderror"
                        required>
                    <option value="" disabled {{ old('metode_pembayaran') ? '' : 'selected' }}>
                        Pilih metode pembayaran
                    </option>
                    <option value="transfer_bank" {{ old('metode_pembayaran') === 'transfer_bank' ? 'selected' : '' }}>
                        Transfer Bank (BCA / Mandiri / BNI / BRI)
                    </option>
                    <option value="e_wallet" {{ old('metode_pembayaran') === 'e_wallet' ? 'selected' : '' }}>
                        E-Wallet (GoPay / OVO / Dana / ShopeePay)
                    </option>
                    <option value="tunai" {{ old('metode_pembayaran') === 'tunai' ? 'selected' : '' }}>
                        Tunai / Cash langsung ke Pemilik
                    </option>
                </select>
                @error('metode_pembayaran')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label>Bukti Pembayaran</label>
                <div class="upload-area" id="uploadArea">
                    <input type="file"
                           name="bukti_pembayaran"
                           id="bukti_pembayaran"
                           accept="image/*"
                           class="@error('bukti_pembayaran') is-invalid @enderror">
                    <i class="bi bi-cloud-arrow-up d-block"></i>
                    <p id="uploadLabel">Klik atau seret file gambar ke sini<br>
                        <small class="text-muted">Format: JPG, PNG, maks 2MB</small>
                    </p>
                </div>
                @error('bukti_pembayaran')
                    <div class="text-danger mt-1" style="font-size:13px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="keterangan">Keterangan <span class="text-muted fw-normal">(opsional)</span></label>
                <textarea id="keterangan"
                          name="keterangan"
                          class="form-control @error('keterangan') is-invalid @enderror"
                          rows="3"
                          placeholder="Catatan tambahan terkait pembayaran...">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn-submit">
                    <i class="bi bi-send-fill"></i> Kirim Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('bukti_pembayaran').addEventListener('change', function() {
        const label = document.getElementById('uploadLabel');
        if (this.files.length > 0) {
            label.innerHTML = '<strong>' + this.files[0].name + '</strong><br><small class="text-muted">Klik untuk ganti file</small>';
        }
    });
</script>

@endsection

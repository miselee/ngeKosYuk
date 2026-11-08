@extends('layouts.app')

@section('title', 'Formulir Pembayaran Kos')

@push('styles')
<style>
    .method-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.65rem;
    }

    @media (max-width: 576px) {
        .method-grid {
            grid-template-columns: 1fr;
        }
    }

    .method-option {
        position: relative;
    }

    .method-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .method-card {
        display: block;
        border: 1px solid var(--nk-border-strong);
        border-radius: 8px;
        padding: 0.75rem 0.9rem;
        background-color: var(--nk-surface);
        transition: all 0.15s ease;
    }

    .method-option input[type="radio"]:checked + .method-card {
        border-color: var(--nk-blue);
        background-color: var(--nk-blue-soft);
        box-shadow: inset 0 0 0 1px var(--nk-blue);
    }

    .preset-chip {
        padding: 0.35rem 0.75rem;
        font-size: 0.78rem;
        font-weight: 600;
        border-radius: 6px;
        border: 1px solid var(--nk-border-strong);
        background-color: var(--nk-bg);
        color: var(--nk-ink);
    }

    .preset-chip:hover {
        border-color: var(--nk-blue);
        color: var(--nk-blue);
        background-color: var(--nk-blue-soft);
    }

    .instruksi-box {
        background-color: var(--nk-bg);
        border: 1px solid var(--nk-border);
        border-left: 3px solid var(--nk-blue);
        border-radius: 8px;
        padding: 0.9rem 1.1rem;
    }
</style>
@endpush

@section('content')
@include('penyewa.partials.navbar')

<div class="container py-4" style="max-width: 980px;">
    <div class="mb-3">
        <a href="{{ url('/penyewa/riwayat-sewa') }}" class="small text-decoration-none fw-medium" style="color: var(--nk-muted);">
            &larr; Kembali ke Sewa Saya
        </a>
    </div>

    <div class="nk-page-header">
        <div>
            <h1 class="nk-page-title">Kirim Pembayaran Sewa</h1>
            <p class="nk-page-subtitle">
                {{ $sewa->kamar->kos->nama_kos }} (Kamar #{{ $sewa->kamar->nomor_kamar }}) &middot; Periode {{ \Carbon\Carbon::parse($sewa->tanggal_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($sewa->tanggal_selesai)->format('d M Y') }}
            </p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger py-2 px-3 small mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger py-2 px-3 small mb-4" role="alert">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        {{-- Left Payment Form --}}
        <div class="col-lg-7">
            <form action="{{ route('penyewa.pembayaran.store') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="nk-card p-4">
                @csrf
                <input type="hidden" name="sewa_id" value="{{ $sewa->id }}">

                <div class="mb-4">
                    <label class="form-label d-block">1. Pilih Metode Pembayaran</label>
                    @php $metodeOld = old('metode_pembayaran', 'transfer_bank'); @endphp
                    <div class="method-grid mb-3">
                        <div class="method-option">
                            <input type="radio" name="metode_pembayaran" id="metode_transfer_bank" value="transfer_bank" {{ $metodeOld === 'transfer_bank' ? 'checked' : '' }} required>
                            <label for="metode_transfer_bank" class="method-card">
                                <strong class="d-block small">Transfer Bank</strong>
                                <span class="text-secondary" style="font-size: 0.74rem;">BCA / Mandiri / BRI</span>
                            </label>
                        </div>
                        <div class="method-option">
                            <input type="radio" name="metode_pembayaran" id="metode_e_wallet" value="e_wallet" {{ $metodeOld === 'e_wallet' ? 'checked' : '' }}>
                            <label for="metode_e_wallet" class="method-card">
                                <strong class="d-block small">E-Wallet / QRIS</strong>
                                <span class="text-secondary" style="font-size: 0.74rem;">GoPay, OVO, Dana</span>
                            </label>
                        </div>
                        <div class="method-option">
                            <input type="radio" name="metode_pembayaran" id="metode_tunai" value="tunai" {{ $metodeOld === 'tunai' ? 'checked' : '' }}>
                            <label for="metode_tunai" class="method-card">
                                <strong class="d-block small">Bayar Tunai</strong>
                                <span class="text-secondary" style="font-size: 0.74rem;">Langsung ke pemilik</span>
                            </label>
                        </div>
                    </div>

                    <div class="instruksi-box small" id="instruksiContainer">
                        <div id="info_transfer_bank">
                            <strong class="d-block mb-1">Instruksi Transfer Bank:</strong>
                            <span class="text-secondary">
                                Silakan transfer ke rekening pemilik kos (<strong>{{ $sewa->kamar->kos->pemilik->name ?? 'Pemilik Kos' }}</strong>) dan unggah tangkapan layar bukti transfer di bawah ini.
                            </span>
                        </div>
                        <div id="info_e_wallet" class="d-none">
                            <strong class="d-block mb-1">Instruksi Pembayaran E-Wallet / QRIS:</strong>
                            <span class="text-secondary">
                                Pindai kode QRIS milik kos atau kirim ke nomor e-wallet pemilik (<strong>{{ $sewa->kamar->kos->pemilik->nomor_hp ?? '-' }}</strong>), lalu lampirkan bukti berhasil bayar.
                            </span>
                        </div>
                        <div id="info_tunai" class="d-none">
                            <strong class="d-block mb-1">Instruksi Pembayaran Tunai:</strong>
                            <span class="text-secondary">
                                Serahkan uang tunai langsung kepada pemilik kos dan unggah foto nota/tanda terima fisik sebagai arsip digital.
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-7">
                        <label for="jumlah" class="form-label">2. Nominal Pembayaran (Rp)</label>
                        <input type="number"
                               id="jumlah"
                               name="jumlah"
                               class="form-control font-mono"
                               min="1000"
                               value="{{ old('jumlah', $sisaTagihan > 0 ? $sisaTagihan : $hargaPerBulan) }}"
                               required>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <button type="button"
                                    class="preset-chip font-mono"
                                    data-amount="{{ $hargaPerBulan }}">
                                1 Bulan (Rp {{ number_format($hargaPerBulan, 0, ',', '.') }})
                            </button>
                            @if($sisaTagihan > 0 && $sisaTagihan != $hargaPerBulan)
                                <button type="button"
                                        class="preset-chip font-mono"
                                        data-amount="{{ $sisaTagihan }}">
                                    Lunasi Sisa (Rp {{ number_format($sisaTagihan, 0, ',', '.') }})
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="col-sm-5">
                        <label for="tanggal_bayar" class="form-label">Tanggal Pembayaran</label>
                        <input type="date"
                               id="tanggal_bayar"
                               name="tanggal_bayar"
                               class="form-control font-mono"
                               value="{{ old('tanggal_bayar', date('Y-m-d')) }}"
                               required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="bukti_pembayaran" class="form-label">3. Unggah Bukti Pembayaran (JPG / PNG / PDF, maks. 2MB)</label>
                    <input type="file"
                           id="bukti_pembayaran"
                           name="bukti_pembayaran"
                           class="form-control"
                           accept=".jpg,.jpeg,.png,.webp,.pdf">
                    <div id="previewBox" class="mt-2 d-none">
                        <img id="previewImg"
                             src=""
                             alt="Pratinjau bukti pembayaran"
                             width="240"
                             height="160"
                             class="rounded-2 border object-fit-cover">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="keterangan" class="form-label">Keterangan Pembayaran (Opsional)</label>
                    <input type="text"
                           id="keterangan"
                           name="keterangan"
                           class="form-control"
                           placeholder="Contoh: Pembayaran kos bulan pertama"
                           value="{{ old('keterangan') }}">
                </div>

                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ url('/penyewa/riwayat-sewa') }}" class="btn-nk-secondary">Batal</a>
                    <button type="submit" class="btn-nk-primary">Kirim Bukti Pembayaran</button>
                </div>
            </form>
        </div>

        {{-- Right Billing Breakdown --}}
        <div class="col-lg-5">
            <div class="nk-card p-4">
                <h2 class="nk-card-title mb-3">Ringkasan Tagihan Sewa</h2>

                <div class="small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Properti Kos</span>
                        <strong class="text-end">{{ $sewa->kamar->kos->nama_kos }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Unit Kamar</span>
                        <strong class="font-mono">Kamar #{{ $sewa->kamar->nomor_kamar }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Tarif Bulanan</span>
                        <strong class="font-mono">Rp {{ number_format($hargaPerBulan, 0, ',', '.') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Durasi Kontrak</span>
                        <strong class="font-mono">{{ $durasiBulan }} bulan</strong>
                    </div>
                </div>

                <div class="py-3 my-3 border-top border-bottom small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Total Nilai Kontrak</span>
                        <strong class="font-mono">Rp {{ number_format($totalKontrak, 0, ',', '.') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Sudah Terverifikasi (Lunas)</span>
                        <strong class="font-mono" style="color: var(--nk-teal);">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</strong>
                    </div>
                    @if($totalMenunggu > 0)
                        <div class="d-flex justify-content-between">
                            <span class="text-secondary">Menunggu Konfirmasi</span>
                            <strong class="font-mono">Rp {{ number_format($totalMenunggu, 0, ',', '.') }}</strong>
                        </div>
                    @endif
                </div>

                <div class="d-flex justify-content-between align-items-baseline">
                    <span class="small fw-semibold">Sisa Tagihan</span>
                    <strong class="font-mono fs-5" style="color: var(--nk-blue);">
                        Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                    </strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const amountInput = document.getElementById('jumlah');
    document.querySelectorAll('.preset-chip').forEach(function (btn) {
        btn.addEventListener('click', function () {
            amountInput.value = btn.getAttribute('data-amount');
        });
    });

    const radios = document.querySelectorAll('input[name="metode_pembayaran"]');
    const panels = {
        transfer_bank: document.getElementById('info_transfer_bank'),
        e_wallet: document.getElementById('info_e_wallet'),
        tunai: document.getElementById('info_tunai')
    };

    function syncMethodPanel() {
        const checked = document.querySelector('input[name="metode_pembayaran"]:checked');
        if (!checked) return;
        Object.keys(panels).forEach(function (key) {
            if (panels[key]) {
                panels[key].classList.toggle('d-none', key !== checked.value);
            }
        });
    }

    radios.forEach(function (r) {
        r.addEventListener('change', syncMethodPanel);
    });
    syncMethodPanel();

    const fileInput = document.getElementById('bukti_pembayaran');
    const previewBox = document.getElementById('previewBox');
    const previewImg = document.getElementById('previewImg');

    if (fileInput) {
        fileInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (ev) {
                    previewImg.src = ev.target.result;
                    previewBox.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                previewBox.classList.add('d-none');
            }
        });
    }
});
</script>
@endpush

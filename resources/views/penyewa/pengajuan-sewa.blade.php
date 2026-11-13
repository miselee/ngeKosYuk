@extends('layouts.app')

@section('title', 'Pengajuan Sewa Kamar')

@section('content')
@include('penyewa.partials.navbar')

<div class="container py-4" style="max-width: 960px;">
    <div class="nk-page-header">
        <div>
            <h1 class="nk-page-title">Formulir Pengajuan Sewa</h1>
            <p class="nk-page-subtitle">Tentukan periode mulai dan selesai sewa untuk Kamar {{ $kamar->nomor_kamar }}.</p>
        </div>
        <a href="{{ url('/penyewa/detail-kos/' . $kamar->kos_id) }}" class="btn-nk-secondary">
            Kembali ke Detail Kos
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger py-2 px-3 small mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger py-2 px-3 small mb-4" role="alert">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        {{-- Left Booking Form --}}
        <div class="col-lg-7">
            <form action="{{ url('/penyewa/pengajuan-sewa') }}" method="POST" class="nk-card p-4">
                @csrf
                <input type="hidden" name="kamar_id" value="{{ $kamar->id }}">
                <input type="hidden" name="kos_id" value="{{ $kamar->kos_id }}">

                <h2 class="nk-card-title mb-3">Periode Kontrak Sewa</h2>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai Masuk</label>
                        <input type="date"
                               id="tanggal_mulai"
                               name="tanggal_mulai"
                               class="form-control font-mono"
                               value="{{ old('tanggal_mulai', date('Y-m-d')) }}"
                               required>
                    </div>
                    <div class="col-sm-6">
                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai Sewa</label>
                        <input type="date"
                               id="tanggal_selesai"
                               name="tanggal_selesai"
                               class="form-control font-mono"
                               value="{{ old('tanggal_selesai', date('Y-m-d', strtotime('+1 month'))) }}"
                               required>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mb-4">
                    <span class="small text-secondary align-self-center">Durasi cepat:</span>
                    <button type="button" class="btn-nk-secondary py-1 px-2 small durasi-preset" data-months="1">1 Bulan</button>
                    <button type="button" class="btn-nk-secondary py-1 px-2 small durasi-preset" data-months="3">3 Bulan</button>
                    <button type="button" class="btn-nk-secondary py-1 px-2 small durasi-preset" data-months="6">6 Bulan</button>
                    <button type="button" class="btn-nk-secondary py-1 px-2 small durasi-preset" data-months="12">12 Bulan</button>
                </div>

                <div class="mb-4">
                    <label for="catatan" class="form-label">Catatan untuk Pemilik Kos (Opsional)</label>
                    <textarea id="catatan"
                              name="catatan"
                              rows="3"
                              class="form-control"
                              placeholder="Contoh: Rencana pindahan hari Sabtu pagi, membawa kendaraan motor.">{{ old('catatan') }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ url('/penyewa/detail-kos/' . $kamar->kos_id) }}" class="btn-nk-secondary">
                        Batal
                    </a>
                    <button type="submit" class="btn-nk-primary">
                        Kirim Pengajuan Sewa
                    </button>
                </div>
            </form>
        </div>

        {{-- Right Unit & Cost Summary --}}
        <div class="col-lg-5">
            <div class="nk-card p-4">
                <span class="nk-badge nk-badge-info mb-2">Ringkasan Unit</span>
                <h3 class="h6 fw-bold mb-1">{{ $kamar->kos->nama_kos }}</h3>
                <p class="small text-secondary mb-3">{{ $kamar->kos->alamat }}</p>

                <div class="py-3 border-top border-bottom mb-3">
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-secondary">Nomor Kamar</span>
                        <strong class="font-mono">Kamar {{ $kamar->nomor_kamar }}</strong>
                    </div>
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-secondary">Harga per Bulan</span>
                        <strong class="font-mono">Rp {{ number_format($kamar->harga_per_bulan, 0, ',', '.') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-secondary">Estimasi Durasi</span>
                        <strong class="font-mono" id="estimasiDurasiLabel">1 bulan</strong>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-baseline mb-2">
                    <span class="small fw-semibold">Estimasi Total Kontrak</span>
                    <strong class="font-mono fs-5" style="color: var(--nk-blue);" id="estimasiTotalLabel">
                        Rp {{ number_format($kamar->harga_per_bulan, 0, ',', '.') }}
                    </strong>
                </div>
                <p class="text-secondary mb-0" style="font-size: 0.76rem;">
                    Pembayaran dilakukan setelah pengajuan sewa Anda disetujui oleh pemilik kos.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const hargaPerBulan = {{ (int) $kamar->harga_per_bulan }};
    const startInput = document.getElementById('tanggal_mulai');
    const endInput = document.getElementById('tanggal_selesai');
    const durasiLabel = document.getElementById('estimasiDurasiLabel');
    const totalLabel = document.getElementById('estimasiTotalLabel');

    function updateEstimation() {
        if (!startInput.value || !endInput.value) return;
        const start = new Date(startInput.value);
        const end = new Date(endInput.value);
        const diffDays = Math.max(1, Math.round((end - start) / (1000 * 60 * 60 * 24)));
        const months = Math.max(1, Math.ceil(diffDays / 30));
        const total = months * hargaPerBulan;

        durasiLabel.textContent = months + ' bulan (' + diffDays + ' hari)';
        totalLabel.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    startInput.addEventListener('change', updateEstimation);
    endInput.addEventListener('change', updateEstimation);

    document.querySelectorAll('.durasi-preset').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const months = parseInt(btn.getAttribute('data-months'), 10);
            const baseDate = startInput.value ? new Date(startInput.value) : new Date();
            baseDate.setMonth(baseDate.getMonth() + months);
            endInput.value = baseDate.toISOString().split('T')[0];
            updateEstimation();
        });
    });

    updateEstimation();
});
</script>
@endpush

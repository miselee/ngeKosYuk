@extends('layouts.app')

@section('title', 'Tulis Ulasan Kos')

@push('styles')
<style>
    .rating-picker {
        display: flex;
        gap: 0.5rem;
    }

    .rating-item {
        flex: 1;
        position: relative;
    }

    .rating-item input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .rating-item label {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 0.65rem 0.5rem;
        border: 1px solid var(--nk-border-strong);
        border-radius: 8px;
        background-color: var(--nk-surface);
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--nk-ink);
        transition: all 0.15s ease;
    }

    .rating-item input[type="radio"]:checked + label {
        border-color: var(--nk-blue);
        background-color: var(--nk-blue-soft);
        color: var(--nk-blue);
        box-shadow: inset 0 0 0 1px var(--nk-blue);
    }
</style>
@endpush

@section('content')
@include('penyewa.partials.navbar')

<div class="container py-4" style="max-width: 680px;">
    <div class="mb-3">
        <a href="{{ url('/penyewa/riwayat-sewa') }}" class="small text-decoration-none fw-medium" style="color: var(--nk-muted);">
            &larr; Kembali ke Sewa Saya
        </a>
    </div>

    <div class="nk-page-header">
        <div>
            <h1 class="nk-page-title">Beri Ulasan: {{ $sewa->kamar->kos->nama_kos }}</h1>
            <p class="nk-page-subtitle">
                Kamar #{{ $sewa->kamar->nomor_kamar }} &middot; Periode {{ \Carbon\Carbon::parse($sewa->tanggal_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($sewa->tanggal_selesai)->format('d M Y') }}
            </p>
        </div>
    </div>

    <form action="{{ url('/penyewa/ulasan') }}" method="POST" class="nk-card p-4">
        @csrf
        <input type="hidden" name="sewa_id" value="{{ $sewa->id }}">
        <input type="hidden" name="kos_id" value="{{ $sewa->kamar->kos_id }}">

        <div class="mb-4">
            <label class="form-label d-block">Penilaian Keseluruhan (1 s/d 5)</label>
            <div class="rating-picker">
                @php
                    $labels = [1 => 'Kurang', 2 => 'Cukup', 3 => 'Baik', 4 => 'Nyaman', 5 => 'Sangat Puas'];
                @endphp
                @foreach($labels as $score => $text)
                    <div class="rating-item">
                        <input type="radio"
                               name="rating"
                               id="rating_{{ $score }}"
                               value="{{ $score }}"
                               {{ old('rating', 5) == $score ? 'checked' : '' }}
                               required>
                        <label for="rating_{{ $score }}">
                            <span class="font-mono fs-5">&#9733; {{ $score }}</span>
                            <span class="text-secondary fw-normal" style="font-size: 0.72rem;">{{ $text }}</span>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mb-4">
            <label for="komentar" class="form-label">Ulasan Tertulis</label>
            <textarea id="komentar"
                      name="komentar"
                      rows="4"
                      class="form-control"
                      placeholder="Ceritakan kondisi kebersihan kamar, keamanan lingkungan, dan keramahan pemilik kos..."
                      required>{{ old('komentar') }}</textarea>
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
            <a href="{{ url('/penyewa/riwayat-sewa') }}" class="btn-nk-secondary">Batal</a>
            <button type="submit" class="btn-nk-primary">Kirim Ulasan</button>
        </div>
    </form>
</div>
@endsection

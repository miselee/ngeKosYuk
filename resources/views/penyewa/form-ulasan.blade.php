
@extends('layouts.app')

@section('title', 'Beri Ulasan - ngeKosYuk')

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
        max-width: 620px;
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

    /* Star rating */
    .star-rating {
        display: flex;
        gap: 6px;
        padding: 8px 0 4px;
    }

    .star-rating .star-btn {
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
        font-size: 32px;
        color: #d1d9e0;
        transition: color .15s, transform .15s;
        line-height: 1;
    }

    .star-rating .star-btn:hover {
        transform: scale(1.15);
    }

    .star-rating .star-btn.active {
        color: #f0c040;
    }

    .star-rating .star-btn:hover,
    .star-rating .star-btn:hover ~ .star-btn {
        color: #d1d9e0;
    }

    .star-rating:hover .star-btn {
        color: #f0c040;
    }

    .star-rating .star-btn:hover ~ .star-btn {
        color: #d1d9e0 !important;
    }

    .star-label {
        font-size: 13px;
        color: #6b7d8e;
        min-height: 20px;
        margin-top: 4px;
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

        .star-rating .star-btn {
            font-size: 28px;
        }
    }
</style>

<div class="form-page">

    <a href="{{ url('/penyewa/riwayat-sewa') }}" class="btn-back-link">
        <i class="bi bi-arrow-left"></i> Kembali ke riwayat sewa
    </a>

    <h1 class="page-title">Beri Ulasan</h1>
    <p class="page-subtitle">Bagikan pengalaman Anda selama tinggal di kos ini.</p>

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
    </div>

    <div class="form-card">
        <form action="{{ url('/penyewa/ulasan') }}" method="POST">
            @csrf
            <input type="hidden" name="sewa_id" value="{{ $sewa->id }}">
            <input type="hidden" name="kos_id" value="{{ $sewa->kamar->kos->id }}">
            <input type="hidden" name="rating" id="ratingInput" value="{{ old('rating', 0) }}">

            <div class="mb-4">
                <label>Rating</label>
                <div class="star-rating" id="starRating">
                    <button type="button" class="star-btn" data-value="1">
                        <i class="bi bi-star-fill"></i>
                    </button>
                    <button type="button" class="star-btn" data-value="2">
                        <i class="bi bi-star-fill"></i>
                    </button>
                    <button type="button" class="star-btn" data-value="3">
                        <i class="bi bi-star-fill"></i>
                    </button>
                    <button type="button" class="star-btn" data-value="4">
                        <i class="bi bi-star-fill"></i>
                    </button>
                    <button type="button" class="star-btn" data-value="5">
                        <i class="bi bi-star-fill"></i>
                    </button>
                </div>
                <div class="star-label" id="starLabel"></div>
                @error('rating')
                    <div class="text-danger" style="font-size:13px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="komentar">Komentar</label>
                <textarea id="komentar"
                          name="komentar"
                          class="form-control @error('komentar') is-invalid @enderror"
                          rows="4"
                          placeholder="Ceritakan pengalaman Anda tinggal di kos ini..."
                          required>{{ old('komentar') }}</textarea>
                @error('komentar')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn-submit">
                    <i class="bi bi-send-fill"></i> Kirim Ulasan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    (function() {
        const container = document.getElementById('starRating');
        const input = document.getElementById('ratingInput');
        const label = document.getElementById('starLabel');
        const buttons = container.querySelectorAll('.star-btn');

        const labels = {
            1: 'Buruk',
            2: 'Kurang',
            3: 'Cukup',
            4: 'Baik',
            5: 'Sangat Baik'
        };

        function setRating(value) {
            input.value = value;
            buttons.forEach(function(btn) {
                const v = parseInt(btn.getAttribute('data-value'));
                if (v <= value) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
            label.textContent = labels[value] || '';
        }

        buttons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                setRating(parseInt(this.getAttribute('data-value')));
            });
        });

        // Restore old value
        var oldVal = parseInt(input.value);
        if (oldVal > 0) {
            setRating(oldVal);
        }
    })();
</script>

@endsection

@extends('layouts.app')

@section('title', 'Ulasan Kos - ngeKosYuk')

@section('content')
@include('pemilik.partials.navbar')

<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Ulasan Kos</h2>
            <p class="text-muted mb-0">Ulasan dari penyewa untuk kos yang Anda kelola.</p>
        </div>
        <div class="card border-0 shadow-sm px-3 py-2">
            <span class="small text-muted">Rata-rata rating</span>
            <strong><i class="bi bi-star-fill text-warning me-1"></i>{{ $rataRataRating ? number_format($rataRataRating, 1) . ' / 5' : 'Belum ada rating' }}</strong>
        </div>
    </div>

    <form method="GET" action="{{ route('pemilik.ulasan.index') }}" class="card card-body border-0 shadow-sm mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label for="kos_id" class="form-label">Kos</label>
                <select id="kos_id" name="kos_id" class="form-select">
                    <option value="">Semua kos</option>
                    @foreach ($daftarKos as $item)
                        <option value="{{ $item->id }}" @selected(request('kos_id') == $item->id)>{{ $item->nama_kos }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="rating" class="form-label">Rating</label>
                <select id="rating" name="rating" class="form-select">
                    <option value="">Semua rating</option>
                    @foreach (range(5, 1) as $rating)
                        <option value="{{ $rating }}" @selected(request('rating') == $rating)>{{ $rating }} bintang</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary flex-fill" type="submit">Filter</button>
                <a class="btn btn-outline-secondary" href="{{ route('pemilik.ulasan.index') }}">Reset</a>
            </div>
        </div>
    </form>

    @forelse ($ulasans as $ulasan)
        <article class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-2">
                    <div>
                        <h5 class="fw-bold mb-1">{{ $ulasan->kos->nama_kos }}</h5>
                        <span class="text-muted small">Oleh {{ $ulasan->penyewa->name }} · {{ $ulasan->created_at->format('d M Y') }}</span>
                    </div>
                    <span class="text-warning text-nowrap" aria-label="Rating {{ $ulasan->rating }} dari 5">
                        @for ($star = 1; $star <= 5; $star++)
                            <i class="bi {{ $star <= $ulasan->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                        @endfor
                        <span class="text-dark ms-1">{{ $ulasan->rating }}/5</span>
                    </span>
                </div>
                <p class="mb-0">{{ $ulasan->komentar }}</p>
            </div>
        </article>
    @empty
        <div class="card border-0 shadow-sm text-center p-5">
            <i class="bi bi-chat-square-heart display-4 text-muted mb-3"></i>
            <h5 class="fw-bold">Belum ada ulasan</h5>
            <p class="text-muted mb-0">Ulasan penyewa akan tampil di sini setelah mereka mengirimkannya.</p>
        </div>
    @endforelse

    <div class="mt-3">{{ $ulasans->links() }}</div>
</div>
@endsection

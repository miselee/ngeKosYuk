@extends('layouts.app')

@section('title', 'Kelola Kos - ngeKosYuk')

@section('content')
@include('pemilik.partials.navbar')

<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Kelola Kos</h2>
            <p class="text-muted mb-0">Atur informasi dan daftar properti kos Anda.</p>
        </div>
        <a href="{{ route('pemilik.kelola-kos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tambah Kos
        </a>
    </div>

    @foreach (['success' => 'success', 'error' => 'danger'] as $key => $type)
        @if (session($key))
            <div class="alert alert-{{ $type }} alert-dismissible fade show" role="alert">
                {{ session($key) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    @endforeach

    @if ($kos->isEmpty())
        <div class="card border-0 shadow-sm text-center p-5">
            <i class="bi bi-buildings display-4 text-muted mb-3"></i>
            <h5 class="fw-bold">Belum ada kos terdaftar</h5>
            <p class="text-muted">Tambahkan kos untuk mulai mengelola kamar dan penyewaan.</p>
            <a href="{{ route('pemilik.kelola-kos.create') }}" class="btn btn-primary align-self-center">Tambah Kos</a>
        </div>
    @else
        <div class="row g-3">
            @foreach ($kos as $item)
                <div class="col-md-6 col-xl-4">
                    <article class="card h-100 border-0 shadow-sm">
                        @if ($item->foto)
                            <img src="{{ $item->foto }}" class="card-img-top" alt="Foto {{ $item->nama_kos }}" style="height: 190px; object-fit: cover;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center text-muted" style="height: 190px;">
                                <i class="bi bi-building display-4"></i>
                            </div>
                        @endif
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between gap-2">
                                <h5 class="card-title fw-bold">{{ $item->nama_kos }}</h5>
                                <span class="badge text-bg-primary align-self-start">{{ ucfirst($item->tipe_kos) }}</span>
                            </div>
                            <p class="text-muted small mb-3"><i class="bi bi-geo-alt me-1"></i>{{ $item->alamat }}</p>
                            <p class="small text-muted">{{ $item->deskripsi ?: 'Belum ada deskripsi.' }}</p>
                            <div class="d-flex gap-3 small text-muted mt-auto mb-3">
                                <span><i class="bi bi-door-open me-1"></i>{{ $item->kamars_count }} kamar</span>
                                <span><i class="bi bi-star-fill text-warning me-1"></i>{{ $item->ulasans_avg_rating ? number_format($item->ulasans_avg_rating, 1) : 'Belum ada rating' }}</span>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('pemilik.kelola-kos.edit', $item) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </a>
                                <form action="{{ route('pemilik.kelola-kos.destroy', $item) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kos ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $kos->links() }}</div>
    @endif
</div>
@endsection

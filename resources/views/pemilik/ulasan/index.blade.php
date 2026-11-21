@extends('layouts.app')

@section('title', 'Ulasan & Rating Kos - ngeKosYuk')

@section('content')
@include('pemilik.partials.navbar')

<div class="container py-4">
    {{-- Page Header --}}
    <div class="nk-page-header">
        <div>
            <h1 class="nk-page-title">Ulasan &amp; Rating Kos</h1>
            <p class="nk-page-subtitle">Pantau kepuasan penghuni, penilaian bintang, dan masukan pengalaman sewa dari para penyewa kos Anda.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('pemilik.dashboard') }}" class="btn-nk-secondary">
                Dashboard
            </a>
            <a href="{{ route('pemilik.kelola-kamar.index') }}" class="btn-nk-secondary">
                Kelola Kamar
            </a>
            <a href="{{ route('pemilik.penyewaan.index') }}" class="btn-nk-primary">
                Daftar Penyewaan
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2 px-3 small mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    {{-- Metric Ledger Strip --}}
    <div class="nk-metric-strip mb-4">
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Rata-rata Rating</span>
            <div class="nk-metric-value" style="color: #b45309;">
                @if($totalUlasan > 0)
                    ★ {{ $rataRataRating }}
                @else
                    <span class="text-secondary">-</span>
                @endif
            </div>
            <div class="nk-metric-sub">Skala 1.0 sampai 5.0 bintang</div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Total Ulasan Masuk</span>
            <div class="nk-metric-value">{{ $totalUlasan }}</div>
            <div class="nk-metric-sub">Dari seluruh penyewa kos</div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Ulasan Positif (★ 4-5)</span>
            @php
                $positifCount = ($bintangCount[5] ?? 0) + ($bintangCount[4] ?? 0);
                $positifPct = $totalUlasan > 0 ? round(($positifCount / $totalUlasan) * 100) : 0;
            @endphp
            <div class="nk-metric-value" style="color: var(--nk-teal);">
                {{ $positifCount }}
            </div>
            <div class="nk-metric-sub"><span class="font-mono">{{ $positifPct }}%</span> tingkat kepuasan</div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Perlu Perhatian (★ 1-2)</span>
            @php
                $negatifCount = ($bintangCount[1] ?? 0) + ($bintangCount[2] ?? 0);
            @endphp
            <div class="nk-metric-value" style="color: {{ $negatifCount > 0 ? 'var(--nk-danger, #b91c1c)' : 'var(--nk-text)' }};">
                {{ $negatifCount }}
            </div>
            <div class="nk-metric-sub">Ulasan untuk evaluasi fasilitas</div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="nk-card p-3 mb-4">
        <form method="GET" action="{{ route('pemilik.ulasan.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label for="q" class="form-label small fw-semibold text-secondary mb-1">Cari Ulasan / Penyewa</label>
                    <input type="text" id="q" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Ketik kata kunci atau nama penyewa...">
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label for="kos_id" class="form-label small fw-semibold text-secondary mb-1">Properti Kos</label>
                    <select id="kos_id" name="kos_id" class="form-select form-select-sm">
                        <option value="">Semua Properti Kos</option>
                        @foreach($daftarKos as $kos)
                            <option value="{{ $kos->id }}" {{ request('kos_id') == $kos->id ? 'selected' : '' }}>
                                {{ $kos->nama_kos }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label for="rating" class="form-label small fw-semibold text-secondary mb-1">Filter Bintang</label>
                    <select id="rating" name="rating" class="form-select form-select-sm">
                        <option value="">Semua Rating Bintang</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>★ ★ ★ ★ ★ (5 Bintang)</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>★ ★ ★ ★ (4 Bintang)</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>★ ★ ★ (3 Bintang)</option>
                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>★ ★ (2 Bintang)</option>
                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>★ (1 Bintang)</option>
                    </select>
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn-nk-primary py-1 px-3 small w-100">
                        Filter
                    </button>
                    @if(request()->hasAny(['q', 'kos_id', 'rating']))
                        <a href="{{ route('pemilik.ulasan.index') }}" class="btn-nk-secondary py-1 px-2 small text-center" title="Reset Filter">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="row g-4">
        {{-- Left: Reviews List --}}
        <div class="col-lg-8">
            <div class="nk-card overflow-hidden">
                <div class="nk-card-header">
                    <h2 class="nk-card-title">Daftar Komentar &amp; Masukan Penghuni</h2>
                    <span class="small font-mono text-secondary">
                        Menampilkan {{ $ulasans->total() }} ulasan
                    </span>
                </div>

                @if($ulasans->count() > 0)
                    <div class="p-3 d-flex flex-column gap-3">
                        @foreach($ulasans as $ulasan)
                            @php
                                $initials = collect(explode(' ', trim($ulasan->penyewa->name ?? 'Penyewa')))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('') ?: 'P';
                            @endphp
                            <div class="p-3 rounded-2 border" style="background-color: var(--nk-bg);">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2 flex-wrap">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white small fw-bold font-mono"
                                             style="width: 34px; height: 34px; background-color: var(--nk-blue); flex-shrink: 0;">
                                            {{ $initials }}
                                        </div>
                                        <div>
                                            <strong class="small d-block text-dark">{{ $ulasan->penyewa->name ?? 'Penyewa' }}</strong>
                                            <span class="small text-secondary font-mono" style="font-size: 11px;">
                                                {{ $ulasan->kos->nama_kos ?? '-' }} &middot; Kamar {{ $ulasan->sewa->kamar->nomor_kamar ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-warning small" title="{{ $ulasan->rating }} dari 5 bintang">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $ulasan->rating)
                                                    ★
                                                @else
                                                    <span class="text-muted opacity-50">★</span>
                                                @endif
                                            @endfor
                                            <span class="fw-bold font-mono text-dark ms-1" style="font-size: 12px;">{{ $ulasan->rating }}.0</span>
                                        </div>
                                        <span class="small text-secondary font-mono" style="font-size: 11px;">
                                            {{ $ulasan->created_at ? $ulasan->created_at->format('d M Y') : '-' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="p-2.5 rounded bg-white border small text-secondary mt-2" style="white-space: pre-line; line-height: 1.5;">
                                    &ldquo;{{ $ulasan->komentar }}&rdquo;
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($ulasans->hasPages())
                        <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <span class="small text-secondary">
                                Menampilkan {{ $ulasans->firstItem() }} - {{ $ulasans->lastItem() }} dari {{ $ulasans->total() }} ulasan
                            </span>
                            <div>
                                {{ $ulasans->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    @endif
                @else
                    <div class="p-5 text-center text-secondary">
                        @if(request()->hasAny(['q', 'kos_id', 'rating']))
                            <p class="mb-2">Tidak ditemukan ulasan yang cocok dengan kriteria filter.</p>
                            <a href="{{ route('pemilik.ulasan.index') }}" class="btn-nk-secondary py-1 px-3 small">
                                Bersihkan Filter
                            </a>
                        @else
                            <p class="mb-1 fw-bold text-dark">Belum Ada Ulasan</p>
                            <p class="small text-secondary mb-0">Ulasan dari penyewa akan otomatis muncul di sini setelah mereka menyewa kamar dan memberikan penilaian.</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Right: Breakdown & Rating Stats --}}
        <div class="col-lg-4">
            {{-- Distribution Breakdown Card --}}
            <div class="nk-card mb-4">
                <div class="nk-card-header">
                    <h2 class="nk-card-title">Distribusi Bintang</h2>
                    <span class="small font-mono text-secondary">{{ $totalUlasan }} ulasan</span>
                </div>
                <div class="p-3 d-flex flex-column gap-2">
                    @for($bintang = 5; $bintang >= 1; $bintang--)
                        @php
                            $count = $bintangCount[$bintang] ?? 0;
                            $pct = $totalUlasan > 0 ? round(($count / $totalUlasan) * 100) : 0;
                        @endphp
                        <a href="{{ route('pemilik.ulasan.index', array_merge(request()->except('rating', 'page'), ['rating' => $bintang])) }}" 
                           class="text-decoration-none text-dark d-flex align-items-center gap-2 small">
                            <span class="font-mono text-warning text-nowrap" style="width: 70px;">
                                {{ $bintang }} ★
                            </span>
                            <div class="progress flex-grow-1" style="height: 8px;">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: {{ $pct }}%; background-color: {{ $bintang >= 4 ? 'var(--nk-teal)' : ($bintang == 3 ? '#d97706' : '#dc2626') }};" 
                                     aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="font-mono text-secondary small text-end" style="width: 45px;">
                                {{ $count }}
                            </span>
                        </a>
                    @endfor
                </div>
            </div>

            {{-- Property Summary Card --}}
            <div class="nk-card">
                <div class="nk-card-header">
                    <h2 class="nk-card-title">Ringkasan per Properti</h2>
                    <span class="small font-mono text-secondary">{{ $daftarKos->count() }} kos</span>
                </div>
                <div class="p-3 d-flex flex-column gap-2">
                    @forelse($daftarKos as $kos)
                        @php
                            $kosUlasanCount = $kos->ulasans()->count();
                            $kosRating = $kosUlasanCount > 0 ? round($kos->ulasans()->avg('rating'), 1) : 0;
                        @endphp
                        <div class="p-2.5 rounded border" style="background-color: var(--nk-bg);">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                <strong class="small text-truncate" style="max-width: 180px;">{{ $kos->nama_kos }}</strong>
                                <span class="small font-mono text-warning fw-bold">
                                    @if($kosUlasanCount > 0)
                                        ★ {{ $kosRating }}
                                    @else
                                        <span class="text-secondary">-</span>
                                    @endif
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center small text-secondary font-mono" style="font-size: 11px;">
                                <span>{{ $kosUlasanCount }} ulasan</span>
                                <a href="{{ route('pemilik.ulasan.index', ['kos_id' => $kos->id]) }}" class="text-decoration-none" style="color: var(--nk-blue);">
                                    Lihat &rarr;
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-3 text-center text-secondary small">
                            Belum ada properti kos terdaftar.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


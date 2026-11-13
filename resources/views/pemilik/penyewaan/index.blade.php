@extends('layouts.app')

@section('title', 'Daftar Penyewaan Kos')

@section('content')
@include('pemilik.partials.navbar')

<div class="container py-4">
    <div class="nk-page-header">
        <div>
            <h1 class="nk-page-title">Daftar Penyewaan Kos</h1>
            <p class="nk-page-subtitle">Kelola permohonan sewa kamar masuk, kontrak aktif penghuni, serta penyelesaian masa sewa.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('pemilik.kelola-kamar.index') }}" class="btn-nk-secondary">
                Kelola Kamar
            </a>
            <a href="{{ route('pemilik.pembayaran.index') }}" class="btn-nk-primary">
                Konfirmasi Pembayaran
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2 px-3 small mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger py-2 px-3 small mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    {{-- Metric Ledger Strip --}}
    <div class="nk-metric-strip mb-4">
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Total Pengajuan</span>
            <div class="nk-metric-value">{{ $totalSewa }}</div>
            <div class="nk-metric-sub">Seluruh riwayat kontrak</div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Menunggu Persetujuan</span>
            <div class="nk-metric-value">{{ $totalPending }}</div>
            <div class="nk-metric-sub">Perlu tindakan pemilik</div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Kontrak Aktif (Disetujui)</span>
            <div class="nk-metric-value" style="color: var(--nk-teal);">{{ $totalDisetujui }}</div>
            <div class="nk-metric-sub">Sedang menghuni kamar</div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Selesai / Ditolak</span>
            <div class="nk-metric-value">{{ $totalSelesai + $totalDitolak }}</div>
            <div class="nk-metric-sub">{{ $totalSelesai }} selesai &middot; {{ $totalDitolak }} ditolak</div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="nk-card p-3 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="nk-tabs">
                <a href="{{ route('pemilik.penyewaan.index') }}"
                   class="nk-tab {{ !request('status') ? 'active' : '' }}">
                    Semua ({{ $totalSewa }})
                </a>
                <a href="{{ route('pemilik.penyewaan.index', ['status' => 'pending']) }}"
                   class="nk-tab {{ request('status') === 'pending' ? 'active' : '' }}">
                    Menunggu ({{ $totalPending }})
                </a>
                <a href="{{ route('pemilik.penyewaan.index', ['status' => 'disetujui']) }}"
                   class="nk-tab {{ request('status') === 'disetujui' ? 'active' : '' }}">
                    Disetujui ({{ $totalDisetujui }})
                </a>
                <a href="{{ route('pemilik.penyewaan.index', ['status' => 'selesai']) }}"
                   class="nk-tab {{ request('status') === 'selesai' ? 'active' : '' }}">
                    Selesai ({{ $totalSelesai }})
                </a>
                <a href="{{ route('pemilik.penyewaan.index', ['status' => 'ditolak']) }}"
                   class="nk-tab {{ request('status') === 'ditolak' ? 'active' : '' }}">
                    Ditolak ({{ $totalDitolak }})
                </a>
            </div>

            <form action="{{ route('pemilik.penyewaan.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <select name="kos_id" class="form-select form-select-sm" style="width: auto;" aria-label="Filter Kos">
                    <option value="">Semua Kos</option>
                    @foreach($daftarKos as $kos)
                        <option value="{{ $kos->id }}" {{ request('kos_id') == $kos->id ? 'selected' : '' }}>
                            {{ $kos->nama_kos }}
                        </option>
                    @endforeach
                </select>
                <input type="text"
                       name="q"
                       class="form-control form-control-sm"
                       style="width: 190px;"
                       placeholder="Cari nama penyewa..."
                       aria-label="Cari nama penyewa"
                       value="{{ request('q') }}">
                <button type="submit" class="btn-nk-primary py-1 px-3 small">Cari</button>
            </form>
        </div>
    </div>

    {{-- Rental Applications Table --}}
    <div class="nk-card overflow-hidden">
        @if($sewas->count() > 0)
            <div class="table-responsive">
                <table class="nk-table">
                    <thead>
                        <tr>
                            <th>Penyewa</th>
                            <th>Properti &amp; Kamar</th>
                            <th>Periode Sewa</th>
                            <th>Status Sewa</th>
                            <th class="text-end">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sewas as $sewa)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $sewa->penyewa->name }}</div>
                                    <div class="small text-secondary font-mono">{{ $sewa->penyewa->nomor_hp ?? $sewa->penyewa->email }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $sewa->kamar->kos->nama_kos }}</div>
                                    <div class="small text-secondary font-mono">
                                        Kamar {{ $sewa->kamar->nomor_kamar }} &middot; Rp {{ number_format($sewa->kamar->harga_per_bulan, 0, ',', '.') }}/bln
                                    </div>
                                </td>
                                <td class="font-mono small">
                                    {{ \Carbon\Carbon::parse($sewa->tanggal_mulai)->format('d M Y') }} s/d
                                    {{ \Carbon\Carbon::parse($sewa->tanggal_selesai)->format('d M Y') }}
                                    @if($sewa->catatan)
                                        <div class="text-secondary mt-1" style="font-family: var(--bs-body-font-family);">
                                            Catatan: {{ $sewa->catatan }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($sewa->status === 'pending')
                                        <span class="nk-badge nk-badge-warning">Menunggu</span>
                                    @elseif($sewa->status === 'disetujui')
                                        <span class="nk-badge nk-badge-success">Disetujui</span>
                                    @elseif($sewa->status === 'ditolak')
                                        <span class="nk-badge nk-badge-danger">Ditolak</span>
                                    @else
                                        <span class="nk-badge nk-badge-neutral">Selesai</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex flex-wrap justify-content-end gap-1">
                                        @if($sewa->status === 'pending')
                                            <form action="{{ route('pemilik.penyewaan.setujui', $sewa->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn-nk-primary py-1 px-2 small">
                                                    Setujui
                                                </button>
                                            </form>
                                            <button type="button"
                                                    class="btn-nk-secondary py-1 px-2 small text-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalTolakSewa{{ $sewa->id }}">
                                                Tolak
                                            </button>
                                        @elseif($sewa->status === 'disetujui')
                                            <form action="{{ route('pemilik.penyewaan.selesai', $sewa->id) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Selesaikan masa sewa untuk {{ $sewa->penyewa->name }}? Status kamar akan kembali Tersedia.')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn-nk-secondary py-1 px-2 small">
                                                    Tandai Selesai
                                                </button>
                                            </form>
                                        @else
                                            <span class="small text-secondary">-</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            @if($sewa->status === 'pending')
                            @push('modals')
                                <div class="modal fade" id="modalTolakSewa{{ $sewa->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form action="{{ route('pemilik.penyewaan.tolak', $sewa->id) }}" method="POST" class="modal-content">
                                            @csrf
                                            @method('PATCH')
                                            <div class="modal-header">
                                                <h2 class="modal-title h6 fw-bold">Tolak Pengajuan Sewa</h2>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="small text-secondary mb-3">
                                                    Anda akan menolak pengajuan sewa dari <strong>{{ $sewa->penyewa->name }}</strong> untuk Kamar {{ $sewa->kamar->nomor_kamar }}.
                                                </p>
                                                <label for="alasan_penolakan_{{ $sewa->id }}" class="form-label">Alasan Penolakan (Opsional)</label>
                                                <textarea id="alasan_penolakan_{{ $sewa->id }}"
                                                          name="alasan_penolakan"
                                                          rows="2"
                                                          class="form-control"
                                                          placeholder="Contoh: Kamar sedang dalam perbaikan saluran air."></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn-nk-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn-nk-primary" style="background-color: var(--nk-rose-ink); border-color: var(--nk-rose-ink);">
                                                    Konfirmasi Tolak
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endpush
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($sewas->hasPages())
                <div class="p-3 border-top">
                    {{ $sewas->links() }}
                </div>
            @endif
        @else
            <div class="p-5 text-center text-secondary small">
                Belum ada data pengajuan penyewaan kos yang sesuai dengan saringan.
            </div>
        @endif
    </div>
</div>
@endsection

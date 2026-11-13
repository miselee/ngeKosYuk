@extends('layouts.app')

@section('title', 'Konfirmasi Pembayaran')

@section('content')
@include('pemilik.partials.navbar')

<div class="container py-4">
    <div class="nk-page-header">
        <div>
            <h1 class="nk-page-title">Konfirmasi Pembayaran</h1>
            <p class="nk-page-subtitle">Periksa bukti pembayaran masuk dari penyewa, verifikasi status lunas, dan cetak kwitansi resmi.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('pemilik.penyewaan.index') }}" class="btn-nk-secondary">
                Daftar Penyewaan
            </a>
            <a href="{{ route('pemilik.dashboard') }}" class="btn-nk-secondary">
                Ringkasan
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
            <span class="nk-metric-label">Total Transaksi</span>
            <div class="nk-metric-value">{{ $totalPembayaran }}</div>
            <div class="nk-metric-sub">{{ $totalDiverifikasi }} diverifikasi &middot; {{ $totalDitolak }} ditolak</div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Menunggu Konfirmasi</span>
            <div class="nk-metric-value">{{ $totalPending }}</div>
            <div class="nk-metric-sub">Perlu pemeriksaan bukti</div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Total Pendapatan Terverifikasi</span>
            <div class="nk-metric-value" style="color: var(--nk-teal);">
                Rp {{ number_format($totalNominalMasuk, 0, ',', '.') }}
            </div>
            <div class="nk-metric-sub">Pembayaran terverifikasi lunas</div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="nk-card p-3 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="nk-tabs">
                <a href="{{ route('pemilik.pembayaran.index') }}"
                   class="nk-tab {{ !request('status') ? 'active' : '' }}">
                    Semua ({{ $totalPembayaran }})
                </a>
                <a href="{{ route('pemilik.pembayaran.index', ['status' => 'pending']) }}"
                   class="nk-tab {{ request('status') === 'pending' ? 'active' : '' }}">
                    Pending ({{ $totalPending }})
                </a>
                <a href="{{ route('pemilik.pembayaran.index', ['status' => 'diverifikasi']) }}"
                   class="nk-tab {{ request('status') === 'diverifikasi' ? 'active' : '' }}">
                    Diverifikasi ({{ $totalDiverifikasi }})
                </a>
                <a href="{{ route('pemilik.pembayaran.index', ['status' => 'ditolak']) }}"
                   class="nk-tab {{ request('status') === 'ditolak' ? 'active' : '' }}">
                    Ditolak ({{ $totalDitolak }})
                </a>
            </div>

            <form action="{{ route('pemilik.pembayaran.index') }}" method="GET" class="d-flex flex-wrap gap-2">
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
                <select name="metode" class="form-select form-select-sm" style="width: auto;" aria-label="Filter Metode">
                    <option value="">Semua Metode</option>
                    <option value="transfer_bank" {{ request('metode') === 'transfer_bank' ? 'selected' : '' }}>Transfer Bank</option>
                    <option value="e_wallet" {{ request('metode') === 'e_wallet' ? 'selected' : '' }}>E-Wallet / QRIS</option>
                    <option value="tunai" {{ request('metode') === 'tunai' ? 'selected' : '' }}>Tunai</option>
                </select>
                <input type="text"
                       name="q"
                       class="form-control form-control-sm"
                       style="width: 180px;"
                       placeholder="Cari penyewa / ket..."
                       aria-label="Cari penyewa atau keterangan"
                       value="{{ request('q') }}">
                <button type="submit" class="btn-nk-primary py-1 px-3 small">Filter</button>
            </form>
        </div>
    </div>

    {{-- Payments Table --}}
    <div class="nk-card overflow-hidden">
        @if($pembayarans->count() > 0)
            <div class="table-responsive">
                <table class="nk-table">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Penyewa</th>
                            <th>Kos &amp; Kamar</th>
                            <th>Metode &amp; Tanggal</th>
                            <th>Nominal</th>
                            <th>Bukti</th>
                            <th>Status</th>
                            <th class="text-end">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pembayarans as $bayar)
                            <tr>
                                <td class="font-mono fw-semibold">{{ $bayar->kode_invoice }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $bayar->sewa->penyewa->name }}</div>
                                    <div class="small text-secondary font-mono">{{ $bayar->sewa->penyewa->nomor_hp ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $bayar->sewa->kamar->kos->nama_kos }}</div>
                                    <div class="small text-secondary font-mono">Kamar {{ $bayar->sewa->kamar->nomor_kamar }}</div>
                                </td>
                                <td>
                                    <span class="nk-badge nk-badge-neutral mb-1">{{ $bayar->label_metode }}</span>
                                    <div class="small text-secondary font-mono">
                                        {{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d M Y') }}
                                    </div>
                                    @if($bayar->keterangan)
                                        <div class="small text-secondary mt-1" style="font-size: 0.75rem;">
                                            {{ $bayar->keterangan }}
                                        </div>
                                    @endif
                                </td>
                                <td class="font-mono fw-bold">
                                    Rp {{ number_format($bayar->jumlah, 0, ',', '.') }}
                                </td>
                                <td>
                                    @if($bayar->bukti_pembayaran)
                                        <a href="{{ asset('storage/' . $bayar->bukti_pembayaran) }}"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="btn-nk-secondary py-1 px-2 small">
                                            Lihat Bukti
                                        </a>
                                    @else
                                        <span class="small text-secondary">Tanpa lampiran</span>
                                    @endif
                                </td>
                                <td>
                                    @if($bayar->status === 'diverifikasi')
                                        <span class="nk-badge nk-badge-success">Diverifikasi</span>
                                    @elseif($bayar->status === 'pending')
                                        <span class="nk-badge nk-badge-warning">Pending</span>
                                    @else
                                        <span class="nk-badge nk-badge-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex flex-wrap justify-content-end gap-1">
                                        <a href="{{ route('pemilik.pembayaran.kwitansi', $bayar->id) }}"
                                           class="btn-nk-secondary py-1 px-2 small">
                                            Kwitansi
                                        </a>

                                        @if($bayar->status === 'pending')
                                            <form action="{{ route('pemilik.pembayaran.verifikasi', $bayar->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn-nk-primary py-1 px-2 small">
                                                    Verifikasi Lunas
                                                </button>
                                            </form>
                                            <button type="button"
                                                    class="btn-nk-secondary py-1 px-2 small text-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalTolakBayar{{ $bayar->id }}">
                                                Tolak
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            @if($bayar->status === 'pending')
                            @push('modals')
                                <div class="modal fade" id="modalTolakBayar{{ $bayar->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form action="{{ route('pemilik.pembayaran.tolak', $bayar->id) }}" method="POST" class="modal-content">
                                            @csrf
                                            @method('PATCH')
                                            <div class="modal-header">
                                                <h2 class="modal-title h6 fw-bold">Tolak Bukti Pembayaran</h2>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="small text-secondary mb-3">
                                                    Tolak pembayaran sebesar <strong class="font-mono">Rp {{ number_format($bayar->jumlah, 0, ',', '.') }}</strong> dari <strong>{{ $bayar->sewa->penyewa->name }}</strong>?
                                                </p>
                                                <label for="alasan_penolakan_bayar_{{ $bayar->id }}" class="form-label">Alasan Penolakan (Opsional)</label>
                                                <textarea id="alasan_penolakan_bayar_{{ $bayar->id }}"
                                                          name="alasan_penolakan"
                                                          rows="2"
                                                          class="form-control"
                                                          placeholder="Contoh: Nominal bukti transfer tidak sesuai atau gambar buram."></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn-nk-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn-nk-primary" style="background-color: var(--nk-rose-ink); border-color: var(--nk-rose-ink);">
                                                    Tolak Pembayaran
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

            @if($pembayarans->hasPages())
                <div class="p-3 border-top">
                    {{ $pembayarans->links() }}
                </div>
            @endif
        @else
            <div class="p-5 text-center text-secondary small">
                Belum ada transaksi pembayaran yang sesuai dengan filter.
            </div>
        @endif
    </div>
</div>
@endsection

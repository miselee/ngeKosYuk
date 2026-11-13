@extends('layouts.app')

@section('title', 'Monitoring Sewa & Pembayaran - Admin')

@section('content')
@include('admin.partials.navbar')

<div class="container py-4">
    <div class="nk-page-header">
        <div>
            <h1 class="nk-page-title">Monitoring Sewa, Pembayaran &amp; Ulasan</h1>
            <p class="nk-page-subtitle">Audit kontrak sewa penghuni, verifikasi bukti pembayaran lintas kos, serta moderasi ulasan properti.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.kos.index') }}" class="btn-nk-secondary">
                Monitoring Properti
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn-nk-secondary">
                Ringkasan Admin
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2 px-3 small mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="nk-metric-strip mb-4">
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Total Kontrak Sewa</span>
            <div class="nk-metric-value">{{ $totalSewa }}</div>
            <div class="nk-metric-sub">
                <span class="font-mono">{{ $sewaAktif }}</span> disetujui &middot; <span class="font-mono">{{ $sewaPending }}</span> menunggu
            </div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Total Transaksi Bayar</span>
            <div class="nk-metric-value">{{ $totalPembayaran }}</div>
            <div class="nk-metric-sub">
                <span class="font-mono">{{ $bayarPending }}</span> menunggu konfirmasi
            </div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Total Pembayaran Terverifikasi</span>
            <div class="nk-metric-value" style="color: var(--nk-teal);">
                Rp {{ number_format($totalNominalLunas, 0, ',', '.') }}
            </div>
            <div class="nk-metric-sub">Akumulasi pembayaran sah</div>
        </div>
    </div>

    <div class="nk-card p-3 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="nk-tabs">
                <a href="{{ route('admin.transaksi.index', ['tab' => 'sewa']) }}"
                   class="nk-tab {{ $tab === 'sewa' ? 'active' : '' }}">
                    Kontrak Sewa ({{ $totalSewa }})
                </a>
                <a href="{{ route('admin.transaksi.index', ['tab' => 'pembayaran']) }}"
                   class="nk-tab {{ $tab === 'pembayaran' ? 'active' : '' }}">
                    Pembayaran ({{ $totalPembayaran }})
                </a>
                <a href="{{ route('admin.transaksi.index', ['tab' => 'ulasan']) }}"
                   class="nk-tab {{ $tab === 'ulasan' ? 'active' : '' }}">
                    Moderasi Ulasan ({{ $totalUlasan ?? $ulasans?->total() ?? 0 }})
                </a>
            </div>

            @if($tab !== 'ulasan')
                <form action="{{ route('admin.transaksi.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <select name="status" class="form-select form-select-sm" style="width: auto;" aria-label="Filter Status">
                        <option value="">Semua Status</option>
                        @if($tab === 'sewa')
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                            <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        @else
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="diverifikasi" {{ request('status') === 'diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                            <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        @endif
                    </select>
                    <input type="text"
                           name="q"
                           class="form-control form-control-sm"
                           style="width: 210px;"
                           placeholder="Cari penyewa atau kos..."
                           aria-label="Cari transaksi"
                           value="{{ request('q') }}">
                    <button type="submit" class="btn-nk-primary py-1 px-3 small">Filter</button>
                </form>
            @endif
        </div>
    </div>

    @if($tab === 'sewa')
        <div class="nk-card overflow-hidden">
            @if($sewas->count() > 0)
                <div class="table-responsive">
                    <table class="nk-table">
                        <thead>
                            <tr>
                                <th>Penyewa</th>
                                <th>Properti Kos &amp; Kamar</th>
                                <th>Mitra Pemilik</th>
                                <th>Periode Kontrak</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sewas as $sewa)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $sewa->penyewa->name ?? '-' }}</div>
                                        <div class="small text-secondary font-mono">{{ $sewa->penyewa->nomor_hp ?? $sewa->penyewa->email ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $sewa->kamar->kos->nama_kos ?? '-' }}</div>
                                        <div class="small text-secondary font-mono">
                                            Kamar {{ $sewa->kamar->nomor_kamar ?? '-' }} &middot; Rp {{ number_format($sewa->kamar->harga_per_bulan ?? 0, 0, ',', '.') }}/bln
                                        </div>
                                    </td>
                                    <td class="small">
                                        {{ $sewa->kamar->kos->pemilik->name ?? '-' }}
                                    </td>
                                    <td class="font-mono small">
                                        {{ \Carbon\Carbon::parse($sewa->tanggal_mulai)->format('d M Y') }} s/d
                                        {{ \Carbon\Carbon::parse($sewa->tanggal_selesai)->format('d M Y') }}
                                    </td>
                                    <td>
                                        @if($sewa->status === 'disetujui')
                                            <span class="nk-badge nk-badge-success">Disetujui</span>
                                        @elseif($sewa->status === 'pending')
                                            <span class="nk-badge nk-badge-warning">Menunggu</span>
                                        @elseif($sewa->status === 'ditolak')
                                            <span class="nk-badge nk-badge-danger">Ditolak</span>
                                        @else
                                            <span class="nk-badge nk-badge-neutral">Selesai</span>
                                        @endif
                                    </td>
                                </tr>
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
                    Tidak ada data kontrak sewa yang sesuai dengan filter.
                </div>
            @endif
        </div>
    @elseif($tab === 'pembayaran')
        <div class="nk-card overflow-hidden">
            @if($pembayarans->count() > 0)
                <div class="table-responsive">
                    <table class="nk-table">
                        <thead>
                            <tr>
                                <th>No. Invoice</th>
                                <th>Penyewa</th>
                                <th>Properti &amp; Kamar</th>
                                <th>Metode &amp; Tanggal</th>
                                <th>Nominal</th>
                                <th>Status</th>
                                <th class="text-end">Dokumen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pembayarans as $bayar)
                                <tr>
                                    <td class="font-mono fw-semibold">{{ $bayar->kode_invoice }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $bayar->sewa->penyewa->name ?? '-' }}</div>
                                        <div class="small text-secondary">{{ $bayar->sewa->penyewa->email ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $bayar->sewa->kamar->kos->nama_kos ?? '-' }}</div>
                                        <div class="small text-secondary font-mono">Kamar {{ $bayar->sewa->kamar->nomor_kamar ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <span class="nk-badge nk-badge-neutral mb-1">{{ $bayar->label_metode }}</span>
                                        <div class="small text-secondary font-mono">
                                            {{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d M Y') }}
                                        </div>
                                    </td>
                                    <td class="font-mono fw-bold">
                                        Rp {{ number_format($bayar->jumlah, 0, ',', '.') }}
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
                                        <a href="{{ route('admin.pembayaran.kwitansi', $bayar->id) }}" class="btn-nk-secondary py-1 px-2 small">
                                            Kwitansi
                                        </a>
                                    </td>
                                </tr>
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
                    Tidak ada transaksi pembayaran yang sesuai dengan filter.
                </div>
            @endif
        </div>
    @else
        <div class="nk-card overflow-hidden">
            @if($ulasans->count() > 0)
                <div class="table-responsive">
                    <table class="nk-table">
                        <thead>
                            <tr>
                                <th>Penyewa</th>
                                <th>Properti Kos</th>
                                <th>Rating</th>
                                <th>Komentar Ulasan</th>
                                <th>Tanggal</th>
                                <th class="text-end">Moderasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ulasans as $ulasan)
                                <tr>
                                    <td class="fw-semibold">{{ $ulasan->penyewa->name ?? '-' }}</td>
                                    <td>{{ $ulasan->kos->nama_kos ?? '-' }}</td>
                                    <td>
                                        <span class="nk-badge nk-badge-warning font-mono">
                                            &#9733; {{ $ulasan->rating }} / 5
                                        </span>
                                    </td>
                                    <td class="small text-secondary">{{ $ulasan->komentar }}</td>
                                    <td class="font-mono small text-secondary">
                                        {{ $ulasan->created_at ? $ulasan->created_at->format('d M Y') : '-' }}
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.ulasan.destroy', $ulasan->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Hapus ulasan dari {{ $ulasan->penyewa->name ?? 'penyewa' }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-nk-secondary py-1 px-2 small text-danger">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($ulasans->hasPages())
                    <div class="p-3 border-top">
                        {{ $ulasans->links() }}
                    </div>
                @endif
            @else
                <div class="p-5 text-center text-secondary small">
                    Belum ada ulasan penghuni yang tercatat di sistem.
                </div>
            @endif
        </div>
    @endif
</div>
@endsection


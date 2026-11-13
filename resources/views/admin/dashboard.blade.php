@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
@include('admin.partials.navbar')

<div class="container py-4">
    <div class="nk-page-header">
        <div>
            <h1 class="nk-page-title">Dashboard Admin</h1>
            <p class="nk-page-subtitle">
                Pusat kendali ekosistem ngeKosYuk: pantau pertumbuhan pengguna, okupansi properti kos, dan arus transaksi sewa lintas mitra.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('profile.edit') }}" class="btn-nk-secondary">
                Edit Profil
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn-nk-secondary">
                Kelola Pengguna
            </a>
            <a href="{{ route('admin.transaksi.index') }}" class="btn-nk-primary">
                Audit Transaksi
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2 px-3 small mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if($sewaPending > 0 || $pembayaranPending > 0)
        <div class="row g-3 mb-4">
            @if($sewaPending > 0)
                <div class="col-md-6">
                    <div class="nk-banner">
                        <div>
                            <strong class="d-block small">{{ $sewaPending }} Pengajuan Sewa Menunggu Konfirmasi Mitra</strong>
                            <span class="small text-secondary">Terdapat kontrak baru yang sedang menunggu persetujuan pemilik kos.</span>
                        </div>
                        <a href="{{ route('admin.transaksi.index', ['tab' => 'sewa', 'status' => 'pending']) }}" class="btn-nk-secondary py-1 px-3 small">
                            Lihat Sewa
                        </a>
                    </div>
                </div>
            @endif

            @if($pembayaranPending > 0)
                <div class="col-md-6">
                    <div class="nk-banner" style="border-left-color: var(--nk-teal);">
                        <div>
                            <strong class="d-block small">{{ $pembayaranPending }} Bukti Pembayaran Menunggu Verifikasi</strong>
                            <span class="small text-secondary">Transaksi pembayaran penyewa sedang dalam antrean pemeriksaan pemilik.</span>
                        </div>
                        <a href="{{ route('admin.transaksi.index', ['tab' => 'pembayaran', 'status' => 'pending']) }}" class="btn-nk-teal py-1 px-3 small">
                            Lihat Pembayaran
                        </a>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <div class="nk-metric-strip mb-4">
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Total Akun Pengguna</span>
            <div class="nk-metric-value">{{ $totalUsers }}</div>
            <div class="nk-metric-sub">
                <span class="font-mono">{{ $totalPemilik }}</span> pemilik &middot; <span class="font-mono">{{ $totalPenyewa }}</span> penyewa
            </div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Properti &amp; Unit Kamar</span>
            <div class="nk-metric-value">{{ $totalKos }} <span class="fs-6 fw-normal text-secondary">kos</span></div>
            <div class="nk-metric-sub">
                <span class="font-mono">{{ $totalKamar }}</span> kamar (<span class="font-mono">{{ $kamarTersedia }}</span> tersedia, <span class="font-mono">{{ $kamarTerisi }}</span> terisi)
            </div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Kontrak Sewa Aktif</span>
            <div class="nk-metric-value" style="color: var(--nk-blue);">{{ $sewaAktif }}</div>
            <div class="nk-metric-sub">
                <span class="font-mono">{{ $sewaPending }}</span> pengajuan menunggu
            </div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Volume Transaksi Terverifikasi</span>
            <div class="nk-metric-value" style="color: var(--nk-teal);">
                Rp {{ number_format($totalPendapatanPlatform, 0, ',', '.') }}
            </div>
            <div class="nk-metric-sub">
                <span class="font-mono">{{ $pembayaranPending }}</span> bukti bayar pending
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="nk-card overflow-hidden mb-4">
                <div class="nk-card-header">
                    <h2 class="nk-card-title">Aktivitas Pembayaran Terbaru</h2>
                    <a href="{{ route('admin.transaksi.index', ['tab' => 'pembayaran']) }}" class="small fw-semibold text-decoration-none" style="color: var(--nk-blue);">
                        Semua Pembayaran
                    </a>
                </div>

                @if($pembayaranTerbaru->count() > 0)
                    <div class="table-responsive">
                        <table class="nk-table">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Penyewa &amp; Kos</th>
                                    <th>Nominal</th>
                                    <th>Status</th>
                                    <th class="text-end">Kwitansi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pembayaranTerbaru as $bayar)
                                    <tr>
                                        <td class="font-mono fw-semibold">{{ $bayar->kode_invoice }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $bayar->sewa->penyewa->name ?? '-' }}</div>
                                            <div class="small text-secondary">
                                                {{ $bayar->sewa->kamar->kos->nama_kos ?? '-' }} &middot; <span class="font-mono">Kamar {{ $bayar->sewa->kamar->nomor_kamar ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="font-mono fw-semibold">
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
                                                Lihat
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-4 text-center text-secondary small">
                        Belum ada aktivitas pembayaran di platform.
                    </div>
                @endif
            </div>

            <div class="nk-card overflow-hidden">
                <div class="nk-card-header">
                    <h2 class="nk-card-title">Pengajuan &amp; Kontrak Sewa Terbaru</h2>
                    <a href="{{ route('admin.transaksi.index', ['tab' => 'sewa']) }}" class="small fw-semibold text-decoration-none" style="color: var(--nk-blue);">
                        Semua Kontrak
                    </a>
                </div>

                @if($sewaTerbaru->count() > 0)
                    <div class="table-responsive">
                        <table class="nk-table">
                            <thead>
                                <tr>
                                    <th>Penyewa</th>
                                    <th>Properti &amp; Kamar</th>
                                    <th>Periode</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sewaTerbaru as $sewa)
                                    <tr>
                                        <td class="fw-semibold">{{ $sewa->penyewa->name ?? '-' }}</td>
                                        <td>
                                            <div>{{ $sewa->kamar->kos->nama_kos ?? '-' }}</div>
                                            <div class="small text-secondary font-mono">Kamar {{ $sewa->kamar->nomor_kamar ?? '-' }}</div>
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
                @else
                    <div class="p-4 text-center text-secondary small">
                        Belum ada data pengajuan sewa kamar.
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-5">
            <div class="nk-card overflow-hidden">
                <div class="nk-card-header">
                    <h2 class="nk-card-title">Pengguna Baru Terdaftar</h2>
                    <a href="{{ route('admin.users.index') }}" class="small fw-semibold text-decoration-none" style="color: var(--nk-blue);">
                        Kelola Pengguna
                    </a>
                </div>
                <div class="p-3 d-flex flex-column gap-2">
                    @forelse($userTerbaru as $u)
                        <div class="p-3 rounded-2 border d-flex justify-content-between align-items-center gap-2" style="background-color: var(--nk-bg);">
                            <div>
                                <strong class="d-block small">{{ $u->name }}</strong>
                                <span class="small text-secondary">{{ $u->email }}</span>
                            </div>
                            @if($u->role === 'admin')
                                <span class="nk-badge nk-badge-info">Admin</span>
                            @elseif($u->role === 'pemilik')
                                <span class="nk-badge nk-badge-teal">Pemilik</span>
                            @else
                                <span class="nk-badge nk-badge-neutral">Penyewa</span>
                            @endif
                        </div>
                    @empty
                        <div class="p-3 text-center text-secondary small">
                            Belum ada akun pengguna terdaftar.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

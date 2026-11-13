@extends('layouts.app')

@section('title', 'Ringkasan Pemilik Kos')

@section('content')
@include('pemilik.partials.navbar')

<div class="container py-4">
    <div class="nk-page-header">
        <div>
            <h1 class="nk-page-title">Halo, {{ Auth::user()->name }}</h1>
            <p class="nk-page-subtitle">Ringkasan operasional properti kos, tingkat hunian kamar, dan antrean verifikasi penyewa.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('profile.edit') }}" class="btn-nk-secondary">
                Edit Profil
            </a>
            <button type="button" class="btn-nk-secondary" data-bs-toggle="modal" data-bs-target="#modalTambahKos">
                + Tambah Properti Kos
            </button>
            <a href="{{ route('pemilik.kelola-kamar.create') }}" class="btn-nk-primary">
                + Tambah Unit Kamar
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2 px-3 small mb-4" role="alert">
            {{ session('success') }}
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

    {{-- Actionable Notification Banners --}}
    @if($sewaPendingCount > 0 || $pembayaranPendingCount > 0)
        <div class="row g-3 mb-4">
            @if($sewaPendingCount > 0)
                <div class="col-md-6">
                    <div class="nk-banner">
                        <div>
                            <strong class="d-block small">{{ $sewaPendingCount }} Pengajuan Sewa Menunggu Persetujuan</strong>
                            <span class="small text-secondary">Segera tinjau agar calon penyewa dapat melanjutkan pembayaran.</span>
                        </div>
                        <a href="{{ route('pemilik.penyewaan.index', ['status' => 'pending']) }}" class="btn-nk-primary py-1 px-3 small">
                            Tinjau Sewa
                        </a>
                    </div>
                </div>
            @endif

            @if($pembayaranPendingCount > 0)
                <div class="col-md-6">
                    <div class="nk-banner" style="border-left-color: var(--nk-teal);">
                        <div>
                            <strong class="d-block small">{{ $pembayaranPendingCount }} Bukti Pembayaran Perlu Diverifikasi</strong>
                            <span class="small text-secondary">Periksa bukti transfer penyewa dan terbitkan kwitansi lunas.</span>
                        </div>
                        <a href="{{ route('pemilik.pembayaran.index', ['status' => 'pending']) }}" class="btn-nk-teal py-1 px-3 small">
                            Periksa Bukti
                        </a>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Unified Metric Ledger Strip --}}
    <div class="nk-metric-strip mb-4">
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Total Properti Kos</span>
            <div class="nk-metric-value">{{ $totalKos }}</div>
            <div class="nk-metric-sub">Properti terdaftar</div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Total Kamar</span>
            <div class="nk-metric-value">{{ $totalKamar }}</div>
            <div class="nk-metric-sub">
                <span class="font-mono">{{ $kamarTersedia }}</span> tersedia &middot; <span class="font-mono">{{ $kamarTerisi }}</span> terisi
            </div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Kamar Tersedia</span>
            <div class="nk-metric-value" style="color: var(--nk-teal);">{{ $kamarTersedia }}</div>
            <div class="nk-metric-sub">Siap diajukan penyewa</div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Antrean Verifikasi</span>
            <div class="nk-metric-value" style="color: var(--nk-blue);">
                {{ $sewaPendingCount + $pembayaranPendingCount }}
            </div>
            <div class="nk-metric-sub">{{ $sewaPendingCount }} sewa &middot; {{ $pembayaranPendingCount }} pembayaran</div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left Column: Recent Rooms Table --}}
        <div class="col-lg-8">
            <div class="nk-card overflow-hidden">
                <div class="nk-card-header">
                    <h2 class="nk-card-title">Daftar Unit Kamar Terbaru</h2>
                    <a href="{{ route('pemilik.kelola-kamar.index') }}" class="small fw-semibold text-decoration-none" style="color: var(--nk-blue);">
                        Kelola Semua Kamar
                    </a>
                </div>

                @if($kamarTerbaru->count() > 0)
                    <div class="table-responsive">
                        <table class="nk-table">
                            <thead>
                                <tr>
                                    <th>No. Kamar</th>
                                    <th>Properti Kos</th>
                                    <th>Tarif / Bulan</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kamarTerbaru as $kamar)
                                    <tr>
                                        <td class="font-mono fw-semibold">Kamar {{ $kamar->nomor_kamar }}</td>
                                        <td>{{ $kamar->kos->nama_kos }}</td>
                                        <td class="font-mono">Rp {{ number_format($kamar->harga_per_bulan, 0, ',', '.') }}</td>
                                        <td>
                                            @if($kamar->status === 'tersedia')
                                                <span class="nk-badge nk-badge-success">Tersedia</span>
                                            @else
                                                <span class="nk-badge nk-badge-danger">Terisi</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('pemilik.kelola-kamar.edit', $kamar->id) }}" class="btn-nk-secondary py-1 px-2 small">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-4 text-center text-secondary small">
                        Belum ada unit kamar terdaftar. Tambahkan properti kos terlebih dahulu lalu buat unit kamar.
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Column: Property List --}}
        <div class="col-lg-4">
            <div class="nk-card">
                <div class="nk-card-header">
                    <h2 class="nk-card-title">Properti Kos Saya</h2>
                    <span class="small font-mono text-secondary">{{ $daftarKos->count() }} lokasi</span>
                </div>
                <div class="p-3 d-flex flex-column gap-2">
                    @forelse($daftarKos as $kos)
                        <div class="p-3 rounded-2 border" style="background-color: var(--nk-bg);">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                <strong class="small">{{ $kos->nama_kos }}</strong>
                                <span class="nk-badge nk-badge-info">{{ ucfirst($kos->tipe_kos) }}</span>
                            </div>
                            <p class="small text-secondary mb-2">{{ $kos->alamat }}</p>
                            <div class="small font-mono text-secondary">
                                Total: <strong>{{ $kos->kamars->count() }}</strong> kamar
                            </div>
                        </div>
                    @empty
                        <div class="p-3 text-center text-secondary small">
                            Anda belum menambahkan data properti kos.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Properti Kos --}}
<div class="modal fade" id="modalTambahKos" tabindex="-1" aria-labelledby="modalTambahKosLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('pemilik.kos.quick-store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header border-bottom">
                <h2 class="modal-title h6 fw-bold" id="modalTambahKosLabel">Tambah Properti Kos Baru</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="nama_kos" class="form-label">Nama Kos</label>
                    <input type="text" id="nama_kos" name="nama_kos" class="form-control" placeholder="Contoh: Kos Griya Melati" required>
                </div>
                <div class="mb-3">
                    <label for="tipe_kos" class="form-label">Tipe Penghuni</label>
                    <select id="tipe_kos" name="tipe_kos" class="form-select" required>
                        <option value="putra">Kos Putra</option>
                        <option value="putri">Kos Putri</option>
                        <option value="campur">Kos Campur</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat Lengkap</label>
                    <textarea id="alamat" name="alamat" rows="2" class="form-control" placeholder="Jl. Kaliurang KM 5, Sleman" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi Fasilitas Umum &amp; Peraturan</label>
                    <textarea id="deskripsi" name="deskripsi" rows="2" class="form-control" placeholder="Parkir motor luas, WiFi 50 Mbps, akses 24 jam"></textarea>
                </div>
                <div class="mb-0">
                    <label for="foto_file" class="form-label">Foto Tampak Depan Kos (Opsional)</label>
                    <input type="file" id="foto_file" name="foto_file" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn-nk-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn-nk-primary">Simpan Properti Kos</button>
            </div>
        </form>
    </div>
</div>
@endsection

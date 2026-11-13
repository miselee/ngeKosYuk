@extends('layouts.app')

@section('title', 'Kelola Pengguna - Admin')

@section('content')
@include('admin.partials.navbar')

<div class="container py-4">
    <div class="nk-page-header">
        <div>
            <h1 class="nk-page-title">Kelola Pengguna Platform</h1>
            <p class="nk-page-subtitle">Atur akun Admin, mitra Pemilik Kos, dan Penyewa beserta hak akses perannya di ngeKosYuk.</p>
        </div>
        <button type="button" class="btn-nk-primary" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
            + Tambah Pengguna Baru
        </button>
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

    @if($errors->any())
        <div class="alert alert-danger py-2 px-3 small mb-4" role="alert">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="nk-metric-strip mb-4">
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Total Seluruh Akun</span>
            <div class="nk-metric-value">{{ $totalUsers }}</div>
            <div class="nk-metric-sub">Pengguna terdaftar aktif</div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Administrator</span>
            <div class="nk-metric-value" style="color: var(--nk-blue);">{{ $totalAdmin }}</div>
            <div class="nk-metric-sub">Pengelola sistem utama</div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Mitra Pemilik Kos</span>
            <div class="nk-metric-value" style="color: var(--nk-teal);">{{ $totalPemilik }}</div>
            <div class="nk-metric-sub">Pengelola properti &amp; kamar</div>
        </div>
        <div class="nk-metric-cell">
            <span class="nk-metric-label">Penyewa Kos</span>
            <div class="nk-metric-value">{{ $totalPenyewa }}</div>
            <div class="nk-metric-sub">Akun pencari &amp; penghuni kos</div>
        </div>
    </div>

    <div class="nk-card p-3 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="nk-tabs">
                <a href="{{ route('admin.users.index') }}"
                   class="nk-tab {{ !request('role') ? 'active' : '' }}">
                    Semua ({{ $totalUsers }})
                </a>
                <a href="{{ route('admin.users.index', ['role' => 'admin']) }}"
                   class="nk-tab {{ request('role') === 'admin' ? 'active' : '' }}">
                    Admin ({{ $totalAdmin }})
                </a>
                <a href="{{ route('admin.users.index', ['role' => 'pemilik']) }}"
                   class="nk-tab {{ request('role') === 'pemilik' ? 'active' : '' }}">
                    Pemilik ({{ $totalPemilik }})
                </a>
                <a href="{{ route('admin.users.index', ['role' => 'penyewa']) }}"
                   class="nk-tab {{ request('role') === 'penyewa' ? 'active' : '' }}">
                    Penyewa ({{ $totalPenyewa }})
                </a>
            </div>

            <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                @if(request('role'))
                    <input type="hidden" name="role" value="{{ request('role') }}">
                @endif
                <input type="text"
                       name="q"
                       class="form-control form-control-sm"
                       style="width: 230px;"
                       placeholder="Cari nama, email, no. HP..."
                       aria-label="Cari pengguna"
                       value="{{ request('q') }}">
                <button type="submit" class="btn-nk-primary py-1 px-3 small">Cari</button>
                @if(request()->hasAny(['role', 'q']))
                    <a href="{{ route('admin.users.index') }}" class="btn-nk-secondary py-1 px-3 small">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <div class="nk-card overflow-hidden">
        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="nk-table">
                    <thead>
                        <tr>
                            <th>Nama &amp; Email</th>
                            <th>Nomor HP</th>
                            <th>Peran (Role)</th>
                            <th>Aktivitas Data</th>
                            <th>Terdaftar</th>
                            <th class="text-end">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    <div class="fw-semibold">
                                        {{ $user->name }}
                                        @if($user->id === Auth::id())
                                            <span class="nk-badge nk-badge-neutral ms-1">Anda</span>
                                        @endif
                                    </div>
                                    <div class="small text-secondary">{{ $user->email }}</div>
                                </td>
                                <td class="font-mono small">
                                    {{ $user->nomor_hp ?: '-' }}
                                </td>
                                <td>
                                    @if($user->role === 'admin')
                                        <span class="nk-badge nk-badge-info">Admin</span>
                                    @elseif($user->role === 'pemilik')
                                        <span class="nk-badge nk-badge-teal">Pemilik Kos</span>
                                    @else
                                        <span class="nk-badge nk-badge-neutral">Penyewa</span>
                                    @endif
                                </td>
                                <td class="small text-secondary">
                                    @if($user->role === 'pemilik')
                                        <span class="font-mono">{{ $user->kos_count }}</span> properti kos
                                    @elseif($user->role === 'penyewa')
                                        <span class="font-mono">{{ $user->sewas_count }}</span> kontrak sewa
                                    @else
                                        Pengelola sistem
                                    @endif
                                </td>
                                <td class="font-mono small text-secondary">
                                    {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button"
                                                class="btn-nk-secondary py-1 px-2 small"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditUser{{ $user->id }}">
                                            Edit
                                        </button>

                                        @if($user->id !== Auth::id())
                                            <form action="{{ route('admin.users.destroy', $user->id) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Hapus akun pengguna {{ $user->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-nk-secondary py-1 px-2 small text-danger">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            @push('modals')
                            <div class="modal fade" id="modalEditUser{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="modal-content">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h2 class="modal-title h6 fw-bold">Edit Pengguna: {{ $user->name }}</h2>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="edit_name_{{ $user->id }}" class="form-label">Nama Lengkap</label>
                                                <input type="text"
                                                       id="edit_name_{{ $user->id }}"
                                                       name="name"
                                                       class="form-control"
                                                       value="{{ $user->name }}"
                                                       required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_email_{{ $user->id }}" class="form-label">Alamat Email</label>
                                                <input type="email"
                                                       id="edit_email_{{ $user->id }}"
                                                       name="email"
                                                       class="form-control"
                                                       value="{{ $user->email }}"
                                                       required>
                                            </div>
                                            <div class="row g-3 mb-3">
                                                <div class="col-sm-6">
                                                    <label for="edit_hp_{{ $user->id }}" class="form-label">Nomor HP</label>
                                                    <input type="text"
                                                           id="edit_hp_{{ $user->id }}"
                                                           name="nomor_hp"
                                                           class="form-control font-mono"
                                                           value="{{ $user->nomor_hp }}">
                                                </div>
                                                <div class="col-sm-6">
                                                    <label for="edit_role_{{ $user->id }}" class="form-label">Peran (Role)</label>
                                                    <select id="edit_role_{{ $user->id }}" name="role" class="form-select" required>
                                                        <option value="penyewa" {{ $user->role === 'penyewa' ? 'selected' : '' }}>Penyewa</option>
                                                        <option value="pemilik" {{ $user->role === 'pemilik' ? 'selected' : '' }}>Pemilik Kos</option>
                                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mb-0">
                                                <label for="edit_pw_{{ $user->id }}" class="form-label">Kata Sandi Baru (Kosongkan jika tidak diubah)</label>
                                                <input type="password"
                                                       id="edit_pw_{{ $user->id }}"
                                                       name="password"
                                                       class="form-control"
                                                       placeholder="Min. 8 karakter">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn-nk-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn-nk-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endpush
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="p-3 border-top">
                    {{ $users->links() }}
                </div>
            @endif
        @else
            <div class="p-5 text-center text-secondary small">
                Tidak ada akun pengguna yang cocok dengan pencarian Anda.
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-labelledby="modalTambahUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.users.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h2 class="modal-title h6 fw-bold" id="modalTambahUserLabel">Tambah Akun Pengguna Baru</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="new_name" class="form-label">Nama Lengkap</label>
                    <input type="text" id="new_name" name="name" class="form-control" placeholder="Contoh: Rina Wijaya" required>
                </div>
                <div class="mb-3">
                    <label for="new_email" class="form-label">Alamat Email</label>
                    <input type="email" id="new_email" name="email" class="form-control" placeholder="rina@kos.com" required>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label for="new_hp" class="form-label">Nomor HP</label>
                        <input type="text" id="new_hp" name="nomor_hp" class="form-control font-mono" placeholder="081234567890">
                    </div>
                    <div class="col-sm-6">
                        <label for="new_role" class="form-label">Peran (Role)</label>
                        <select id="new_role" name="role" class="form-select" required>
                            <option value="penyewa">Penyewa</option>
                            <option value="pemilik">Pemilik Kos</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="mb-0">
                    <label for="new_password" class="form-label">Kata Sandi Awal</label>
                    <input type="password" id="new_password" name="password" class="form-control" placeholder="Minimal 8 karakter" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-nk-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn-nk-primary">Buat Akun</button>
            </div>
        </form>
    </div>
</div>
@endsection


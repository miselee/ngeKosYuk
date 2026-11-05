<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm border-bottom sticky-top">
    <div class="container-fluid px-3 px-lg-4">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('pemilik.dashboard') }}">
            <i class="bi bi-house-heart-fill logo-icon text-primary"></i>
            <span class="brand-text">NgeKosYuk</span>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1" style="font-size: 11px;">
                Pemilik Kos
            </span>
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPemilik" aria-controls="navbarPemilik" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarPemilik">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('pemilik.dashboard') ? 'active fw-bold' : '' }}" href="{{ route('pemilik.dashboard') }}">
                        <i class="bi bi-grid-1x2-fill me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('pemilik.kelola-kos.*') ? 'active fw-bold' : '' }}" href="{{ route('pemilik.kelola-kos.index') }}">
                        <i class="bi bi-buildings me-1"></i> Kelola Kos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('pemilik.kelola-kamar.*') ? 'active fw-bold' : '' }}" href="{{ route('pemilik.kelola-kamar.index') }}">
                        <i class="bi bi-door-open-fill me-1"></i> Kelola Kamar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('pemilik.penyewaan.*') ? 'active fw-bold' : '' }}" href="{{ route('pemilik.penyewaan.index') }}">
                        <i class="bi bi-file-earmark-text-fill me-1"></i> Daftar Penyewaan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('pemilik.pembayaran.*') ? 'active fw-bold' : '' }}" href="{{ route('pemilik.pembayaran.index') }}">
                        <i class="bi bi-credit-card-2-front-fill me-1"></i> Pembayaran
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('pemilik.ulasan.*') ? 'active fw-bold' : '' }}" href="{{ route('pemilik.ulasan.index') }}">
                        <i class="bi bi-star-fill me-1"></i> Ulasan Kos
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3 pt-2 pt-lg-0">
                <div class="d-flex align-items-center text-muted small">
                    <i class="bi bi-person-circle fs-5 text-primary me-1.5"></i>
                    <span class="fw-semibold text-dark">{{ Auth::user()->name }}</span>
                </div>
                <form action="{{ url('/logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1 rounded-pill px-3">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<style>
    .brand-text {
        color: #2468B8;
        font-size: 21px;
        font-weight: 700;
        letter-spacing: -0.5px;
    }

    .navbar-light .navbar-nav .nav-link {
        color: #495e74;
        font-weight: 500;
        padding: 8px 14px;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .navbar-light .navbar-nav .nav-link:hover {
        color: #2468B8;
        background-color: #f1f7fe;
    }

    .navbar-light .navbar-nav .nav-link.active {
        color: #2468B8;
        background-color: #eaf4ff;
    }
</style>

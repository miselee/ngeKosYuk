
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid px-3">

        <a class="navbar-brand d-flex align-items-center gap-2"
            href="{{ url('/penyewa/dashboard') }}">

                <i class="bi bi-house-heart-fill logo-icon"></i>
                <span>NgeKosYuk</span>

            </a>

        <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarPenyewa"
            aria-controls="navbarPenyewa"
            aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarPenyewa">
            <ul class="navbar-nav ms-auto align-items-lg-center">

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('penyewa/dashboard') ? 'active' : '' }}"
                       href="{{ url('/penyewa/dashboard') }}">
                        Beranda
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('penyewa/cari-kos*') || request()->is('penyewa/detail-kos*') ? 'active' : '' }}"
                       href="{{ url('/penyewa/cari-kos') }}">
                        Cari Kos
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('penyewa/riwayat-sewa*') ? 'active' : '' }}"
                       href="{{ url('/penyewa/riwayat-sewa') }}">
                        Riwayat Sewa
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('penyewa/pembayaran*') ? 'active' : '' }}"
                       href="{{ url('/penyewa/pembayaran') }}">
                        Pembayaran
                    </a>
                </li>

                <li class="nav-item">
                    <form action="{{ url('/logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="nav-link border-0 bg-transparent">
                            <i class="bi bi-box-arrow-right"></i> Log Out
                        </button>
                    </form>
                </li>

            </ul>
        </div>
    </div>
</nav>

<style>
    .navbar {
        padding: 12px 2%;
    }

    .navbar-brand {
        color: #2468B8 !important;
        font-size: 21px;
        font-weight: 700;
    }

    .navbar .nav-link {
        color: #2468B8;
        margin-left: 15px;
        font-weight: 500;
    }

    .navbar .nav-link:hover {
        color: #2696D2;
    }

    .navbar .nav-link:focus {
        color: #2468B8;
    }

    .navbar .nav-link.active {
        color: #2696D2;
        font-weight: 700;
        position: relative;
    }

    @media (min-width: 992px) {
        .navbar .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 15px;
            right: 0;
            height: 2.5px;
            background: #2696D2;
            border-radius: 3px;
        }
    }
</style>

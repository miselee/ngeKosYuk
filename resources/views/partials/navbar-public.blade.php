<nav class="nk-navbar">
    <div class="container py-2 d-flex flex-wrap align-items-center justify-content-between gap-3">

        <div class="d-flex align-items-center gap-4">
            <a class="nk-brand" href="{{ route('landing') }}">
                <img src="{{ asset('images/logo/logo-ngekosyuk.jpeg') }}"
                     alt="Logo ngeKosYuk"
                     width="34"
                     height="34"
                     class="nk-brand-logo">
                <span>ngeKosYuk</span>
            </a>

            <ul class="nk-nav-list d-none d-md-flex">
                <li>
                    <a class="nk-nav-link {{ request()->routeIs('landing') ? 'active' : '' }}"
                    href="{{ route('landing') }}">
                        Beranda
                    </a>
                </li>

                <li>
                    <a class="nk-nav-link {{ request()->routeIs('kos.cari', 'kos.detail', 'penyewa.cari-kos', 'penyewa.detail-kos') ? 'active' : '' }}"
                    href="{{ route('kos.cari') }}">
                        Cari Kos
                    </a>
                </li>

                @auth
                    @if(Auth::user()->role === 'penyewa')
                        <li>
                            <a class="nk-nav-link"
                            href="{{ url('/penyewa/riwayat-sewa') }}">
                                Sewa Saya
                            </a>
                        </li>

                        <li>
                            <a class="nk-nav-link"
                            href="{{ route('penyewa.pembayaran.index') }}">
                                Pembayaran
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>
        </div>

        <div class="d-flex align-items-center gap-3">
            @auth
                <span class="small fw-medium text-secondary">
                    {{ Auth::user()->name }}
                </span>

                @if(Auth::user()->role === 'penyewa')
                    <a href="{{ url('/penyewa/dashboard') }}"
                       class="btn-nk-secondary py-1 px-3 small text-decoration-none">
                        Dashboard
                    </a>
                @elseif(Auth::user()->role === 'pemilik')
                    <a href="{{ route('pemilik.dashboard') }}"
                       class="btn-nk-secondary py-1 px-3 small text-decoration-none">
                        Dashboard
                    </a>
                @elseif(Auth::user()->role === 'admin')
                    <a href="{{ url('/admin/dashboard') }}"
                       class="btn-nk-secondary py-1 px-3 small text-decoration-none">
                        Dashboard
                    </a>
                @endif

                <form action="{{ url('/logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn-nk-secondary py-1 px-3 small">
                        Keluar
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                   class="btn-nk-secondary py-1 px-3 small text-decoration-none">
                    Masuk
                </a>

                <a href="{{ url('/register') }}"
                   class="btn-nk-primary py-1 px-3 small text-decoration-none">
                    Daftar
                </a>
            @endauth
        </div>

        {{-- Mobile Nav --}}
        <ul class="nk-nav-list d-flex d-md-none w-100 pt-2 border-top overflow-auto">
            <li>
                <a class="nk-nav-link {{ request()->routeIs('landing') ? 'active' : '' }}"
                   href="{{ route('landing') }}">Beranda</a>
            </li>
            <li>
                <a class="nk-nav-link {{ request()->routeIs('kos.cari', 'kos.detail') ? 'active' : '' }}"
                   href="{{ route('kos.cari') }}">Cari Kos</a>
            </li>
        </ul>

    </div>
</nav>
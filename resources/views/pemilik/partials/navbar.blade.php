<nav class="nk-navbar">
    <div class="container py-2 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-4">
            <a class="nk-brand" href="{{ route('pemilik.dashboard') }}">
                <img src="{{ asset('images/logo/logo-ngekosyuk.jpeg') }}"
                     alt="Logo ngeKosYuk"
                     width="34"
                     height="34"
                     class="nk-brand-logo">
                <span>ngeKosYuk</span>
                <span class="nk-brand-role">Pemilik</span>
            </a>

            <ul class="nk-nav-list d-none d-md-flex">
                <li>
                    <a class="nk-nav-link {{ request()->routeIs('pemilik.dashboard') ? 'active' : '' }}"
                       href="{{ route('pemilik.dashboard') }}">
                        Ringkasan
                    </a>
                </li>
                <li>
                    <a class="nk-nav-link {{ request()->routeIs('pemilik.kelola-kamar.*') ? 'active' : '' }}"
                       href="{{ route('pemilik.kelola-kamar.index') }}">
                        Kelola Kamar
                    </a>
                </li>
                <li>
                    <a class="nk-nav-link {{ request()->routeIs('pemilik.penyewaan.*') ? 'active' : '' }}"
                       href="{{ route('pemilik.penyewaan.index') }}">
                        Penyewaan
                    </a>
                </li>
                <li>
                    <a class="nk-nav-link {{ request()->routeIs('pemilik.pembayaran.*') ? 'active' : '' }}"
                       href="{{ route('pemilik.pembayaran.index') }}">
                        Pembayaran
                    </a>
                </li>
            </ul>
        </div>

        <div class="d-flex align-items-center gap-3">
            <span class="small fw-medium text-secondary d-none d-sm-inline">
                {{ Auth::user()->name }}
            </span>
            <form action="{{ url('/logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn-nk-secondary py-1 px-3 small">
                    Keluar
                </button>
            </form>
        </div>

        {{-- Mobile Nav Strip --}}
        <ul class="nk-nav-list d-flex d-md-none w-100 pt-2 border-top overflow-auto">
            <li>
                <a class="nk-nav-link {{ request()->routeIs('pemilik.dashboard') ? 'active' : '' }}"
                   href="{{ route('pemilik.dashboard') }}">Ringkasan</a>
            </li>
            <li>
                <a class="nk-nav-link {{ request()->routeIs('pemilik.kelola-kamar.*') ? 'active' : '' }}"
                   href="{{ route('pemilik.kelola-kamar.index') }}">Kelola Kamar</a>
            </li>
            <li>
                <a class="nk-nav-link {{ request()->routeIs('pemilik.penyewaan.*') ? 'active' : '' }}"
                   href="{{ route('pemilik.penyewaan.index') }}">Penyewaan</a>
            </li>
            <li>
                <a class="nk-nav-link {{ request()->routeIs('pemilik.pembayaran.*') ? 'active' : '' }}"
                   href="{{ route('pemilik.pembayaran.index') }}">Pembayaran</a>
            </li>
        </ul>
    </div>
</nav>

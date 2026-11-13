<nav class="nk-navbar">
    <div class="container py-2">
        <div class="nk-navbar-row">
            <div class="d-flex align-items-center gap-3 min-w-0">
                <a class="nk-brand flex-shrink-0" href="{{ route('landing') }}">
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

            <div class="nk-navbar-actions">
                <button type="button"
                        class="nk-theme-toggle"
                        data-nk-theme-toggle
                        aria-pressed="false"
                        aria-label="Aktifkan mode gelap"
                        title="Ganti mode gelap atau terang">
                    <i class="bi bi-moon-stars" data-nk-theme-icon aria-hidden="true"></i>
                    <span class="d-none d-sm-inline" data-nk-theme-label>Gelap</span>
                </button>
                @auth
                    @php
                        $navInitials = collect(explode(' ', trim(Auth::user()->name)))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('') ?: 'U';
                        $dashUrl = match(Auth::user()->role) {
                            'admin' => url('/admin/dashboard'),
                            'pemilik' => route('pemilik.dashboard'),
                            default => url('/penyewa/dashboard'),
                        };
                    @endphp
                    <a href="{{ route('profile.edit') }}"
                       class="nk-user-pill {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                       title="Kelola profil &amp; kata sandi">
                        <span class="nk-user-avatar" aria-hidden="true">{{ $navInitials }}</span>
                        <span class="nk-user-info d-none d-sm-flex">
                            <span class="nk-user-name">{{ Auth::user()->name }}</span>
                            <span class="nk-user-sub">Profil Saya</span>
                        </span>
                    </a>

                    <a href="{{ $dashUrl }}"
                       class="btn-nk-secondary py-1 px-3 small text-decoration-none d-none d-lg-inline-flex">
                        Dashboard
                    </a>

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
        </div>

        {{-- Mobile Nav --}}
        <ul class="nk-nav-list d-flex d-md-none w-100 mt-2 pt-2 border-top overflow-auto">
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
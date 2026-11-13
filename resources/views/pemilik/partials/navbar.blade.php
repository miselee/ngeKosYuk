<nav class="nk-navbar">
    <div class="container py-2">
        <div class="nk-navbar-row">
            <div class="d-flex align-items-center gap-3 min-w-0">
                <a class="nk-brand flex-shrink-0" href="{{ route('pemilik.dashboard') }}">
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

            @php
                $navInitials = collect(explode(' ', trim(Auth::user()->name)))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('') ?: 'P';
            @endphp
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
                <a href="{{ route('profile.edit') }}"
                   class="nk-user-pill {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                   title="Kelola profil &amp; kata sandi">
                    <span class="nk-user-avatar" aria-hidden="true">{{ $navInitials }}</span>
                    <span class="nk-user-info d-none d-sm-flex">
                        <span class="nk-user-name">{{ Auth::user()->name }}</span>
                        <span class="nk-user-sub">Profil Saya</span>
                    </span>
                </a>
                <form action="{{ url('/logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn-nk-secondary py-1 px-3 small">
                        Keluar
                    </button>
                </form>
            </div>
        </div>

        {{-- Mobile Nav Strip --}}
        <ul class="nk-nav-list d-flex d-md-none w-100 mt-2 pt-2 border-top overflow-auto">
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
            <li>
                <a class="nk-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                   href="{{ route('profile.edit') }}">Profil Saya</a>
            </li>
        </ul>
    </div>
</nav>

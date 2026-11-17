@extends('layouts.app')

@section('title', 'Daftar Akun')

@push('styles')
<style>
    .auth-shell {
        min-height: 100vh;
        display: grid;
        grid-template-columns: 1fr;
        background-color: var(--nk-bg);
    }

    @media (min-width: 992px) {
        .auth-shell {
            grid-template-columns: 0.95fr 1.05fr;
        }
    }

    .auth-visual {
        --av-bg: #F8FAFC;
        --av-ink: #0F172A;
        --av-muted: #334155;
        --av-border: #CBD5E1;
        --av-ambient-glow: rgba(59, 130, 246, 0.14);
        --av-cloud-1: rgba(59, 130, 246, 0.34);
        --av-cloud-1-mid: rgba(147, 197, 253, 0.22);
        --av-cloud-2: rgba(37, 99, 235, 0.26);
        --av-cloud-2-mid: rgba(96, 165, 250, 0.18);
        --av-cloud-3: rgba(96, 165, 250, 0.30);
        --av-cloud-3-mid: rgba(191, 219, 254, 0.18);
        --av-scrim-start: rgba(248, 250, 252, 0.84);
        --av-scrim-mid: rgba(248, 250, 252, 0.58);

        position: relative;
        background-color: var(--av-bg);
        color: var(--av-ink);
        border-right: 1px solid var(--nk-border);
        padding: 3rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        overflow: hidden;
    }

    [data-theme="dark"] .auth-visual {
        --av-bg: #020305;
        --av-ink: #FFFFFF;
        --av-muted: #E2E8F0;
        --av-border: rgba(148, 163, 184, 0.35);
        --av-ambient-glow: rgba(226, 232, 240, 0.14);
        --av-cloud-1: rgba(226, 232, 240, 0.38);
        --av-cloud-1-mid: rgba(148, 163, 184, 0.18);
        --av-cloud-2: rgba(148, 163, 184, 0.28);
        --av-cloud-2-mid: rgba(100, 116, 139, 0.14);
        --av-cloud-3: rgba(255, 255, 255, 0.22);
        --av-cloud-3-mid: rgba(148, 163, 184, 0.1);
        --av-scrim-start: rgba(2, 3, 5, 0.72);
        --av-scrim-mid: rgba(2, 3, 5, 0.45);
    }

    .auth-cloud-canvas {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 0;
    }

    .auth-ambient {
        position: absolute;
        inset: -20%;
        pointer-events: none;
        z-index: 1;
        background: radial-gradient(circle at 22% 24%, var(--av-ambient-glow) 0%, transparent 55%);
    }

    .cloud-layer {
        position: absolute;
        border-radius: 999px;
        filter: blur(48px);
        will-change: transform, opacity;
    }

    .cloud-layer-1 {
        width: 620px;
        height: 260px;
        top: 6%;
        left: -14%;
        background: radial-gradient(ellipse at center, var(--av-cloud-1) 0%, var(--av-cloud-1-mid) 45%, transparent 75%);
        transform: rotate(-16deg);
        animation: cloudDriftOne 18s ease-in-out infinite alternate;
    }

    .cloud-layer-2 {
        width: 680px;
        height: 290px;
        top: 38%;
        right: -18%;
        background: radial-gradient(ellipse at center, var(--av-cloud-2) 0%, var(--av-cloud-2-mid) 48%, transparent 76%);
        transform: rotate(22deg);
        animation: cloudDriftTwo 22s ease-in-out infinite alternate;
    }

    .cloud-layer-3 {
        width: 520px;
        height: 230px;
        bottom: 4%;
        left: 10%;
        background: radial-gradient(ellipse at center, var(--av-cloud-3) 0%, var(--av-cloud-3-mid) 50%, transparent 74%);
        transform: rotate(-10deg);
        animation: cloudDriftThree 20s ease-in-out infinite alternate;
    }

    @keyframes cloudDriftOne {
        0% { transform: translate3d(-6%, -4%, 0) rotate(-16deg) scale(1); opacity: 0.7; }
        100% { transform: translate3d(16%, 12%, 0) rotate(-8deg) scale(1.15); opacity: 0.95; }
    }

    @keyframes cloudDriftTwo {
        0% { transform: translate3d(8%, 6%, 0) rotate(22deg) scale(1.06); opacity: 0.65; }
        100% { transform: translate3d(-15%, -10%, 0) rotate(12deg) scale(0.95); opacity: 0.9; }
    }

    @keyframes cloudDriftThree {
        0% { transform: translate3d(-8%, 5%, 0) rotate(-10deg) scale(0.96); opacity: 0.6; }
        100% { transform: translate3d(14%, -8%, 0) rotate(-3deg) scale(1.12); opacity: 0.85; }
    }

    .auth-visual-content {
        position: relative;
        z-index: 2;
    }

    .auth-visual-brand,
    .auth-visual-title {
        color: var(--av-ink);
    }

    .auth-visual-desc,
    .auth-visual-footer {
        color: var(--av-muted);
    }

    .auth-visual-footer {
        border-top: 1px solid var(--av-border);
    }

    .auth-visual-copy {
        background: radial-gradient(ellipse at 25% 50%, var(--av-scrim-start) 0%, var(--av-scrim-mid) 55%, transparent 85%);
        padding: 1.5rem 1.25rem;
        margin-left: -1.25rem;
        border-radius: 14px;
    }

    .auth-form-pane {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2.5rem 1.5rem;
        background-color: var(--nk-surface);
    }

    .auth-form-box {
        width: 100%;
        max-width: 440px;
    }

    .role-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
    }

    .role-option {
        position: relative;
    }

    .role-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .role-label {
        display: block;
        border: 1px solid var(--nk-border-strong);
        border-radius: 8px;
        padding: 0.75rem 0.9rem;
        background-color: var(--nk-surface);
        transition: border-color 0.15s ease, background-color 0.15s ease;
    }

    .role-option input[type="radio"]:checked + .role-label {
        border-color: var(--nk-blue);
        background-color: var(--nk-blue-soft);
        box-shadow: inset 0 0 0 1px var(--nk-blue);
    }

    .role-option input[type="radio"]:focus-visible + .role-label {
        outline: 2px solid var(--nk-blue);
        outline-offset: 2px;
    }

    .role-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--nk-ink);
        display: block;
    }

    .role-desc {
        font-size: 0.75rem;
        color: var(--nk-muted);
        display: block;
        margin-top: 0.15rem;
    }
</style>
@endpush

@section('content')
<div class="auth-shell">
    <div class="auth-visual d-none d-lg-flex">
        <canvas class="auth-cloud-canvas" id="authCloudCanvas" aria-hidden="true"></canvas>
        <div class="auth-ambient" aria-hidden="true">
            <span class="cloud-layer cloud-layer-1"></span>
            <span class="cloud-layer cloud-layer-2"></span>
            <span class="cloud-layer cloud-layer-3"></span>
        </div>

        <a href="{{ route('landing') }}" class="auth-visual-content auth-visual-brand d-inline-flex align-items-center gap-3 text-decoration-none">
            <img src="{{ asset('images/logo/logo-ngekosyuk.jpeg') }}"
                 alt="Logo ngeKosYuk"
                 width="42"
                 height="42"
                 class="nk-brand-logo"
                 style="width: 42px; height: 42px;">
            <span class="fw-bold fs-5">ngeKosYuk</span>
        </a>

        <div class="auth-visual-content auth-visual-copy my-auto" style="max-width: 460px;">
            <h1 class="auth-visual-title fw-bold mb-3" style="font-size: 2rem; letter-spacing: -0.03em; line-height: 1.2;">
                Mulai sewa atau kelola properti kos dalam hitungan menit.
            </h1>
            <p class="auth-visual-desc mb-0" style="font-size: 0.92rem;">
                Pilih peran Anda saat mendaftar: cari kos sesuai anggaran sebagai Penyewa, atau kelola ketersediaan kamar sebagai Pemilik Kos.
            </p>
        </div>

        <div class="auth-visual-content auth-visual-footer pt-3 small">
            Data kontrak sewa dan histori transaksi tersimpan rapi dalam satu akun.
        </div>
    </div>

    <div class="auth-form-pane">
        <div class="auth-form-box">
            <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                <a href="{{ route('landing') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none" style="color: var(--nk-ink);">
                    <img src="{{ asset('images/logo/logo-ngekosyuk.jpeg') }}"
                         alt="Logo ngeKosYuk"
                         width="34"
                         height="34"
                         class="nk-brand-logo">
                    <span class="fw-bold fs-5">ngeKosYuk</span>
                </a>
                <button type="button"
                        class="nk-theme-toggle"
                        data-nk-theme-toggle
                        aria-pressed="false"
                        aria-label="Aktifkan mode gelap"
                        title="Ganti mode gelap atau terang">
                    <i class="bi bi-moon-stars" data-nk-theme-icon aria-hidden="true"></i>
                    <span data-nk-theme-label>Gelap</span>
                </button>
            </div>

            <h2 class="fw-bold mb-1" style="font-size: 1.45rem; letter-spacing: -0.025em;">Buat Akun Baru</h2>
            <p class="text-secondary small mb-4">Lengkapi identitas singkat di bawah ini untuk mulai menggunakan ngeKosYuk.</p>

            @if(session('error'))
                <div class="alert alert-warning py-2 px-3 small mb-3" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger py-2 px-3 small mb-3" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ url('/register') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label d-block">Daftar Sebagai</label>
                    <div class="role-grid">
                        <div class="role-option">
                            <input type="radio"
                                   name="role"
                                   id="role_penyewa"
                                   value="penyewa"
                                   {{ old('role', 'penyewa') === 'penyewa' ? 'checked' : '' }}
                                   required>
                            <label for="role_penyewa" class="role-label">
                                <span class="role-title">Penyewa Kos</span>
                                <span class="role-desc">Cari &amp; ajukan sewa kamar</span>
                            </label>
                        </div>
                        <div class="role-option">
                            <input type="radio"
                                   name="role"
                                   id="role_pemilik"
                                   value="pemilik"
                                   {{ old('role') === 'pemilik' ? 'checked' : '' }}>
                            <label for="role_pemilik" class="role-label">
                                <span class="role-title">Pemilik Kos</span>
                                <span class="role-desc">Kelola kamar &amp; tagihan</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text"
                           id="name"
                           name="name"
                           class="form-control"
                           placeholder="Contoh: Budi Santoso"
                           value="{{ old('name') }}"
                           required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label for="email" class="form-label">Alamat Email</label>
                        <input type="email"
                               id="email"
                               name="email"
                               class="form-control"
                               placeholder="nama@email.com"
                               value="{{ old('email') }}"
                               required>
                    </div>
                    <div class="col-sm-6">
                        <label for="nomor_hp" class="form-label">Nomor WhatsApp / HP</label>
                        <input type="text"
                               id="nomor_hp"
                               name="nomor_hp"
                               class="form-control font-mono"
                               placeholder="081234567890"
                               value="{{ old('nomor_hp') }}">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <label for="password" class="form-label">Kata Sandi</label>
                        <input type="password"
                               id="password"
                               name="password"
                               class="form-control"
                               placeholder="Min. 6 karakter"
                               required>
                    </div>
                    <div class="col-sm-6">
                        <label for="password_confirmation" class="form-label">Ulangi Kata Sandi</label>
                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               class="form-control"
                               placeholder="Ketik ulang sandi"
                               required>
                    </div>
                </div>

                <button type="submit" class="btn-nk-primary w-100 py-2 mb-3">
                    Buat Akun
                </button>
            </form>

            <p class="text-center text-secondary small mb-2">
                Sudah memiliki akun?
                <a href="{{ url('/login') }}" class="fw-semibold text-decoration-none" style="color: var(--nk-blue);">
                    Masuk di sini
                </a>
            </p>
            <p class="text-center small mb-0">
                <a href="{{ route('kos.cari') }}" class="text-decoration-none" style="color: var(--nk-muted);">
                    Lihat Daftar Kos Tanpa Login
                </a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('authCloudCanvas');
    if (canvas && canvas.getContext) {
        const ctx = canvas.getContext('2d');
        let width = 0;
        let height = 0;

        // Seed drifting cloud formations and soft mist ribbons across the canvas.
        const clouds = [
            { xRatio: 0.15, yRatio: 0.18, rx: 310, ry: 120, vx: 0.38, vy: 0.08, angle: -0.28, alpha: 0.24, phase: 0.0 },
            { xRatio: 0.68, yRatio: 0.28, rx: 350, ry: 145, vx: -0.32, vy: 0.05, angle: 0.22, alpha: 0.20, phase: 1.4 },
            { xRatio: 0.35, yRatio: 0.50, rx: 380, ry: 155, vx: 0.44, vy: -0.06, angle: -0.18, alpha: 0.26, phase: 2.6 },
            { xRatio: 0.80, yRatio: 0.65, rx: 290, ry: 130, vx: -0.40, vy: -0.04, angle: 0.30, alpha: 0.22, phase: 3.9 },
            { xRatio: 0.20, yRatio: 0.78, rx: 330, ry: 135, vx: 0.34, vy: 0.06, angle: -0.12, alpha: 0.19, phase: 5.1 },
            { xRatio: 0.52, yRatio: 0.12, rx: 260, ry: 105, vx: -0.28, vy: 0.07, angle: 0.14, alpha: 0.18, phase: 4.3 }
        ].map(function (c) {
            return Object.assign({ x: 0, y: 0 }, c);
        });

        function resizeCanvas() {
            const rect = canvas.parentElement ? canvas.parentElement.getBoundingClientRect() : null;
            width = rect && rect.width > 0 ? rect.width : 720;
            height = rect && rect.height > 0 ? rect.height : 900;
            canvas.width = width;
            canvas.height = height;
            clouds.forEach(function (c) {
                if (c.x === 0 && c.y === 0) {
                    c.x = c.xRatio * width;
                    c.y = c.yRatio * height;
                }
            });
        }

        function renderClouds(now) {
            const t = (now || 0) * 0.001;
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';

            ctx.fillStyle = isDark ? '#020305' : '#F8FAFC';
            ctx.fillRect(0, 0, width, height);

            clouds.forEach(function (c, idx) {
                c.x += c.vx + Math.sin(t * 0.45 + c.phase) * 0.18;
                c.y += c.vy + Math.cos(t * 0.35 + c.phase) * 0.14;

                if (c.x - c.rx > width + 120) c.x = -c.rx - 100;
                if (c.x + c.rx < -120) c.x = width + c.rx + 100;
                if (c.y - c.ry > height + 100) c.y = -c.ry - 80;
                if (c.y + c.ry < -100) c.y = height + c.ry + 80;

                const pulseAlpha = c.alpha + Math.sin(t * 0.6 + idx) * 0.04;
                ctx.save();
                ctx.translate(c.x, c.y);
                ctx.rotate(c.angle + Math.sin(t * 0.2 + c.phase) * 0.05);
                ctx.scale(1, c.ry / c.rx);

                const grad = ctx.createRadialGradient(0, 0, c.rx * 0.08, 0, 0, c.rx);
                if (isDark) {
                    grad.addColorStop(0, 'rgba(241, 245, 249, ' + pulseAlpha.toFixed(3) + ')');
                    grad.addColorStop(0.45, 'rgba(148, 163, 184, ' + (pulseAlpha * 0.55).toFixed(3) + ')');
                    grad.addColorStop(1, 'rgba(2, 3, 5, 0)');
                } else {
                    grad.addColorStop(0, 'rgba(37, 99, 235, ' + (pulseAlpha * 1.15).toFixed(3) + ')');
                    grad.addColorStop(0.48, 'rgba(96, 165, 250, ' + (pulseAlpha * 0.78).toFixed(3) + ')');
                    grad.addColorStop(1, 'rgba(248, 250, 252, 0)');
                }

                ctx.fillStyle = grad;
                ctx.beginPath();
                ctx.arc(0, 0, c.rx, 0, Math.PI * 2);
                ctx.fill();
                ctx.restore();
            });
        }

        function tick(now) {
            renderClouds(now);
            window.requestAnimationFrame(tick);
        }

        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);
        window.requestAnimationFrame(tick);

        if (window.MutationObserver) {
            new MutationObserver(function () {
                renderClouds(window.performance ? window.performance.now() : 0);
            }).observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['data-theme']
            });
        }
    }
});
</script>
@endpush

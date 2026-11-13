<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ngeKosYuk') - ngeKosYuk</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo/logo-ngekosyuk.jpeg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script>
        /* Apply saved theme before first paint to prevent light-to-dark flicker. */
        (function () {
            try {
                var saved = localStorage.getItem('nk-theme');
                var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                var theme = (saved === 'dark' || saved === 'light') ? saved : (prefersDark ? 'dark' : 'light');
                document.documentElement.setAttribute('data-theme', theme);
                document.documentElement.setAttribute('data-bs-theme', theme);
            } catch (e) {}
        })();
    </script>
    <style>
        :root {
            --nk-blue: #1E40AF;
            --nk-blue-hover: #1E3A8A;
            --nk-blue-btn: #1E40AF;
            --nk-blue-btn-hover: #1E3A8A;
            --nk-blue-soft: #EFF6FF;
            --nk-blue-border: #BFDBFE;
            --nk-teal: #0F766E;
            --nk-teal-btn: #0F766E;
            --nk-teal-soft: #F0FDFA;
            --nk-teal-border: #99F6E4;
            --nk-bg: #F8FAFC;
            --nk-surface: #FFFFFF;
            --nk-surface-subtle: #F1F5F9;
            --nk-border: #E2E8F0;
            --nk-border-strong: #CBD5E1;
            --nk-ink: #0F172A;
            --nk-muted: #475569;

            /* Semantic status tokens (WCAG AA verified >= 6.3:1) */
            --nk-emerald-bg: #D1FAE5;
            --nk-emerald-ink: #065F46;
            --nk-amber-bg: #FEF3C7;
            --nk-amber-ink: #92400E;
            --nk-rose-bg: #FFE4E6;
            --nk-rose-ink: #9F1239;
        }

        [data-theme="dark"] {
            --nk-blue: #60A5FA;
            --nk-blue-hover: #93C5FD;
            --nk-blue-btn: #2563EB;
            --nk-blue-btn-hover: #1D4ED8;
            --nk-blue-soft: #172554;
            --nk-blue-border: #1E3A8A;
            --nk-teal: #2DD4BF;
            --nk-teal-btn: #0F766E;
            --nk-teal-soft: #042F2E;
            --nk-teal-border: #115E59;
            --nk-bg: #0B1120;
            --nk-surface: #111827;
            --nk-surface-subtle: #1E293B;
            --nk-border: #1E293B;
            --nk-border-strong: #334155;
            --nk-ink: #F8FAFC;
            --nk-muted: #94A3B8;

            --nk-emerald-bg: #064E3B;
            --nk-emerald-ink: #6EE7B7;
            --nk-amber-bg: #451A03;
            --nk-amber-ink: #FCD34D;
            --nk-rose-bg: #4C0519;
            --nk-rose-ink: #FDA4AF;
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            background-color: var(--nk-bg);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--nk-ink);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            margin: 0;
        }

        .font-mono {
            font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, monospace;
            font-variant-numeric: tabular-nums;
        }

        .tabular-nums {
            font-variant-numeric: tabular-nums;
        }

        /* Accessible Focus & Interactive Affordance */
        a, button, [role="button"], input[type="submit"], input[type="button"], input[type="checkbox"], input[type="radio"], select, label[for] {
            cursor: pointer;
        }

        :focus-visible {
            outline: 2px solid var(--nk-blue);
            outline-offset: 2px;
        }

        /* Navigation Bar */
        .nk-navbar {
            background-color: var(--nk-surface);
            border-bottom: 1px solid var(--nk-border);
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .nk-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.625rem;
            text-decoration: none;
            color: var(--nk-ink);
            font-weight: 700;
            font-size: 1.125rem;
            letter-spacing: -0.02em;
        }

        .nk-brand:hover {
            color: var(--nk-blue);
        }

        .nk-brand-logo {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid var(--nk-border);
        }

        .nk-brand-role {
            font-size: 0.72rem;
            font-weight: 600;
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
            background-color: var(--nk-blue-soft);
            color: var(--nk-blue);
            letter-spacing: 0.01em;
        }

        .nk-nav-list {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nk-navbar-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: nowrap;
        }

        .nk-navbar-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-left: auto;
            flex-shrink: 0;
        }

        .nk-nav-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--nk-muted);
            text-decoration: none;
            border-radius: 6px;
            white-space: nowrap;
            transition: color 0.15s ease, background-color 0.15s ease;
            position: relative;
        }

        .nk-nav-link:hover {
            color: var(--nk-ink);
            background-color: var(--nk-surface-subtle);
        }

        .nk-nav-link.active {
            color: var(--nk-blue);
            background-color: var(--nk-blue-soft);
            font-weight: 600;
        }

        /* Navbar Account Pill */
        .nk-user-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.3rem 0.65rem 0.3rem 0.35rem;
            border: 1px solid var(--nk-border);
            border-radius: 8px;
            background-color: var(--nk-bg);
            text-decoration: none;
            color: var(--nk-ink);
            transition: border-color 0.15s ease, background-color 0.15s ease;
        }

        .nk-user-pill:hover, .nk-user-pill.active {
            border-color: var(--nk-blue);
            background-color: var(--nk-blue-soft);
            color: var(--nk-ink);
        }

        .nk-user-avatar {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background-color: var(--nk-blue-soft);
            color: var(--nk-blue);
            border: 1px solid var(--nk-blue-border);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.72rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .nk-user-info {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
        }

        .nk-user-name {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--nk-ink);
            max-width: 140px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .nk-user-sub {
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--nk-blue);
        }

        /* Structural Page Header */
        .nk-page-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1rem;
            padding-bottom: 1.25rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--nk-border);
        }

        .nk-page-title {
            font-size: clamp(1.35rem, 2vw, 1.65rem);
            font-weight: 700;
            color: var(--nk-ink);
            letter-spacing: -0.025em;
            margin: 0;
            line-height: 1.2;
        }

        .nk-page-subtitle {
            font-size: 0.9rem;
            color: var(--nk-muted);
            margin: 0.35rem 0 0 0;
            max-width: 65ch;
        }

        /* Structural Cards & Surfaces */
        .nk-card {
            background-color: var(--nk-surface);
            border: 1px solid var(--nk-border);
            border-radius: 10px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .nk-card-interactive:hover {
            border-color: var(--nk-border-strong);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
        }

        .nk-card-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--nk-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.75rem;
        }

        .nk-card-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--nk-ink);
            margin: 0;
            letter-spacing: -0.01em;
        }

        /* Metric Ledger Strip (Replaces AI-slop 4 rainbow icon cards) */
        .nk-metric-strip {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            background-color: var(--nk-surface);
            border: 1px solid var(--nk-border);
            border-radius: 10px;
            overflow: hidden;
        }

        .nk-metric-cell {
            padding: 1.15rem 1.35rem;
            border-right: 1px solid var(--nk-border);
            border-bottom: 1px solid var(--nk-border);
        }

        .nk-metric-cell:last-child {
            border-right: none;
        }

        .nk-metric-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--nk-muted);
            margin-bottom: 0.35rem;
            display: block;
        }

        .nk-metric-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--nk-ink);
            letter-spacing: -0.025em;
            line-height: 1.15;
            font-variant-numeric: tabular-nums;
        }

        .nk-metric-sub {
            font-size: 0.78rem;
            color: var(--nk-muted);
            margin-top: 0.3rem;
        }

        /* Buttons */
        .btn-nk-primary {
            background-color: var(--nk-blue-btn);
            color: #FFFFFF;
            border: 1px solid var(--nk-blue-btn);
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.55rem 1.1rem;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            text-decoration: none;
            transition: background-color 0.15s ease, border-color 0.15s ease;
        }

        .btn-nk-primary:hover, .btn-nk-primary:focus {
            background-color: var(--nk-blue-btn-hover);
            border-color: var(--nk-blue-btn-hover);
            color: #FFFFFF;
        }

        .btn-nk-secondary {
            background-color: var(--nk-surface);
            color: var(--nk-ink);
            border: 1px solid var(--nk-border-strong);
            font-weight: 500;
            font-size: 0.875rem;
            padding: 0.55rem 1rem;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            text-decoration: none;
            transition: background-color 0.15s ease, border-color 0.15s ease;
        }

        .btn-nk-secondary:hover, .btn-nk-secondary:focus {
            background-color: var(--nk-surface-subtle);
            color: var(--nk-ink);
            border-color: var(--nk-muted);
        }

        .btn-nk-teal {
            background-color: var(--nk-teal-btn);
            color: #FFFFFF;
            border: 1px solid var(--nk-teal-btn);
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.55rem 1.1rem;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            text-decoration: none;
            transition: opacity 0.15s ease;
        }

        .btn-nk-teal:hover {
            opacity: 0.92;
            color: #FFFFFF;
        }

        .nk-theme-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.4rem 0.75rem;
            border-radius: 8px;
            border: 1px solid var(--nk-border-strong);
            background-color: var(--nk-surface);
            color: var(--nk-ink);
            font-size: 0.8rem;
            font-weight: 600;
            line-height: 1.2;
            white-space: nowrap;
            transition: background-color 0.15s ease, border-color 0.15s ease;
        }

        .nk-theme-toggle:hover {
            background-color: var(--nk-surface-subtle);
            border-color: var(--nk-blue);
        }

        /* Semantic Status Badges (Rectangular 6px radius, not floating pills) */
        .nk-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1.2;
            white-space: nowrap;
        }

        .nk-badge-success {
            background-color: var(--nk-emerald-bg);
            color: var(--nk-emerald-ink);
        }

        .nk-badge-warning {
            background-color: var(--nk-amber-bg);
            color: var(--nk-amber-ink);
        }

        .nk-badge-danger {
            background-color: var(--nk-rose-bg);
            color: var(--nk-rose-ink);
        }

        .nk-badge-info {
            background-color: var(--nk-blue-soft);
            color: var(--nk-blue);
        }

        .nk-badge-teal {
            background-color: var(--nk-teal-soft);
            color: var(--nk-teal);
        }

        .nk-badge-neutral {
            background-color: var(--nk-surface-subtle);
            color: var(--nk-muted);
        }

        /* Segmented Filter Tabs */
        .nk-tabs {
            display: inline-flex;
            flex-wrap: wrap;
            gap: 0.25rem;
            background-color: var(--nk-surface-subtle);
            padding: 0.25rem;
            border-radius: 8px;
            border: 1px solid var(--nk-border);
        }

        .nk-tab {
            padding: 0.4rem 0.85rem;
            font-size: 0.82rem;
            font-weight: 500;
            color: var(--nk-muted);
            text-decoration: none;
            border-radius: 6px;
            border: none;
            background: transparent;
            transition: background-color 0.15s ease, color 0.15s ease;
        }

        .nk-tab:hover {
            color: var(--nk-ink);
        }

        .nk-tab.active {
            background-color: var(--nk-surface);
            color: var(--nk-ink);
            font-weight: 600;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
        }

        /* Data Tables */
        .nk-table {
            width: 100%;
            margin-bottom: 0;
            border-collapse: collapse;
        }

        .nk-table th {
            background-color: var(--nk-bg);
            color: var(--nk-muted);
            font-size: 0.76rem;
            font-weight: 600;
            padding: 0.75rem 1.15rem;
            border-bottom: 1px solid var(--nk-border);
            text-align: left;
            white-space: nowrap;
        }

        .nk-table td {
            padding: 0.9rem 1.15rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--nk-border);
            color: var(--nk-ink);
            font-size: 0.875rem;
        }

        .nk-table tbody tr:last-child td {
            border-bottom: none;
        }

        .nk-table tbody tr:hover {
            background-color: var(--nk-surface-subtle);
        }

        /* Form Controls */
        .form-label {
            font-size: 0.84rem;
            font-weight: 600;
            color: var(--nk-ink);
            margin-bottom: 0.35rem;
        }

        .form-control, .form-select {
            border: 1px solid var(--nk-border-strong);
            border-radius: 8px;
            padding: 0.55rem 0.85rem;
            font-size: 0.875rem;
            color: var(--nk-ink);
            background-color: var(--nk-surface);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--nk-blue);
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.12);
        }

        /* Actionable Banner */
        .nk-banner {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 0.9rem 1.25rem;
            border-radius: 8px;
            border: 1px solid var(--nk-border);
            border-left: 4px solid var(--nk-blue);
            background-color: var(--nk-surface);
        }

        .font-mono {
            white-space: nowrap;
        }

        /* Pagination (Bootstrap 5 markup, brand palette) */
        .pagination {
            margin-bottom: 0;
            gap: 0.25rem;
        }

        .pagination .page-link {
            border: 1px solid var(--nk-border);
            border-radius: 6px !important;
            color: var(--nk-ink);
            font-size: 0.82rem;
            padding: 0.35rem 0.7rem;
            font-variant-numeric: tabular-nums;
        }

        .pagination .page-link:hover {
            background-color: var(--nk-blue-soft);
            border-color: var(--nk-blue-border);
            color: var(--nk-blue);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--nk-blue-btn);
            border-color: var(--nk-blue-btn);
            color: #FFFFFF;
        }

        .pagination .page-item.disabled .page-link {
            color: var(--nk-border-strong);
            background-color: var(--nk-surface);
        }

        nav[role="navigation"] p.small.text-muted {
            margin-bottom: 0;
            color: var(--nk-muted) !important;
        }

        /* Bootstrap utility harmonization for Light and Dark modes */
        .text-secondary {
            color: var(--nk-muted) !important;
        }

        .text-dark {
            color: var(--nk-ink) !important;
        }

        .border, .border-top, .border-bottom, .border-start, .border-end {
            border-color: var(--nk-border) !important;
        }

        .modal-content {
            background-color: var(--nk-surface);
            color: var(--nk-ink);
            border-color: var(--nk-border-strong);
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

@yield('content')

{{-- Modals must live outside tables; a div inside tbody breaks Bootstrap's modal layout. --}}
@stack('modals')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
        function syncToggleButtons(theme) {
            var isDark = theme === 'dark';
            var buttons = document.querySelectorAll('[data-nk-theme-toggle]');
            buttons.forEach(function (btn) {
                btn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
                btn.setAttribute('aria-label', isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap');
                var icon = btn.querySelector('[data-nk-theme-icon]');
                var label = btn.querySelector('[data-nk-theme-label]');
                if (icon) {
                    icon.className = isDark ? 'bi bi-sun' : 'bi bi-moon-stars';
                }
                if (label) {
                    label.textContent = isDark ? 'Terang' : 'Gelap';
                }
            });
        }

        function applyTheme(theme, persist) {
            document.documentElement.setAttribute('data-theme', theme);
            document.documentElement.setAttribute('data-bs-theme', theme);
            if (persist) {
                try {
                    localStorage.setItem('nk-theme', theme);
                } catch (e) {}
            }
            syncToggleButtons(theme);
        }

        document.addEventListener('DOMContentLoaded', function () {
            var current = document.documentElement.getAttribute('data-theme') || 'light';
            syncToggleButtons(current);

            document.querySelectorAll('[data-nk-theme-toggle]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var active = document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
                    applyTheme(active === 'dark' ? 'light' : 'dark', true);
                });
            });

            if (window.matchMedia) {
                var media = window.matchMedia('(prefers-color-scheme: dark)');
                media.addEventListener('change', function (event) {
                    try {
                        if (!localStorage.getItem('nk-theme')) {
                            applyTheme(event.matches ? 'dark' : 'light', false);
                        }
                    } catch (e) {}
                });
            }
        });
    })();
</script>
@stack('scripts')
</body>
</html>

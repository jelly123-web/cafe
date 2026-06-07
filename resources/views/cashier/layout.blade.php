<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', ($cafeBrand['name'] ?? config('app.name')) . ' - Kasir')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@7.3.0/dist/turbo.es2017-umd.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/nprogress@0.2.0/nprogress.css">
    <script src="https://unpkg.com/nprogress@0.2.0/nprogress.js"></script>

    <style>
        :root {
            --bg: #F4F5F7;
            --white: #FFFFFF;
            --border: #E8EAED;
            --border-light: #F0F1F3;
            --fg: #1A1D23;
            --fg-secondary: #5F6577;
            --muted: #9CA3B4;
            --accent: #D97706;
            --accent-light: #FEF3C7;
            --accent-dark: #B45309;
            --green: #059669;
            --green-light: #D1FAE5;
            --red: #DC2626;
            --red-light: #FEE2E2;
            --blue: #2563EB;
            --blue-light: #DBEAFE;
            --shadow-xs: 0 1px 2px rgba(0,0,0,0.03);
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.02);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.05);
            --shadow-lg: 0 8px 30px rgba(0,0,0,0.07);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-full: 999px;
            --font: 'Plus Jakarta Sans', -apple-system, sans-serif;
            --transition: 0.2s ease;
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: var(--font); background: var(--bg); color: var(--fg); min-height: 100vh; -webkit-font-smoothing: antialiased; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }

        .dashboard-layout { display: flex; min-height: 100vh; position: relative; overflow-x: hidden; }
        .sidebar {
            width: 260px; background: var(--white); border-right: 1px solid var(--border);
            display: flex; flex-direction: column; position: fixed;
            top: 0; left: 0; height: 100vh; z-index: 1000;
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }
        body.sidebar-collapsed .sidebar { transform: translateX(-100%); }
        .sidebar-brand { padding: 20px 22px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }
        .sidebar-logo {
            width: 38px; height: 38px; border-radius: var(--radius-sm);
            background: linear-gradient(135deg, var(--accent) 0%, #F59E0B 100%);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 16px; font-weight: 900; flex-shrink: 0; overflow: hidden;
        }
        .sidebar-logo.has-image { background: #fff; }
        .sidebar-logo img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .sidebar-brand-text { display: flex; flex-direction: column; }
        .sidebar-brand-name { font-size: 16px; font-weight: 800; color: var(--fg); letter-spacing: -0.3px; }
        .sidebar-brand-role { font-size: 11px; color: var(--muted); font-weight: 500; }
        .sidebar-nav { flex: 1; padding: 12px 10px; overflow-y: auto; }
        .nav-section-title { font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 1px; padding: 12px 12px 6px; }
        .nav-item {
            display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: var(--radius-sm);
            color: var(--fg-secondary); text-decoration: none; font-size: 13px; font-weight: 600;
            transition: all var(--transition); cursor: pointer; position: relative;
        }
        .nav-item:hover { background: var(--bg); color: var(--fg); }
        .nav-item.active { background: var(--accent-light); color: var(--accent-dark); }
        .nav-item.active::before {
            content: ''; position: absolute; left: -10px; top: 50%; transform: translateY(-50%);
            width: 3px; height: 20px; background: var(--accent); border-radius: 0 3px 3px 0;
        }
        .nav-item i { width: 20px; text-align: center; font-size: 15px; flex-shrink: 0; }
        .sidebar-footer { padding: 16px; border-top: 1px solid var(--border); }
        .sidebar-user { display: flex; align-items: center; gap: 10px; padding: 8px; border-radius: var(--radius-sm); cursor: pointer; transition: background var(--transition); }
        .sidebar-user:hover { background: var(--bg); }
        .sidebar-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: linear-gradient(135deg, #6366F1, #8B5CF6);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 14px; font-weight: 700; flex-shrink: 0; overflow: hidden;
        }
        .sidebar-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .sidebar-user-info { min-width: 0; }
        .sidebar-user-name { font-size: 13px; font-weight: 700; }
        .sidebar-user-email { font-size: 11px; color: var(--muted); }

        .main-content {
            flex: 1; margin-left: 260px; display: flex; flex-direction: column; min-height: 100vh;
            transition: margin-left 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }
        body.sidebar-collapsed .main-content { margin-left: 0; }

        .topbar {
            background: var(--white); border-bottom: 1px solid var(--border);
            padding: 0 28px; height: 64px; display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
        }
        .topbar-left { display: flex; align-items: center; gap: 0; }
        .topbar-hamburger {
            display: flex; width: 36px; height: 36px; border: 1.5px solid var(--border); background: var(--white);
            border-radius: var(--radius-sm); align-items: center; justify-content: center; cursor: pointer;
            color: var(--fg-secondary); font-size: 20px; font-weight: 800; transition: all var(--transition);
        }
        .topbar-hamburger i { font-size: 15px; line-height: 1; }
        .topbar-hamburger:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-light); }
        .topbar-brand-title { font-size: 22px; font-weight: 900; color: var(--fg); letter-spacing: -0.6px; }
        .topbar-right { display: flex; align-items: center; gap: 6px; }
        .topbar-btn {
            width: 38px; height: 38px; border: 1px solid var(--border); background: var(--white); border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--fg-secondary);
            font-size: 15px; transition: all var(--transition); position: relative; text-decoration: none;
        }
        .topbar-btn:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-light); }

        .page-body { flex: 1; padding: 28px; animation: fadeIn .25s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.3); z-index: 900; }
        .sidebar-overlay.show { display: block; }

        .pagination-wrap { display: flex; flex-direction: column; gap: 1rem; margin-top: 1.5rem; align-items: center; border-top: 1px solid var(--border); padding-top: 1.25rem; }
        .pagination-meta { color: var(--muted); font-size: 0.85rem; font-weight: 500; }
        .pagination-links { display: flex; gap: 0.45rem; align-items: center; flex-wrap: wrap; justify-content: center; }
        .pagination-link {
            text-decoration: none; color: var(--fg-secondary); background: #fff; border: 1px solid var(--border);
            padding: 0.5rem 0.85rem; border-radius: 10px; font-size: 0.85rem; font-weight: 700;
            transition: all 0.2s; box-shadow: var(--shadow-xs); display: inline-flex; align-items: center; justify-content: center; min-width: 38px; cursor: pointer;
        }
        .pagination-link:hover:not(.disabled) { border-color: var(--accent); background: var(--accent-light); color: var(--accent); transform: translateY(-1px); }
        .pagination-link.active { background: var(--accent); color: #fff; border-color: var(--accent); box-shadow: var(--shadow-sm); }
        .pagination-link.disabled { color: var(--muted); opacity: 0.6; cursor: not-allowed; background: #fdfdfd; box-shadow: none; }
        .pagination-dots { color: var(--muted); padding: 0 0.25rem; font-weight: 900; }

        #nprogress .bar { background: var(--accent) !important; height: 3px !important; }
        #nprogress .spinner-icon { border-top-color: var(--accent) !important; border-left-color: var(--accent) !important; }

        @media (max-width: 1100px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.mobile-open { transform: translateX(0); box-shadow: var(--shadow-lg); }
            .main-content { margin-left: 0; }
            .page-body { padding: 16px; }
        }
    </style>

    @stack('head')
    @include('components.page-transition-guard')
</head>
<body data-turbo-prefetch="true">
    <div class="dashboard-layout">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <div class="sidebar-logo {{ !empty($cafeBrand['logo_url']) ? 'has-image' : '' }}">
                    @if(!empty($cafeBrand['logo_url']))
                        <img src="{{ $cafeBrand['logo_url'] }}" alt="{{ $cafeBrand['name'] ?? 'Cafe Logo' }}">
                    @else
                        {{ strtoupper(substr($cafeBrand['name'] ?? 'M', 0, 1)) }}
                    @endif
                </div>
                <div class="sidebar-brand-text">
                    <span class="sidebar-brand-name">{{ $cafeBrand['name'] ?? 'MakanYuk' }}</span>
                    <span class="sidebar-brand-role">Kasir Panel</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section-title">Utama</div>
                <a class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="fas fa-chart-pie"></i> Dashboard</a>
                <a class="nav-item {{ request()->routeIs('cashier.orders.*') ? 'active' : '' }}" href="{{ route('cashier.orders.index') }}"><i class="fas fa-clipboard-list"></i> Pesanan</a>
                <a class="nav-item {{ request()->routeIs('cashier.transactions.*') ? 'active' : '' }}" href="{{ route('cashier.transactions.index') }}"><i class="fas fa-cash-register"></i> Transaksi</a>
                <a class="nav-item {{ request()->routeIs('cashier.payments.*') ? 'active' : '' }}" href="{{ route('cashier.payments.index') }}"><i class="fas fa-wallet"></i> Pembayaran</a>
                <a class="nav-item {{ request()->routeIs('cashier.receipts.*') ? 'active' : '' }}" href="{{ route('cashier.receipts.index') }}"><i class="fas fa-file-invoice"></i> Struk</a>
                <a class="nav-item {{ request()->routeIs('cashier.tables.*') ? 'active' : '' }}" href="{{ route('cashier.tables.index') }}"><i class="fas fa-chair"></i> Meja</a>
                <a class="nav-item {{ request()->routeIs('cashier.reports.*') ? 'active' : '' }}" href="{{ route('cashier.reports.index') }}"><i class="fas fa-chart-line"></i> Laporan Kasir</a>
            </nav>

            <div class="sidebar-footer">
                <div class="sidebar-user" onclick="window.location.href='{{ route('profile.edit') }}'">
                    <div class="sidebar-avatar">
                        @if(auth()->user()?->profile_photo_url)
                            <img src="{{ auth()->user()->profile_photo_url }}" alt="Avatar">
                        @else
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        @endif
                    </div>
                    <div class="sidebar-user-info">
                        <div class="sidebar-user-name">{{ auth()->user()->name ?? '-' }}</div>
                        <div class="sidebar-user-email">{{ auth()->user()->username ?? '-' }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin-top:10px;">
                    @csrf
                    <button type="submit" style="width:100%; background:transparent; border:1.5px solid var(--red-light); color:var(--red); padding:8px; border-radius:var(--radius-sm); font-weight:700; cursor:pointer; font-family:var(--font); font-size:12px; transition:all var(--transition);"><i class="fas fa-right-from-bracket"></i> Logout</button>
                </form>
            </div>
        </aside>

        <div class="main-content">
            <header class="topbar">
                <div class="topbar-left">
                    <span class="topbar-brand-title">{{ $cafeBrand['name'] ?? 'MakanYuk' }}</span>
                </div>
                <div class="topbar-right">
                    <a href="{{ route('profile.edit') }}" class="topbar-btn" title="Profil"><i class="fas fa-user"></i></a>
                </div>
            </header>

            <main class="page-body">
                @yield('content')
            </main>
        </div>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <script>
        (function () {
            NProgress.configure({ showSpinner: false, minimum: 0.2 });
            document.addEventListener('turbo:click', () => NProgress.start());
            document.addEventListener('turbo:load', () => NProgress.done());
            document.addEventListener('turbo:before-render', () => NProgress.done());

            const fadeInObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    entry.target.classList.add('visible');
                    fadeInObserver.unobserve(entry.target);
                });
            }, { threshold: 0.05 });

            const initFadeIn = () => {
                document.querySelectorAll('.fade-in').forEach((el) => {
                    if (el.classList.contains('visible')) {
                        return;
                    }

                    fadeInObserver.observe(el);
                });
            };

            const initSidebar = () => {
                const btn = document.getElementById('sidebarToggle');
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                if (!btn || !sidebar || !overlay) return;
                if (btn.dataset.sidebarBound === '1') return;
                btn.dataset.sidebarBound = '1';

                const isMobile = () => window.innerWidth <= 1100;
                const closeMobileSidebar = () => {
                    sidebar.classList.remove('mobile-open');
                    overlay.classList.remove('show');
                };

                btn.addEventListener('click', () => {
                    if (isMobile()) {
                        sidebar.classList.toggle('mobile-open');
                        overlay.classList.toggle('show');
                        document.body.classList.remove('sidebar-collapsed');
                        return;
                    }

                    closeMobileSidebar();
                    document.body.classList.toggle('sidebar-collapsed');
                    try {
                        localStorage.setItem('cashier_sidebar_collapsed', document.body.classList.contains('sidebar-collapsed') ? '1' : '0');
                    } catch (e) {}
                });

                overlay.addEventListener('click', closeMobileSidebar);
                window.addEventListener('resize', () => {
                    if (!isMobile()) closeMobileSidebar();
                });

                try {
                    const isCollapsed = localStorage.getItem('cashier_sidebar_collapsed');
                    if (isCollapsed === '1' && !isMobile()) {
                        document.body.classList.add('sidebar-collapsed');
                    }
                } catch (e) {}
            };

            document.addEventListener('turbo:load', () => {
                initSidebar();
                initFadeIn();
            });

            if (document.readyState !== 'loading') {
                initFadeIn();
            } else {
                document.addEventListener('DOMContentLoaded', initFadeIn, { once: true });
            }
        })();
    </script>
    @include('components.live-sync')
    @stack('scripts')
</body>
</html>

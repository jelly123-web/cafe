<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', ($cafeBrand['name'] ?? 'Cafe') . ' - Leader Kasir')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite([
            'resources/css/superadmin/dashboard.css',
            'resources/css/superadmin/users.css',
            'resources/css/superadmin/access.css',
            'resources/css/superadmin/menus.css',
            'resources/js/superadmin/dashboard.js',
            'resources/js/superadmin/users.js',
            'resources/js/superadmin/access.js',
            'resources/js/superadmin/menus.js',
        ])
    @else
        <link rel="stylesheet" href="{{ asset('css/superadmin/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/superadmin/users.css') }}">
        <link rel="stylesheet" href="{{ asset('css/superadmin/access.css') }}">
        <link rel="stylesheet" href="{{ asset('css/superadmin/menus.css') }}">
        <script defer src="{{ asset('js/superadmin/dashboard.js') }}"></script>
        <script defer src="{{ asset('js/superadmin/users.js') }}"></script>
        <script defer src="{{ asset('js/superadmin/access.js') }}"></script>
        <script defer src="{{ asset('js/superadmin/menus.js') }}"></script>
    @endif

    @stack('head')

    <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@7.3.0/dist/turbo.es2017-umd.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/nprogress@0.2.0/nprogress.css">
    <script src="https://unpkg.com/nprogress@0.2.0/nprogress.js"></script>
    <style>
        #nprogress .bar { background: #795548 !important; height: 3px !important; }
        #nprogress .spinner-icon { border-top-color: #795548 !important; border-left-color: #795548 !important; }
        .sidebar-toggle {
            position: fixed;
            top: 16px;
            left: 16px;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            border: none;
            background: #ffffff;
            color: #795548;
            font-size: 26px;
            font-weight: 900;
            cursor: pointer;
            z-index: 2100;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
            transition: all .2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .sidebar-toggle:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1); }
        body:not(.sidebar-collapsed) .sidebar-toggle { left: 306px; }
        body.sidebar-collapsed .sidebar-toggle { left: 16px; }
        .sidebar-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(62, 39, 35, 0.35);
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s ease;
            z-index: 1500;
        }
        body.sidebar-open .sidebar-backdrop { opacity: 1; pointer-events: auto; }
        .app-shell {
            display: grid;
            grid-template-columns: 290px minmax(0, 1fr);
            min-height: 100vh;
            transition: grid-template-columns .2s ease;
        }
        .sidebar {
            transition: transform .2s ease, opacity .2s ease;
            z-index: 1700;
            background: rgba(255, 255, 255, 0.94);
            position: sticky;
            top: 0;
            width: 290px;
            grid-column: 1;
            height: 100vh;
            overflow-y: auto;
            padding-top: 24px;
        }
        .main-panel {
            grid-column: 2;
            min-width: 0;
            position: relative;
            z-index: 1;
            animation: fadeIn .25s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        body.sidebar-collapsed .app-shell { grid-template-columns: 0 minmax(0, 1fr) !important; }
        body.sidebar-collapsed .sidebar {
            transform: translateX(-100%);
            opacity: 0;
            pointer-events: none;
        }
        body.sidebar-collapsed .main-panel { grid-column: 2; }
        @media (max-width: 1100px) {
            body:not(.sidebar-collapsed) .sidebar-toggle { left: 16px; }
            .app-shell {
                display: block;
                min-height: 100vh;
            }
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                width: 290px;
                min-height: 100vh;
                border-right: 1px solid rgba(121, 85, 72, 0.08);
                border-bottom: 0 !important;
                padding-top: 24px;
            }
            body.sidebar-collapsed .sidebar { transform: translateX(-110%); opacity: 1; pointer-events: none; }
            body:not(.sidebar-collapsed) .sidebar { pointer-events: auto; }
            .main-panel {
                padding: 5.5rem 1rem 1.5rem;
            }
        }
    </style>
</head>
<body data-turbo-prefetch="true">
    <script>
        (function () {
            try {
                if (localStorage.getItem('leader_sidebar_collapsed') === '1') {
                    document.body.classList.add('sidebar-collapsed');
                }
            } catch (e) {}
        })();
    </script>
    <button class="sidebar-toggle" type="button" id="sidebarToggle" aria-label="Toggle Sidebar">=</button>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
    <div id="toastWrap" class="toast-wrap"></div>
    <div class="app-shell">
        <aside class="sidebar">
            <div class="sidebar-brand">
                @if (!empty($cafeBrand['logo_url']))
                    <img src="{{ $cafeBrand['logo_url'] }}" alt="{{ $cafeBrand['name'] ?? 'Cafe' }}" style="width:64px;height:64px;object-fit:cover;border-radius:18px;box-shadow:0 4px 15px rgba(121, 85, 72, 0.12);margin-bottom:0.5rem;">
                @else
                    <span class="badge">{{ strtoupper(substr($cafeBrand['name'] ?? 'Cafe', 0, 4)) }}</span>
                @endif
                <h2>{{ $cafeBrand['name'] ?? 'Leader Kasir' }}</h2>
                <p>Monitoring transaksi, kas masuk/keluar, dan laporan kasir.</p>
            </div>

            <nav class="nav-menu">
                <a class="nav-item {{ request()->routeIs('leader-cashier.index') ? 'active' : '' }}" href="{{ route('leader-cashier.index') }}">
                    Monitoring Leader
                </a>
                <a class="nav-item {{ request()->routeIs('cashier.orders.*') ? 'active' : '' }}" href="{{ route('cashier.orders.index') }}">
                    Pesanan
                </a>
                <a class="nav-item {{ request()->routeIs('leader-cashier.transactions.*') ? 'active' : '' }}" href="{{ route('leader-cashier.transactions.index') }}">
                    Transaksi
                </a>
                <a class="nav-item {{ request()->routeIs('cashier.payments.*') ? 'active' : '' }}" href="{{ route('cashier.payments.index') }}">
                    Pembayaran
                </a>
                <a class="nav-item {{ request()->routeIs('cashier.receipts.*') ? 'active' : '' }}" href="{{ route('cashier.receipts.index') }}">
                    Struk
                </a>
                <a class="nav-item {{ request()->routeIs('cashier.tables.*') ? 'active' : '' }}" href="{{ route('cashier.tables.index') }}">
                    Meja
                </a>
                <a class="nav-item {{ request()->routeIs('cashier.reports.*') ? 'active' : '' }}" href="{{ route('cashier.reports.index') }}">
                    Laporan Kasir
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="user-card">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                        <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent);">
                        <div style="display: flex; flex-direction: column;">
                            <span>Login sebagai</span>
                            <strong>{{ auth()->user()->name ?? '-' }}</strong>
                        </div>
                    </div>
                    <small style="display: block; margin-bottom: 0.5rem;">{{ auth()->user()->username ?? '-' }}</small>
                    <a href="{{ route('profile.edit') }}" style="display: block; font-size: 0.8rem; color: var(--primary); text-decoration: none; font-weight: 600; margin-bottom: 1rem;">Edit Profil</a>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout" type="submit">Logout</button>
                </form>
            </div>
        </aside>

        <main class="main-panel">
            <header class="page-header">
                <div>
                    <span class="page-kicker">@yield('kicker', 'Cafe Control')</span>
                    <h1>@yield('page_title', 'Leader Kasir')</h1>
                    <p>@yield('page_description', 'Monitoring transaksi, kas masuk/keluar, dan laporan kasir.')</p>
                </div>
            </header>

            @yield('content')
        </main>
    </div>

    <script>
        (function () {
            NProgress.configure({ showSpinner: false, minimum: 0.2 });
            document.addEventListener('turbo:click', () => NProgress.start());
            document.addEventListener('turbo:load', () => NProgress.done());
            document.addEventListener('turbo:before-render', () => NProgress.done());

            const toggle = document.getElementById('sidebarToggle');
            const backdrop = document.getElementById('sidebarBackdrop');
            const openSidebar = () => {
                document.body.classList.remove('sidebar-collapsed');
                document.body.classList.add('sidebar-open');
                try { localStorage.setItem('leader_sidebar_collapsed', '0'); } catch (e) {}
            };
            const closeSidebar = () => {
                document.body.classList.add('sidebar-collapsed');
                document.body.classList.remove('sidebar-open');
                try { localStorage.setItem('leader_sidebar_collapsed', '1'); } catch (e) {}
            };

            if (window.innerWidth <= 1100) {
                closeSidebar();
            } else {
                document.body.classList.remove('sidebar-collapsed');
                document.body.classList.remove('sidebar-open');
                try { localStorage.setItem('leader_sidebar_collapsed', '0'); } catch (e) {}
            }

            toggle?.addEventListener('click', function () {
                if (document.body.classList.contains('sidebar-collapsed')) {
                    openSidebar();
                } else {
                    closeSidebar();
                }
            });

            backdrop?.addEventListener('click', closeSidebar);
        })();
    </script>
    @stack('scripts')
</body>
</html>

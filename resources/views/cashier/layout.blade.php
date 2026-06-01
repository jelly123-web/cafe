<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', ($cafeBrand['name'] ?? config('app.name')) . ' - Kasir')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/superadmin/dashboard.css'])
    @else
        <link rel="stylesheet" href="{{ asset('css/superadmin/dashboard.css') }}">
    @endif

    {{-- Turbo & NProgress for Instant Page Transitions --}}
    <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@7.3.0/dist/turbo.es2017-umd.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/nprogress@0.2.0/nprogress.css">
    <script src="https://unpkg.com/nprogress@0.2.0/nprogress.js"></script>
    <style>
        #nprogress .bar { background: #795548 !important; height: 3px !important; }
        #nprogress .spinner-icon { border-top-color: #795548 !important; border-left-color: #795548 !important; }
        .sidebar-toggle {
            position: fixed;
            top: 14px;
            left: 14px;
            z-index: 9999;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            border: none;
            background: #ffffff;
            color: #795548;
            font-size: 26px;
            font-weight: 900;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
            transition: all .2s ease;
        }
        .sidebar-toggle:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1); }
        .app-shell {
            display: flex;
            min-height: 100vh;
            transition: all .2s ease;
        }
        .sidebar {
            width: 320px;
            flex-shrink: 0;
            transition: all .2s ease;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            background: rgba(255, 255, 255, 0.94);
            border-right: 1px solid rgba(121, 85, 72, 0.08);
            z-index: 1000;
            padding-top: 24px;
        }
        .main-panel {
            flex: 1;
            min-width: 0;
            padding: 1.5rem;
            animation: fadeIn .25s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        body:not(.sidebar-collapsed) .sidebar-toggle { left: 334px; }
        body.sidebar-collapsed .sidebar-toggle { left: 14px; }
        body.sidebar-collapsed .sidebar { 
            width: 0;
            opacity: 0;
            pointer-events: none;
            border-right: none;
            transform: translateX(-100%);
            overflow: hidden;
            padding: 0;
        }

        @media (max-width: 1100px) {
            body:not(.sidebar-collapsed) .sidebar-toggle { left: 14px; }
            .app-shell {
                display: block;
                min-height: 100vh;
            }
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                width: min(82vw, 320px);
                height: 100vh;
                z-index: 9998;
                transform: translateX(-105%);
                transition: transform .2s ease;
                border-bottom: 0 !important;
                padding-top: 24px;
            }
            .main-panel {
                padding: 5.5rem 1rem 1.5rem;
            }
            body:not(.sidebar-collapsed) .main-panel {
                margin-left: min(82vw, 320px);
            }
            .app-shell.sidebar-open .sidebar { transform: translateX(0); opacity: 1; pointer-events: auto; }
            .app-shell.sidebar-open::after {
                content: '';
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,.25);
                z-index: 9997;
            }
        }
    </style>
    @stack('head')
</head>
<body data-turbo-prefetch="true">
    <script>
        (function () {
            try {
                const isCollapsed = localStorage.getItem('cashier_sidebar_collapsed');
                if (isCollapsed === '1' || (isCollapsed === null && window.innerWidth <= 1100)) {
                    document.body.classList.add('sidebar-collapsed');
                }
            } catch (e) {}
        })();
    </script>
    @php
        $currentUser = auth()->user();
        $isLeaderCashier = (string) ($currentUser->role ?? '') === 'leader_cashier';
    @endphp
    <button id="sidebarToggle" class="sidebar-toggle" type="button">=</button>
    <div id="cashierShell" class="app-shell">
        <aside class="sidebar">
            <div class="sidebar-brand">
                @if (!empty($cafeBrand['logo_url']))
                    <img src="{{ $cafeBrand['logo_url'] }}" alt="{{ $cafeBrand['name'] ?? 'Cafe' }}" style="width:64px;height:64px;object-fit:cover;border-radius:18px;box-shadow:0 4px 15px rgba(121, 85, 72, 0.12);margin-bottom:0.5rem;">
                @else
                    <span class="badge">{{ strtoupper(substr($cafeBrand['name'] ?? 'Cafe', 0, 4)) }}</span>
                @endif
                <h2>{{ $cafeBrand['name'] ?? config('app.name') }}</h2>
                <p>{{ $isLeaderCashier ? 'Panel Leader Kasir' : 'Panel Kasir' }}</p>
            </div>

            <nav class="nav-menu">
                @if ($isLeaderCashier)
                    <a class="nav-item {{ request()->routeIs('leader-cashier.*') ? 'active' : '' }}" href="{{ route('leader-cashier.index') }}">Monitoring Leader</a>
                @endif
                <a class="nav-item {{ request()->routeIs('cashier.orders.*') ? 'active' : '' }}" href="{{ route('cashier.orders.index') }}">Pesanan</a>
                <a class="nav-item {{ request()->routeIs('cashier.transactions.*') ? 'active' : '' }}" href="{{ route('cashier.transactions.index') }}">Transaksi</a>
                <a class="nav-item {{ request()->routeIs('cashier.payments.*') ? 'active' : '' }}" href="{{ route('cashier.payments.index') }}">Pembayaran</a>
                <a class="nav-item {{ request()->routeIs('cashier.receipts.*') ? 'active' : '' }}" href="{{ route('cashier.receipts.index') }}">Struk</a>
                <a class="nav-item {{ request()->routeIs('cashier.tables.*') ? 'active' : '' }}" href="{{ route('cashier.tables.index') }}">Meja</a>
                <a class="nav-item {{ request()->routeIs('cashier.reports.*') ? 'active' : '' }}" href="{{ route('cashier.reports.index') }}">Laporan Kasir</a>
            </nav>

            <div class="sidebar-footer">
                <div class="user-card">
                    <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem;">
                        <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid var(--accent);">
                        <div style="display:flex;flex-direction:column;">
                            <span>Login sebagai</span>
                            <strong>{{ auth()->user()->name ?? '-' }}</strong>
                        </div>
                    </div>
                    <small style="display:block;margin-bottom:0.5rem;">{{ auth()->user()->username ?? '-' }}</small>
                    <a href="{{ route('profile.edit') }}" style="display: block; font-size: 0.8rem; color: var(--primary); text-decoration: none; font-weight: 600; margin-bottom: 1rem;">Edit Profil</a>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout" type="submit">Logout</button>
                </form>
            </div>
        </aside>
        <main class="main-panel">
            @yield('content')
        </main>
    </div>
    <script>
        (function () {
            // NProgress configuration
            NProgress.configure({ showSpinner: false, minimum: 0.2 });
            document.addEventListener('turbo:click', () => NProgress.start());
            document.addEventListener('turbo:load', () => NProgress.done());
            document.addEventListener('turbo:before-render', () => NProgress.done());

            const initSidebar = () => {
                const shell = document.getElementById('cashierShell');
                const btn = document.getElementById('sidebarToggle');
                const body = document.body;

                if (!shell || !btn) return;

                // Remove existing listeners if any (for turbo)
                btn.replaceWith(btn.cloneNode(true));
                const newBtn = document.getElementById('sidebarToggle');

                const saveState = (collapsed) => {
                    try { localStorage.setItem('cashier_sidebar_collapsed', collapsed ? '1' : '0'); } catch (e) {}
                };

                // Restore state from localStorage on load/turbo-load
                const isCollapsed = localStorage.getItem('cashier_sidebar_collapsed');
                if (isCollapsed === '1' || (isCollapsed === null && window.innerWidth <= 1100)) {
                    body.classList.add('sidebar-collapsed');
                    shell.classList.remove('sidebar-open');
                } else if (isCollapsed === '0' && window.innerWidth > 1100) {
                    body.classList.remove('sidebar-collapsed');
                }

                newBtn.addEventListener('click', function () {
                    if (window.innerWidth > 1100) {
                        const isCollapsed = body.classList.toggle('sidebar-collapsed');
                        saveState(isCollapsed);
                    } else {
                        shell.classList.toggle('sidebar-open');
                        const isCollapsed = !shell.classList.contains('sidebar-open');
                        body.classList.toggle('sidebar-collapsed', isCollapsed);
                        saveState(isCollapsed);
                    }
                });

                shell.addEventListener('click', function (e) {
                    if (e.target === shell && window.innerWidth <= 1100) {
                        body.classList.add('sidebar-collapsed');
                        shell.classList.remove('sidebar-open');
                        saveState(true);
                    }
                });
            };

            document.addEventListener('turbo:load', initSidebar);
            
            // Handle window resize
            window.addEventListener('resize', function() {
                const shell = document.getElementById('cashierShell');
                if (window.innerWidth > 1100 && shell) {
                    shell.classList.remove('sidebar-open');
                    document.body.classList.remove('sidebar-collapsed');
                }
                if (window.innerWidth <= 1100) {
                    document.body.classList.add('sidebar-collapsed');
                }
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>

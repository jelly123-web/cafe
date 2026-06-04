<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $cafeBrand['name'] ?? 'Superadmin')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite([
            'resources/css/superadmin/dashboard.css',
            'resources/css/superadmin/users.css',
            'resources/css/superadmin/access.css',
            'resources/css/superadmin/menus.css',
            'resources/css/superadmin/packages.css',
            'resources/js/superadmin/dashboard.js',
            'resources/js/superadmin/users.js',
            'resources/js/superadmin/access.js',
            'resources/js/superadmin/menus.js',
            'resources/js/superadmin/packages.js',
        ])
    @else
        <link rel="stylesheet" href="{{ asset('css/superadmin/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/superadmin/users.css') }}">
        <link rel="stylesheet" href="{{ asset('css/superadmin/access.css') }}">
        <link rel="stylesheet" href="{{ asset('css/superadmin/menus.css') }}">
        <link rel="stylesheet" href="{{ asset('css/superadmin/packages.css') }}">
        <script defer src="{{ asset('js/superadmin/dashboard.js') }}"></script>
        <script defer src="{{ asset('js/superadmin/users.js') }}"></script>
        <script defer src="{{ asset('js/superadmin/access.js') }}"></script>
        <script defer src="{{ asset('js/superadmin/menus.js') }}"></script>
        <script defer src="{{ asset('js/superadmin/packages.js') }}"></script>
    @endif
    @stack('head')

    {{-- Turbo & NProgress for Instant Page Transitions --}}
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
        body.sidebar-open .sidebar-backdrop { opacity: 0; pointer-events: none; }
        .app-shell {
            display: flex;
            min-height: 100vh;
            transition: all .2s ease;
        }
        .sidebar {
            width: 290px;
            flex-shrink: 0;
            transition: all .2s ease;
            background: rgba(255, 255, 255, 0.94);
            border-right: 1px solid rgba(121, 85, 72, 0.08);
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1700;
            padding-top: 24px;
        }
        .main-panel {
            flex: 1;
            min-width: 0;
            animation: fadeIn .25s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        body:not(.sidebar-collapsed) .sidebar-toggle { left: 306px; }
        body.sidebar-collapsed .sidebar-toggle { left: 16px; }
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
            body.sidebar-open .sidebar-backdrop { opacity: 1; pointer-events: auto; }
            body:not(.sidebar-collapsed) .sidebar-toggle { left: 16px; }
            .app-shell { flex-direction: column; }
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
        }
        .toast-wrap {
            position: fixed;
            top: 16px;
            right: 16px;
            z-index: 2000;
            display: grid;
            gap: 10px;
            width: min(360px, calc(100vw - 32px));
        }
        .toast-item {
            border-radius: 12px;
            padding: 0.8rem 1rem;
            color: #fff;
            box-shadow: 0 8px 20px rgba(0,0,0,.16);
            animation: toast-in .22s ease;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: flex-start;
            font-weight: 500;
        }
        .toast-item.success { background: #4caf50; }
        .toast-item.error { background: #e57373; }
        .toast-item button {
            border: 0;
            background: transparent;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
            line-height: 1;
        }
        @keyframes toast-in {
            from { transform: translateY(-10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Global Pagination Styles */
        .pagination-wrap { display: flex; flex-direction: column; gap: 1rem; margin-top: 1.5rem; align-items: center; border-top: 1px dashed var(--accent); padding-top: 1.25rem; }
        .pagination-meta { color: var(--text-muted); font-size: 0.85rem; font-weight: 500; }
        .pagination-links { display: flex; gap: 0.45rem; align-items: center; flex-wrap: wrap; justify-content: center; }
        .pagination-link {
            text-decoration: none;
            color: var(--primary);
            background: #fff;
            border: 1px solid var(--accent);
            padding: 0.5rem 0.85rem;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 700;
            transition: all 0.2s;
            box-shadow: 0 4px 10px var(--shadow);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            cursor: pointer;
        }
        .pagination-link:hover:not(.disabled) { border-color: var(--highlight); background: #fffaf5; transform: translateY(-1px); }
        .pagination-link.active { background: var(--highlight); color: #fff; border-color: var(--highlight); box-shadow: 0 4px 12px rgba(212, 163, 115, 0.3); }
        .pagination-link.disabled { color: var(--secondary); opacity: 0.6; cursor: not-allowed; background: #fdfdfd; box-shadow: none; }
        .pagination-dots { color: var(--text-muted); padding: 0 0.25rem; font-weight: 900; }
    </style>
</head>
<body data-turbo-prefetch="true">
    <script>
        (function () {
            try {
                const isCollapsed = localStorage.getItem('superadmin_sidebar_collapsed');
                if (isCollapsed === '1' || (isCollapsed === null && window.innerWidth <= 1100)) {
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
                <h2>{{ $cafeBrand['name'] ?? 'Superadmin' }}</h2>
                <p>Kelola cabang, akun, akses sistem, dan pengaturan cafe.</p>
            </div>

            <nav class="nav-menu">
                <a class="nav-item {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}" href="{{ route('superadmin.dashboard') }}">
                    Dashboard
                </a>
                <a class="nav-item {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}" href="{{ route('superadmin.users.index') }}">
                    Akun Pengguna
                </a>
                <a class="nav-item {{ request()->routeIs('superadmin.access.*') ? 'active' : '' }}" href="{{ route('superadmin.access.index') }}">
                    Hak Akses
                </a>
                <a class="nav-item {{ request()->routeIs('superadmin.menus.*') ? 'active' : '' }}" href="{{ route('superadmin.menus.index') }}">
                    Manajemen Menu
                </a>
                <a class="nav-item {{ request()->routeIs('superadmin.packages.*') ? 'active' : '' }}" href="{{ route('superadmin.packages.index') }}">
                    Paket Makanan
                </a>
                <a class="nav-item {{ request()->routeIs('superadmin.promos.*') ? 'active' : '' }}" href="{{ route('superadmin.promos.index') }}" data-turbo="false">
                    Manajemen Promo
                </a>
                <a class="nav-item {{ request()->routeIs('superadmin.employees.*') ? 'active' : '' }}" href="{{ route('superadmin.employees.index') }}">
                    Data Karyawan
                </a>
                <a class="nav-item {{ request()->routeIs('superadmin.payrolls.*') ? 'active' : '' }}" href="{{ route('superadmin.payrolls.index') }}">
                    Gaji Karyawan
                </a>
                <a class="nav-item {{ request()->routeIs('superadmin.payments.*') ? 'active' : '' }}" href="{{ route('superadmin.payments.index') }}">
                    Pembayaran Kasir
                </a>
                <a class="nav-item {{ request()->routeIs('superadmin.menu-categories.*') ? 'active' : '' }}" href="{{ route('superadmin.menu-categories.index') }}">
                    Kategori Menu
                </a>
                <a class="nav-item {{ request()->routeIs('superadmin.tables.*') ? 'active' : '' }}" href="{{ route('superadmin.tables.index') }}">
                    Meja
                </a>
                <a class="nav-item {{ request()->routeIs('superadmin.reports.*') ? 'active' : '' }}" href="{{ route('superadmin.reports.index') }}">
                    Laporan
                </a>
                <a class="nav-item {{ request()->routeIs('superadmin.settings.*') ? 'active' : '' }}" href="{{ route('superadmin.settings.index') }}" data-turbo="false">
                    Pengaturan Sistem
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
                    <h1>@yield('page_title', 'Dashboard')</h1>
                    <p>@yield('page_description', 'Ringkasan kontrol operasional cafe.')</p>
                </div>
            </header>

            @yield('content')
        </main>
    </div>
    <script>
        (function () {
            // NProgress configuration for speed perception
            NProgress.configure({ showSpinner: false, minimum: 0.3 });
            document.addEventListener('turbo:click', () => NProgress.start());
            document.addEventListener('turbo:load', () => NProgress.done());
            document.addEventListener('turbo:before-render', () => NProgress.done());

            const STORAGE_KEY = 'superadmin_sidebar_collapsed';
            const MOBILE_BREAKPOINT = 1100;

            const readSidebarState = () => {
                try {
                    return localStorage.getItem(STORAGE_KEY);
                } catch (e) {
                    return null;
                }
            };

            const saveSidebarState = (collapsed) => {
                try {
                    localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0');
                } catch (e) {}
            };

            const applySidebarState = () => {
                const isMobile = window.innerWidth <= MOBILE_BREAKPOINT;
                const storedState = readSidebarState();
                const shouldCollapse = storedState === '1' || (storedState === null && isMobile);

                document.body.classList.toggle('sidebar-collapsed', shouldCollapse);
                document.body.classList.toggle('sidebar-open', !shouldCollapse && isMobile);
            };

            const initSidebar = () => {
                const toggle = document.getElementById('sidebarToggle');
                const backdrop = document.getElementById('sidebarBackdrop');

                if (!toggle) return;

                // Remove existing listeners if any (for turbo)
                toggle.replaceWith(toggle.cloneNode(true));
                const newToggle = document.getElementById('sidebarToggle');

                const openSidebar = () => {
                    document.body.classList.remove('sidebar-collapsed');
                    document.body.classList.add('sidebar-open');
                    saveSidebarState(false);
                };
                const closeSidebar = () => {
                    document.body.classList.add('sidebar-collapsed');
                    document.body.classList.remove('sidebar-open');
                    saveSidebarState(true);
                };

                applySidebarState();

                backdrop?.addEventListener('click', closeSidebar);

                newToggle.addEventListener('click', function () {
                    if (window.innerWidth > MOBILE_BREAKPOINT) {
                        const isCollapsed = document.body.classList.toggle('sidebar-collapsed');
                        document.body.classList.remove('sidebar-open');
                        saveSidebarState(isCollapsed);
                    } else if (document.body.classList.contains('sidebar-collapsed')) {
                        openSidebar();
                    } else {
                        closeSidebar();
                    }
                });
            };

            document.addEventListener('turbo:load', initSidebar);
            document.addEventListener('turbo:before-cache', () => {
                const isMobile = window.innerWidth <= MOBILE_BREAKPOINT;

                if (!isMobile) {
                    document.body.classList.remove('sidebar-open');
                }

                saveSidebarState(document.body.classList.contains('sidebar-collapsed'));
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth > 1100) {
                    document.body.classList.remove('sidebar-open');
                } else if (!document.body.classList.contains('sidebar-collapsed')) {
                    document.body.classList.add('sidebar-open');
                }
            });

            // Toast system
            const wrap = document.getElementById('toastWrap');
            window.showToast = function (message, type = 'success', timeout = 3200) {
                if (!wrap || !message) return;
                const el = document.createElement('div');
                el.className = 'toast-item ' + (type === 'error' ? 'error' : 'success');
                el.innerHTML = '<span>' + String(message) + '</span><button type="button">x</button>';
                const close = () => {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(-8px)';
                    setTimeout(() => el.remove(), 180);
                };
                el.querySelector('button')?.addEventListener('click', close);
                wrap.appendChild(el);
                setTimeout(close, timeout);
            };

            document.addEventListener('turbo:load', () => {
                const status = "{{ session('status') }}";
                const error = "{{ session('error') }}";
                if (status) window.showToast(status, 'success');
                if (error) window.showToast(error, 'error', 4200);
            });

            document.addEventListener('submit', (event) => {
                if (event.defaultPrevented) {
                    return;
                }

                const form = event.target;
                if (!(form instanceof HTMLFormElement) || form.dataset.skipSubmitState === 'true') {
                    return;
                }

                const submitter = event.submitter;
                if (!(submitter instanceof HTMLButtonElement || submitter instanceof HTMLInputElement)) {
                    return;
                }

                if (!submitter.dataset.defaultText) {
                    submitter.dataset.defaultText = submitter.textContent?.trim() || submitter.value || '';
                }

                const method = String(form.method || 'GET').toUpperCase();
                const spoofMethod = form.querySelector('input[name="_method"]')?.value?.toUpperCase() || '';
                const effectiveMethod = spoofMethod || method;
                const busyText = submitter.dataset.busyText
                    || (effectiveMethod === 'DELETE' ? 'Menghapus...' : effectiveMethod === 'GET' ? 'Membuka...' : 'Menyimpan...');

                submitter.disabled = true;
                if (submitter instanceof HTMLInputElement) {
                    submitter.value = busyText;
                } else {
                    submitter.textContent = busyText;
                }
            });
        })();
    </script>
    @include('components.live-sync')
    @stack('scripts')
</body>
</html>

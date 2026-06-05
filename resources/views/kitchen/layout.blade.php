<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', ($cafeBrand['name'] ?? config('app.name')) . ' - Kitchen')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-main: #F9F5F0;
            --bg-card: #FFFFFF;
            --primary: #795548;
            --secondary: #BCAAA4;
            --accent: #D7CCC8;
            --highlight: #D4A373;
            --text-main: #6D4C41;
            --text-muted: #A1887F;
            --profit: #81C784;
            --loss: #E57373;
            --shadow: rgba(121, 85, 72, 0.08);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            line-height: 1.6;
            background-image: radial-gradient(var(--accent) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        .app-shell {
            display: grid;
            grid-template-columns: 290px minmax(0, 1fr);
            min-height: 100vh;
            transition: grid-template-columns .2s ease;
        }

        .sidebar-toggle {
            position: fixed;
            top: 16px;
            left: 16px;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            border: none;
            background: #ffffff;
            color: var(--primary);
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
        .sidebar-toggle:hover { transform: translateY(-1px); }
        .sidebar-backdrop { position: fixed; inset: 0; background: rgba(62,39,35,.35); opacity: 0; pointer-events: none; transition: opacity .2s ease; z-index: 1500; }
        body.sidebar-open .sidebar-backdrop { opacity: 1; pointer-events: auto; }
        .sidebar { padding: 1.75rem 1.4rem; border-right: 1px solid rgba(121, 85, 72, 0.08); background: rgba(255, 255, 255, 0.94); backdrop-filter: blur(10px); display: flex; flex-direction: column; gap: 1.25rem; transition: transform .2s ease, opacity .2s ease; z-index: 1700; }
        .sidebar-brand { display: flex; flex-direction: column; }
        .sidebar-logo { width: 64px; height: 64px; object-fit: cover; border-radius: 18px; box-shadow: 0 4px 15px rgba(121, 85, 72, 0.12); margin-bottom: 0.5rem; }
        .sidebar-brand h2 { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.8rem; margin: 0.5rem 0 0.25rem; }
        .sidebar-brand p { color: var(--text-muted); font-size: 0.95rem; }
        .nav-menu { display: grid; gap: 0.75rem; }
        .nav-item { text-decoration: none; color: var(--text-main); background: var(--bg-card); border-radius: 16px; padding: 0.9rem 1rem; box-shadow: 0 4px 15px var(--shadow); border: 1px solid transparent; transition: all 0.2s ease; font-weight: 500; font-size: 0.95rem; }
        .nav-item.active, .nav-item:hover { border-color: rgba(212, 163, 115, 0.35); background: #fffaf5; }
        .sidebar-footer { margin-top: auto; display: grid; gap: 0.85rem; }
        .user-card { background: var(--bg-card); border-radius: 18px; padding: 1rem; box-shadow: 0 4px 15px var(--shadow); }
        .user-info-row { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; }
        .profile-photo { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent); }
        .user-info-text { display: flex; flex-direction: column; }
        .user-info-text span { color: var(--text-muted); font-size: 0.8rem; }
        .user-info-text strong { color: var(--primary); font-size: 0.95rem; }
        .logout-btn { background-color: var(--highlight); color: #fff; border: none; padding: 0.75rem 1.5rem; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3); width: 100%; font-size: 0.95rem; }
        .logout-btn:hover { background-color: #c68b59; transform: translateY(-2px); }

        .main-panel { padding: 2rem 2.5rem; overflow-y: auto; animation: fadeIn .25s ease-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        body.sidebar-collapsed .app-shell { grid-template-columns: 0 minmax(0, 1fr) !important; }
        body.sidebar-collapsed .sidebar { transform: translateX(-100%); opacity: 0; pointer-events: none; }
        @media (max-width: 1100px) {
            body:not(.sidebar-collapsed) .sidebar-toggle { left: 16px; }
            .app-shell { grid-template-columns: 1fr; }
            .sidebar { position: fixed; top: 0; left: 0; width: 290px; min-height: 100vh; border-right: 1px solid rgba(121, 85, 72, 0.08); border-bottom: 0; }
            body.sidebar-collapsed .sidebar { transform: translateX(-110%); opacity: 1; pointer-events: none; }
        }
        @media (max-width: 768px) {
            .main-panel { padding: 1.5rem 1rem; }
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
    @stack('head')

    {{-- Turbo & NProgress for Instant Page Transitions --}}
    <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@7.3.0/dist/turbo.es2017-umd.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/nprogress@0.2.0/nprogress.css">
    <script src="https://unpkg.com/nprogress@0.2.0/nprogress.js"></script>
    <style>
        #nprogress .bar { background: #795548 !important; height: 3px !important; }
        #nprogress .spinner-icon { border-top-color: #795548 !important; border-left-color: #795548 !important; }
    </style>
  @include('components.page-transition-guard')
</head>
<body data-turbo-prefetch="true">
    <button class="sidebar-toggle" type="button" id="sidebarToggle" aria-label="Toggle Sidebar">=</button>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
    <div class="app-shell">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <img src="{{ $cafeBrand['logo_url'] ?: 'https://placehold.co/64x64/D4A373/FFFFFF?text=BB' }}" alt="{{ $cafeBrand['name'] ?? config('app.name') }}" class="sidebar-logo">
                <h2>{{ $cafeBrand['name'] ?? config('app.name') }}</h2>
                <p>Panel Dapur</p>
            </div>
            <nav class="nav-menu">
                <a class="nav-item {{ request()->routeIs('kitchen.dashboard') ? 'active' : '' }}" href="{{ route('kitchen.dashboard') }}">Dashboard Dapur</a>
                <a class="nav-item {{ request()->routeIs('kitchen.orders.*') ? 'active' : '' }}" href="{{ route('kitchen.orders.index') }}">Pesanan Masuk</a>
                <a class="nav-item {{ request()->routeIs('kitchen.history.*') ? 'active' : '' }}" href="{{ route('kitchen.history.index') }}">Riwayat Pesanan</a>
                <a class="nav-item {{ request()->routeIs('kitchen.menus.*') ? 'active' : '' }}" href="{{ route('kitchen.menus.index') }}">Menu Habis</a>
            </nav>
            <div class="sidebar-footer">
                <div class="user-card">
                    <div class="user-info-row">
                        <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" class="profile-photo">
                        <div class="user-info-text">
                            <span>Login sebagai</span>
                            <strong>{{ auth()->user()->name ?? 'Koki' }}</strong>
                        </div>
                    </div>
                    <small style="display:block;margin-bottom:0.5rem;">{{ auth()->user()->username ?? 'koki.dapur' }}</small>
                    <a href="{{ route('profile.edit') }}" style="display: block; font-size: 0.8rem; color: var(--primary); text-decoration: none; font-weight: 600; margin-bottom: 0.5rem;">Edit Profil</a>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout-btn" type="submit">Logout</button>
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
                const toggle = document.getElementById('sidebarToggle');
                const backdrop = document.getElementById('sidebarBackdrop');
                
                if (!toggle) return;

                // Remove existing listeners if any (for turbo)
                toggle.replaceWith(toggle.cloneNode(true));
                const newToggle = document.getElementById('sidebarToggle');

                const openSidebar = () => { 
                    document.body.classList.remove('sidebar-collapsed'); 
                    document.body.classList.add('sidebar-open'); 
                };
                const closeSidebar = () => { 
                    document.body.classList.add('sidebar-collapsed'); 
                    document.body.classList.remove('sidebar-open'); 
                };

                if (window.innerWidth <= 1100) closeSidebar();

                newToggle.addEventListener('click', () => document.body.classList.contains('sidebar-collapsed') ? openSidebar() : closeSidebar());
                backdrop?.addEventListener('click', closeSidebar);
            };

            document.addEventListener('turbo:load', initSidebar);

            window.addEventListener('resize', () => { 
                if (window.innerWidth > 1100) document.body.classList.remove('sidebar-open'); 
            });
        })();
    </script>
    @include('components.live-sync')
    @stack('scripts')
</body>
</html>

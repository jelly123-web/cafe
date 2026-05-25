<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Superadmin')</title>
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
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <span class="badge">Cafe</span>
                <h2>Superadmin</h2>
                <p>Kelola cabang, akun, dan akses sistem.</p>
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
                <a class="nav-item {{ request()->routeIs('superadmin.menu-categories.*') ? 'active' : '' }}" href="{{ route('superadmin.menu-categories.index') }}">
                    Kategori Menu
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="user-card">
                    <span>Login sebagai</span>
                    <strong>{{ auth()->user()->name ?? '-' }}</strong>
                    <small>{{ auth()->user()->username ?? '-' }}</small>
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

            @if (session('status'))
                <div class="alert success">{{ session('status') }}</div>
            @endif

            @if (session('error'))
                <div class="alert error">{{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', $cafeBrand['name'] ?? 'Dapur')</title>
  
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
      --bg-card: #FFFFFF;
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
      --purple: #7C3AED;
      --purple-light: #EDE9FE;
      --teal: #0D9488;
      --teal-light: #CCFBF1;
      --shadow-xs: 0 1px 2px rgba(0,0,0,0.03);
      --shadow-sm: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.02);
      --shadow-md: 0 4px 12px rgba(0,0,0,0.05);
      --shadow-lg: 0 8px 30px rgba(0,0,0,0.07);
      --shadow-xl: 0 20px 60px rgba(0,0,0,0.1);
      --radius-sm: 8px;
      --radius-md: 12px;
      --radius-lg: 16px;
      --radius-xl: 20px;
      --radius-full: 999px;
      --font: 'Plus Jakarta Sans', -apple-system, sans-serif;
      --transition: 0.2s ease;
    }

    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body {
      font-family: var(--font);
      background: var(--bg);
      color: var(--fg);
      line-height: 1.6;
      min-height: 100vh;
      -webkit-font-smoothing: antialiased;
    }

    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--muted); }

    .dashboard-layout { display: flex; min-height: 100vh; position: relative; overflow-x: hidden; }

    .sidebar {
      width: 260px; background: var(--white); border-right: 1px solid var(--border);
      display: flex; flex-direction: column; position: fixed;
      top: 0; left: 0; height: 100vh; z-index: 1000; transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }
    body.sidebar-collapsed .sidebar { transform: translateX(-100%); }
    .sidebar-brand { padding: 20px 22px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }
    .sidebar-logo { width: 38px; height: 38px; background: linear-gradient(135deg, var(--accent) 0%, #F59E0B 100%); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; color: white; font-size: 16px; font-weight: 900; flex-shrink: 0; overflow: hidden; }
    .sidebar-logo.has-image { background: #fff; }
    .sidebar-logo img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .sidebar-brand-text { display: flex; flex-direction: column; }
    .sidebar-brand-name { font-size: 16px; font-weight: 800; color: var(--fg); letter-spacing: -0.3px; }
    .sidebar-brand-role { font-size: 11px; color: var(--muted); font-weight: 500; }
    .sidebar-nav { flex: 1; padding: 12px 10px; overflow-y: auto; }
    .nav-section-title { font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 1px; padding: 12px 12px 6px; }
    .nav-item { display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: var(--radius-sm); color: var(--fg-secondary); text-decoration: none; font-size: 13px; font-weight: 600; transition: all var(--transition); cursor: pointer; position: relative; }
    .nav-item:hover { background: var(--bg); color: var(--fg); }
    .nav-item.active { background: var(--accent-light); color: var(--accent-dark); }
    .nav-item.active::before { content: ''; position: absolute; left: -10px; top: 50%; transform: translateY(-50%); width: 3px; height: 20px; background: var(--accent); border-radius: 0 3px 3px 0; }
    .nav-item i { width: 20px; text-align: center; font-size: 15px; flex-shrink: 0; }
    .sidebar-footer { padding: 16px; border-top: 1px solid var(--border); }
    .sidebar-user { display: flex; align-items: center; gap: 10px; padding: 8px; border-radius: var(--radius-sm); cursor: pointer; transition: background var(--transition); }
    .sidebar-user:hover { background: var(--bg); }
    .sidebar-avatar { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #6366F1, #8B5CF6); display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; font-weight: 700; flex-shrink: 0; overflow: hidden; }
    .sidebar-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .sidebar-user-info { min-width: 0; }
    .sidebar-user-name { font-size: 13px; font-weight: 700; }
    .sidebar-user-email { font-size: 11px; color: var(--muted); }

    .main-content { flex: 1; margin-left: 260px; display: flex; flex-direction: column; min-height: 100vh; transition: margin-left 0.35s cubic-bezier(0.4, 0, 0.2, 1); }
    body.sidebar-collapsed .main-content { margin-left: 0; }

    .topbar {
      background: var(--white); border-bottom: 1px solid var(--border);
      padding: 0 28px; height: 64px;
      display: flex; align-items: center; justify-content: space-between;
      position: sticky; top: 0; z-index: 50;
    }
    .topbar-left { display: flex; align-items: center; gap: 0; }
    .topbar-hamburger { display: flex; width: 36px; height: 36px; border: 1.5px solid var(--border); background: var(--white); border-radius: var(--radius-sm); align-items: center; justify-content: center; cursor: pointer; color: var(--fg-secondary); font-size: 20px; font-weight: 800; transition: all var(--transition); position: fixed; top: 18px; left: 276px; z-index: 1101; box-shadow: var(--shadow-xs); }
    .topbar-hamburger .sidebar-toggle-mark { font-size: 22px; line-height: 1; font-weight: 900; transform: translateY(-1px); }
    .topbar-hamburger:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-light); }
    body.sidebar-collapsed .topbar-hamburger { left: 16px; }
    .topbar-brand-title { font-size: 22px; font-weight: 900; color: var(--fg); letter-spacing: -0.6px; font-family: var(--font); }
    .topbar-right { display: flex; align-items: center; gap: 6px; }
    .topbar-btn { width: 38px; height: 38px; border: 1px solid var(--border); background: var(--white); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--fg-secondary); font-size: 15px; transition: all var(--transition); position: relative; text-decoration: none; }
    .topbar-btn:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-light); }
    .topbar-btn .notif-dot { position: absolute; top: 7px; right: 7px; width: 7px; height: 7px; background: var(--red); border-radius: 50%; border: 1.5px solid var(--white); }

    .page-body { flex: 1; padding: 28px; }
    .page-header { margin-bottom: 24px; }
    .page-header-title {
      font-size: 22px; font-weight: 900; letter-spacing: -0.4px;
      color: var(--fg); margin-bottom: 4px;
      display: flex; align-items: center; gap: 10px;
    }
    .page-header-title i { color: var(--accent); font-size: 20px; }
    .page-header-desc { font-size: 14px; color: var(--fg-secondary); line-height: 1.6; }

    .toast-container { position: fixed; bottom: 24px; right: 24px; z-index: 3000; display: flex; flex-direction: column; gap: 8px; }
    .toast { background: var(--fg); color: white; padding: 12px 20px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; box-shadow: var(--shadow-lg); animation: toastIn 0.3s ease, toastOut 0.3s ease 2.2s forwards; font-family: var(--font); }
    .toast.success { background: var(--green); }
    .toast.error { background: var(--red); }
    @keyframes toastIn { from { opacity: 0; transform: translateY(16px) scale(0.96); } to { opacity: 1; transform: translateY(0) scale(1); } }
    @keyframes toastOut { from { opacity: 1; } to { opacity: 0; transform: translateY(8px); } }

    .fade-in { opacity: 0; transform: translateY(12px); transition: opacity 0.4s ease, transform 0.4s ease; }
    .fade-in.visible { opacity: 1; transform: translateY(0); }

    @media (max-width: 1024px) {
      .sidebar { transform: translateX(-100%); }
      .sidebar.mobile-open { transform: translateX(0); box-shadow: var(--shadow-lg); }
      .main-content { margin-left: 0; }
      .topbar-hamburger { top: 18px; left: 16px; }
      .page-body { padding: 16px; }
    }

    .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.3); z-index: 900; }
    .sidebar-overlay.show { display: block; }

    #nprogress .bar { background: var(--accent) !important; height: 3px !important; }
    #nprogress .spinner-icon { border-top-color: var(--accent) !important; border-left-color: var(--accent) !important; }
  </style>

  @stack('head')
</head>
<body data-turbo-prefetch="true">
  @php
    $currentUser = auth()->user();
    $canViewKitchenOrders = $currentUser?->hasPermission('kitchen_orders') ?? false;
    $canViewKitchenHistory = $currentUser?->hasPermission('kitchen_history') ?? false;
    $canViewKitchenMenus = $currentUser?->hasPermission('kitchen_menus') ?? false;
  @endphp

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
          <span class="sidebar-brand-role">Dapur Panel</span>
        </div>
      </div>
      <nav class="sidebar-nav">
        <div class="nav-section-title">Utama</div>
        @if($canViewKitchenOrders)
          <a href="{{ route('kitchen.dashboard') }}" class="nav-item {{ request()->routeIs('kitchen.dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-pie"></i> Dashboard
          </a>
          <a href="{{ route('kitchen.orders.index') }}" class="nav-item {{ request()->routeIs('kitchen.orders.*') ? 'active' : '' }}">
            <i class="fas fa-receipt"></i> Pesanan Masuk
          </a>
        @endif
        @if($canViewKitchenHistory)
          <a href="{{ route('kitchen.history.index') }}" class="nav-item {{ request()->routeIs('kitchen.history.*') ? 'active' : '' }}">
            <i class="fas fa-clock-rotate-left"></i> Riwayat
          </a>
        @endif
        @if($canViewKitchenMenus)
          <a href="{{ route('kitchen.menus.index') }}" class="nav-item {{ request()->routeIs('kitchen.menus.*') ? 'active' : '' }}">
            <i class="fas fa-utensils"></i> Menu Habis
          </a>
        @endif
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
            <div class="sidebar-user-name">{{ auth()->user()->name ?? 'User' }}</div>
            <div class="sidebar-user-email">{{ auth()->user()->username ?? 'username' }}</div>
          </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="margin-top: 10px;">
          @csrf
          <button type="submit" style="width:100%; background:transparent; border:1.5px solid var(--red-light); color:var(--red); padding:8px; border-radius:var(--radius-sm); font-weight:700; cursor:pointer; font-family:var(--font); font-size:12px; transition:all var(--transition);">
            <i class="fas fa-sign-out-alt"></i> Logout
          </button>
        </form>
      </div>
    </aside>

    <div class="main-content">
      <header class="topbar">
        <div class="topbar-left">
          <button class="topbar-hamburger" id="sidebarToggle" type="button" title="Toggle Sidebar">
            <span class="sidebar-toggle-mark">=</span>
          </button>
          <span class="topbar-brand-title">{{ $cafeBrand['name'] ?? 'MakanYuk' }}</span>
        </div>
        <div class="topbar-right">
          <button class="topbar-btn" type="button" title="Notifikasi">
            <i class="far fa-bell"></i>
            <span class="notif-dot"></span>
          </button>
          <a href="{{ route('profile.edit') }}" class="topbar-btn" title="Profil">
            <i class="fas fa-user"></i>
          </a>
        </div>
      </header>

      <div class="page-body">
        @hasSection('page_title')
          @if(trim($__env->yieldContent('page_title')) !== '')
            <div class="page-header fade-in">
              <h1 class="page-header-title">@yield('page_icon') @yield('page_title')</h1>
              @hasSection('page_description')
                <p class="page-header-desc">@yield('page_description')</p>
              @endif
            </div>
          @endif
        @endif
        @yield('content')
      </div>
    </div>
  </div>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>
  <div class="toast-container" id="toastContainer"></div>

  <script>
    (function() {
        const initSidebar = () => {
            const btn = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (!btn || !sidebar || !overlay) return;
            if (btn.dataset.sidebarBound === '1') return;
            btn.dataset.sidebarBound = '1';

            const isMobile = () => window.innerWidth <= 1024;
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
            });

            overlay.addEventListener('click', closeMobileSidebar);

            window.addEventListener('resize', () => {
                if (!isMobile()) {
                    closeMobileSidebar();
                }
            });
        };

        const initAnimations = () => {
            const obs = new IntersectionObserver((entries) => {
                entries.forEach((e) => {
                    if (e.isIntersecting) {
                        e.target.classList.add('visible');
                        obs.unobserve(e.target);
                    }
                });
            }, { threshold: 0.05 });

            document.querySelectorAll('.fade-in').forEach((el) => obs.observe(el));
        };

        window.showToast = function(msg, type = 'success') {
            const c = document.getElementById('toastContainer');
            if (!c) return;
            const t = document.createElement('div');
            t.className = 'toast ' + type;
            const icon = type === 'success' ? 'fa-circle-check' : 'fa-circle-xmark';
            t.innerHTML = '<i class="fas ' + icon + '"></i> ' + msg;
            c.appendChild(t);
            setTimeout(() => {
                t.style.opacity = '0';
                setTimeout(() => t.remove(), 400);
            }, 2600);
        };

        document.addEventListener('turbo:load', () => {
            initSidebar();
            initAnimations();

            const status = "{{ session('status') }}";
            const error = "{{ session('error') }}";
            if (status) window.showToast(status, 'success');
            if (error) window.showToast(error, 'error');
        });

        NProgress.configure({ showSpinner: false, minimum: 0.3 });
        document.addEventListener('turbo:click', () => NProgress.start());
        document.addEventListener('turbo:load', () => NProgress.done());
        document.addEventListener('turbo:before-render', () => NProgress.done());
    })();
  </script>
  @include('components.live-sync')
  @stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', ($cafeBrand['name'] ?? 'Cafe') . ' - Leader Kasir')</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/leader-cashier.css') }}">
  
  <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@7.3.0/dist/turbo.es2017-umd.js"></script>
  <link rel="stylesheet" href="https://unpkg.com/nprogress@0.2.0/nprogress.css">
  <script src="https://unpkg.com/nprogress@0.2.0/nprogress.js"></script>

  @stack('head')
</head>
<body data-turbo-prefetch="false">

<div class="dashboard-layout">

  <!-- SIDEBAR (Matched to Superadmin) -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo">
        @if(!empty($cafeBrand['logo_url']))
            <img src="{{ $cafeBrand['logo_url'] }}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">
        @else
            {{ strtoupper(substr($cafeBrand['name'] ?? 'M', 0, 1)) }}
        @endif
      </div>
      <div class="sidebar-brand-text">
        <span class="sidebar-brand-name">{{ $cafeBrand['name'] ?? 'MakanYuk' }}</span>
        <span class="sidebar-brand-role">Leader Kasir Panel</span>
      </div>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-section-title">Utama</div>
      <a href="{{ route('leader-cashier.index') }}" class="nav-item {{ request()->routeIs('leader-cashier.index') ? 'active' : '' }}">
        <i class="fas fa-chart-line"></i> Monitoring
      </a>
      <a href="{{ route('cashier.orders.index') }}" class="nav-item {{ request()->routeIs('cashier.orders.*') ? 'active' : '' }}">
        <i class="fas fa-receipt"></i> Pesanan
      </a>
      <a href="{{ route('leader-cashier.transactions.index') }}" class="nav-item {{ request()->routeIs('leader-cashier.transactions.*') ? 'active' : '' }}">
        <i class="fas fa-history"></i> Transaksi
      </a>
      <a href="{{ route('leader-cashier.payments.index') }}" class="nav-item {{ request()->routeIs('leader-cashier.payments.*') ? 'active' : '' }}">
        <i class="fas fa-cash-register"></i> Pembayaran
      </a>
      <a href="{{ route('cashier.receipts.index') }}" class="nav-item {{ request()->routeIs('cashier.receipts.*') ? 'active' : '' }}">
        <i class="fas fa-print"></i> Struk
      </a>
      <a href="{{ route('cashier.tables.index') }}" class="nav-item {{ request()->routeIs('cashier.tables.*') ? 'active' : '' }}">
        <i class="fas fa-chair"></i> Meja
      </a>
      <a href="{{ route('cashier.reports.index') }}" class="nav-item {{ request()->routeIs('cashier.reports.*') ? 'active' : '' }}">
        <i class="fas fa-file-invoice-dollar"></i> Laporan Kasir
      </a>
    </nav>

    <div class="sidebar-footer">
      <div class="sidebar-user" onclick="window.location.href='{{ route('profile.edit') }}'">
        <div class="sidebar-avatar">
          @if(auth()->user()->profile_photo_url)
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

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- MAIN CONTENT -->
  <div class="main-content">
    <header class="topbar">
      <div class="topbar-left">
      <button class="topbar-hamburger" id="sidebarToggle" type="button" title="Toggle Sidebar">
        <span class="sidebar-toggle-mark">=</span>
      </button>
        <span class="topbar-brand-title">{{ $cafeBrand['name'] ?? 'MakanYuk' }}</span>
      </div>
      <div class="topbar-right">
        <button class="topbar-btn" title="Notifikasi"><i class="far fa-bell"></i><span class="notif-dot"></span></button>
        <a href="{{ route('profile.edit') }}" class="topbar-btn" title="Pengaturan"><i class="fas fa-gear"></i></a>
      </div>
    </header>

    <div class="page-body">
        @hasSection('page_title')
          <div class="page-header fade-in">
            <h1 class="page-header-title">
                @hasSection('page_icon')
                    <i class="@yield('page_icon')"></i>
                @endif
                @yield('page_title')
            </h1>
            @hasSection('page_description')
              <p class="page-header-desc">@yield('page_description')</p>
            @endif
          </div>
        @endif
        @yield('content')
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="accessToast" aria-live="polite" aria-atomic="true"></div>

<script>
  (() => {
    // ===== TURBO CONFIG =====
    NProgress.configure({ showSpinner: false, minimum: 0.2 });
    document.addEventListener('turbo:click', () => NProgress.start());
    document.addEventListener('turbo:load', () => NProgress.done());
    document.addEventListener('turbo:before-render', () => NProgress.done());

    // ===== SIDEBAR TOGGLE =====
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('sidebarOverlay');
    
    const isMobile = () => window.innerWidth <= 900;
    const closeMobileSidebar = () => {
        sidebar?.classList.remove('mobile-open');
        overlay?.classList.remove('show');
    };

    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            if (isMobile()) {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('show');
                document.body.classList.remove('sidebar-collapsed');
            } else {
                closeMobileSidebar();
                document.body.classList.toggle('sidebar-collapsed');
                localStorage.setItem('leader_cashier_sidebar_collapsed', document.body.classList.contains('sidebar-collapsed') ? '1' : '0');
            }
        });
    }
    if (overlay) {
        overlay.addEventListener('click', () => {
            closeMobileSidebar();
        });
    }

    const isCollapsed = localStorage.getItem('leader_cashier_sidebar_collapsed');
    if (isCollapsed === '1') {
        document.body.classList.add('sidebar-collapsed');
    }

    window.addEventListener('resize', () => {
        if (!isMobile()) {
            closeMobileSidebar();
        }
    });

    // ===== FADE IN =====
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.05 });
    
    const initFadeIn = () => {
        document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));
    };
    
    document.addEventListener('turbo:load', initFadeIn);
    initFadeIn();

    // ===== TOAST GLOBAL =====
    window.showToast = (message, type = 'success') => {
      const toast = document.getElementById('accessToast');
      if (!toast) return;
      const icon = type === 'success' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-exclamation-circle"></i>';
      toast.innerHTML = icon + ' ' + message;
      toast.className = `toast ${type} show`;
      window.clearTimeout(window.__leaderToast);
      window.__leaderToast = window.setTimeout(() => toast.classList.remove('show'), 2800);
    };
  })();
</script>

@include('components.live-sync')
@stack('scripts')
</body>
</html>

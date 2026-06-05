<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', $cafeBrand['name'] ?? 'Superadmin')</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <link rel="stylesheet" href="{{ asset('css/superadmin/dashboard.css') }}?v={{ file_exists(public_path('css/superadmin/dashboard.css')) ? filemtime(public_path('css/superadmin/dashboard.css')) : '1' }}">

  @stack('head')

  {{-- Turbo & NProgress --}}
  <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@7.3.0/dist/turbo.es2017-umd.js"></script>
  <link rel="stylesheet" href="https://unpkg.com/nprogress@0.2.0/nprogress.css">
  <script src="https://unpkg.com/nprogress@0.2.0/nprogress.js"></script>
  
  <style>
    #nprogress .bar { background: var(--accent) !important; height: 3px !important; }
    #nprogress .spinner-icon { border-top-color: var(--accent) !important; border-left-color: var(--accent) !important; }
  </style>

  <style>
    .superadmin-menu-toggle {
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      width: 36px !important;
      height: 36px !important;
      min-width: 36px !important;
      padding: 0 !important;
      border: 1px solid var(--border) !important;
      border-radius: var(--radius-sm) !important;
      background: var(--white) !important;
      color: #111827 !important;
      font-family: var(--font) !important;
      font-size: 18px !important;
      line-height: 1 !important;
      font-weight: 900 !important;
      letter-spacing: 0 !important;
      text-indent: 0 !important;
      vertical-align: middle !important;
      cursor: pointer !important;
      box-shadow: none !important;
      transition: all var(--transition) !important;
    }

    .superadmin-menu-toggle:hover {
      border-color: var(--accent) !important;
      background: var(--accent-light) !important;
      color: #111827 !important;
      transform: none !important;
      box-shadow: none !important;
    }

    .superadmin-menu-toggle:active {
      transform: none !important;
      box-shadow: none !important;
    }

    .superadmin-menu-toggle:focus-visible {
      outline: 3px solid rgba(217, 119, 6, 0.15) !important;
      outline-offset: 2px !important;
    }

    .topbar .topbar-title {
      gap: 0;
    }

    .topbar .topbar-kicker {
      font-size: 1.25rem;
      line-height: 1.1;
      font-weight: 800;
      color: var(--fg);
      letter-spacing: 0;
    }

    .topbar .topbar-page-title {
      display: none;
    }

    .superadmin-page-heading {
      margin: 0 0 22px;
    }

    .superadmin-page-heading h1 {
      margin: 0;
      font-size: 1.35rem;
      line-height: 1.2;
      font-weight: 800;
      color: var(--fg);
      letter-spacing: 0;
    }

    .superadmin-page-heading p {
      margin: 6px 0 0;
      color: var(--fg-secondary);
      font-size: 0.95rem;
      line-height: 1.5;
    }

    @media (max-width: 768px) {
      .topbar .topbar-kicker {
        font-size: 1.1rem;
      }

      .superadmin-page-heading h1 {
        font-size: 1.15rem;
      }

      .superadmin-page-heading p {
        font-size: 0.85rem;
      }
    }

    body.sidebar-collapsed .superadmin-menu-toggle {
      background: var(--white) !important;
      border-color: var(--border) !important;
      color: #111827 !important;
      box-shadow: none !important;
    }

    .topbar-left {
      display: flex !important;
      align-items: center !important;
      gap: 16px !important;
      min-width: 0 !important;
    }

    @media (max-width: 768px) {
      .superadmin-menu-toggle {
        width: 36px !important;
        height: 36px !important;
        min-width: 36px !important;
        font-size: 18px !important;
      }
    }
  </style>
  @include('components.page-transition-guard')
</head>
<body data-turbo-prefetch="true">

  <div class="dashboard-layout">

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-brand">
        <div class="sidebar-logo">M</div>
        <div class="sidebar-brand-text">
          <span class="sidebar-brand-name">{{ $cafeBrand['name'] ?? 'cafecaf' }}</span>
          <span class="sidebar-brand-role">Superadmin Panel</span>
        </div>
      </div>
      <nav class="sidebar-nav">
        <div class="nav-section-title">UTAMA</div>
        <a href="{{ route('superadmin.dashboard') }}" class="nav-item {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-pie"></i> <span>Dashboard</span>
        </a>
        <a href="{{ route('superadmin.payments.index') }}" class="nav-item {{ request()->routeIs('superadmin.payments.*') ? 'active' : '' }}">
            <i class="fas fa-cash-register"></i> <span>Pembayaran Kasir</span>
        </a>
        <a href="{{ route('superadmin.menus.index') }}" class="nav-item {{ request()->routeIs('superadmin.menus.*') ? 'active' : '' }}">
            <i class="fas fa-utensils"></i> <span>Menu</span>
        </a>
        <a href="{{ route('superadmin.packages.index') }}" class="nav-item {{ request()->routeIs('superadmin.packages.*') ? 'active' : '' }}">
            <i class="fas fa-box-open"></i> <span>Paket Makanan</span>
        </a>
        <a href="{{ route('superadmin.promos.index') }}" class="nav-item {{ request()->routeIs('superadmin.promos.*') ? 'active' : '' }}" data-turbo="false">
            <i class="fas fa-tags"></i> <span>Promo</span>
        </a>
        <a href="{{ route('superadmin.tables.index') }}" class="nav-item {{ request()->routeIs('superadmin.tables.*') ? 'active' : '' }}">
            <i class="fas fa-chair"></i> <span>Meja</span>
        </a>

        <div class="nav-section-title">KEUANGAN</div>
        <a href="{{ route('superadmin.payrolls.index') }}" class="nav-item {{ request()->routeIs('superadmin.payrolls.*') ? 'active' : '' }}">
            <i class="fas fa-money-bill-wave"></i> <span>Gaji Karyawan</span>
        </a>
        <a href="{{ route('superadmin.reports.index') }}" class="nav-item {{ request()->routeIs('superadmin.reports.*') ? 'active' : '' }}">
            <i class="fas fa-file-invoice-dollar"></i> <span>Laporan</span>
        </a>

        <div class="nav-section-title">PENGATURAN</div>
        <a href="{{ route('superadmin.users.index') }}" class="nav-item {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}">
            <i class="fas fa-users-gear"></i> <span>Pengguna</span>
        </a>
        <a href="{{ route('superadmin.access.index') }}" class="nav-item {{ request()->routeIs('superadmin.access.*') ? 'active' : '' }}">
            <i class="fas fa-shield-halved"></i> <span>Hak Akses</span>
        </a>
        <a href="{{ route('superadmin.employees.index') }}" class="nav-item {{ request()->routeIs('superadmin.employees.*') ? 'active' : '' }}">
            <i class="fas fa-user-tie"></i> <span>Data Karyawan</span>
        </a>
        <a href="{{ route('superadmin.menu-categories.index') }}" class="nav-item {{ request()->routeIs('superadmin.menu-categories.*') ? 'active' : '' }}">
            <i class="fas fa-list"></i> <span>Kategori Menu</span>
        </a>
        <a href="{{ route('superadmin.settings.index') }}" class="nav-item {{ request()->routeIs('superadmin.settings.*') ? 'active' : '' }}" data-turbo="false">
            <i class="fas fa-sliders"></i> <span>Pengaturan Sistem</span>
        </a>
      </nav>
      <div class="sidebar-footer">
        <div class="sidebar-user" onclick="window.location.href='{{ route('profile.edit') }}'">
          <div class="sidebar-avatar">
            @if(auth()->user()->profile_photo_url)
                <img src="{{ auth()->user()->profile_photo_url }}" alt="Avatar" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
            @else
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            @endif
          </div>
          <div class="sidebar-user-info">
            <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
            <div class="sidebar-user-email">{{ auth()->user()->username }}</div>
          </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="margin-top: 10px;">
            @csrf
            <button type="submit" class="btn-sm" style="width:100%; justify-content:center; border-color:var(--red-light); color:var(--red);">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </button>
        </form>
      </div>
    </aside>

    <!-- SIDEBAR OVERLAY (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- MAIN CONTENT -->
    <div class="main-content">

      <!-- TOP BAR -->
      <header class="topbar">
        <div class="topbar-left">
          <button class="topbar-hamburger superadmin-menu-toggle" id="sidebarToggle" aria-label="Buka tutup sidebar" title="Buka tutup sidebar">=</button>
          <div class="topbar-title">
            <span class="topbar-kicker">@yield('kicker', $cafeBrand['name'] ?? 'cafecaf')</span>
            <span class="topbar-page-title">@yield('page_title', 'Dashboard')</span>
          </div>
        </div>
        @hasSection('topbar_right')
          @yield('topbar_right')
        @else
        <div class="topbar-right">
          <div class="topbar-search">
            <i class="fas fa-search"></i>
            <span>Cari...</span>
          </div>
          <button class="topbar-btn" title="Notifikasi">
            <i class="far fa-bell"></i>
            <span class="notif-dot"></span>
          </button>
          <a href="{{ route('superadmin.settings.index') }}" class="topbar-btn" title="Pengaturan"><i class="fas fa-gear"></i></a>
        </div>
        @endif
      </header>

      <!-- PAGE BODY -->
      <div class="page-body">
        @hasSection('page_title')
          @if(trim($__env->yieldContent('page_title')) !== '')
            <div class="superadmin-page-heading">
              <h1>@yield('page_title')</h1>
              @hasSection('page_description')
                <p>@yield('page_description')</p>
              @endif
            </div>
          @endif
        @endif
        @yield('content')
      </div><!-- /page-body -->
    </div><!-- /main-content -->
  </div><!-- /dashboard-layout -->

  <!-- TOAST CONTAINER -->
  <div class="toast-container" id="toastContainer"></div>

  <script>
    (function() {
        // Sidebar logic
        const toggleSidebarMobile = () => {
          document.getElementById('sidebar').classList.toggle('mobile-open');
          document.getElementById('sidebarOverlay').classList.toggle('show');
        };

        const closeSidebarMobile = () => {
          document.getElementById('sidebar').classList.remove('mobile-open');
          document.getElementById('sidebarOverlay').classList.remove('show');
          window.dispatchEvent(new CustomEvent('superadmin:sidebar-toggle', { detail: { collapsed: false, mobile: true, closed: true } }));
        };

        const toggleSidebarDesktop = () => {
          const isCollapsed = document.body.classList.toggle('sidebar-collapsed');
          localStorage.setItem('superadmin_sidebar_collapsed', isCollapsed ? '1' : '0');
          window.dispatchEvent(new CustomEvent('superadmin:sidebar-toggle', { detail: { collapsed: isCollapsed, mobile: false } }));
        };

        const initSidebar = () => {
            const btnMobile = document.getElementById('sidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');
            
            if(btnMobile) {
                btnMobile.replaceWith(btnMobile.cloneNode(true));
                const freshBtnMobile = document.getElementById('sidebarToggle');
                freshBtnMobile.addEventListener('click', () => {
                    if (window.innerWidth <= 1024) {
                        toggleSidebarMobile();
                    } else {
                        toggleSidebarDesktop();
                    }
                });
            }
            if(overlay) {
                overlay.addEventListener('click', closeSidebarMobile);
            }

            // Restore state
            if (localStorage.getItem('superadmin_sidebar_collapsed') === '1') {
                document.body.classList.add('sidebar-collapsed');
            }
        };

        // Entrance animations
        const initAnimations = () => {
            const fadeObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        fadeObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.05 });

            document.querySelectorAll('.fade-in').forEach(el => fadeObserver.observe(el));
        };

        // Toast system
        window.showToast = function(msg, icon = 'fa-check-circle') {
          const container = document.getElementById('toastContainer');
          if(!container) return;
          const toast = document.createElement('div');
          toast.className = 'toast';
          toast.innerHTML = '<i class="fas ' + icon + '"></i> ' + msg;
          container.appendChild(toast);
          setTimeout(() => {
              toast.style.opacity = '0';
              setTimeout(() => toast.remove(), 400);
          }, 2600);
        };

        // Turbo integration
        document.addEventListener('turbo:load', () => {
            initSidebar();
            initAnimations();
            
            const status = "{{ session('status') }}";
            const error = "{{ session('error') }}";
            if (status) window.showToast(status, 'fa-check-circle');
            if (error) window.showToast(error, 'fa-exclamation-circle');
        });

        // NProgress
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

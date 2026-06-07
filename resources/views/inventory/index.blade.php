<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inventory & Perlengkapan - {{ $cafeBrand['name'] ?? 'MakanYuk Cafe' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --white: #ffffff;
            --border: #E5E7EB;
            --border-light: #F3F4F6;
            --radius-lg: 16px;
            --radius-md: 10px;
            --radius-sm: 8px;
            --radius-full: 9999px;
            --accent: #D97706;
            --accent-dark: #B45309;
            --accent-light: #FFFBEB;
            --fg: #111827;
            --fg-secondary: #374151;
            --muted: #6B7280;
            --green: #059669;
            --green-light: #D1FAE5;
            --red: #DC2626;
            --red-light: #FEE2E2;
            --blue: #2563EB;
            --blue-light: #DBEAFE;
            --orange: #E65100;
            --orange-light: #FFF3E0;
            --bg: #F9FAFB;
            --shadow-xs: 0 1px 2px rgba(0,0,0,0.05);
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
            --font: 'Plus Jakarta Sans', -apple-system, sans-serif;
            --transition: 0.2s ease;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--font);
            background-color: var(--bg);
            color: var(--fg);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        .dashboard-layout {
            display: flex;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        .sidebar {
            background: var(--white);
            border-right: 1px solid var(--border);
            width: 260px;
            padding: 20px 16px 16px;
            display: flex;
            flex-direction: column;
            gap: 24px;
            overflow-y: auto;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1000;
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body.sidebar-collapsed .sidebar { transform: translateX(-100%); }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 900;
        }
        .sidebar-overlay.show { display: block; }

        .topbar-hamburger {
            display: flex; width: 36px; height: 36px; border: 1.5px solid var(--border);
            background: var(--white); border-radius: var(--radius-sm);
            align-items: center; justify-content: center; cursor: pointer;
            color: var(--fg-secondary); font-size: 20px; font-weight: 800;
            transition: all var(--transition);
        }
        .topbar-hamburger i { font-size: 15px; line-height: 1; }
        .topbar-hamburger:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-light); }

        .sidebar-brand {
            padding: 0 6px 16px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-logo {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .sidebar-brand h2 {
            font-size: 16px;
            font-weight: 900;
            color: var(--fg);
            letter-spacing: -0.3px;
            margin: 0;
        }

        .sidebar-brand p {
            font-size: 11px;
            color: var(--muted);
            margin: 0;
        }

        .sidebar-brand-text {
            display: flex;
            flex-direction: column;
        }

        .nav-menu {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .nav-section-title {
            font-size: 10px;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 12px 12px 6px;
        }

        .nav-item {
            text-decoration: none;
            color: var(--fg-secondary);
            background: transparent;
            border-radius: var(--radius-md);
            padding: 12px 16px;
            font-weight: 700;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all var(--transition);
        }

        .nav-item i {
            width: 20px;
            text-align: center;
            color: var(--muted);
        }

        .nav-item.active {
            background: var(--accent-light);
            color: var(--accent-dark);
        }

        .nav-item.active i {
            color: var(--accent);
        }

        .nav-item:hover {
            background: var(--border-light);
            color: var(--fg);
        }

        .sidebar-footer {
            margin-top: auto;
            border-top: 1px solid var(--border);
            padding-top: 16px;
        }

        .user-card {
            background: transparent;
            border: 0;
            border-radius: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .user-info-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px;
            border-radius: var(--radius-sm);
            transition: background var(--transition);
        }

        .user-info-row:hover { background: var(--bg); }

        .profile-photo {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-info-text span {
            display: block;
            font-size: 11px;
            color: var(--muted);
            font-weight: 600;
            text-transform: uppercase;
        }

        .user-info-text strong {
            display: block;
            font-size: 14px;
            color: var(--fg);
            font-weight: 800;
        }

        .logout-btn {
            background: transparent;
            color: var(--red);
            border: 1.5px solid #FECACA;
            padding: 10px;
            border-radius: var(--radius-md);
            font-weight: 800;
            cursor: pointer;
            width: 100%;
            font-size: 13px;
            transition: all var(--transition);
            font-family: var(--font);
        }

        .logout-btn:hover {
            background: var(--red-light);
            border-color: var(--red);
        }

        .main-content {
            flex: 1;
            margin-left: 260px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: margin-left 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body.sidebar-collapsed .main-content { margin-left: 0; }

        .topbar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 0 28px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-brand-title {
            font-size: 22px;
            font-weight: 900;
            color: var(--fg);
            letter-spacing: -0.6px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .topbar-btn {
            width: 38px;
            height: 38px;
            border: 1px solid var(--border);
            background: var(--white);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--fg-secondary);
            font-size: 15px;
            transition: all var(--transition);
            text-decoration: none;
        }

        .topbar-btn:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: var(--accent-light);
        }

        .page-body {
            flex: 1;
            padding: 28px;
            overflow-y: auto;
        }

        .dashboard-topbar {
            margin-bottom: 28px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 16px;
        }

        .dashboard-topbar h1 {
            font-size: 24px;
            font-weight: 900;
            color: var(--fg);
            margin: 4px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: -0.3px;
        }

        .dashboard-topbar h1 i { color: var(--accent); }

        .dashboard-topbar p {
            font-size: 14px;
            color: var(--muted);
            font-weight: 500;
        }

        .page-kicker {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--accent-light);
            color: var(--accent-dark);
            font-size: 11px;
            padding: 4px 10px;
            border-radius: var(--radius-full);
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .section-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-xs);
            margin-bottom: 24px;
        }

        .section-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 24px 28px 16px;
            border-bottom: 1px solid var(--border-light);
            flex-wrap: wrap;
        }

        .section-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
            font-weight: 900;
            color: var(--fg);
        }

        .section-card-title i {
            color: var(--accent);
            font-size: 16px;
        }

        .section-card-body {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead th {
            padding: 14px 24px;
            background: #FBFBFC;
            border-bottom: 1px solid var(--border);
            color: var(--muted);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            text-align: left;
            white-space: nowrap;
        }

        .data-table tbody td {
            padding: 16px 24px;
            border-bottom: 1px solid var(--border-light);
            vertical-align: middle;
            font-size: 14px;
            color: var(--fg-secondary);
            font-weight: 500;
        }

        .data-table tbody tr:last-child td { border-bottom: none; }
        .data-table tbody tr:hover { background: var(--bg); }

        .cell-item {
            font-weight: 800;
            color: var(--fg);
        }

        .amount {
            font-variant-numeric: tabular-nums;
            font-weight: 700;
        }

        .text-danger {
            color: var(--red);
            font-weight: 800;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: var(--radius-full);
            font-size: 12px;
            font-weight: 800;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: currentColor;
        }

        .badge-in { background: var(--green-light); color: var(--green); }
        .badge-out { background: var(--orange-light); color: var(--orange); }
        .badge-opname { background: var(--blue-light); color: var(--blue); }
        .badge-low { background: var(--red-light); color: var(--red); }
        .badge-low .status-dot { animation: dotPulse 1.5s infinite; }

        @keyframes dotPulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 18px;
            border-radius: var(--radius-md);
            font-weight: 800;
            font-size: 13px;
            cursor: pointer;
            transition: all var(--transition);
            font-family: var(--font);
            border: 1.5px solid transparent;
            text-decoration: none;
        }

        .btn:hover { transform: translateY(-1px); }

        .btn-primary {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }

        .btn-primary:hover {
            background: var(--accent-dark);
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.25);
        }

        .btn-delete {
            background: transparent;
            color: var(--red);
            border: 1px solid #FECACA;
            padding: 6px 12px;
            font-size: 12px;
            border-radius: var(--radius-sm);
        }

        .btn-delete:hover {
            background: var(--red-light);
            border-color: var(--red);
        }

        .drawer-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            backdrop-filter: blur(2px);
            z-index: 1200;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
        }

        .drawer-backdrop.open {
            opacity: 1;
            visibility: visible;
        }

        .inventory-drawer {
            position: fixed;
            top: 0;
            right: 0;
            width: min(520px, 95vw);
            height: 100vh;
            background: var(--white);
            z-index: 1201;
            transform: translateX(102%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-lg);
            display: flex;
            flex-direction: column;
        }

        .inventory-drawer.open { transform: translateX(0); }

        .drawer-head {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }

        .drawer-head h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 900;
            color: var(--fg);
        }

        .drawer-close {
            border: 1px solid var(--border);
            background: var(--white);
            color: var(--fg-secondary);
            border-radius: var(--radius-sm);
            padding: 8px 14px;
            cursor: pointer;
            font-weight: 700;
            font-size: 12px;
            font-family: var(--font);
            transition: all var(--transition);
        }

        .drawer-close:hover {
            border-color: var(--red);
            color: var(--red);
            background: var(--red-light);
        }

        .drawer-body {
            padding: 28px 24px;
            overflow-y: auto;
            flex: 1;
        }

        .drawer-field {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 20px;
        }

        .drawer-field label {
            font-size: 12px;
            font-weight: 800;
            color: var(--fg-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .drawer-field input,
        .drawer-field select,
        .drawer-field textarea {
            width: 100%;
            padding: 12px 14px;
            border-radius: var(--radius-sm);
            border: 1.5px solid var(--border);
            background: var(--white);
            color: var(--fg);
            font-size: 14px;
            font-weight: 500;
            outline: none;
            transition: all var(--transition);
            font-family: var(--font);
        }

        .drawer-field input:focus,
        .drawer-field select:focus,
        .drawer-field textarea:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1);
        }

        .drawer-foot {
            padding: 20px 24px;
            border-top: 1px solid var(--border);
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            background: var(--bg);
            flex-shrink: 0;
        }

        .btn-drawer-cancel {
            border: 1.5px solid var(--border);
            background: var(--white);
            color: var(--fg-secondary);
            border-radius: var(--radius-sm);
            padding: 11px 22px;
            cursor: pointer;
            font-weight: 800;
            font-size: 13px;
            font-family: var(--font);
            transition: all var(--transition);
        }

        .btn-drawer-cancel:hover {
            border-color: var(--red);
            color: var(--red);
            background: var(--red-light);
        }

        .drawer-error {
            background: var(--red-light);
            color: var(--red);
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            border: 1px solid #FECACA;
            font-weight: 700;
            font-size: 13px;
            margin-top: 12px;
            display: none;
        }

        .toast-wrap {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
            width: min(360px, 90vw);
        }

        .toast-item {
            background: var(--fg);
            color: white;
            padding: 14px 20px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            font-size: 13px;
            font-weight: 700;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            animation: toast-in 0.3s ease;
        }

        .toast-item.success { border-left: 5px solid var(--green); }
        .toast-item.error { border-left: 5px solid var(--red); }
        .toast-item button {
            background: transparent;
            border: none;
            color: rgba(255,255,255,0.6);
            cursor: pointer;
            font-size: 16px;
        }

        @keyframes toast-in {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .pagination-area {
            padding: 20px 24px;
            border-top: 1px solid var(--border-light);
        }

        .pagination-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .pagination-meta {
            color: var(--muted);
            font-size: 12px;
            font-weight: 600;
        }

        .pagination-links {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 4px;
            flex-wrap: wrap;
        }

        .pagination-link,
        .pagination-dots {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            border: 1px solid var(--border);
            color: var(--fg-secondary);
            padding: 0 10px;
            background: var(--white);
            transition: all var(--transition);
        }

        .pagination-link:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: var(--accent-light);
        }

        .pagination-link.active {
            background: var(--accent);
            border-color: var(--accent);
            color: white;
        }

        .pagination-link.disabled {
            opacity: 0.35;
            pointer-events: none;
        }

        .empty-state {
            padding: 30px 24px;
            text-align: center;
            color: var(--muted);
            font-size: 13px;
        }

        .action-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.mobile-open {
                transform: translateX(0);
                box-shadow: var(--shadow-lg);
            }
            .main-content {
                margin-left: 0;
            }
        }

        @media (max-width: 768px) {
            .page-body { padding: 16px; }
            .dashboard-topbar { flex-direction: column; align-items: flex-start; }
            .data-table { min-width: 800px; }
            .section-card-header { padding: 18px 18px 14px; }
        }
    </style>
</head>
<body>
    @php
        $bahanItems = $allItems->where('type', 'bahan')->values();
        $barangItems = $allItems->where('type', 'barang')->values();
        $isInventoryTab = ($activeTab ?? 'inventory') === 'inventory';
        $isMovementTab = ($activeTab ?? '') === 'movement';
    @endphp

    <div class="dashboard-layout">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <img src="{{ $cafeBrand['logo_url'] ?: 'https://placehold.co/56x56/D97706/FFFFFF?text=MY' }}" alt="{{ $cafeBrand['name'] ?? 'MakanYuk Cafe' }}" class="sidebar-logo">
                <div class="sidebar-brand-text">
                    <h2>{{ $cafeBrand['name'] ?? 'MakanYuk Cafe' }}</h2>
                    <p>Panel Gudang</p>
                </div>
            </div>

            <nav class="nav-menu">
                <div class="nav-section-title">Utama</div>
                <a class="nav-item {{ $isInventoryTab ? 'active' : '' }}" href="{{ route('inventory.index') }}">
                    <i class="fas fa-boxes-stacked"></i> Inventory
                </a>
                <a class="nav-item {{ $isMovementTab ? 'active' : '' }}" href="{{ route('inventory.in.page') }}">
                    <i class="fas fa-arrow-right-arrow-left"></i> Barang Masuk/Keluar
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="user-card">
                    <div class="user-info-row">
                        <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" class="profile-photo">
                        <div class="user-info-text">
                            <span>Login sebagai</span>
                            <strong>{{ auth()->user()->name ?? 'Admin Gudang' }}</strong>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout-btn" type="submit"><i class="fas fa-right-from-bracket"></i> Logout</button>
                </form>
            </div>
        </aside>

        <div class="main-content">
            <header class="topbar">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <button class="topbar-hamburger" id="sidebarToggle" type="button" title="Toggle Sidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    <span class="topbar-brand-title">{{ $cafeBrand['name'] ?? 'MakanYuk' }}</span>
                </div>
                <div class="topbar-right">
                    <a href="{{ route('profile.edit') }}" class="topbar-btn" title="Profil"><i class="fas fa-user"></i></a>
                </div>
            </header>

            <main class="page-body">
            <div class="dashboard-topbar">
                <div>
                    <span class="page-kicker"><i class="fas fa-warehouse"></i> ERP System</span>
                    <h1>
                        <i class="fas {{ $isInventoryTab ? 'fa-boxes-stacked' : 'fa-arrow-right-arrow-left' }}"></i>
                        {{ $isInventoryTab ? 'Inventory & Perlengkapan' : 'Barang Masuk/Keluar' }}
                    </h1>
                    <p>
                        {{ $isInventoryTab
                            ? 'Kelola bahan baku dan perlengkapan (panci, sendok, dll) di satu tempat.'
                            : 'Catat barang masuk, barang keluar, dan histori perpindahan stok gudang.' }}
                    </p>
                </div>

                @if ($isInventoryTab)
                    <div class="action-row">
                        <button class="btn btn-primary" type="button" onclick="openDrawer('category')"><i class="fas fa-folder-plus"></i> Kategori</button>
                        <button class="btn btn-primary" type="button" onclick="openDrawer('bahan')"><i class="fas fa-seedling"></i> Bahan Baku</button>
                        <button class="btn btn-primary" type="button" onclick="openDrawer('barang')"><i class="fas fa-blender"></i> Perlengkapan</button>
                        <button class="btn btn-primary" type="button" onclick="openDrawer('opname')"><i class="fas fa-clipboard-check"></i> Opname</button>
                    </div>
                @else
                    <div class="action-row">
                        <button class="btn btn-primary" type="button" onclick="openDrawer('stock_in')"><i class="fas fa-arrow-down"></i> Barang Masuk</button>
                        <button class="btn btn-primary" type="button" onclick="openDrawer('stock_out')"><i class="fas fa-arrow-up"></i> Barang Keluar</button>
                    </div>
                @endif
            </div>

            @if (session('success'))
                <div class="section-card" style="padding: 16px 20px; color: var(--green); border-color: #A7F3D0; background: var(--green-light); font-weight: 800;">
                    {{ session('success') }}
                </div>
            @endif

            @if ($isInventoryTab)
                <section class="section-card">
                    <div class="section-card-header">
                        <div class="section-card-title"><i class="fas fa-seedling"></i> Stok Bahan Baku</div>
                        <button class="btn-delete btn-delete-all" type="button" data-delete-type="bahan">
                            <i class="fas fa-trash-can"></i> Hapus Semua
                        </button>
                    </div>
                    <div class="section-card-body">
                        <table class="data-table" id="tableBahan">
                            <thead>
                                <tr>
                                    <th>Nama Bahan</th>
                                    <th>Kategori</th>
                                    <th>Baik</th>
                                    <th>Kurang Baik</th>
                                    <th>Rusak</th>
                                    <th>Total</th>
                                    <th>Min. Stok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bahanItems as $item)
                                    @php $isLow = (float) $item->total_stock <= (float) $item->min_stock; @endphp
                                    <tr data-item-id="{{ $item->id }}">
                                        <td><span class="cell-item">{{ $item->name }}</span></td>
                                        <td>{{ $item->category?->name ?? '-' }} ({{ $item->unit }})</td>
                                        <td><span class="amount">{{ rtrim(rtrim(number_format((float) $item->stock_good, 2, '.', ''), '0'), '.') }}</span></td>
                                        <td><span class="amount">{{ rtrim(rtrim(number_format((float) $item->stock_less_good, 2, '.', ''), '0'), '.') }}</span></td>
                                        <td><span class="amount text-danger">{{ rtrim(rtrim(number_format((float) $item->stock_damaged, 2, '.', ''), '0'), '.') }}</span></td>
                                        <td>
                                            <span class="amount {{ $isLow ? 'text-danger' : '' }}">
                                                {{ rtrim(rtrim(number_format((float) $item->total_stock, 2, '.', ''), '0'), '.') }} {{ $item->unit }}
                                            </span>
                                        </td>
                                        <td><span class="amount">{{ rtrim(rtrim(number_format((float) $item->min_stock, 2, '.', ''), '0'), '.') }}</span></td>
                                        <td>
                                            @if ($isLow)
                                                <span class="status-badge badge-low" style="margin-right: 8px;"><span class="status-dot"></span> Stok Rendah</span>
                                            @endif
                                            <button class="btn-delete btn-delete-item" type="button" data-item-id="{{ $item->id }}"><i class="fas fa-trash"></i> Hapus</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="8" class="empty-state">Belum ada data bahan baku.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="section-card">
                    <div class="section-card-header">
                        <div class="section-card-title"><i class="fas fa-blender"></i> Stok Barang & Perlengkapan</div>
                        <button class="btn-delete btn-delete-all" type="button" data-delete-type="barang">
                            <i class="fas fa-trash-can"></i> Hapus Semua
                        </button>
                    </div>
                    <div class="section-card-body">
                        <table class="data-table" id="tableBarang">
                            <thead>
                                <tr>
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th>Baik</th>
                                    <th>Kurang Baik</th>
                                    <th>Rusak</th>
                                    <th>Total</th>
                                    <th>Satuan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($barangItems as $item)
                                    @php $isLow = (float) $item->total_stock <= (float) $item->min_stock && (float) $item->min_stock > 0; @endphp
                                    <tr data-item-id="{{ $item->id }}">
                                        <td><span class="cell-item">{{ $item->name }}</span></td>
                                        <td>{{ $item->category?->name ?? '-' }}</td>
                                        <td><span class="amount">{{ (int) $item->stock_good }}</span></td>
                                        <td><span class="amount">{{ (int) $item->stock_less_good }}</span></td>
                                        <td><span class="amount text-danger">{{ (int) $item->stock_damaged }}</span></td>
                                        <td><span class="amount {{ $isLow ? 'text-danger' : '' }}">{{ (int) $item->total_stock }}</span></td>
                                        <td>{{ $item->unit }}</td>
                                        <td>
                                            @if ($isLow)
                                                <span class="status-badge badge-low" style="margin-right: 8px;"><span class="status-dot"></span> Stok Rendah</span>
                                            @endif
                                            <button class="btn-delete btn-delete-item" type="button" data-item-id="{{ $item->id }}"><i class="fas fa-trash"></i> Hapus</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="8" class="empty-state">Belum ada data barang/perlengkapan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif

            @if ($isMovementTab)
                <section class="section-card">
                    <div class="section-card-header">
                        <div class="section-card-title"><i class="fas fa-clock-rotate-left"></i> Riwayat Barang Masuk/Keluar</div>
                        <button class="btn-delete btn-delete-all-movements" type="button">
                            <i class="fas fa-trash-can"></i> Hapus Semua
                        </button>
                    </div>
                    <div class="section-card-body">
                        <table class="data-table" id="tableMovements">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Bahan/Barang</th>
                                    <th>Tipe</th>
                                    <th>Kondisi</th>
                                    <th>Qty</th>
                                    <th>Catatan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($movements as $movement)
                                    @php
                                        $typeClass = match ($movement->type) {
                                            'in' => 'badge-in',
                                            'out' => 'badge-out',
                                            default => 'badge-opname',
                                        };
                                        $typeLabel = match ($movement->type) {
                                            'in' => 'Masuk',
                                            'out' => 'Keluar',
                                            default => 'Opname',
                                        };
                                        $conditionLabel = match ($movement->stock_condition) {
                                            'good' => 'Baik',
                                            'less_good' => 'Kurang Baik',
                                            'damaged' => 'Rusak',
                                            default => '-',
                                        };
                                        if ($movement->type === 'opname' && $movement->to_stock_condition) {
                                            $toLabel = match ($movement->to_stock_condition) {
                                                'good' => 'Baik',
                                                'less_good' => 'Kurang Baik',
                                                'damaged' => 'Rusak',
                                                default => '-',
                                            };
                                            $conditionLabel .= ' -> ' . $toLabel;
                                        }
                                    @endphp
                                    <tr data-movement-id="{{ $movement->id }}">
                                        <td>{{ optional($movement->moved_at)->format('d M Y, H:i') ?? '-' }}</td>
                                        <td><span class="cell-item">{{ $movement->item?->name ?? '-' }}</span></td>
                                        <td><span class="status-badge {{ $typeClass }}"><span class="status-dot"></span> {{ $typeLabel }}</span></td>
                                        <td>{{ $conditionLabel }}</td>
                                        <td><span class="amount">{{ rtrim(rtrim(number_format((float) $movement->qty, 2, '.', ''), '0'), '.') }} {{ $movement->item?->unit ?? '' }}</span></td>
                                        <td>{{ $movement->notes ?: '-' }}</td>
                                        <td>
                                            <button class="btn-delete btn-delete-movement" type="button" data-movement-id="{{ $movement->id }}">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="empty-state">Belum ada riwayat pergerakan stok.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination-area">
                        {{ $movements->links('components.pagination') }}
                    </div>
                </section>
            @endif
            </main>
        </div>
    </div>

    <div class="drawer-backdrop" id="drawerBackdrop"></div>
    <aside class="inventory-drawer" id="inventoryDrawer" aria-hidden="true">
        <div class="drawer-head">
            <h3 id="drawerTitle">Form Inventory</h3>
            <button type="button" class="drawer-close" onclick="closeDrawer()">Tutup</button>
        </div>
        <div class="drawer-body">
            <form id="formCategory" method="POST" action="{{ route('inventory.categories.store') }}" style="display:none;">
                @csrf
                <div class="drawer-field">
                    <label>Tipe kategori</label>
                    <select name="type" required>
                        <option value="">Pilih tipe</option>
                        <option value="bahan">Bahan Baku</option>
                        <option value="barang">Barang/Perlengkapan</option>
                    </select>
                </div>
                <div class="drawer-field">
                    <label>Nama kategori</label>
                    <input name="name" placeholder="Contoh: Alat Masak" required>
                </div>
                <div class="drawer-field">
                    <label>Unit default</label>
                    <select name="unit" required>
                        <option value="">Pilih unit</option>
                        <option value="kg">kg</option>
                        <option value="pcs">pcs</option>
                        <option value="set">set</option>
                        <option value="liter">liter</option>
                        <option value="pack">pack</option>
                    </select>
                </div>
            </form>

            <form id="formBahan" method="POST" action="{{ route('inventory.items.store') }}" style="display:none;">
                @csrf
                <div class="drawer-field">
                    <label>Kategori Bahan Baku</label>
                    <select name="inventory_category_id" id="selectBahanCat" required>
                        <option value="">Pilih kategori bahan</option>
                        @foreach ($categories->where('type', 'bahan') as $category)
                            <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->unit }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="drawer-field">
                    <label>Nama bahan</label>
                    <input name="name" placeholder="Contoh: Biji Kopi Arabica" required>
                </div>
                <div class="drawer-field">
                    <label>Stok minimum (peringatan)</label>
                    <input type="number" step="0.01" min="0" name="min_stock" value="0">
                </div>
            </form>

            <form id="formBarang" method="POST" action="{{ route('inventory.items.store') }}" style="display:none;">
                @csrf
                <div class="drawer-field">
                    <label>Kategori Barang/Perlengkapan</label>
                    <select name="inventory_category_id" id="selectBarangCat" required>
                        <option value="">Pilih kategori barang</option>
                        @foreach ($categories->where('type', 'barang') as $category)
                            <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->unit }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="drawer-field">
                    <label>Nama barang</label>
                    <input name="name" placeholder="Contoh: Panci Stainless / Kompor Gas" required>
                </div>
            </form>

            <form id="formOpname" method="POST" action="{{ route('inventory.stock.opname') }}" style="display:none;">
                @csrf
                <div class="drawer-field">
                    <label>Bahan/Barang</label>
                    <select name="inventory_item_id" required>
                        <option value="">Pilih bahan/barang</option>
                        @foreach ($allItems as $item)
                            <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="drawer-field">
                    <label>Dari kondisi</label>
                    <select name="from_condition" required>
                        <option value="">Pilih kondisi asal</option>
                        <option value="good">Baik</option>
                        <option value="less_good">Kurang Baik</option>
                        <option value="damaged">Rusak</option>
                    </select>
                </div>
                <div class="drawer-field">
                    <label>Ke kondisi</label>
                    <select name="to_condition" required>
                        <option value="">Pilih kondisi tujuan</option>
                        <option value="good">Baik</option>
                        <option value="less_good">Kurang Baik</option>
                        <option value="damaged">Rusak</option>
                    </select>
                </div>
                <div class="drawer-field">
                    <label>Qty opname</label>
                    <input type="number" step="0.01" min="0.01" name="qty" required>
                </div>
                <div class="drawer-field">
                    <label>Catatan</label>
                    <textarea name="notes" rows="3" placeholder="Contoh: 2kg tomat busuk, pindah dari baik ke rusak"></textarea>
                </div>
            </form>

            <form id="formStockIn" method="POST" action="{{ route('inventory.stock.in') }}" style="display:none;">
                @csrf
                <div class="drawer-field">
                    <label>Bahan/Barang</label>
                    <select name="inventory_item_id" required>
                        <option value="">Pilih bahan/barang</option>
                        @foreach ($allItems as $item)
                            <option value="{{ $item->id }}">{{ $item->name }} ({{ rtrim(rtrim(number_format((float) $item->total_stock, 2, '.', ''), '0'), '.') }} {{ $item->unit }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="drawer-field">
                    <label>Kondisi stok masuk</label>
                    <select name="stock_condition" required>
                        <option value="">Pilih kondisi</option>
                        <option value="good">Baik</option>
                        <option value="less_good">Kurang Baik</option>
                        <option value="damaged">Rusak</option>
                    </select>
                </div>
                <div class="drawer-field">
                    <label>Jumlah masuk</label>
                    <input type="number" step="0.01" min="0.01" name="qty" placeholder="Contoh: 10" required>
                </div>
                <div class="drawer-field">
                    <label>Catatan</label>
                    <textarea name="notes" rows="3" placeholder="Contoh: pembelian supplier A"></textarea>
                </div>
            </form>

            <form id="formStockOut" method="POST" action="{{ route('inventory.stock.out') }}" style="display:none;">
                @csrf
                <div class="drawer-field">
                    <label>Bahan/Barang</label>
                    <select name="inventory_item_id" required>
                        <option value="">Pilih bahan/barang</option>
                        @foreach ($allItems as $item)
                            <option value="{{ $item->id }}">{{ $item->name }} ({{ rtrim(rtrim(number_format((float) $item->total_stock, 2, '.', ''), '0'), '.') }} {{ $item->unit }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="drawer-field">
                    <label>Ambil dari kondisi</label>
                    <select name="stock_condition" required>
                        <option value="">Pilih kondisi</option>
                        <option value="good">Baik</option>
                        <option value="less_good">Kurang Baik</option>
                        <option value="damaged">Rusak</option>
                    </select>
                </div>
                <div class="drawer-field">
                    <label>Jumlah keluar</label>
                    <input type="number" step="0.01" min="0.01" name="qty" placeholder="Contoh: 2" required>
                </div>
                <div class="drawer-field">
                    <label>Dipakai untuk</label>
                    <input type="text" name="used_for" placeholder="Contoh: Produksi harian" required>
                </div>
                <div class="drawer-field">
                    <label>Bahan/barang dipakai</label>
                    <input type="text" name="used_items" placeholder="Contoh: 2 panci, 5kg gula" required>
                </div>
                <div class="drawer-field">
                    <label>Catatan / Alasan Keluar</label>
                    <textarea name="notes" rows="3" placeholder="Contoh: Panci rusak dibuang / bahan untuk masak hari ini"></textarea>
                </div>
            </form>

            <div id="drawerError" class="drawer-error"></div>
        </div>
        <div class="drawer-foot">
            <button type="button" class="btn-drawer-cancel" onclick="closeDrawer()">Batal</button>
            <button type="button" class="btn btn-primary" id="btnSubmitDrawer" onclick="submitActiveForm()"><i class="fas fa-check"></i> Simpan Data</button>
        </div>
    </aside>

    <div id="toastWrap" class="toast-wrap"></div>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <script>
        (function () {
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
                    localStorage.setItem('inventory_sidebar_collapsed', document.body.classList.contains('sidebar-collapsed') ? '1' : '0');
                });

                overlay.addEventListener('click', closeMobileSidebar);

                window.addEventListener('resize', () => {
                    if (!isMobile()) {
                        closeMobileSidebar();
                    }
                });

                const isCollapsed = localStorage.getItem('inventory_sidebar_collapsed');
                if (isCollapsed === '1') {
                    document.body.classList.add('sidebar-collapsed');
                }
            };
            initSidebar();

            const inventoryDrawer = document.getElementById('inventoryDrawer');
            const drawerBackdrop = document.getElementById('drawerBackdrop');
            const toastWrap = document.getElementById('toastWrap');
            const btnSubmit = document.getElementById('btnSubmitDrawer');
            let activeFormId = null;

            window.showToast = function (message, type = 'success') {
                const el = document.createElement('div');
                el.className = 'toast-item ' + type;
                el.innerHTML = '<span>' + String(message) + '</span><button type="button"><i class="fas fa-times"></i></button>';
                el.querySelector('button').onclick = () => el.remove();
                toastWrap.appendChild(el);
                setTimeout(() => {
                    el.style.opacity = '0';
                    setTimeout(() => el.remove(), 200);
                }, 3500);
            };

            const titleMap = {
                category: 'Input Kategori Baru',
                bahan: 'Tambah Bahan Baku',
                barang: 'Tambah Perlengkapan',
                opname: 'Stok Opname',
                stock_in: 'Barang Masuk',
                stock_out: 'Barang Keluar',
            };

            const formMap = {
                category: 'formCategory',
                bahan: 'formBahan',
                barang: 'formBarang',
                opname: 'formOpname',
                stock_in: 'formStockIn',
                stock_out: 'formStockOut',
            };

            window.openDrawer = (type) => {
                document.querySelectorAll('.inventory-drawer form').forEach((form) => {
                    form.style.display = 'none';
                });
                activeFormId = formMap[type] || null;
                if (!activeFormId) return;
                document.getElementById(activeFormId).style.display = 'block';
                document.getElementById('drawerTitle').textContent = titleMap[type] || 'Form Inventory';
                document.getElementById('drawerError').style.display = 'none';
                inventoryDrawer.classList.add('open');
                drawerBackdrop.classList.add('open');
                inventoryDrawer.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            };

            window.closeDrawer = () => {
                inventoryDrawer.classList.remove('open');
                drawerBackdrop.classList.remove('open');
                inventoryDrawer.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
                document.getElementById('drawerError').style.display = 'none';
                document.querySelectorAll('.inventory-drawer form').forEach((form) => form.reset());
            };

            const formatAmount = (value, type) => {
                const numeric = Number(value || 0);
                if (type === 'barang') {
                    return String(parseInt(numeric, 10));
                }
                return String(numeric.toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2,
                }));
            };

            const buildInventoryRow = (item) => {
                const isBahan = item.type === 'bahan';
                const total = Number(item.total_stock || 0);
                const minStock = Number(item.min_stock || 0);
                const isLow = total <= minStock && minStock > 0;

                return `
                    <tr data-item-id="${item.id}">
                        <td><span class="cell-item">${item.name}</span></td>
                        <td>${item.category?.name || '-'}${isBahan ? ` (${item.unit})` : ''}</td>
                        <td><span class="amount">${formatAmount(item.stock_good, item.type)}</span></td>
                        <td><span class="amount">${formatAmount(item.stock_less_good, item.type)}</span></td>
                        <td><span class="amount text-danger">${formatAmount(item.stock_damaged, item.type)}</span></td>
                        <td><span class="amount ${isLow ? 'text-danger' : ''}">${formatAmount(item.total_stock, item.type)}${isBahan ? ` ${item.unit}` : ''}</span></td>
                        <td>${isBahan ? `<span class="amount">${formatAmount(item.min_stock, item.type)}</span>` : item.unit}</td>
                        <td>
                            ${isLow ? '<span class="status-badge badge-low" style="margin-right: 8px;"><span class="status-dot"></span> Stok Rendah</span>' : ''}
                            <button class="btn-delete btn-delete-item" type="button" data-item-id="${item.id}"><i class="fas fa-trash"></i> Hapus</button>
                        </td>
                    </tr>
                `;
            };

            const bindDeleteEvents = () => {
                document.querySelectorAll('.btn-delete-item').forEach((btn) => {
                    btn.onclick = async () => {
                        const id = btn.dataset.itemId;
                        if (!id || !confirm('Hapus item ini dari inventory?')) return;

                        try {
                            const formData = new FormData();
                            formData.append('_method', 'DELETE');

                            const res = await fetch(`{{ url('gudang/items') }}/${id}`, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                                body: formData,
                            });

                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Gagal menghapus item.');

                            document.querySelector(`tr[data-item-id="${id}"]`)?.remove();
                            document.querySelectorAll('#tableBahan tbody, #tableBarang tbody').forEach((tbody) => {
                                if (!tbody.querySelector('tr')) {
                                    tbody.innerHTML = '<tr class="empty-row"><td colspan="8" class="empty-state">Belum ada data.</td></tr>';
                                }
                            });
                            window.showToast(data.message || 'Item berhasil dihapus.');
                        } catch (error) {
                            window.showToast(error.message || 'Gagal menghapus item.', 'error');
                        }
                    };
                });

                document.querySelectorAll('.btn-delete-all').forEach((btn) => {
                    btn.onclick = async () => {
                        const type = btn.dataset.deleteType;
                        if (!type) return;

                        const label = type === 'barang' ? 'barang/perlengkapan' : 'bahan baku';
                        if (!confirm(`Hapus semua data ${label}?`)) return;

                        try {
                            const formData = new FormData();
                            formData.append('_method', 'DELETE');

                            const res = await fetch(`{{ url('gudang/items/type') }}/${type}`, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                                body: formData,
                            });

                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Gagal menghapus semua data.');

                            const tableId = type === 'barang' ? 'tableBarang' : 'tableBahan';
                            const tableBody = document.querySelector(`#${tableId} tbody`);
                            if (tableBody) {
                                tableBody.innerHTML = '<tr class="empty-row"><td colspan="8" class="empty-state">Belum ada data.</td></tr>';
                            }

                            window.showToast(data.message || 'Semua data berhasil dihapus.');
                        } catch (error) {
                            window.showToast(error.message || 'Gagal menghapus semua data.', 'error');
                        }
                    };
                });

                document.querySelectorAll('.btn-delete-movement').forEach((btn) => {
                    btn.onclick = async () => {
                        const id = btn.dataset.movementId;
                        if (!id || !confirm('Hapus riwayat ini?')) return;

                        try {
                            const formData = new FormData();
                            formData.append('_method', 'DELETE');

                            const res = await fetch(`{{ url('gudang/movements') }}/${id}`, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                                body: formData,
                            });

                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Gagal menghapus riwayat.');

                            document.querySelector(`tr[data-movement-id="${id}"]`)?.remove();
                            const movementBody = document.querySelector('#tableMovements tbody');
                            if (movementBody && !movementBody.querySelector('tr')) {
                                movementBody.innerHTML = '<tr><td colspan="7" class="empty-state">Belum ada riwayat pergerakan stok.</td></tr>';
                            }
                            window.showToast(data.message || 'Riwayat berhasil dihapus.');
                        } catch (error) {
                            window.showToast(error.message || 'Gagal menghapus riwayat.', 'error');
                        }
                    };
                });

                const deleteAllMovementsButton = document.querySelector('.btn-delete-all-movements');
                if (deleteAllMovementsButton) {
                    deleteAllMovementsButton.onclick = async () => {
                        if (!confirm('Hapus semua riwayat barang masuk/keluar?')) return;

                        try {
                            const formData = new FormData();
                            formData.append('_method', 'DELETE');

                            const res = await fetch(`{{ route('inventory.movements.destroy-all') }}`, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                                body: formData,
                            });

                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Gagal menghapus semua riwayat.');

                            const movementBody = document.querySelector('#tableMovements tbody');
                            if (movementBody) {
                                movementBody.innerHTML = '<tr><td colspan="7" class="empty-state">Belum ada riwayat pergerakan stok.</td></tr>';
                            }
                            window.showToast(data.message || 'Semua riwayat berhasil dihapus.');
                        } catch (error) {
                            window.showToast(error.message || 'Gagal menghapus semua riwayat.', 'error');
                        }
                    };
                }
            };

            window.submitActiveForm = async () => {
                if (!activeFormId) return;
                const form = document.getElementById(activeFormId);
                if (!(form instanceof HTMLFormElement)) return;

                const formData = new FormData(form);
                const drawerError = document.getElementById('drawerError');

                if (['formStockIn', 'formStockOut', 'formOpname'].includes(activeFormId)) {
                    form.submit();
                    return;
                }

                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
                drawerError.style.display = 'none';

                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: formData,
                    });

                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Gagal menyimpan data.');

                    if (activeFormId === 'formCategory') {
                        const category = data.category;
                        const selectId = category.type === 'bahan' ? 'selectBahanCat' : 'selectBarangCat';
                        const select = document.getElementById(selectId);
                        if (select) {
                            const option = new Option(`${category.name} (${category.unit})`, category.id);
                            select.add(option);
                        }
                        window.showToast(data.message || 'Kategori berhasil ditambahkan.');
                    } else {
                        const item = data.item;
                        const tableId = item.type === 'bahan' ? 'tableBahan' : 'tableBarang';
                        const tbody = document.querySelector(`#${tableId} tbody`);
                        tbody?.querySelector('.empty-row')?.remove();
                        tbody?.insertAdjacentHTML('afterbegin', buildInventoryRow(item));
                        bindDeleteEvents();
                        window.showToast(data.message || 'Item berhasil ditambahkan.');
                    }

                    closeDrawer();
                } catch (error) {
                    drawerError.textContent = error.message || 'Gagal menyimpan data.';
                    drawerError.style.display = 'block';
                } finally {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="fas fa-check"></i> Simpan Data';
                }
            };

            bindDeleteEvents();
            drawerBackdrop.addEventListener('click', closeDrawer);

            setInterval(async () => {
                try {
                    const res = await fetch('{{ route('inventory.live') }}', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    (data.items || []).forEach((item) => {
                        const row = document.querySelector(`tr[data-item-id="${item.id}"]`);
                        if (!row) return;

                        const cells = row.querySelectorAll('td');
                        if (cells.length < 7) return;

                        cells[2].querySelector('.amount').textContent = formatAmount(item.stock_good, item.type);
                        cells[3].querySelector('.amount').textContent = formatAmount(item.stock_less_good, item.type);
                        cells[4].querySelector('.amount').textContent = formatAmount(item.stock_damaged, item.type);
                        if (item.type === 'bahan') {
                            cells[5].querySelector('.amount').textContent = `${formatAmount(item.total_stock, item.type)} ${item.unit}`;
                            cells[6].querySelector('.amount').textContent = formatAmount(item.min_stock, item.type);
                        } else {
                            cells[5].querySelector('.amount').textContent = formatAmount(item.total_stock, item.type);
                            cells[6].textContent = item.unit;
                        }
                    });
                } catch (e) {
                }
            }, 4000);
        })();
    </script>
    @include('components.live-sync')
</body>
</html>

@extends('superadmin.layout')

@section('title', 'Paket Makanan — cafecaf')
@section('page_title', 'Paket Makanan')
@section('page_description', '')

@push('head')
    <style>
    /* ===== VARIABEL DESAIN ===== */
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

    /* ===== CONTENT TOOLBAR ===== */
    .content-toolbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      flex-wrap: wrap;
      gap: 12px;
    }

    .search-box { display: flex; gap: 6px; }
    .search-box input {
      border: 1.5px solid var(--border);
      background: var(--white);
      padding: 10px 16px;
      border-radius: var(--radius-sm);
      min-width: 280px;
      font-family: var(--font);
      font-size: 13px;
      font-weight: 500;
      color: var(--fg);
      outline: none;
      transition: all var(--transition);
    }
    .search-box input::placeholder { color: var(--muted); }
    .search-box input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(217,119,6,0.1); }
    .search-box button {
      background: var(--accent); color: white; border: none;
      padding: 10px 18px; border-radius: var(--radius-sm); cursor: pointer;
      font-weight: 700; font-size: 13px; font-family: var(--font);
      transition: all var(--transition); display: inline-flex; align-items: center; gap: 6px;
    }
    .search-box button:hover { background: var(--accent-dark); transform: translateY(-1px); }

    .toolbar-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

    .primary-link {
      display: inline-flex; align-items: center; gap: 6px;
      background: var(--accent); color: white; text-decoration: none;
      padding: 10px 20px; border-radius: var(--radius-sm);
      font-weight: 700; font-size: 13px; border: none; cursor: pointer;
      transition: all var(--transition); font-family: var(--font);
    }
    .primary-link:hover { background: var(--accent-dark); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(217,119,6,0.25); }

    .secondary-link {
      display: inline-flex; align-items: center; gap: 6px;
      background: transparent; color: var(--fg-secondary); text-decoration: none;
      padding: 9px 18px; border-radius: var(--radius-sm);
      font-weight: 700; font-size: 13px; border: 1.5px solid var(--border); cursor: pointer;
      transition: all var(--transition); font-family: var(--font);
    }
    .secondary-link:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-light); }

    .danger-link {
      display: inline-flex; align-items: center; gap: 6px;
      background: transparent; color: var(--red); border: 1.5px solid #FECACA;
      padding: 9px 18px; border-radius: var(--radius-sm);
      font-weight: 700; font-size: 13px; cursor: pointer;
      transition: all var(--transition); font-family: var(--font);
    }
    .danger-link:hover { background: var(--red-light); border-color: var(--red); }

    /* ===== PANEL ===== */
    .panel {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      overflow: hidden;
    }
    .panel-head {
      display: flex; justify-content: space-between; align-items: center; gap: 12px;
      padding: 18px 24px; border-bottom: 1px solid var(--border-light);
    }
    .panel-head h2 {
      font-size: 15px; font-weight: 800; color: var(--fg);
      letter-spacing: -0.2px; display: flex; align-items: center; gap: 8px;
      margin: 0;
    }
    .panel-head h2 i { color: var(--accent); font-size: 16px; }
    .panel-head span { font-size: 12px; color: var(--muted); font-weight: 600; }

    /* ===== PACKAGE LIST ===== */
    .package-list { display: flex; flex-direction: column; gap: 0; }

    .package-card {
      display: flex; gap: 16px; padding: 18px 24px;
      border-bottom: 1px solid var(--border-light);
      align-items: center; transition: all var(--transition);
      animation: cardIn 0.3s ease;
    }
    @keyframes cardIn {
      from { opacity: 0; transform: translateY(8px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .package-card:last-child { border-bottom: none; }
    .package-card:hover { background: #FAFBFC; }

    .package-thumb {
      width: 72px; height: 72px; border-radius: var(--radius-sm);
      object-fit: cover; background-color: var(--bg); flex-shrink: 0;
      border: 1px solid var(--border-light); transition: transform 0.3s ease;
    }
    .package-card:hover .package-thumb { transform: scale(1.05); }

    .package-meta { flex: 1; display: flex; flex-direction: column; gap: 4px; min-width: 0; }
    .package-meta h3 { font-size: 14px; font-weight: 700; color: var(--fg); margin: 0; letter-spacing: -0.2px; }
    .package-meta p { color: var(--muted); font-size: 12px; margin: 0; font-weight: 500; }

    .package-pricing { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 2px; }

    /* ===== TAGS ===== */
    .tag {
      display: inline-flex; align-items: center; padding: 3px 10px;
      border-radius: var(--radius-full); font-size: 11px; font-weight: 700;
      background-color: var(--bg); color: var(--fg-secondary); letter-spacing: 0.2px;
    }
    .tag-success { background-color: var(--green-light); color: var(--green); }
    .tag-muted { background-color: #F3F4F6; color: var(--muted); }
    .tag-category { background-color: var(--accent-light); color: var(--accent-dark); }
    .tag-free { background-color: #FFF7ED; color: #C2410C; }

    .package-menu-list { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 4px; }
    .package-menu-tag {
      display: inline-flex; align-items: center; padding: 2px 8px;
      border-radius: 6px; font-size: 11px; font-weight: 600;
      background: var(--bg); color: var(--fg-secondary);
      border: 1px solid var(--border-light);
    }

    /* ===== PACKAGE ACTIONS ===== */
    .package-actions { display: flex; gap: 4px; align-items: center; flex-shrink: 0; }
    .package-actions a, .package-actions button {
      border: none; background: transparent; cursor: pointer; padding: 7px 12px;
      font-weight: 700; font-size: 12px; text-decoration: none; font-family: var(--font);
      border-radius: var(--radius-sm); transition: all var(--transition);
      display: inline-flex; align-items: center; gap: 4px;
    }
    .btn-open-edit { color: var(--accent); }
    .btn-open-edit:hover { background: var(--accent-light); color: var(--accent-dark); }
    .btn-delete-package { color: var(--muted); }
    .btn-delete-package:hover { background: var(--red-light); color: var(--red); }

    /* ===== EMPTY STATE ===== */
    .empty-state { padding: 40px 24px; text-align: center; color: var(--muted); font-size: 14px; background: #fff; border-radius: var(--radius-lg); }
    .empty-state::before { content: '\f56e'; font-family: 'Font Awesome 6 Free'; font-weight: 900; display: block; font-size: 32px; margin-bottom: 8px; color: var(--border); }
    .empty-state em { font-style: normal; font-weight: 700; color: var(--fg-secondary); }

    /* ===== PAGINATION ===== */
    .menu-pagination { padding: 14px 24px; border-top: 1px solid var(--border-light); }
    .pagination-wrap { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
    .pagination-meta { font-size: 12px; color: var(--muted); font-weight: 500; }
    .pagination-links { display: flex; gap: 4px; flex-wrap: wrap; }
    .pagination-link, .pagination-dots {
      display: inline-flex; align-items: center; justify-content: center;
      min-width: 34px; height: 34px; border-radius: var(--radius-sm);
      font-size: 12px; font-weight: 600; text-decoration: none;
      border: 1px solid var(--border); color: var(--fg-secondary);
      padding: 0 8px; background: var(--white); transition: all var(--transition);
      font-family: var(--font); cursor: pointer;
    }
    .pagination-link:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-light); }
    .pagination-link.active { background: var(--accent); border-color: var(--accent); color: white; }

    /* ===== DRAWER ===== */
    .drawer-backdrop {
      position: fixed; inset: 0; background: rgba(0,0,0,0.3);
      backdrop-filter: blur(2px); z-index: 1200;
      opacity: 0; visibility: hidden; transition: all 0.25s ease;
    }
    .drawer-backdrop.open { opacity: 1; visibility: visible; }

    .package-drawer {
      position: fixed; top: 0; right: 0;
      width: min(580px, 95vw); height: 100vh;
      background: var(--white); z-index: 1201;
      transform: translateX(108%);
      opacity: 0; visibility: hidden; pointer-events: none;
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.2s ease, visibility 0.2s ease;
      box-shadow: var(--shadow-xl);
      display: flex; flex-direction: column;
    }
    .package-drawer.open { transform: translateX(0); opacity: 1; visibility: visible; pointer-events: auto; }

    .drawer-head {
      padding: 18px 24px; border-bottom: 1px solid var(--border);
      display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;
    }
    .drawer-head h3 {
      margin: 0; font-size: 16px; font-weight: 800; color: var(--fg);
      display: flex; align-items: center; gap: 8px; letter-spacing: -0.2px;
    }
    .drawer-head h3 i { color: var(--accent); }
    .drawer-head .drawer-subtitle { margin: 3px 0 0; font-size: 12px; color: var(--muted); }

    .drawer-close {
      border: 1px solid var(--border); background: var(--white);
      color: var(--fg-secondary); border-radius: var(--radius-sm);
      padding: 8px 14px; cursor: pointer; font-weight: 700; font-size: 12px;
      font-family: var(--font); transition: all var(--transition);
    }
    .drawer-close:hover { border-color: var(--red); color: var(--red); background: var(--red-light); }

    /* ===== DRAWER BODY ===== */
    .drawer-body {
      padding: 24px; overflow-y: auto; flex: 1;
      scrollbar-width: thin; scrollbar-color: var(--border) transparent;
    }
    .drawer-body::-webkit-scrollbar { width: 5px; }
    .drawer-body::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }

    /* ===== FORM GRID ===== */
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
    .drawer-field { display: flex; flex-direction: column; gap: 6px; }
    .drawer-field.full { grid-column: 1 / -1; }
    .drawer-field label, .drawer-field > span {
      font-size: 12px; font-weight: 700; color: var(--fg-secondary);
      text-transform: uppercase; letter-spacing: 0.5px;
    }
    .drawer-field input[type="text"],
    .drawer-field input[type="number"],
    .drawer-field select {
      width: 100%; padding: 10px 14px;
      border: 1.5px solid var(--border); border-radius: var(--radius-sm);
      background: var(--white); color: var(--fg);
      font-size: 14px; font-weight: 500; outline: none;
      transition: all var(--transition); font-family: var(--font);
      -webkit-appearance: none; min-height: 42px;
    }
    .drawer-field input::placeholder { color: var(--muted); font-weight: 400; }
    .drawer-field input:focus, .drawer-field select:focus {
      border-color: var(--accent); box-shadow: 0 0 0 3px rgba(217,119,6,0.1);
    }
    .drawer-field select {
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%239CA3B4' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
      background-repeat: no-repeat; background-position: right 12px center; padding-right: 32px;
    }

    /* ===== PHOTO PICKER ===== */
    .photo-picker { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 6px; }
    .photo-picker-btn {
      display: inline-flex; align-items: center; gap: 6px;
      background: var(--bg); color: var(--fg-secondary);
      padding: 9px 16px; border-radius: var(--radius-sm); cursor: pointer;
      font-weight: 700; font-size: 13px; font-family: var(--font);
      transition: all var(--transition); border: 1.5px solid var(--border);
    }
    .photo-picker-btn:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-light); }
    .photo-name { font-size: 12px; color: var(--muted); max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    .selected-photo {
      position: relative; width: 120px; margin-top: 8px;
      border-radius: var(--radius-md); padding: 3px; background: var(--white);
      box-shadow: var(--shadow-sm); border: 1px solid var(--border);
      cursor: pointer; transition: all var(--transition);
    }
    .selected-photo:hover { border-color: var(--accent); transform: scale(1.03); }
    .selected-photo img { width: 100%; height: 110px; object-fit: cover; border-radius: calc(var(--radius-md) - 3px); }
    .photo-clear {
      position: absolute; top: -8px; right: -8px; width: 24px; height: 24px;
      border-radius: 50%; border: none; background: var(--red); color: white;
      cursor: pointer; font-weight: 800; font-size: 11px;
      display: flex; align-items: center; justify-content: center;
      box-shadow: 0 2px 6px rgba(220,38,38,0.3); z-index: 10;
      transition: all var(--transition);
    }
    .photo-clear:hover { transform: scale(1.1); }

    /* ===== PACKAGE MENU SELECTOR ===== */
    .package-menu-selector {
      background: var(--bg); border: 1.5px solid var(--border);
      border-radius: var(--radius-md); padding: 20px; margin-top: 4px;
    }
    .package-menu-selector-header {
      display: flex; justify-content: space-between; align-items: center;
      margin-bottom: 16px; gap: 12px;
    }
    .package-menu-selector-header h3 {
      margin: 0; font-size: 14px; font-weight: 800; color: var(--fg);
      display: flex; align-items: center; gap: 8px;
    }
    .package-menu-selector-header h3 i { color: var(--accent); }
    .package-menu-selector-header p { color: var(--muted); font-size: 12px; margin-top: 2px; }

    .menu-count-badge {
      font-weight: 700; font-size: 12px; color: var(--accent);
      background: var(--white); padding: 5px 14px; border-radius: var(--radius-full);
      border: 1.5px solid var(--accent); white-space: nowrap;
    }

    .menu-selector-box {
      max-height: 380px; overflow-y: auto;
      display: flex; flex-direction: column; gap: 6px;
      scrollbar-width: thin; scrollbar-color: var(--border) transparent;
    }
    .menu-selector-box::-webkit-scrollbar { width: 4px; }
    .menu-selector-box::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }

    .menu-option-item {
      display: flex; align-items: center; gap: 12px;
      padding: 12px 14px; border-radius: var(--radius-sm);
      cursor: pointer; transition: all var(--transition);
      border: 1.5px solid transparent; background: var(--white);
    }
    .menu-option-item:hover { border-color: var(--border); box-shadow: var(--shadow-xs); }
    .menu-option-item.selected {
      border-color: var(--accent); background: var(--accent-light);
      box-shadow: 0 0 0 1px var(--accent);
    }
    .menu-option-item input[type="checkbox"] {
      width: 20px; height: 20px; cursor: pointer; accent-color: var(--accent);
      border-radius: 4px; flex-shrink: 0;
    }
    .package-menu-info { flex: 1; min-width: 0; }
    .package-menu-info strong { font-size: 13px; color: var(--fg); display: block; font-weight: 700; }
    .package-menu-info small { color: var(--muted); font-size: 12px; font-weight: 500; }

    /* ===== QTY CONTROL ===== */
    .qty-control {
      display: flex; align-items: center; gap: 2px;
      background: var(--white); border: 1.5px solid var(--border);
      border-radius: var(--radius-sm); padding: 2px;
      box-shadow: var(--shadow-xs);
    }
    .qty-btn {
      width: 30px; height: 30px; border-radius: 6px; border: none;
      background: var(--bg); color: var(--fg-secondary); cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      font-weight: 800; font-size: 14px; transition: all var(--transition);
      font-family: var(--font);
    }
    .qty-btn:hover { background: var(--accent); color: white; }
    .qty-input {
      width: 36px; text-align: center; border: none; background: transparent;
      font-weight: 700; color: var(--fg); font-family: var(--font); font-size: 14px;
      -moz-appearance: textfield;
    }
    .qty-input::-webkit-outer-spin-button, .qty-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

    /* ===== FORM ERROR ===== */
    .form-error {
      background: var(--red-light); color: var(--red);
      padding: 12px 16px; border-radius: var(--radius-sm);
      border-left: 4px solid var(--red); font-weight: 600; font-size: 13px;
    }

    /* ===== DRAWER FOOT ===== */
    .drawer-foot {
      padding: 16px 24px; border-top: 1px solid var(--border);
      display: flex; gap: 8px; justify-content: flex-end;
      background: #FAFBFC; flex-shrink: 0;
    }
    .btn-drawer-cancel {
      border: 1.5px solid var(--border); background: var(--white);
      color: var(--fg-secondary); border-radius: var(--radius-sm);
      padding: 10px 20px; cursor: pointer; font-weight: 700; font-size: 13px;
      font-family: var(--font); transition: all var(--transition); min-width: 90px;
    }
    .btn-drawer-cancel:hover { border-color: var(--red); color: var(--red); background: var(--red-light); }

    /* ===== CROPPER MODAL ===== */
    .cropper-modal { position: fixed; inset: 0; z-index: 1700; display: grid; place-items: center; background: rgba(0,0,0,0.4); backdrop-filter: blur(3px); }
    .cropper-modal[hidden] { display: none !important; }
    .cropper-dialog { width: min(520px, calc(100vw - 2rem)); background: var(--white); border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-xl); overflow: hidden; }
    .cropper-head, .cropper-foot { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 20px; border-bottom: 1px solid var(--border-light); }
    .cropper-foot { border-bottom: 0; border-top: 1px solid var(--border-light); justify-content: flex-end; }
    .cropper-head strong { color: var(--fg); font-size: 14px; font-weight: 800; display: flex; align-items: center; gap: 8px; }
    .cropper-head strong i { color: var(--accent); }
    .cropper-body { display: grid; place-items: center; gap: 12px; padding: 20px; }
    .cropper-canvas { width: min(340px, 75vw); height: min(340px, 75vw); border: 1.5px dashed var(--border); border-radius: var(--radius-md); background: var(--bg); cursor: move; touch-action: none; }
    .cropper-control { display: flex; flex-direction: column; gap: 4px; width: min(340px, 75vw); color: var(--fg-secondary); font-size: 12px; font-weight: 600; }
    .cropper-control input { accent-color: var(--accent); width: 100%; }
    .cropper-modal-close, .cropper-done { border: 1px solid var(--border); background: var(--white); color: var(--fg-secondary); border-radius: var(--radius-sm); padding: 8px 16px; cursor: pointer; font-weight: 700; font-size: 12px; font-family: var(--font); transition: all var(--transition); }
    .cropper-modal-close:hover { border-color: var(--fg-secondary); color: var(--fg); }
    .cropper-done { background: var(--accent); color: white; border-color: var(--accent); }
    .cropper-done:hover { background: var(--accent-dark); }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
      .content-toolbar { flex-direction: column; align-items: stretch; gap: 12px; }
      .search-box input { min-width: 0; flex: 1; }
      .toolbar-actions { display: grid; grid-template-columns: 1fr 1fr; }
      .form-grid { grid-template-columns: 1fr; }
      .package-card { flex-direction: column; align-items: flex-start; }
      .package-actions { align-self: flex-end; }
      .package-menu-selector-header { flex-direction: column; align-items: stretch; }
    }
    @media (max-width: 480px) {
      .toolbar-actions { grid-template-columns: 1fr; }
      .menu-option-item { flex-wrap: wrap; }
      .qty-control { margin-left: auto; }
    }
    </style>
@endpush

@section('content')
    <!-- TOOLBAR -->
    <div class="content-toolbar fade-in">
        <div class="search-box">
            <input type="text" id="packageSearchInput" placeholder="Cari paket atau kode..." autocomplete="off">
        </div>
        <div class="toolbar-actions">
            <form method="POST" action="{{ route('superadmin.packages.destroy-all') }}" onsubmit="return confirm('Hapus semua paket? Tindakan ini tidak bisa dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="danger-link"><i class="fas fa-trash-can"></i> Hapus Semua Paket</button>
            </form>
            <button type="button" class="primary-link" id="openCreateDrawer"><i class="fas fa-plus"></i> Tambah Paket</button>
        </div>
    </div>

    <!-- PANEL -->
    <div class="panel fade-in">
        <div class="panel-head">
            <h2><i class="fas fa-box-open"></i> Daftar Paket</h2>
            <span id="packageCount">{{ $packages->total() }} paket</span>
        </div>

        <div class="package-list" id="packagesList">
            @forelse ($packages as $package)
                @php
                    $packageImage = $package->image_path
                        ? (Storage::disk('public')->exists($package->image_path) ? Storage::disk('public')->url($package->image_path) : asset('images/menu-placeholder.svg'))
                        : asset('images/menu-placeholder.svg');
                @endphp
                <div class="package-card" data-package-id="{{ $package->id }}">
                    <img class="package-thumb" src="{{ $packageImage }}" alt="{{ $package->name }}">
                    <div class="package-meta">
                        <h3>{{ $package->name }}</h3>
                        <div class="package-pricing">
                            <span class="tag tag-success">Rp {{ number_format((float) $package->selling_price, 0, ',', '.') }}</span>
                            @if($package->free_item)
                                <span class="tag tag-free"><i class="fas fa-gift" style="margin-right:3px;"></i> Free: {{ $package->free_item }}</span>
                            @endif
                        </div>
                        <div class="package-menu-list">
                            @foreach ($package->menus as $menu)
                                <span class="package-menu-tag">({{ $menu->pivot->quantity }}x) {{ $menu->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="package-actions">
                        <button
                            type="button"
                            class="btn-open-edit"
                            data-id="{{ $package->id }}"
                            data-name="{{ $package->name }}"
                            data-selling-price="{{ (float) $package->selling_price }}"
                            data-free-item="{{ $package->free_item }}"
                            data-category-id="{{ $package->menu_category_id }}"
                            data-image-url="{{ $packageImage }}"
                            data-menu-ids='@json($package->menus->pluck("id")->values())'
                            data-menu-quantities='@json($package->menus->pluck("pivot.quantity", "id"))'
                        ><i class="fas fa-pen"></i> Edit</button>
                        <button type="button" class="btn-delete-package" data-id="{{ $package->id }}"><i class="fas fa-trash"></i> Hapus</button>
                    </div>
                </div>
            @empty
                <div class="empty-state" id="emptyState"><em>Belum ada paket makanan.</em></div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="menu-pagination">
            {{ $packages->links('components.pagination') }}
        </div>
    </div>

    <!-- DRAWER BACKDROP -->
    <div class="drawer-backdrop" id="packageDrawerBackdrop"></div>

    <!-- DRAWER -->
    <aside class="package-drawer" id="packageDrawer" aria-hidden="true">
        <div class="drawer-head">
            <div>
                <h3 id="drawerTitle"><i class="fas fa-plus-circle"></i> Tambah Paket</h3>
                <p class="drawer-subtitle">Kelola paket tanpa pindah halaman.</p>
            </div>
            <button type="button" class="drawer-close" id="closeDrawerBtn">Tutup</button>
        </div>

        <form class="drawer-body package-form" id="packageDrawerForm" action="{{ route('superadmin.packages.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="drawerMethod" name="_method" value="">
            <div>
                <div class="form-grid">
                    <label class="drawer-field">
                        <span>Nama Paket</span>
                        <input type="text" name="name" id="drawerName" required placeholder="Contoh: Paket Keluarga A">
                    </label>
                    <label class="drawer-field">
                        <span>Harga Jual Paket</span>
                        <input type="number" step="0.01" min="0" name="selling_price" id="drawerSellingPrice" required placeholder="0">
                    </label>
                    <label class="drawer-field full">
                        <span>Barang Gratis (Opsional)</span>
                        <select name="free_item" id="drawerFreeItem">
                            <option value="">Tidak ada barang gratis</option>
                            @foreach ($menus_all as $menu)
                                <option value="{{ $menu->name }}">{{ $menu->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="drawer-field full">
                        <span>Kategori</span>
                        <select name="menu_category_id" id="drawerCategory">
                            <option value="">Tanpa Kategori</option>
                            @foreach (\App\Models\MenuCategory::orderBy('name')->get() as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <div class="drawer-field full" data-cropper data-cropper-size="600">
                        <span>Foto Paket</span>
                        <div class="photo-picker">
                            <label for="drawerImage" class="photo-picker-btn"><i class="fas fa-image"></i> Pilih Foto</label>
                            <span class="photo-name" id="drawerImageName" data-cropper-filename>Belum ada file dipilih</span>
                        </div>
                        <input type="file" name="image" id="drawerImage" accept="image/*" data-cropper-input style="display:none;">
                        <input type="hidden" name="cropped_image" id="drawer_cropped_image" data-cropper-output>
                        
                        <div class="selected-photo" id="drawerImageWrap" data-cropper-preview-wrap hidden>
                            <img id="drawerImagePreview" src="{{ asset('images/menu-placeholder.svg') }}" alt="Preview" data-cropper-preview title="Klik untuk crop ulang">
                            <button type="button" class="photo-clear" id="clearDrawerImage" data-cropper-clear>x</button>
                        </div>
                        <p style="margin:6px 0 0;color:var(--muted);font-size:12px;">Kosongkan jika tidak ganti foto.</p>

                        <!-- Cropper Modal -->
                        <div class="cropper-modal" data-cropper-panel hidden>
                            <div class="cropper-dialog">
                                <div class="cropper-head">
                                    <strong><i class="fas fa-crop"></i> Atur Crop Foto Paket</strong>
                                    <button type="button" class="cropper-modal-close" data-cropper-close>Tutup</button>
                                </div>
                                <div class="cropper-body">
                                    <canvas class="cropper-canvas" data-cropper-canvas></canvas>
                                    <label class="cropper-control">
                                        Zoom crop
                                        <input type="range" min="1" max="3" step="0.05" value="1" data-cropper-zoom>
                                    </label>
                                </div>
                                <div class="cropper-foot">
                                    <button type="button" class="cropper-done" data-cropper-close>Pakai Crop Ini</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="package-menu-selector">
                    <div class="package-menu-selector-header">
                        <div>
                            <h3><i class="fas fa-list-check"></i> Isi Paket & Jumlah</h3>
                            <p>Pilih menu dan tentukan jumlah per porsinya.</p>
                        </div>
                        <span class="menu-count-badge" data-package-menu-count>0 item terpilih</span>
                    </div>

                    <div class="menu-selector-box" id="drawerMenuSelector">
                        @foreach ($menus_all as $menu)
                            <label class="menu-option-item" data-menu-id="{{ $menu->id }}">
                                <input type="checkbox" name="menus[]" value="{{ $menu->id }}" class="package-menu-checkbox">
                                <div class="package-menu-info">
                                    <strong>{{ $menu->name }}</strong>
                                    <small>{{ $menu->code }} - Rp {{ number_format((float) $menu->selling_price, 0, ',', '.') }}</small>
                                </div>
                                <div class="qty-control" style="display:none;">
                                    <button type="button" class="qty-btn minus">-</button>
                                    <input type="number" name="menu_quantities[{{ $menu->id }}]" value="1" min="1" class="qty-input">
                                    <button type="button" class="qty-btn plus">+</button>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-error" id="drawerError" style="display:none;margin-top:16px;"></div>
            </div>

            <div class="drawer-foot">
                <button type="button" class="btn-drawer-cancel" id="cancelDrawerBtn">Batal</button>
                <button type="submit" class="primary-link" id="submitDrawerBtn"><i class="fas fa-check"></i> Simpan Paket</button>
            </div>
        </form>
    </aside>
@endsection

@push('scripts')
    <script src="{{ asset('js/cafe-image-cropper.js') }}?v=4"></script>
    <script>
        (function () {
            const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const list = document.getElementById('packagesList');
            const countEl = document.getElementById('packageCount');
            const defaultImage = @json(asset('images/menu-placeholder.svg'));
            const packagesStoreUrl = @json(route('superadmin.packages.store'));
            const packageBaseUrl = @json(url('superadmin/packages'));
            const drawer = document.getElementById('packageDrawer');
            const backdrop = document.getElementById('packageDrawerBackdrop');
            const openCreateBtn = document.getElementById('openCreateDrawer');
            const closeDrawerBtn = document.getElementById('closeDrawerBtn');
            const cancelDrawerBtn = document.getElementById('cancelDrawerBtn');
            const drawerForm = document.getElementById('packageDrawerForm');
            const drawerTitle = document.getElementById('drawerTitle');
            const drawerMethod = document.getElementById('drawerMethod');
            const submitBtn = document.getElementById('submitDrawerBtn');
            const drawerError = document.getElementById('drawerError');
            const menuCountBadge = document.querySelector('[data-package-menu-count]');
            const menuSelector = document.getElementById('drawerMenuSelector');
            const freeItemSelect = document.getElementById('drawerFreeItem');
            let currentPackageId = null;
            let latestMenus = [];

            const escapeHtml = (v) => String(v ?? '').replace(/[&<>"']/g, (m) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
            const formatMoney = (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(v || 0));

            const fetchLatestMenus = async () => {
                const res = await fetch(@json(route('superadmin.menus.index')), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const data = await res.json();
                latestMenus = Array.isArray(data.menus) ? data.menus : [];
                return latestMenus;
            };

            const updateCount = () => {
                const total = list.querySelectorAll('[data-package-id]').length;
                countEl.textContent = total + ' paket';
            };

            const resetDrawerError = () => {
                drawerError.textContent = '';
                drawerError.style.display = 'none';
            };

            const setMenuSelection = (menuIds, quantities = {}) => {
                const selected = new Set((menuIds || []).map(String));
                menuSelector.querySelectorAll('.menu-option-item').forEach((option) => {
                    const checkbox = option.querySelector('.package-menu-checkbox');
                    const qtyControl = option.querySelector('.qty-control');
                    const qtyInput = option.querySelector('.qty-input');
                    const id = option.getAttribute('data-menu-id');
                    const isSelected = selected.has(String(id));
                    checkbox.checked = isSelected;
                    option.classList.toggle('selected', isSelected);
                    qtyControl.style.display = isSelected ? 'flex' : 'none';
                    qtyInput.value = Number(quantities[id] || quantities[String(id)] || 1);
                });
                menuCountBadge.textContent = selected.size + ' item terpilih';
            };

            const bindMenuSelectorEvents = () => {
                menuSelector.querySelectorAll('.menu-option-item').forEach((opt) => {
                    const cb = opt.querySelector('.package-menu-checkbox');
                    const qty = opt.querySelector('.qty-control');
                    const inp = opt.querySelector('.qty-input');
                    const sync = (state) => {
                        const checked = state !== undefined ? state : cb.checked;
                        cb.checked = checked;
                        opt.classList.toggle('selected', checked);
                        qty.style.display = checked ? 'flex' : 'none';
                        menuCountBadge.textContent = menuSelector.querySelectorAll('.package-menu-checkbox:checked').length + ' item terpilih';
                    };
                    opt.addEventListener('click', (e) => { if (!e.target.closest('.qty-control') && e.target !== cb) sync(!cb.checked); });
                    cb.addEventListener('change', () => sync());
                    opt.querySelector('.plus')?.addEventListener('click', (e) => { e.stopPropagation(); inp.value = parseInt(inp.value || 1) + 1; });
                    opt.querySelector('.minus')?.addEventListener('click', (e) => { e.stopPropagation(); inp.value = Math.max(1, parseInt(inp.value || 1) - 1); });
                    inp?.addEventListener('click', (e) => e.stopPropagation());
                });
            };

            const rebuildMenuSources = async (selectedIds = [], quantities = {}, selectedFreeItem = '') => {
                const menus = await fetchLatestMenus();
                freeItemSelect.innerHTML = '<option value="">Tidak ada barang gratis</option>' + menus.map((menu) => '<option value="' + escapeHtml(menu.name) + '">' + escapeHtml(menu.name) + '</option>').join('');
                freeItemSelect.value = selectedFreeItem || '';
                const selected = new Set((selectedIds || []).map(String));
                menuSelector.innerHTML = menus.map((menu) => {
                    const checked = selected.has(String(menu.id));
                    const qty = Number(quantities[menu.id] || quantities[String(menu.id)] || 1);
                    return '<label class="menu-option-item ' + (checked ? 'selected' : '') + '" data-menu-id="' + menu.id + '">' +
                        '<div class="menu-option-main">' +
                            '<input class="package-menu-checkbox" type="checkbox" name="menus[]" value="' + menu.id + '" ' + (checked ? 'checked' : '') + '>' +
                            '<div><strong>' + escapeHtml(menu.name) + '</strong><small>' + escapeHtml(menu.code || '') + ' - ' + formatMoney(menu.selling_price || 0) + '</small></div>' +
                        '</div>' +
                        '<div class="qty-control" style="display:' + (checked ? 'flex' : 'none') + ';">' +
                            '<button type="button" class="minus">-</button>' +
                            '<input class="qty-input" type="number" min="1" name="menu_quantities[' + menu.id + ']" value="' + qty + '">' +
                            '<button type="button" class="plus">+</button>' +
                        '</div>' +
                    '</label>';
                }).join('');
                bindMenuSelectorEvents();
                setMenuSelection(selectedIds, quantities);
            };

            const resetDrawer = () => {
                drawerForm.reset();
                drawerForm.action = packagesStoreUrl;
                drawerMethod.disabled = true;
                drawerMethod.value = '';
                drawerTitle.innerHTML = '<i class="fas fa-plus-circle"></i> Tambah Paket';
                submitBtn.innerHTML = '<i class="fas fa-check"></i> Simpan Paket';
                currentPackageId = null;
                document.getElementById('drawerImageWrap').hidden = true;
                document.getElementById('drawerImagePreview').src = defaultImage;
                document.getElementById('drawerImageName').textContent = 'Belum ada file dipilih';
                const cropperOutput = document.getElementById('drawer_cropped_image');
                if (cropperOutput) cropperOutput.value = '';
                setMenuSelection([], {});
                resetDrawerError();
            };

            const openDrawer = () => { drawer.classList.add('open'); backdrop.classList.add('open'); drawer.setAttribute('aria-hidden','false'); document.body.style.overflow='hidden'; };
            const closeDrawer = () => { drawer.classList.remove('open'); backdrop.classList.remove('open'); drawer.setAttribute('aria-hidden','true'); document.body.style.overflow=''; };

            const buildPackageHtml = (p) => {
                const img = escapeHtml(p.image_url || defaultImage);
                const chips = Array.isArray(p.menus) && p.menus.length
                    ? p.menus.map((m) => `<span class="package-menu-tag">(${escapeHtml(m.quantity)}x) ${escapeHtml(m.name)}</span>`).join('')
                    : '<span class="package-menu-tag">Belum ada menu</span>';
                return `
                    <div class="package-card" data-package-id="${p.id}">
                        <img class="package-thumb" src="${img}" alt="${escapeHtml(p.name)}">
                        <div class="package-meta">
                            <h3>${escapeHtml(p.name)}</h3>
                            <div class="package-pricing">
                                <span class="tag tag-success">${formatMoney(p.selling_price)}</span>
                                ${p.free_item ? `<span class="tag tag-free"><i class="fas fa-gift" style="margin-right:3px;"></i> Free: ${escapeHtml(p.free_item)}</span>` : ''}
                            </div>
                            <div class="package-menu-list">${chips}</div>
                        </div>
                        <div class="package-actions">
                            <button type="button" class="btn-open-edit"
                                data-id="${p.id}" data-name="${escapeHtml(p.name)}"
                                data-selling-price="${p.selling_price}" data-free-item="${escapeHtml(p.free_item || '')}"
                                data-category-id="${p.menu_category_id || ''}" data-image-url="${img}"
                                data-menu-ids='${JSON.stringify(p.menus.map(m => m.id))}'
                                data-menu-quantities='${JSON.stringify(Object.fromEntries(p.menus.map(m => [m.id, m.quantity])))}'
                            ><i class="fas fa-pen"></i> Edit</button>
                            <button type="button" class="btn-delete-package" data-id="${p.id}"><i class="fas fa-trash"></i> Hapus</button>
                        </div>
                    </div>
                `;
            };

            const bindActions = (root) => {
                root.querySelectorAll('.btn-open-edit').forEach(btn => {
                    btn.addEventListener('click', async () => {
                        resetDrawer();
                        currentPackageId = btn.getAttribute('data-id');
                        drawerTitle.innerHTML = '<i class="fas fa-pen-to-square"></i> Edit Paket';
                        submitBtn.innerHTML = '<i class="fas fa-check"></i> Simpan Perubahan';
                        drawerForm.action = `${packageBaseUrl}/${currentPackageId}`;
                        drawerMethod.disabled = false;
                        drawerMethod.value = 'PUT';
                        document.getElementById('drawerName').value = btn.getAttribute('data-name');
                        document.getElementById('drawerSellingPrice').value = btn.getAttribute('data-selling-price');
                        document.getElementById('drawerCategory').value = btn.getAttribute('data-category-id') || '';
                        const img = btn.getAttribute('data-image-url') || defaultImage;
                        document.getElementById('drawerImagePreview').src = img;
                        if (img && img !== defaultImage) document.getElementById('drawerImageWrap').hidden = false;
                        const selectedIds = JSON.parse(btn.getAttribute('data-menu-ids') || '[]');
                        const selectedQuantities = JSON.parse(btn.getAttribute('data-menu-quantities') || '{}');
                        await rebuildMenuSources(selectedIds, selectedQuantities, btn.getAttribute('data-free-item') || '');
                        openDrawer();
                    });
                });
                root.querySelectorAll('.btn-delete-package').forEach(btn => {
                    btn.addEventListener('click', async () => {
                        const id = btn.getAttribute('data-id');
                        if (!id || !confirm('Hapus paket ini?')) return;
                        const res = await fetch(`${packageBaseUrl}/${id}`, {
                            method: 'POST',
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Content-Type': 'application/json' },
                            body: JSON.stringify({ _method: 'DELETE' })
                        });
                        const data = await res.json();
                        if (!res.ok) return window.showToast?.(data.message || 'Gagal hapus paket.', 'error');
                        list.querySelector(`[data-package-id="${id}"]`)?.remove();
                        if (!list.querySelector('[data-package-id]')) list.innerHTML = '<div class="empty-state" id="emptyState"><em>Belum ada paket makanan.</em></div>';
                        updateCount();
                        window.showToast?.(data.message, 'success');
                    });
                });
            };

            closeDrawer();
            window.addEventListener('pageshow', closeDrawer);
            openCreateBtn?.addEventListener('click', async () => { resetDrawer(); await rebuildMenuSources([], {}, ''); openDrawer(); });
            [closeDrawerBtn, cancelDrawerBtn, backdrop].forEach(el => el?.addEventListener('click', closeDrawer));

            window.addEventListener('superadmin:sidebar-toggle', () => {
                closeDrawer();
            });

            bindMenuSelectorEvents();

            drawerForm?.addEventListener('submit', async (e) => {
                e.preventDefault();
                resetDrawerError();
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
                try {
                    const res = await fetch(drawerForm.action, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                        body: new FormData(drawerForm)
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        drawerError.innerHTML = data.errors ? Object.values(data.errors).flat().join('<br>') : data.message;
                        drawerError.style.display = 'block';
                        return;
                    }
                    const p = data.package;
                    const old = list.querySelector(`[data-package-id="${p.id}"]`);
                    if (old) {
                        old.outerHTML = buildPackageHtml(p);
                        bindActions(list.querySelector(`[data-package-id="${p.id}"]`));
                    } else {
                        const empty = document.getElementById('emptyState');
                        if (empty) empty.remove();
                        list.insertAdjacentHTML('afterbegin', buildPackageHtml(p));
                        bindActions(list.querySelector(`[data-package-id="${p.id}"]`));
                    }
                    updateCount();
                    window.showToast?.(data.message, 'success');
                    closeDrawer();
                } catch (err) {
                    drawerError.textContent = err.message;
                    drawerError.style.display = 'block';
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = currentPackageId ? '<i class="fas fa-check"></i> Simpan Perubahan' : '<i class="fas fa-check"></i> Simpan Paket';
                }
            });

            bindActions(document);

            /* ===== LIVE SEARCH ===== */
            const searchInput = document.getElementById('packageSearchInput');
            if (searchInput && list) {
                searchInput.addEventListener('input', (e) => {
                    const query = e.target.value.toLowerCase().trim();
                    const cards = list.querySelectorAll('.package-card');
                    let hasVisible = false;

                    cards.forEach(card => {
                        const name = card.querySelector('h3')?.textContent.toLowerCase() || '';
                        
                        if (name.includes(query)) {
                            card.style.display = '';
                            hasVisible = true;
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    let emptyMsg = document.getElementById('searchEmptyMsg');
                    if (!hasVisible && query !== '') {
                        if (!emptyMsg) {
                            emptyMsg = document.createElement('div');
                            emptyMsg.id = 'searchEmptyMsg';
                            emptyMsg.className = 'empty-state';
                            emptyMsg.innerHTML = `<em>Tidak ada paket ditemukan untuk "${escapeHtml(query)}"</em>`;
                            list.appendChild(emptyMsg);
                        }
                    } else if (emptyMsg) {
                        emptyMsg.remove();
                    }
                });
            }
        })();
    </script>
@endpush

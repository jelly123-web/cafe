@extends('superadmin.layout')

@section('title', 'Manajemen Menu — cafecaf')
@section('page_title', 'Manajemen Menu')
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

    /* ===== SEARCH BOX ===== */
    .search-box {
      display: flex;
      gap: 6px;
    }

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

    .search-box input:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1);
    }

    .search-box button {
      background: var(--accent);
      color: white;
      border: none;
      padding: 10px 18px;
      border-radius: var(--radius-sm);
      cursor: pointer;
      font-weight: 700;
      font-size: 13px;
      font-family: var(--font);
      transition: all var(--transition);
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .search-box button:hover {
      background: var(--accent-dark);
      transform: translateY(-1px);
    }

    /* ===== TOOLBAR ACTIONS ===== */
    .toolbar-actions {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }

    .primary-link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: var(--accent);
      color: white;
      text-decoration: none;
      padding: 10px 20px;
      border-radius: var(--radius-sm);
      font-weight: 700;
      font-size: 13px;
      border: none;
      cursor: pointer;
      transition: all var(--transition);
      font-family: var(--font);
    }

    .primary-link:hover {
      background: var(--accent-dark);
      transform: translateY(-1px);
      box-shadow: 0 4px 14px rgba(217, 119, 6, 0.25);
    }

    .danger-link {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      background: transparent;
      color: var(--red);
      border: 1.5px solid #FECACA;
      padding: 9px 18px;
      border-radius: var(--radius-sm);
      font-weight: 700;
      font-size: 13px;
      cursor: pointer;
      transition: all var(--transition);
      font-family: var(--font);
    }

    .danger-link:hover {
      background: var(--red-light);
      border-color: var(--red);
    }

    /* ===== CATEGORY FILTER BAR ===== */
    .category-filter-bar {
      display: flex;
      gap: 6px;
      margin-bottom: 20px;
      overflow-x: auto;
      padding-bottom: 4px;
      scrollbar-width: none;
    }

    .category-filter-bar::-webkit-scrollbar { display: none; }

    .category-filter-bar.is-loading {
      pointer-events: none;
    }

    .category-filter-bar.is-loading .filter-pill:not(.active) {
      opacity: 0.65;
    }

    .content-toolbar.fade-in,
    #menuContentRegion .fade-in {
      opacity: 1;
      transform: none;
    }

    .filter-pill {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 24px;
      background: var(--white);
      border: 1px solid var(--border-light);
      border-radius: var(--radius-full);
      color: var(--fg-secondary);
      font-size: 14px;
      font-weight: 700;
      text-decoration: none;
      white-space: nowrap;
      transition: all var(--transition);
      font-family: var(--font);
      box-shadow: var(--shadow-sm);
    }

    .filter-pill:hover {
      background: var(--bg);
      transform: translateY(-1px);
    }

    .filter-pill.active {
      background: var(--accent);
      border-color: var(--accent);
      color: white;
      box-shadow: 0 4px 12px rgba(217, 119, 6, 0.2);
    }

    .filter-pill i {
      font-size: 16px;
      opacity: 0.8;
    }

    .filter-pill.active i {
      opacity: 1;
    }

    .filter-pill span {
      font-size: 12px;
      margin-left: 2px;
      opacity: 0.7;
    }

    .filter-pill.active span {
      opacity: 0.9;
      font-weight: 600;
    }

    /* ===== PANEL ===== */
    .panel {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      overflow: hidden;
    }

    .panel-head {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      padding: 18px 24px;
      border-bottom: 1px solid var(--border-light);
    }

    .panel-head h2 {
      font-size: 15px;
      font-weight: 800;
      color: var(--fg);
      letter-spacing: -0.2px;
      display: flex;
      align-items: center;
      gap: 8px;
      margin: 0;
    }

    .panel-head h2 i { color: var(--accent); font-size: 16px; }

    .panel-head span {
      font-size: 12px;
      color: var(--muted);
      font-weight: 600;
    }

    /* ===== MENU CARD LIST ===== */
    .menu-card-list {
      display: flex;
      flex-direction: column;
      gap: 0;
    }

    .menu-card {
      display: flex;
      gap: 16px;
      padding: 18px 24px;
      border-bottom: 1px solid var(--border-light);
      align-items: center;
      transition: all var(--transition);
      animation: cardIn 0.3s ease;
    }

    @keyframes cardIn {
      from { opacity: 0; transform: translateY(8px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .menu-card:last-child { border-bottom: none; }

    .menu-card:hover { background: #FAFBFC; }

    .menu-thumb {
      width: 72px;
      height: 72px;
      border-radius: var(--radius-sm);
      object-fit: cover;
      background-color: var(--bg);
      flex-shrink: 0;
      border: 1px solid var(--border-light);
      transition: transform 0.3s ease;
    }

    .menu-card:hover .menu-thumb {
      transform: scale(1.05);
    }

    .menu-meta {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 4px;
      min-width: 0;
    }

    .menu-meta h3 {
      font-size: 14px;
      font-weight: 700;
      color: var(--fg);
      margin: 0;
      letter-spacing: -0.2px;
    }

    .menu-meta p {
      color: var(--muted);
      font-size: 12px;
      margin: 0;
      font-weight: 500;
    }

    .menu-pricing {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      margin: 0;
      margin-top: 4px;
    }

    /* ===== TAGS ===== */
    .tag {
      display: inline-flex;
      align-items: center;
      padding: 3px 10px;
      border-radius: var(--radius-full);
      font-size: 11px;
      font-weight: 700;
      background-color: var(--bg);
      color: var(--fg-secondary);
      letter-spacing: 0.2px;
    }

    .tag-success {
      background-color: var(--green-light);
      color: var(--green);
    }

    .tag-muted {
      background-color: #F3F4F6;
      color: var(--muted);
    }

    .tag-category {
      background-color: var(--accent-light);
      color: var(--accent-dark);
    }

    /* ===== ACTIONS ===== */
    .actions {
      display: flex;
      gap: 4px;
      align-items: center;
      flex-shrink: 0;
    }

    .actions a,
    .actions button {
      border: none;
      background: transparent;
      cursor: pointer;
      padding: 7px 12px;
      font-weight: 700;
      font-size: 12px;
      text-decoration: none;
      font-family: var(--font);
      border-radius: var(--radius-sm);
      transition: all var(--transition);
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }

    .btn-open-edit,
    .package-manage-link {
      color: var(--accent);
    }

    .btn-open-edit:hover,
    .package-manage-link:hover {
      background: var(--accent-light);
      color: var(--accent-dark);
    }

    .btn-delete-menu {
      color: var(--muted);
    }

    .btn-delete-menu:hover {
      background: var(--red-light);
      color: var(--red);
    }

    /* ===== ALERT / EMPTY STATE ===== */
    .alert {
      padding: 40px 24px;
      border-radius: var(--radius-md);
      text-align: center;
      color: var(--muted);
      font-size: 14px;
      background: #fff;
    }

    .alert::before {
      content: '\f56e';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      display: block;
      font-size: 32px;
      margin-bottom: 8px;
      color: var(--border);
    }

    .alert em { font-style: normal; font-weight: 700; color: var(--fg-secondary); }

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
    .pagination-link.active { background: var(--accent); border-color: var(--accent); color: white; box-shadow: 0 2px 8px rgba(217,119,6,0.2); }
    .pagination-link.disabled { opacity: 0.35; pointer-events: none; }

    /* ===== DRAWER ===== */
    .drawer-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.3);
      backdrop-filter: blur(2px);
      z-index: 1200;
      opacity: 0;
      visibility: hidden;
      transition: all 0.25s ease;
    }

    .drawer-backdrop.open {
      opacity: 1;
      visibility: visible;
    }

    /* ===== DRAWER ===== */
    .menu-drawer {
      position: fixed;
      top: 0;
      right: 0;
      width: min(560px, 95vw);
      height: 100vh;
      background: var(--white);
      z-index: 1201;
      transform: translateX(108%);
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.2s ease, visibility 0.2s ease;
      box-shadow: var(--shadow-xl);
      display: flex;
      flex-direction: column;
    }

    .menu-drawer.open {
      transform: translateX(0);
      opacity: 1;
      visibility: visible;
      pointer-events: auto;
    }

    .drawer-head {
      padding: 18px 24px;
      border-bottom: 1px solid var(--border);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-shrink: 0;
    }

    .drawer-head h3 {
      margin: 0;
      font-size: 16px;
      font-weight: 800;
      color: var(--fg);
      display: flex;
      align-items: center;
      gap: 8px;
      letter-spacing: -0.2px;
    }

    .drawer-head h3 i { color: var(--accent); }

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

    /* ===== DRAWER BODY ===== */
    .drawer-body {
      padding: 24px;
      overflow-y: auto;
      flex: 1;
      min-height: 0;
      scrollbar-width: thin;
      scrollbar-color: var(--border) transparent;
    }

    .menu-drawer form {
      min-height: 100%;
      display: flex;
      flex-direction: column;
    }

    .drawer-body::-webkit-scrollbar { width: 5px; }
    .drawer-body::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }

    /* ===== DRAWER FORM GRID ===== */
    .drawer-form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    .drawer-field {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .drawer-field.full {
      grid-column: 1 / -1;
    }

    .drawer-field label {
      font-size: 12px;
      font-weight: 700;
      color: var(--fg-secondary);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .drawer-field input,
    .drawer-field select {
      width: 100%;
      padding: 10px 14px;
      border: 1.5px solid var(--border);
      border-radius: var(--radius-sm);
      background: var(--white);
      color: var(--fg);
      font-size: 14px;
      font-weight: 500;
      outline: none;
      transition: all var(--transition);
      font-family: var(--font);
      -webkit-appearance: none;
    }

    .drawer-field input::placeholder { color: var(--muted); font-weight: 400; }

    .drawer-field input:focus,
    .drawer-field select:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1);
    }

    .drawer-field select {
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%239CA3B4' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 12px center;
      padding-right: 32px;
    }

    /* ===== DRAWER ERROR ===== */
    .drawer-error {
      background: var(--red-light);
      color: var(--red);
      padding: 12px 16px;
      border-radius: var(--radius-sm);
      border-left: 4px solid var(--red);
      font-weight: 600;
      font-size: 13px;
      margin-top: 16px;
      display: none;
    }

    /* ===== DRAWER IMAGE BOX ===== */
    .drawer-image-box {
      grid-column: span 2;
      display: grid;
      grid-template-columns: 1fr 130px;
      gap: 20px;
      align-items: start;
      margin-top: 4px;
    }

    .drawer-file-label {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: var(--bg);
      color: var(--fg-secondary);
      padding: 9px 16px;
      border-radius: var(--radius-sm);
      cursor: pointer;
      font-weight: 700;
      font-size: 13px;
      margin-bottom: 6px;
      transition: all var(--transition);
      border: 1.5px solid var(--border);
    }

    .drawer-file-label:hover {
      border-color: var(--accent);
      color: var(--accent);
      background: var(--accent-light);
    }

    .drawer-file-name {
      display: block;
      font-size: 12px;
      color: var(--muted);
      max-width: 250px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .drawer-image-preview-wrap {
      position: relative;
      width: 130px;
    }

    .drawer-image-preview {
      width: 130px;
      height: 130px;
      object-fit: cover;
      border-radius: var(--radius-md);
      border: 1.5px solid var(--border);
      padding: 3px;
      background: var(--white);
      cursor: pointer;
      transition: all var(--transition);
    }

    .drawer-image-preview:hover {
      border-color: var(--accent);
      transform: scale(1.03);
    }

    .drawer-image-clear {
      position: absolute;
      top: -8px;
      right: -8px;
      width: 24px;
      height: 24px;
      border-radius: 50%;
      border: none;
      background: var(--red);
      color: white;
      cursor: pointer;
      font-weight: 800;
      font-size: 11px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 2px 6px rgba(220, 38, 38, 0.3);
      z-index: 10;
      transition: all var(--transition);
    }

    .drawer-image-clear:hover { transform: scale(1.1); }

    /* ===== CROPPER MODAL ===== */
    .cropper-modal {
      position: fixed;
      inset: 0;
      z-index: 1700;
      display: grid;
      place-items: center;
      background: rgba(0, 0, 0, 0.4);
      backdrop-filter: blur(3px);
    }

    .cropper-modal[hidden] { display: none !important; }

    .cropper-dialog {
      width: min(520px, calc(100vw - 2rem));
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-xl);
      overflow: hidden;
    }

    .cropper-head,
    .cropper-foot {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 14px 20px;
      border-bottom: 1px solid var(--border-light);
    }

    .cropper-foot {
      border-bottom: 0;
      border-top: 1px solid var(--border-light);
      justify-content: flex-end;
    }

    .cropper-head strong {
      color: var(--fg);
      font-size: 14px;
      font-weight: 800;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .cropper-head strong i { color: var(--accent); }

    .cropper-body {
      display: grid;
      place-items: center;
      gap: 12px;
      padding: 20px;
    }

    .cropper-canvas {
      width: min(340px, 75vw);
      height: min(340px, 75vw);
      border: 1.5px dashed var(--border);
      border-radius: var(--radius-md);
      background: var(--bg);
      cursor: move;
      touch-action: none;
    }

    .cropper-control {
      display: flex;
      flex-direction: column;
      gap: 4px;
      width: min(340px, 75vw);
      color: var(--fg-secondary);
      font-size: 12px;
      font-weight: 600;
    }

    .cropper-control input {
      accent-color: var(--accent);
      width: 100%;
    }

    .cropper-modal-close,
    .cropper-done {
      border: 1px solid var(--border);
      background: var(--white);
      color: var(--fg-secondary);
      border-radius: var(--radius-sm);
      padding: 8px 16px;
      cursor: pointer;
      font-weight: 700;
      font-size: 12px;
      font-family: var(--font);
      transition: all var(--transition);
    }

    .cropper-modal-close:hover { border-color: var(--fg-secondary); color: var(--fg); }

    .cropper-done {
      background: var(--accent);
      color: white;
      border-color: var(--accent);
    }

    .cropper-done:hover {
      background: var(--accent-dark);
      border-color: var(--accent-dark);
    }

    /* ===== DRAWER FOOT ===== */
    .drawer-foot {
      padding: 16px 24px;
      border-top: 1px solid var(--border);
      display: flex;
      gap: 8px;
      justify-content: flex-end;
      background: #FAFBFC;
      flex-shrink: 0;
    }

    .btn-drawer-cancel {
      border: 1.5px solid var(--border);
      background: var(--white);
      color: var(--fg-secondary);
      border-radius: var(--radius-sm);
      padding: 10px 20px;
      cursor: pointer;
      font-weight: 700;
      font-size: 13px;
      font-family: var(--font);
      transition: all var(--transition);
    }

    .btn-drawer-cancel:hover {
      border-color: var(--red);
      color: var(--red);
      background: var(--red-light);
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1024px) {
      .drawer-image-box { grid-template-columns: 1fr; }
      .drawer-image-preview { width: 100%; height: 160px; }
    }

    @media (max-width: 768px) {
      .content-toolbar { flex-direction: column; align-items: stretch; gap: 12px; }
      .search-box input { min-width: 0; flex: 1; }
      .toolbar-actions { display: grid; grid-template-columns: 1fr 1fr; }

      .drawer-form-grid { grid-template-columns: 1fr; }
      .drawer-image-box { grid-template-columns: 1fr; }
      .drawer-image-preview { width: 100%; height: 160px; }

      .menu-card { flex-direction: column; align-items: flex-start; }
      .actions { align-self: flex-end; }
    }

    @media (max-width: 480px) {
      .toolbar-actions { grid-template-columns: 1fr; }
      .filter-pill { padding: 6px 12px; font-size: 12px; }
    }
    </style>
@endpush

@section('content')
    <!-- TOOLBAR -->
    <div class="content-toolbar fade-in">
        <div class="search-box">
            <input type="text" id="menuSearchInput" placeholder="Cari menu atau kode..." autocomplete="off">
        </div>
        <div class="toolbar-actions">
            <form method="POST" action="{{ route('superadmin.menus.destroy-all') }}" onsubmit="return confirm('Hapus semua menu? Tindakan ini tidak bisa dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="danger-link"><i class="fas fa-trash-can"></i> Hapus Semua Menu</button>
            </form>
            <button type="button" class="primary-link" id="btnOpenCreate"><i class="fas fa-plus"></i> Tambah Menu</button>
        </div>
    </div>

    <div id="menuContentRegion">
        @include('superadmin.menus._content')
    </div>

    <!-- SIDE MODAL (DRAWER) -->
    <div id="drawerBackdrop" class="drawer-backdrop"></div>
    <aside id="menuDrawer" class="menu-drawer" aria-hidden="true">
        <div class="drawer-head">
            <h3 id="drawerTitle"><i class="fas fa-plus-circle"></i> Tambah Menu</h3>
            <button type="button" class="drawer-close" id="btnCloseDrawer">Tutup</button>
        </div>
        <form id="drawerForm" method="POST" enctype="multipart/form-data" style="display: contents;">
            @csrf
            <input type="hidden" name="_method" id="drawerMethod" value="POST" disabled>
            <div class="drawer-body">
                <div class="drawer-form-grid">
                    <div class="drawer-field">
                        <label for="f_code">Kode Menu</label>
                        <input id="f_code" type="text" name="code" required placeholder="Cth: NGK-001">
                    </div>
                    <div class="drawer-field">
                        <label for="f_category">Kategori</label>
                        <select id="f_category" name="menu_category_id">
                            <option value="">Tanpa kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="drawer-field full">
                        <label for="f_name">Nama Menu</label>
                        <input id="f_name" type="text" name="name" required placeholder="Cth: Caramel Macchiato">
                    </div>
                    <div class="drawer-field">
                        <label for="f_selling">Harga Jual</label>
                        <input id="f_selling" type="number" step="0.01" name="selling_price" required placeholder="35000">
                    </div>
                    <div class="drawer-field">
                        <label for="f_cost">Harga Modal</label>
                        <input id="f_cost" type="number" step="0.01" name="cost_price" required placeholder="12000">
                    </div>
                    <div class="drawer-field full drawer-image-box" data-cropper data-cropper-size="600">
                        <div>
                            <label>Foto Menu</label>
                            <br>
                            <label for="f_image" class="drawer-file-label"><i class="fas fa-image"></i> Pilih Foto</label>
                            <input id="f_image" type="file" name="image" accept="image/*" data-cropper-input style="display:none;">
                            <input type="hidden" name="cropped_image" id="f_cropped_image" data-cropper-output>
                            <span class="drawer-file-name" id="f_image_name" data-cropper-filename>Belum ada file dipilih</span>
                        </div>
                        <div class="drawer-image-preview-wrap" data-cropper-preview-wrap hidden>
                            <button type="button" class="drawer-image-clear" id="btnClearImage" data-cropper-clear>x</button>
                            <img id="f_preview" src="{{ asset('images/menu-placeholder.svg') }}" class="drawer-image-preview" alt="Preview" data-cropper-preview title="Klik untuk crop ulang">
                        </div>

                        <div class="cropper-modal" data-cropper-panel hidden>
                            <div class="cropper-dialog">
                                <div class="cropper-head">
                                    <strong><i class="fas fa-crop"></i> Atur Crop Foto Menu</strong>
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
                <div id="drawerError" class="drawer-error"></div>
            </div>
            <div class="drawer-foot">
                <button type="button" class="btn-drawer-cancel" id="btnCancelDrawer">Batal</button>
                <button type="submit" class="primary-link" id="btnSubmitDrawer"><i class="fas fa-check"></i> Simpan Menu</button>
            </div>
        </form>
    </aside>
@endsection

@push('scripts')
    <script src="{{ asset('js/cafe-image-cropper.js') }}?v=4"></script>
    <script>
        (function () {
            const drawer = document.getElementById('menuDrawer');
            const backdrop = document.getElementById('drawerBackdrop');
            const form = document.getElementById('drawerForm');
            const drawerTitle = document.getElementById('drawerTitle');
            const drawerMethod = document.getElementById('drawerMethod');
            const btnSubmit = document.getElementById('btnSubmitDrawer');
            const getCsrfToken = () => document.querySelector('input[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const defaultImage = @json(asset('images/menu-placeholder.svg'));
            const menuBaseUrl = @json(url('superadmin/menus'));
            const storeUrl = @json(route('superadmin.menus.store'));
            const getList = () => document.getElementById('menusList');
            const getCountEl = () => document.getElementById('menuCount');
            const getContentRegion = () => document.getElementById('menuContentRegion');
            const getCategoryBar = () => document.getElementById('menuCategoryFilterBar');
            const resolveCategoryFromUrl = (url) => {
                const params = new URL(url || window.location.href, window.location.origin).searchParams;
                return params.get('category_id') || 'all';
            };
            const getCurrentCategoryId = () => getCategoryBar()?.dataset.currentCategoryId || resolveCategoryFromUrl(window.location.href);
            let currentSearchQuery = '';
            let currentCategorySlug = 'all';
            const syncActiveCategoryPill = (categoryId = 'all') => {
                const categoryBar = getCategoryBar();
                if (!categoryBar) return;
                categoryBar.dataset.currentCategoryId = String(categoryId || 'all');
                categoryBar.querySelectorAll('.filter-pill').forEach((pill) => {
                    const pillCategoryId = pill.getAttribute('data-category-id') || 'all';
                    pill.classList.toggle('active', String(pillCategoryId) === String(categoryId));
                });
            };

            const openDrawer = () => { drawer.classList.add('open'); backdrop.classList.add('open'); drawer.setAttribute('aria-hidden', 'false'); };
            const closeDrawer = () => { drawer.classList.remove('open'); backdrop.classList.remove('open'); drawer.setAttribute('aria-hidden', 'true'); };

            const resetForm = () => {
                form.reset();
                form.action = storeUrl;
                drawerMethod.disabled = true;
                drawerTitle.innerHTML = '<i class="fas fa-plus-circle"></i> Tambah Menu';
                btnSubmit.innerHTML = '<i class="fas fa-check"></i> Simpan Menu';
                document.getElementById('f_preview').src = defaultImage;
                document.getElementById('f_image_name').textContent = 'Belum ada file dipilih';
                document.getElementById('drawerError').style.display = 'none';

                const previewWrap = document.querySelector('[data-cropper-preview-wrap]');
                if (previewWrap) previewWrap.hidden = true;
                const cropperOutput = document.getElementById('f_cropped_image');
                if (cropperOutput) cropperOutput.value = '';
            };

            document.getElementById('btnOpenCreate').addEventListener('click', () => {
                resetForm();
                openDrawer();
            });

            [document.getElementById('btnCloseDrawer'), document.getElementById('btnCancelDrawer'), backdrop].forEach(el => {
                el.addEventListener('click', closeDrawer);
            });

            window.addEventListener('superadmin:sidebar-toggle', () => {
                closeDrawer();
            });

            const escapeHtml = (v) => String(v ?? '').replace(/[&<>"']/g, (m) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
            const formatMoney = (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(v || 0));

            const revealMenuContent = (root = document) => {
                root.querySelectorAll('.content-toolbar.fade-in, #menuContentRegion .fade-in').forEach((el) => {
                    el.classList.add('visible');
                    el.style.opacity = '1';
                    el.style.transform = 'none';
                });
            };

            const updateVisibleCount = (count) => {
                const countEl = getCountEl();
                if (!countEl) return;
                if (currentCategorySlug === 'paket') {
                    countEl.textContent = `${count} paket`;
                    return;
                }
                countEl.textContent = currentCategorySlug === 'all' ? `${count} item` : `${count} menu`;
            };

            const updateListHeading = () => {
                const titleEl = document.getElementById('menuListTitle');
                const iconEl = document.getElementById('menuListIcon');
                if (!titleEl || !iconEl) return;
                if (currentCategorySlug === 'paket') {
                    titleEl.textContent = 'Daftar Paket';
                    iconEl.className = 'fas fa-box-open';
                } else {
                    titleEl.textContent = 'Daftar Menu';
                    iconEl.className = 'fas fa-list';
                }
            };

            const applyCombinedFilters = () => {
                const list = getList();
                if (!list) return;

                const cards = [...list.querySelectorAll('.menu-card')];
                let visibleCount = 0;

                cards.forEach((card) => {
                    const categorySlug = (card.getAttribute('data-category-slug') || '').toLowerCase();
                    const itemType = card.getAttribute('data-item-type') || 'menu';
                    const name = card.querySelector('h3')?.textContent.toLowerCase() || '';
                    const code = card.querySelector('p')?.textContent.toLowerCase() || '';
                    const matchesSearch = !currentSearchQuery || name.includes(currentSearchQuery) || code.includes(currentSearchQuery);
                    const matchesCategory = currentCategorySlug === 'all'
                        ? true
                        : (currentCategorySlug === 'paket'
                            ? itemType === 'package'
                            : categorySlug === currentCategorySlug);

                    const shouldShow = matchesSearch && matchesCategory;
                    card.style.display = shouldShow ? '' : 'none';
                    if (shouldShow) visibleCount += 1;
                });

                let emptyState = document.getElementById('emptyState');
                if (visibleCount === 0) {
                    if (!emptyState) {
                        emptyState = document.createElement('div');
                        emptyState.className = 'alert';
                        emptyState.id = 'emptyState';
                        list.appendChild(emptyState);
                    }
                    const suffix = currentSearchQuery ? ` untuk "${currentSearchQuery}"` : '';
                    if (currentCategorySlug === 'paket') {
                        emptyState.innerHTML = `<em>Belum ada paket${suffix}.</em>`;
                    } else if (currentCategorySlug === 'all') {
                        emptyState.innerHTML = `<em>Belum ada menu atau paket${suffix}.</em>`;
                    } else {
                        emptyState.innerHTML = `<em>Belum ada menu kategori ${currentCategorySlug}${suffix}.</em>`;
                    }
                } else if (emptyState) {
                    emptyState.remove();
                }

                updateListHeading();
                updateVisibleCount(visibleCount);
            };

            const updatePillCount = (catId, delta) => {
                const id = catId == null || catId === 'none' ? '' : String(catId);
                const pill = document.querySelector(`.filter-pill[data-category-id="${id}"]`) || document.querySelector(`.filter-pill[data-category-id="all"]`);
                if (pill) {
                    const span = pill.querySelector('span');
                    if (span) {
                        const current = parseInt(span.textContent.replace(/[^0-9]/g, '')) || 0;
                        span.textContent = `(${Math.max(0, current + delta)})`;
                    }
                }
                if (catId !== 'all') updatePillCount('all', delta);
            };

            const bindCategoryFilters = () => {
                const categoryBar = getCategoryBar();
                if (!categoryBar || categoryBar.dataset.bound === '1') return;
                categoryBar.dataset.bound = '1';
                categoryBar.addEventListener('click', (event) => {
                    const link = event.target.closest('.filter-pill[href]');
                    if (!link) return;
                    event.preventDefault();

                    const categoryId = link.getAttribute('data-category-id') || 'all';
                    currentCategorySlug = (link.getAttribute('data-category-slug') || 'all').toLowerCase();
                    syncActiveCategoryPill(categoryId);
                    applyCombinedFilters();
                    window.history.replaceState({ path: link.href }, '', link.href);
                });
            };

            const bindActions = (root) => {
                if (!root) return;
                root.querySelectorAll('.btn-open-edit').forEach(btn => {
                    btn.addEventListener('click', () => {
                        resetForm();
                        const id = btn.getAttribute('data-id');
                        drawerTitle.innerHTML = '<i class="fas fa-pen"></i> Edit Menu';
                        btnSubmit.innerHTML = '<i class="fas fa-check"></i> Simpan Perubahan';
                        form.action = `${menuBaseUrl}/${id}`;
                        drawerMethod.disabled = false;
                        drawerMethod.value = 'PUT';
                        
                        document.getElementById('f_code').value = btn.getAttribute('data-code');
                        document.getElementById('f_name').value = btn.getAttribute('data-name');
                        document.getElementById('f_category').value = btn.getAttribute('data-category-id') || '';
                        document.getElementById('f_selling').value = btn.getAttribute('data-selling-price');
                        document.getElementById('f_cost').value = btn.getAttribute('data-cost-price');
                        
                        const imageUrl = btn.getAttribute('data-image-url');
                        document.getElementById('f_preview').src = imageUrl;

                        const previewWrap = document.querySelector('[data-cropper-preview-wrap]');
                        if (imageUrl && imageUrl !== defaultImage) {
                            if (previewWrap) previewWrap.hidden = false;
                        } else {
                            if (previewWrap) previewWrap.hidden = true;
                        }

                        openDrawer();
                    });
                });

                root.querySelectorAll('.btn-delete-menu').forEach(btn => {
                    btn.addEventListener('click', async () => {
                        const id = btn.getAttribute('data-id');
                        if (!confirm('Hapus menu ini?')) return;

                        try {
                            const list = getList();
                            const countEl = getCountEl();
                            const payload = new FormData();
                            payload.append('_method', 'DELETE');
                            payload.append('_token', getCsrfToken());

                            const res = await fetch(`${menuBaseUrl}/${id}`, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': getCsrfToken()
                                },
                                body: payload
                            });

                            const data = await res.json().catch(() => ({}));
                            if (!res.ok) {
                                throw new Error(data.message || 'Gagal menghapus menu.');
                            }

                            const card = list.querySelector(`[data-menu-id="${id}"]`);
                            if (!card) return;
                            const catId = card.getAttribute('data-category-id-val');
                            updatePillCount(catId, -1);
                            card.remove();
                            if (!list.querySelector('.menu-card')) list.innerHTML = '<div class="alert" id="emptyState"><em>Belum ada menu.</em></div>';
                            applyCombinedFilters();
                            window.showToast?.(data.message || 'Menu berhasil dihapus', 'success');
                        } catch (err) {
                            window.showToast?.(err.message || 'Gagal menghapus menu.', 'error');
                        }
                    });
                });
            };

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                btnSubmit.disabled = true;
                btnSubmit.textContent = 'Menyimpan...';
                
                try {
                    const list = getList();
                    const countEl = getCountEl();
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                        body: new FormData(form)
                    });

                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Terjadi kesalahan');

                    const menu = data.menu;
                    let card = list.querySelector(`[data-menu-id="${menu.id}"]`);
                    const isNew = !card;
                    const oldCatId = card?.getAttribute('data-category-id-val');

                    const html = `
                        <img class="menu-thumb" src="${escapeHtml(menu.image_url)}" alt="${escapeHtml(menu.name)}">
                        <div class="menu-meta">
                            <h3>${escapeHtml(menu.name)}</h3>
                            <p>${escapeHtml(menu.code)}</p>
                            <div class="menu-pricing">
                                <span class="tag tag-category">${escapeHtml(menu.category_name)}</span>
                                <span class="tag tag-success">${formatMoney(menu.selling_price)}</span>
                                <span class="tag tag-muted">Modal ${formatMoney(menu.cost_price)}</span>
                            </div>
                        </div>
                        <div class="actions">
                            <button type="button" class="btn-open-edit" 
                                data-id="${menu.id}" data-code="${escapeHtml(menu.code)}"
                                data-name="${escapeHtml(menu.name)}" data-category-id="${menu.menu_category_id ?? ''}"
                                data-selling-price="${menu.selling_price}" data-cost-price="${menu.cost_price}"
                                data-image-url="${escapeHtml(menu.image_url)}"
                            ><i class="fas fa-pen"></i> Edit</button>
                            <button type="button" class="btn-delete-menu" data-id="${menu.id}"><i class="fas fa-trash"></i> Hapus</button>
                        </div>
                    `;

                    if (isNew) {
                        const empty = document.getElementById('emptyState');
                        if (empty) empty.remove();
                        card = document.createElement('div');
                        card.className = 'menu-card';
                        card.setAttribute('data-menu-id', menu.id);
                        card.setAttribute('data-item-type', 'menu');
                        card.setAttribute('data-category-slug', String(menu.category_name || '').toLowerCase());
                        list.prepend(card);
                        updatePillCount(menu.menu_category_id, 1);
                    } else if (oldCatId !== (menu.menu_category_id ? String(menu.menu_category_id) : 'none')) {
                        updatePillCount(oldCatId, -1);
                        updatePillCount(menu.menu_category_id, 1);
                    }

                    card.setAttribute('data-category-id-val', menu.menu_category_id || 'none');
                    card.setAttribute('data-item-type', 'menu');
                    card.setAttribute('data-category-slug', String(menu.category_name || '').toLowerCase());
                    card.innerHTML = html;
                    bindActions(card);
                    applyCombinedFilters();
                    window.showToast?.(data.message, 'success');
                    closeDrawer();
                } catch (err) {
                    const errEl = document.getElementById('drawerError');
                    errEl.textContent = err.message;
                    errEl.style.display = 'block';
                } finally {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = drawerMethod.disabled ? '<i class="fas fa-check"></i> Simpan Menu' : '<i class="fas fa-check"></i> Simpan Perubahan';
                }
            });

            closeDrawer();
            window.addEventListener('pageshow', closeDrawer);
            revealMenuContent();
            bindCategoryFilters();
            bindActions(getList());
            syncActiveCategoryPill(getCurrentCategoryId());
            currentCategorySlug = (document.querySelector('.filter-pill.active')?.getAttribute('data-category-slug') || 'all').toLowerCase();
            applyCombinedFilters();
            window.addEventListener('pageshow', () => {
                syncActiveCategoryPill(getCurrentCategoryId());
                currentCategorySlug = (document.querySelector('.filter-pill.active')?.getAttribute('data-category-slug') || 'all').toLowerCase();
                applyCombinedFilters();
            });
            window.hydrateManagedImages?.(document);

            /* ===== LIVE SEARCH ===== */
            const searchInput = document.getElementById('menuSearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    currentSearchQuery = e.target.value.toLowerCase().trim();
                    applyCombinedFilters();
                });
            }
        })();
    </script>
@endpush

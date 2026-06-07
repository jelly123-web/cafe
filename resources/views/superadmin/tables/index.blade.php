@extends('superadmin.layout')

@section('title', 'Meja Cafe — MakanYuk')
@section('page_title', 'Meja Cafe')
@section('page_description', 'Lihat, tambah, dan kelola meja yang dipakai pelanggan untuk scan QR.')
@section('page_icon')
    <i class="fas fa-chair"></i>
@endsection
@section('kicker', 'Utama')

@push('head')
    <style>
    /* ===== TABLE TOOLBAR ===== */
    .table-toolbar {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      padding: 18px 24px;
      display: flex;
      justify-content: space-between;
      gap: 16px;
      align-items: center;
      flex-wrap: wrap;
      margin-bottom: 20px;
    }

    .toolbar-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

    /* ===== SEARCH BOX ===== */
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
    .search-box input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1); }

    /* ===== BUTTONS ===== */
    .primary-link {
      display: inline-flex; align-items: center; gap: 6px;
      background: var(--accent); color: white; text-decoration: none;
      padding: 10px 20px; border-radius: var(--radius-sm);
      font-weight: 700; font-size: 13px; border: none; cursor: pointer;
      transition: all var(--transition); font-family: var(--font);
    }
    .primary-link:hover { background: var(--accent-dark); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(217,119,6,0.25); }

    .secondary-link {
      display: inline-flex; align-items: center; gap: 5px;
      background: transparent; color: var(--fg-secondary); text-decoration: none;
      padding: 8px 14px; border-radius: var(--radius-sm);
      font-weight: 700; font-size: 12px; border: 1.5px solid var(--border); cursor: pointer;
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

    /* ===== TABLE GRID ===== */
    .table-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 16px;
    }

    /* ===== TABLE CARD ===== */
    .table-card {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      padding: 20px;
      display: flex;
      flex-direction: column;
      gap: 16px;
      transition: all 0.25s ease;
      animation: cardIn 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    @keyframes cardIn {
      from { opacity: 0; transform: translateY(8px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .table-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: var(--accent);
      opacity: 0;
      transition: opacity var(--transition);
    }

    .table-card:hover {
      border-color: transparent;
      box-shadow: var(--shadow-lg);
      transform: translateY(-2px);
    }

    .table-card:hover::before {
      opacity: 1;
    }

    /* ===== TABLE CARD HEAD ===== */
    .table-card-head {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      align-items: flex-start;
    }

    .table-card h3 {
      font-size: 15px;
      font-weight: 800;
      color: var(--fg);
      margin: 0;
      letter-spacing: -0.2px;
    }

    /* ===== TABLE PILL ===== */
    .table-pill {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 3px 10px;
      border-radius: var(--radius-full);
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.3px;
      margin-bottom: 4px;
    }

    .table-pill.active {
      background: var(--green-light);
      color: var(--green);
    }

    .table-pill.inactive {
      background: #F3F4F6;
      color: var(--muted);
    }

    .table-pill .pill-dot {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: currentColor;
    }

    .table-pill.active .pill-dot {
      animation: dotPulse 2s infinite;
    }

    @keyframes dotPulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.3; }
    }

    /* ===== TABLE STATUS INFO ===== */
    .table-status-row {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }

    .table-stat {
      display: flex;
      align-items: center;
      gap: 5px;
      font-size: 12px;
      color: var(--muted);
      font-weight: 500;
    }

    .table-stat i { font-size: 12px; }

    /* ===== QR BOX ===== */
    .qr-box {
      display: flex;
      flex-direction: column;
      gap: 8px;
      align-items: flex-start;
      padding-top: 14px;
      border-top: 1px dashed var(--border);
    }

    .qr-preview-wrap {
      display: flex;
      align-items: flex-start;
      gap: 14px;
      width: 100%;
    }

    .qr-preview {
      width: 110px;
      height: 110px;
      padding: 6px;
      background: var(--white);
      border-radius: var(--radius-sm);
      border: 1.5px solid var(--border);
      display: block;
      object-fit: contain;
      flex-shrink: 0;
      transition: all var(--transition);
    }

    .qr-preview:hover {
      border-color: var(--accent);
      transform: scale(1.03);
    }

    .qr-info {
      display: flex;
      flex-direction: column;
      gap: 4px;
      min-width: 0;
    }

    .qr-info small {
      color: var(--muted);
      font-size: 12px;
      line-height: 1.5;
    }

    .qr-token-label {
      font-family: 'SF Mono', 'Fira Code', monospace;
      font-size: 11px;
      color: var(--fg-secondary);
      background: var(--bg);
      padding: 4px 8px;
      border-radius: 4px;
      word-break: break-all;
      border: 1px solid var(--border-light);
    }

    /* ===== TABLE ACTIONS ===== */
    .table-actions {
      display: flex;
      gap: 6px;
      flex-wrap: wrap;
      padding-top: 12px;
      border-top: 1px solid var(--border-light);
    }

    .table-actions form { margin: 0; display: inline-flex; }

    /* ===== EMPTY STATE ===== */
    .table-empty {
      grid-column: 1 / -1;
      text-align: center;
      padding: 48px 24px;
      color: var(--muted);
      font-size: 14px;
      background: #fff;
      border-radius: var(--radius-lg);
    }

    .table-empty::before {
      content: '\f0fc';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      display: block;
      font-size: 36px;
      margin-bottom: 10px;
      color: var(--border);
    }

    .table-empty em { font-style: normal; font-weight: 700; color: var(--fg-secondary); }

    /* ===== PAGINATION ===== */
    .pagination-area { margin-top: 20px; }
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
    .pagination-link.disabled { opacity: 0.35; pointer-events: none; }

    /* ===== DRAWER ===== */
    .drawer-backdrop {
      position: fixed; inset: 0; background: rgba(0,0,0,0.3);
      backdrop-filter: blur(2px); z-index: 1200;
      opacity: 0; visibility: hidden; transition: all 0.25s ease;
    }
    .drawer-backdrop.open { opacity: 1; visibility: visible; }

    .table-drawer {
      position: fixed; top: 0; right: 0;
      width: min(480px, 95vw); height: 100vh;
      background: var(--white); z-index: 1201;
      transform: translateX(108%);
      opacity: 0; visibility: hidden; pointer-events: none;
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.2s ease, visibility 0.2s ease;
      box-shadow: var(--shadow-xl);
      display: flex; flex-direction: column;
    }
    .table-drawer.open { transform: translateX(0); opacity: 1; visibility: visible; pointer-events: auto; }

    .drawer-head {
      padding: 20px 24px; border-bottom: 1px solid var(--border);
      display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;
    }
    .drawer-head h3 {
      margin: 0; font-size: 16px; font-weight: 800; color: var(--fg);
      display: flex; align-items: center; gap: 8px; letter-spacing: -0.2px;
    }
    .drawer-head h3 i { color: var(--accent); }

    .drawer-close {
      border: 1px solid var(--border); background: var(--white);
      color: var(--fg-secondary); border-radius: var(--radius-sm);
      padding: 8px 14px; cursor: pointer; font-weight: 700; font-size: 12px;
      font-family: var(--font); transition: all var(--transition);
    }
    .drawer-close:hover { border-color: var(--red); color: var(--red); background: var(--red-light); }

    /* ===== DRAWER BODY ===== */
    .drawer-body {
      padding: 28px 24px; overflow-y: auto; flex: 1;
      scrollbar-width: thin; scrollbar-color: var(--border) transparent;
      display: flex; flex-direction: column; gap: 18px;
    }
    .drawer-body::-webkit-scrollbar { width: 5px; }
    .drawer-body::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }

    .drawer-form { display: flex; flex-direction: column; flex: 1; height: 100%; min-height: 0; }

    .drawer-field { display: flex; flex-direction: column; gap: 10px; }

    .drawer-field label {
      font-size: 12px; font-weight: 700; color: var(--fg-secondary);
      text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px;
    }

    .drawer-field input[type="text"] {
      width: 100%; padding: 10px 14px;
      border: 1.5px solid var(--border); border-radius: var(--radius-sm);
      background: var(--white); color: var(--fg);
      font-size: 14px; font-weight: 500; outline: none;
      transition: all var(--transition); font-family: var(--font);
    }
    .drawer-field input::placeholder { color: var(--muted); font-weight: 400; }
    .drawer-field input[type="text"]:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1); }

    /* ===== SWITCH ===== */
    .switch-row {
      display: inline-flex; align-items: center; gap: 12px;
      cursor: pointer; min-height: 32px; margin-top: 2px;
    }

    .switch-text {
      font-size: 13px; font-weight: 600; color: var(--fg-secondary);
      line-height: 1; white-space: nowrap;
    }

    .switch-row input[type="checkbox"] {
      position: absolute; opacity: 0; pointer-events: none;
    }

    .switch-ui {
      width: 44px; height: 24px;
      background-color: var(--border);
      border-radius: 50px; position: relative;
      display: inline-block; transition: background-color 0.25s ease;
      flex-shrink: 0;
    }

    .switch-ui::after {
      content: ''; position: absolute;
      top: 2px; left: 2px; width: 20px; height: 20px;
      background-color: var(--white); border-radius: 50%;
      transition: transform 0.3s ease;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .switch-row input[type="checkbox"]:checked + .switch-ui {
      background-color: var(--accent);
    }

    .switch-row input[type="checkbox"]:checked + .switch-ui::after {
      transform: translateX(20px);
    }

    /* ===== DRAWER FOOT ===== */
    .drawer-foot {
      padding: 20px 24px; border-top: 1px solid var(--border);
      display: flex; gap: 12px; justify-content: flex-end;
      background: #FAFBFC; flex-shrink: 0;
    }

    .btn-drawer-cancel {
      border: 1.5px solid var(--border); background: var(--white);
      color: var(--fg-secondary); border-radius: var(--radius-sm);
      padding: 11px 22px; cursor: pointer; font-weight: 700; font-size: 13px;
      font-family: var(--font); transition: all var(--transition); min-width: 116px;
    }
    .btn-drawer-cancel:hover { border-color: var(--red); color: var(--red); background: var(--red-light); }

    .form-error {
      font-size: 12px; color: var(--red); min-height: 1rem;
      font-weight: 600; padding: 10px 14px; background: var(--red-light);
      border-radius: var(--radius-sm); border: 1px solid #FECACA;
      display: none;
    }
    .form-error:not(:empty) { display: block; }

    /* ===== QR MODAL ===== */
    .qr-modal-backdrop {
      position: fixed; inset: 0; background: rgba(0,0,0,0.35);
      backdrop-filter: blur(2px); opacity: 0; visibility: hidden;
      transition: all 0.25s ease; z-index: 1300;
    }
    .qr-modal-backdrop.open { opacity: 1; visibility: visible; }

    .qr-modal {
      position: fixed; left: 50%; top: 50%;
      transform: translate(-50%, -45%);
      width: min(400px, 92vw); background: var(--white);
      border: 1px solid var(--border); border-radius: var(--radius-lg);
      box-shadow: var(--shadow-xl); z-index: 1301;
      opacity: 0; visibility: hidden; transition: all 0.25s ease;
    }
    .qr-modal.open { opacity: 1; visibility: visible; transform: translate(-50%, -50%); }

    .qr-modal-head {
      padding: 14px 20px; border-bottom: 1px solid var(--border-light);
      display: flex; justify-content: space-between; align-items: center; gap: 8px;
    }
    .qr-modal-head strong {
      font-size: 14px; font-weight: 800; color: var(--fg);
      display: flex; align-items: center; gap: 8px;
    }
    .qr-modal-head strong i { color: var(--accent); }

    .qr-modal-body {
      padding: 20px; display: flex; flex-direction: column; gap: 12px; align-items: center;
    }

    .qr-modal-img {
      width: 200px; height: 200px;
      border: 1.5px solid var(--border); border-radius: var(--radius-md);
      padding: 8px; background: var(--white); object-fit: contain;
      transition: all var(--transition);
    }
    .qr-modal-img:hover { border-color: var(--accent); }

    .qr-url {
      width: 100%; font-size: 11px; color: var(--muted);
      word-break: break-all; text-align: center;
      font-family: 'SF Mono', 'Fira Code', monospace;
      background: var(--bg); padding: 8px 12px; border-radius: var(--radius-sm);
      border: 1px solid var(--border-light);
    }
    </style>
@endpush

@section('content')
    <!-- TOOLBAR -->
    <div class="table-toolbar fade-in">
        <div>
            <h2><i class="fas fa-chair"></i> Daftar Meja</h2>
            <p>Setiap meja punya QR unik untuk scan pelanggan.</p>
        </div>
        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <div class="search-box">
                <input type="text" id="tableSearchInput" placeholder="Cari meja (cth: Meja 1)..." autocomplete="off">
            </div>
            <div class="toolbar-actions">
                <button type="button" class="primary-link" id="openCreateDrawer"><i class="fas fa-plus"></i> Tambah Meja</button>
                <form method="POST" action="{{ route('superadmin.tables.destroy-all') }}" onsubmit="return confirm('Hapus semua meja? Aksi ini tidak bisa dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="danger-link"><i class="fas fa-trash-can"></i> Hapus Semua Meja</button>
                </form>
            </div>
        </div>
    </div>

    <!-- TABLE GRID -->
    <div class="table-grid fade-in">
        @forelse ($tables as $table)
            <article class="table-card" data-table-id="{{ $table->id }}">
                <div class="table-card-head">
                    <div>
                        <span class="table-pill {{ $table->is_active ? 'active' : 'inactive' }}">
                            <span class="pill-dot"></span> Meja {{ $table->number }}
                        </span>
                        <h3>{{ $table->name }}</h3>
                    </div>
                </div>
                <div class="table-status-row">
                    <span class="table-stat">
                        <i class="fas {{ $table->is_active ? 'fa-circle-check' : 'fa-circle-xmark' }}" style="color: {{ $table->is_active ? 'var(--green)' : 'var(--muted)' }};"></i> 
                        {{ $table->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                    <span class="table-stat"><i class="fas fa-receipt"></i> {{ $table->sales_count }} transaksi</span>
                </div>
                <div class="qr-box">
                    <div class="qr-preview-wrap">
                        <img class="qr-preview" 
                            src="{{ route('superadmin.tables.qr', $table->id) }}" 
                            alt="QR Meja {{ $table->number }}" 
                            loading="lazy"
                            style="{{ !$table->is_active ? 'opacity:0.5;' : '' }}"
                        >
                        <div class="qr-info">
                            <small>Scan QR untuk membuka halaman pelanggan meja ini.</small>
                            <div class="qr-token-label">Token: {{ $table->qr_token }}</div>
                        </div>
                    </div>
                </div>
                <div class="table-actions">
                    <a class="secondary-link" href="{{ route('tables.show', $table) }}" target="_blank" rel="noopener"><i class="fas fa-external-link-alt"></i> Buka Halaman</a>
                    <button type="button" class="secondary-link btn-show-qr" 
                        data-show-url="{{ route('tables.show', $table) }}" 
                        data-qr-url="{{ route('superadmin.tables.qr', $table->id) }}">
                        <i class="fas fa-qrcode"></i> Lihat QR
                    </button>
                    <button type="button" class="secondary-link btn-open-edit" 
                        data-id="{{ $table->id }}" 
                        data-number="{{ $table->number }}" 
                        data-name="{{ $table->name }}" 
                        data-active="{{ $table->is_active ? 1 : 0 }}">
                        <i class="fas fa-pen"></i> Edit
                    </button>
                    <form method="POST" action="{{ route('superadmin.tables.destroy', $table->id) }}" onsubmit="return confirm('Hapus meja ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="danger-link"><i class="fas fa-trash"></i> Hapus</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="table-empty" id="emptyState">
                <em>Belum ada meja.</em>
            </div>
        @endforelse
    </div>

    <!-- PAGINATION -->
    <div class="pagination-area fade-in">
        {{ $tables->links('components.pagination') }}
    </div>

    <!-- DRAWER BACKDROP -->
    <div id="drawerBackdrop" class="drawer-backdrop"></div>

    <!-- TABLE DRAWER -->
    <aside id="tableDrawer" class="table-drawer" aria-hidden="true">
        <div class="drawer-head">
            <h3 id="drawerTitle"><i class="fas fa-plus-circle"></i> Tambah Meja</h3>
            <button type="button" class="drawer-close" id="closeDrawerBtn">Tutup</button>
        </div>
        <form id="drawerForm" class="drawer-form" method="POST" action="{{ route('superadmin.tables.store') }}">
            @csrf
            <input type="hidden" name="_method" id="methodSpoof" value="">
            <div class="drawer-body">
                <div class="drawer-field">
                    <label for="drawer_number">Nomor Meja</label>
                    <input id="drawer_number" type="text" name="number" required placeholder="Cth: 7">
                </div>
                <div class="drawer-field">
                    <label for="drawer_name">Nama Meja</label>
                    <input id="drawer_name" type="text" name="name" required placeholder="Cth: Meja Tengah 3">
                </div>
                <div class="drawer-field">
                    <label style="margin-top:4px;">Status</label>
                    <label class="switch-row" for="drawer_is_active">
                        <input id="drawer_is_active" type="checkbox" name="is_active" value="1" checked>
                        <span class="switch-ui" aria-hidden="true"></span>
                        <span class="switch-text">Aktif</span>
                    </label>
                </div>
                <div class="form-error" id="drawerError"></div>
            </div>
            <div class="drawer-foot">
                <button type="button" class="btn-drawer-cancel" id="cancelDrawerBtn">Batal</button>
                <button type="submit" class="primary-link" id="submitDrawerBtn"><i class="fas fa-check"></i> Simpan Meja</button>
            </div>
        </form>
    </aside>

    <!-- QR MODAL -->
    <div id="qrModalBackdrop" class="qr-modal-backdrop"></div>
    <section id="qrModal" class="qr-modal" aria-hidden="true">
        <div class="qr-modal-head">
            <strong><i class="fas fa-qrcode"></i> QR Meja</strong>
            <button type="button" class="drawer-close" id="closeQrModalBtn">Tutup</button>
        </div>
        <div class="qr-modal-body">
            <img id="qrPreviewImg" class="qr-modal-img" alt="QR Meja">
            <div class="qr-url" id="qrPreviewUrl"></div>
        </div>
    </section>

    <!-- TOAST CONTAINER -->
    <div class="toast-container" id="toastContainer"></div>
@endsection

@push('scripts')
    <script>
        (function () {
            const drawer = document.getElementById('tableDrawer');
            const backdrop = document.getElementById('drawerBackdrop');
            const openCreateBtn = document.getElementById('openCreateDrawer');
            const closeBtn = document.getElementById('closeDrawerBtn');
            const cancelBtn = document.getElementById('cancelDrawerBtn');
            const title = document.getElementById('drawerTitle');
            const form = document.getElementById('drawerForm');
            const methodSpoof = document.getElementById('methodSpoof');
            const submitBtn = document.getElementById('submitDrawerBtn');
            const errorEl = document.getElementById('drawerError');
            const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const grid = document.querySelector('.table-grid');
            const qrModal = document.getElementById('qrModal');
            const qrBackdrop = document.getElementById('qrModalBackdrop');
            const closeQrBtn = document.getElementById('closeQrModalBtn');
            const qrPreviewImg = document.getElementById('qrPreviewImg');
            const qrPreviewUrl = document.getElementById('qrPreviewUrl');

            const openDrawer = () => {
                drawer.classList.add('open');
                backdrop.classList.add('open');
                drawer.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            };

            const closeDrawer = () => {
                drawer.classList.remove('open');
                backdrop.classList.remove('open');
                drawer.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            };

            const openQrModal = (url, qrUrl) => {
                qrPreviewUrl.textContent = url;
                qrPreviewImg.src = qrUrl || '';
                qrModal.classList.add('open');
                qrBackdrop.classList.add('open');
                qrModal.setAttribute('aria-hidden', 'false');
            };

            const closeQrModal = () => {
                qrModal.classList.remove('open');
                qrBackdrop.classList.remove('open');
                qrModal.setAttribute('aria-hidden', 'true');
            };

            const setCreateMode = () => {
                title.innerHTML = '<i class="fas fa-plus-circle"></i> Tambah Meja';
                form.action = "{{ route('superadmin.tables.store') }}";
                methodSpoof.value = '';
                form.dataset.editId = '';
                form.reset();
                document.getElementById('drawer_is_active').checked = true;
                submitBtn.innerHTML = '<i class="fas fa-check"></i> Simpan Meja';
                submitBtn.disabled = false;
                errorEl.textContent = '';
            };

            const setEditMode = (btn) => {
                const id = btn.getAttribute('data-id');
                title.innerHTML = '<i class="fas fa-pen-to-square"></i> Edit Meja';
                form.action = "{{ url('superadmin/tables') }}/" + id;
                methodSpoof.value = 'PUT';
                form.dataset.editId = id || '';
                document.getElementById('drawer_number').value = btn.getAttribute('data-number') || '';
                document.getElementById('drawer_name').value = btn.getAttribute('data-name') || '';
                document.getElementById('drawer_is_active').checked = btn.getAttribute('data-active') === '1';
                submitBtn.innerHTML = '<i class="fas fa-check"></i> Simpan Perubahan';
                submitBtn.disabled = false;
                errorEl.textContent = '';
            };

            const bindActionButtons = (root = document) => {
                root.querySelectorAll('.btn-open-edit').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        setEditMode(btn);
                        openDrawer();
                    });
                });

                root.querySelectorAll('.btn-show-qr').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const url = btn.getAttribute('data-show-url');
                        const qrUrl = btn.getAttribute('data-qr-url');
                        if (url) openQrModal(url, qrUrl);
                    });
                });
            };

            openCreateBtn?.addEventListener('click', () => {
                setCreateMode();
                openDrawer();
            });

            [closeBtn, cancelBtn, backdrop].forEach((el) => el?.addEventListener('click', closeDrawer));
            [closeQrBtn, qrBackdrop].forEach((el) => el?.addEventListener('click', closeQrModal));
            
            bindActionButtons();

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                errorEl.textContent = '';

                if (!form.reportValidity()) {
                    return;
                }

                submitBtn.disabled = true;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

                const formData = new FormData(form);
                const isEdit = methodSpoof.value === 'PUT';
                
                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken()
                        },
                        body: formData
                    });

                    if (res.status === 419) {
                        throw new Error('Sesi telah berakhir. Refresh halaman lalu coba lagi.');
                    }

                    const payload = await res.json();
                    if (!res.ok) {
                        const msg = payload.message || (payload.errors ? Object.values(payload.errors)[0][0] : 'Gagal menyimpan meja.');
                        throw new Error(msg);
                    }

                    window.location.reload();
                } catch (err) {
                    errorEl.textContent = err.message || 'Terjadi kesalahan.';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });

            /* ===== LIVE SEARCH ===== */
            const searchInput = document.getElementById('tableSearchInput');
            const tableGrid = document.querySelector('.table-grid');
            
            if (searchInput && tableGrid) {
                searchInput.addEventListener('input', (e) => {
                    const query = e.target.value.toLowerCase().trim();
                    const cards = tableGrid.querySelectorAll('.table-card:not(.table-empty)');
                    let hasVisible = false;

                    cards.forEach(card => {
                        const name = card.querySelector('h3')?.textContent.toLowerCase() || '';
                        const number = card.querySelector('.table-pill')?.textContent.toLowerCase() || '';
                        
                        if (name.includes(query) || number.includes(query)) {
                            card.style.display = '';
                            hasVisible = true;
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            }
        })();
    </script>
@endpush

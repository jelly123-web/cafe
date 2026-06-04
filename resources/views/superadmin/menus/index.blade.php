@extends('superadmin.layout')

@push('head')
    <style>
        :root {
            --bg-main: #f9f5f0;
            --bg-card: #ffffff;
            --primary: #795548;
            --secondary: #bcaaa4;
            --accent: #d7ccc8;
            --highlight: #d4a373;
            --text-main: #6d4c41;
            --text-muted: #a1887f;
            --profit: #81c784;
            --loss: #e57373;
            --shadow: rgba(121, 85, 72, 0.08);
        }
        .content-toolbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem; }
        .search-box { display:flex; gap:0.5rem; }
        .search-box input { border:1px solid var(--accent); background:var(--bg-card); padding:0.6rem 1rem; border-radius:12px; min-width:260px; }
        .search-box button { background:var(--secondary); color:#fff; border:none; padding:0.6rem 1.2rem; border-radius:12px; cursor:pointer; font-weight:600; }
        .search-box button:hover { background:var(--primary); }
        .toolbar-actions { display:flex; align-items:center; gap:0.65rem; flex-wrap:wrap; justify-content:flex-end; }
        
        /* Lightened Button Colors */
        .primary-link { display:inline-flex; align-items:center; background:#e2b68c; color:#fff; text-decoration:none; padding:0.6rem 1.2rem; border-radius:12px; font-weight:600; border:none; cursor:pointer; transition: all 0.2s; }
        .primary-link:hover { background:#d4a373; transform: translateY(-1px); }
        .danger-link { display:inline-flex; align-items:center; justify-content:center; background:transparent; color:#e57373; border:1px solid #ffcdd2; padding:0.6rem 1.1rem; border-radius:12px; font-weight:700; cursor:pointer; transition: all 0.2s; }
        .danger-link:hover { background:#fff0f0; border-color:#e57373; }

        .panel { background:var(--bg-card); padding:2rem; border-radius:20px; box-shadow:0 4px 15px var(--shadow); }
        .panel-head { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:1.5rem; padding-bottom:0.75rem; border-bottom:2px solid var(--accent); }
        .panel-head h2 { font-family:'Playfair Display', Georgia, serif; color:var(--primary); font-size:1.9rem; margin:0; }
        .panel-head span { font-size:0.9rem; color:var(--text-muted); font-weight:500; }
        .menu-card-list { display:flex; flex-direction:column; gap:1rem; }
        .menu-card { display:flex; gap:1.25rem; padding:1rem; background:#fffaf5; border:1px solid var(--accent); border-radius:16px; align-items:flex-start; }
        .menu-thumb { width:90px; height:90px; border-radius:12px; object-fit:cover; background-color:var(--bg-main); flex-shrink:0; }
        .menu-meta { flex:1; display:flex; flex-direction:column; justify-content:center; gap:0.45rem; min-width:0; }
        .menu-meta h3 { font-family:'Playfair Display', Georgia, serif; color:var(--primary); font-size:1.15rem; margin-bottom:0.1rem; }
        .menu-meta p { color:var(--text-muted); font-size:0.85rem; margin:0; }
        .menu-pricing { display:flex; flex-wrap:wrap; gap:0.5rem; margin:0; }
        .tag { display:inline-block; padding:0.25rem 0.75rem; border-radius:50px; font-size:0.75rem; font-weight:600; background-color:#efebe9; color:var(--primary); }
        .tag-success { background-color:#e8f5e9; color:#558b2f; }
        .tag-muted { background-color:#f5f5f5; color:#9e9e9e; }
        .actions { display:flex; gap:1rem; align-items:center; flex-wrap:nowrap; white-space:nowrap; min-height:24px; }
        .actions a, .actions button { border:none; background:transparent; cursor:pointer; padding:0; font-weight:700; font-size:0.98rem; text-decoration: none; line-height:1; }
        .btn-open-edit { color:#c68b59; }
        .btn-open-edit:hover { color:var(--primary); text-decoration:underline; }
        .btn-delete-menu { color:var(--loss); }
        .btn-delete-menu:hover { color:#b71c1c; text-decoration:underline; }
        .alert { background:var(--bg-card); border-radius:12px; padding:1rem 1.1rem; box-shadow:0 4px 15px var(--shadow); margin-bottom:1rem; color:var(--text-muted); font-style:italic; }
        .menu-pagination { margin-top:1.25rem; }

        /* Category Filter Bar */
        .category-filter-bar { display: flex; gap: 0.65rem; margin-bottom: 1.5rem; overflow-x: auto; padding-bottom: 0.5rem; scrollbar-width: none; }
        .category-filter-bar::-webkit-scrollbar { display: none; }
        .filter-pill { display: inline-flex; align-items: center; padding: 0.6rem 1.15rem; background: #fff; border: 1px solid var(--accent); border-radius: 50px; color: var(--text-main); font-size: 0.85rem; font-weight: 600; text-decoration: none; white-space: nowrap; transition: all 0.2s; }
        .filter-pill:hover { border-color: var(--highlight); background: var(--bg-main); }
        .filter-pill.active { background: var(--highlight); color: #fff; border-color: var(--highlight); box-shadow: 0 4px 10px rgba(212, 163, 115, 0.25); }
        .filter-pill span { font-size: 0.75rem; opacity: 0.7; margin-left: 4px; font-weight: 500; }

        /* Drawer Styles */
        .drawer-backdrop { position:fixed; inset:0; background:rgba(56, 37, 30, 0.32); backdrop-filter:blur(2px); z-index:1200; opacity:0; visibility:hidden; transition:0.2s ease; }
        .drawer-backdrop.open { opacity:1; visibility:visible; }
        .menu-drawer {
            position: fixed;
            top: 0;
            right: 0;
            width: min(600px, 95vw);
            height: 100vh;
            background: #fff;
            z-index: 1201;
            transform: translateX(102%);
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: -10px 0 34px rgba(79, 53, 43, 0.12);
            display: flex;
            flex-direction: column;
        }
        .menu-drawer.open { transform:translateX(0); }
        .drawer-head {
            padding: 1.5rem 1.75rem;
            border-bottom: 1px solid var(--accent);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            flex-shrink: 0;
        }
        .drawer-head h3 { margin: 0; font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.6rem; }
        .drawer-close { border: 1.5px solid var(--accent); background: #fff; color: var(--primary); border-radius: 12px; padding: 0.5rem 1rem; cursor: pointer; font-weight: 700; font-size: 0.9rem; transition: all 0.2s; }
        .drawer-close:hover { background: var(--bg-main); border-color: var(--highlight); }
        
        .drawer-body { 
            padding: 1.75rem; 
            overflow-y: auto; 
            flex: 1;
            scrollbar-width: thin;
            scrollbar-color: var(--accent) transparent;
        }
        .drawer-body::-webkit-scrollbar { width: 6px; }
        .drawer-body::-webkit-scrollbar-thumb { background: var(--accent); border-radius: 10px; }

        .drawer-foot { 
            padding: 1.25rem 1.75rem; 
            border-top: 1px solid var(--accent); 
            display: flex; 
            gap: 1rem; 
            justify-content: flex-end; 
            background: #fdfaf8;
            flex-shrink: 0;
        }
        .btn-drawer-cancel { border: 1.5px solid var(--accent); background:#fff; color:var(--primary); border-radius:12px; padding:0.75rem 1.5rem; cursor:pointer; font-weight:700; transition: all 0.2s; }
        .btn-drawer-cancel:hover { border-color: var(--loss); color: var(--loss); }

        /* Form Layout in Drawer */
        .drawer-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1rem; }
        .drawer-field { display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1rem; }
        .drawer-field.full { grid-column: 1 / -1; }
        .drawer-field label { font-size: 0.9rem; font-weight: 700; color: var(--text-main); }
        .drawer-field input, .drawer-field select { 
            width: 100%; 
            padding: 0.75rem 1.15rem; 
            border: 1.5px solid var(--accent); 
            border-radius: 14px; 
            background: #fff; 
            color: var(--text-main); 
            font-size: 1rem; 
            outline: none; 
            transition: all 0.2s;
        }
        .drawer-field input:focus, .drawer-field select:focus { border-color: var(--highlight); box-shadow: 0 0 0 4px rgba(212, 163, 115, 0.12); }
        .drawer-error { background: #fff5f5; color: var(--loss); padding: 0.85rem 1.15rem; border-radius: 12px; border-left: 4px solid var(--loss); font-weight: 600; margin-top: 0.5rem; display: none; }

        /* Image Picker in Drawer */
        .drawer-image-box { grid-column: span 2; display: grid; grid-template-columns: 1fr 130px; gap: 1.5rem; align-items: start; margin-top: 0.5rem; }
        .drawer-image-preview-wrap { position: relative; width: 130px; }
        .drawer-image-preview { width: 130px; height: 130px; object-fit: cover; border-radius: 18px; border: 1.5px dashed var(--accent); padding: 4px; background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.06); cursor: pointer; transition: all 0.2s; }
        .drawer-image-preview:hover { border-color: var(--highlight); transform: scale(1.02); }
        .drawer-image-clear { position: absolute; top: -8px; right: -8px; width: 26px; height: 26px; border-radius: 50%; border: none; background: var(--loss); color: #fff; cursor: pointer; font-weight: 800; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(229, 115, 115, 0.4); z-index: 10; }
        .drawer-file-label { display: inline-flex; background: var(--primary); color: #fff; padding: 0.65rem 1.25rem; border-radius: 10px; cursor: pointer; font-weight: 700; font-size: 0.9rem; margin-bottom: 0.5rem; transition: all 0.2s; }
        .drawer-file-label:hover { background: #5d3a31; transform: translateY(-1px); }
        .drawer-file-name { display: block; font-size: 0.85rem; color: var(--text-muted); font-style: italic; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

        /* Cropper Modal in Drawer */
        .cropper-modal { position:fixed; inset:0; z-index:1700; display:grid; place-items:center; background:rgba(56,37,30,.34); backdrop-filter:blur(3px); }
        .cropper-modal[hidden] { display:none !important; }
        .cropper-dialog { width:min(560px, calc(100vw - 2rem)); background:#fff; border:1px solid var(--accent); border-radius:18px; box-shadow:0 18px 45px rgba(62,39,35,.18); overflow:hidden; }
        .cropper-head, .cropper-foot { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:1rem 1.15rem; border-bottom:1px solid var(--accent); }
        .cropper-foot { border-bottom:0; border-top:1px solid var(--accent); justify-content:flex-end; }
        .cropper-head strong { color:var(--primary); font-family:'Playfair Display', Georgia, serif; font-size:1.2rem; }
        .cropper-body { display:grid; place-items:center; gap:.85rem; padding:1.15rem; }
        .cropper-canvas { width:min(360px, 78vw); height:min(360px, 78vw); border:1px dashed var(--accent); border-radius:18px; background:#fffaf5; cursor:move; touch-action:none; }
        .cropper-control { display:grid; gap:0.35rem; width:min(360px, 78vw); color:var(--text-muted); font-size:0.85rem; font-weight:600; }
        .cropper-control input { accent-color:var(--highlight); }
        .cropper-modal-close, .cropper-done { border:1px solid var(--accent); background:#fff; color:var(--primary); border-radius:12px; padding:.55rem 1rem; cursor:pointer; font-weight:700; }
        .cropper-done { background:var(--highlight); color:#fff; border-color:var(--highlight); }

        @media (max-width: 768px) {
            .drawer-form-grid { grid-template-columns: 1fr; }
            .drawer-image-box { grid-template-columns: 1fr; }
            .drawer-image-preview { width: 100%; height: 160px; }
            .content-toolbar { flex-direction: column; align-items: stretch; gap: 1rem; }
            .toolbar-actions { display: grid; grid-template-columns: 1fr 1fr; }
            .menu-card { flex-direction: column; }
            .actions { gap:0.85rem; }
        }
    </style>
@endpush

@section('title', 'Manajemen Menu')
@section('page_title', 'Manajemen Menu')
@section('page_description', 'Tambah, edit, hapus menu, foto, dan harga.')

@section('content')
    <div class="content-toolbar">
        <form method="GET" action="{{ route('superadmin.menus.index') }}" class="search-box" id="menusSearchForm">
            <input type="text" name="search" id="menusSearchInput" placeholder="Cari menu atau kode" value="{{ request('search') }}">
            @if(request('category_id'))
                <input type="hidden" name="category_id" value="{{ request('category_id') }}">
            @endif
            <button type="submit">Cari</button>
        </form>

        <div class="toolbar-actions">
            <form method="POST" action="{{ route('superadmin.menus.destroy-all') }}" onsubmit="return confirm('Hapus semua menu? Tindakan ini tidak bisa dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="danger-link">Hapus Semua Menu</button>
            </form>
            <button type="button" class="primary-link" id="btnOpenCreate">+ Tambah Menu</button>
        </div>
    </div>

    <div class="category-filter-bar">
        <a href="{{ route('superadmin.menus.index', ['search' => request('search')]) }}" 
           class="filter-pill {{ !request('category_id') ? 'active' : '' }}"
           data-category-id="all">
            Semua <span>({{ $total_menus }})</span>
        </a>
        @foreach ($categories as $category)
            <a href="{{ route('superadmin.menus.index', ['category_id' => $category->id, 'search' => request('search')]) }}" 
               class="filter-pill {{ request('category_id') == $category->id ? 'active' : '' }}"
               data-category-id="{{ $category->id }}">
                {{ $category->name }} <span>({{ $category->menus_count }})</span>
            </a>
        @endforeach
    </div>

    <div class="panel">
        <div class="panel-head">
            <h2>Daftar Menu</h2>
            <span id="menuCount">{{ $menus->total() }} menu</span>
        </div>

        <div class="menu-card-list" id="menusList">
            @forelse ($menus as $menu)
                @php
                    $menuImage = $menu->image_path
                        ? (Storage::disk('public')->exists($menu->image_path) ? Storage::disk('public')->url($menu->image_path) : asset('images/menu-placeholder.svg'))
                        : asset('images/menu-placeholder.svg');
                @endphp
                <div class="menu-card" data-menu-id="{{ $menu->id }}" data-category-id-val="{{ $menu->menu_category_id ?? 'none' }}">
                    <img class="menu-thumb" src="{{ $menuImage }}" alt="{{ $menu->name }}">
                    <div class="menu-meta">
                        <h3>{{ $menu->name }}</h3>
                        <p>{{ $menu->code }}</p>
                        <div class="menu-pricing">
                            <span class="tag">{{ $menu->category?->name ?? 'Tanpa kategori' }}</span>
                            <span class="tag tag-success">Rp {{ number_format((float) $menu->selling_price, 0, ',', '.') }}</span>
                            <span class="tag tag-muted">Modal Rp {{ number_format((float) $menu->cost_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="actions">
                            <button type="button" class="btn-open-edit" 
                                data-id="{{ $menu->id }}"
                                data-code="{{ $menu->code }}"
                                data-name="{{ $menu->name }}"
                                data-category-id="{{ $menu->menu_category_id }}"
                                data-selling-price="{{ (float) $menu->selling_price }}"
                                data-cost-price="{{ (float) $menu->cost_price }}"
                                data-image-url="{{ $menuImage }}"
                            >Edit</button>
                            <button type="button" class="btn-delete-menu" data-id="{{ $menu->id }}">Hapus</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert" id="emptyState">Belum ada menu.</div>
            @endforelse
        </div>

        <div class="menu-pagination">
            {{ $menus->links('components.pagination') }}
        </div>
    </div>

    <!-- SIDE MODAL (DRAWER) -->
    <div id="drawerBackdrop" class="drawer-backdrop"></div>
    <aside id="menuDrawer" class="menu-drawer" aria-hidden="true">
        <div class="drawer-head">
            <h3 id="drawerTitle">Tambah Menu</h3>
            <button type="button" class="drawer-close" id="btnCloseDrawer">Tutup</button>
        </div>
        <form id="drawerForm" method="POST" enctype="multipart/form-data" style="display: contents;">
            @csrf
            <input type="hidden" name="_method" id="drawerMethod" value="POST" disabled>
            <div class="drawer-body">
                <div class="drawer-form-grid">
                    <div class="drawer-field">
                        <label for="f_code">Kode Menu</label>
                        <input id="f_code" type="text" name="code" required>
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
                            <label for="f_image" class="drawer-file-label">Pilih Foto</label>
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
                                    <strong>Atur Crop Foto Menu</strong>
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
                <button type="submit" class="primary-link" id="btnSubmitDrawer">Simpan Menu</button>
            </div>
        </form>
    </aside>

    <script src="{{ asset('js/cafe-image-cropper.js') }}?v=4"></script>
    <script>
        (function () {
            const drawer = document.getElementById('menuDrawer');
            const backdrop = document.getElementById('drawerBackdrop');
            const form = document.getElementById('drawerForm');
            const drawerTitle = document.getElementById('drawerTitle');
            const drawerMethod = document.getElementById('drawerMethod');
            const btnSubmit = document.getElementById('btnSubmitDrawer');
            const list = document.getElementById('menusList');
            const countEl = document.getElementById('menuCount');
            const filterPills = document.querySelectorAll('.filter-pill');
            const searchInput = document.getElementById('menusSearchInput');
            
            const getCsrfToken = () => document.querySelector('input[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const defaultImage = @json(asset('images/menu-placeholder.svg'));
            const menuBaseUrl = @json(url('superadmin/menus'));
            const storeUrl = @json(route('superadmin.menus.store'));

            const openDrawer = () => { drawer.classList.add('open'); backdrop.classList.add('open'); drawer.setAttribute('aria-hidden', 'false'); };
            const closeDrawer = () => { drawer.classList.remove('open'); backdrop.classList.remove('open'); drawer.setAttribute('aria-hidden', 'true'); };

            const resetForm = () => {
                form.reset();
                form.action = storeUrl;
                drawerMethod.disabled = true;
                drawerTitle.textContent = 'Tambah Menu';
                btnSubmit.textContent = 'Simpan Menu';
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

            const escapeHtml = (v) => String(v ?? '').replace(/[&<>"']/g, (m) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
            const formatMoney = (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(v || 0));

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

            const bindActions = (root) => {
                root.querySelectorAll('.btn-open-edit').forEach(btn => {
                    btn.addEventListener('click', () => {
                        resetForm();
                        const id = btn.getAttribute('data-id');
                        drawerTitle.textContent = 'Edit Menu';
                        btnSubmit.textContent = 'Simpan Perubahan';
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
                            if (!list.querySelector('.menu-card')) list.innerHTML = '<div class="alert" id="emptyState">Belum ada menu.</div>';
                            countEl.textContent = list.querySelectorAll('.menu-card').length + ' menu';
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
                                <span class="tag">${escapeHtml(menu.category_name)}</span>
                                <span class="tag tag-success">${formatMoney(menu.selling_price)}</span>
                                <span class="tag tag-muted">Modal ${formatMoney(menu.cost_price)}</span>
                            </div>
                            <div class="actions">
                                <button type="button" class="btn-open-edit" 
                                    data-id="${menu.id}" data-code="${escapeHtml(menu.code)}"
                                    data-name="${escapeHtml(menu.name)}" data-category-id="${menu.menu_category_id ?? ''}"
                                    data-selling-price="${menu.selling_price}" data-cost-price="${menu.cost_price}"
                                    data-image-url="${escapeHtml(menu.image_url)}"
                                >Edit</button>
                                <button type="button" class="btn-delete-menu" data-id="${menu.id}">Hapus</button>
                            </div>
                        </div>
                    `;

                    if (isNew) {
                        const empty = document.getElementById('emptyState');
                        if (empty) empty.remove();
                        card = document.createElement('div');
                        card.className = 'menu-card';
                        card.setAttribute('data-menu-id', menu.id);
                        list.prepend(card);
                        updatePillCount(menu.menu_category_id, 1);
                    } else if (oldCatId !== (menu.menu_category_id ? String(menu.menu_category_id) : 'none')) {
                        updatePillCount(oldCatId, -1);
                        updatePillCount(menu.menu_category_id, 1);
                    }

                    card.setAttribute('data-category-id-val', menu.menu_category_id || 'none');
                    card.innerHTML = html;
                    bindActions(card);
                    
                    countEl.textContent = list.querySelectorAll('.menu-card').length + ' menu';
                    window.showToast?.(data.message, 'success');
                    closeDrawer();
                } catch (err) {
                    const errEl = document.getElementById('drawerError');
                    errEl.textContent = err.message;
                    errEl.style.display = 'block';
                } finally {
                    btnSubmit.disabled = false;
                    btnSubmit.textContent = drawerMethod.disabled ? 'Simpan Menu' : 'Simpan Perubahan';
                }
            });

            bindActions(list);
        })();
    </script>
@endsection

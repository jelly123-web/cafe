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
        .primary-link { display:inline-flex; align-items:center; background:var(--highlight); color:#fff; text-decoration:none; padding:0.6rem 1.2rem; border-radius:12px; font-weight:600; border:none; cursor:pointer; }
        .primary-link:hover { background:#c68b59; }
        .danger-link { display:inline-flex; align-items:center; justify-content:center; background:transparent; color:var(--loss); border:1px solid #ffcdd2; padding:0.6rem 1.1rem; border-radius:12px; font-weight:700; cursor:pointer; }
        .danger-link:hover { background:#fff0f0; border-color:var(--loss); }
        .panel { background:var(--bg-card); padding:2rem; border-radius:20px; box-shadow:0 4px 15px var(--shadow); }
        .panel-head { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:1.5rem; padding-bottom:0.75rem; border-bottom:2px solid var(--accent); }
        .panel-head h2 { font-family:'Playfair Display', Georgia, serif; color:var(--primary); font-size:1.9rem; margin:0; }
        .panel-head span { font-size:0.9rem; color:var(--text-muted); font-weight:500; }
        .menu-card-list { display:flex; flex-direction:column; gap:1rem; }
        .menu-card { display:flex; gap:1.25rem; padding:1rem; background:#fffaf5; border:1px solid var(--accent); border-radius:16px; }
        .menu-thumb { width:90px; height:90px; border-radius:12px; object-fit:cover; background-color:var(--bg-main); flex-shrink:0; }
        .menu-meta { flex:1; display:flex; flex-direction:column; justify-content:center; }
        .menu-meta h3 { font-family:'Playfair Display', Georgia, serif; color:var(--primary); font-size:1.15rem; margin-bottom:0.1rem; }
        .menu-meta p { color:var(--text-muted); font-size:0.85rem; margin-bottom:0.5rem; }
        .menu-pricing { display:flex; flex-wrap:wrap; gap:0.5rem; margin-bottom:0.3rem; }
        .tag { display:inline-block; padding:0.25rem 0.75rem; border-radius:50px; font-size:0.75rem; font-weight:600; background-color:#efebe9; color:var(--primary); }
        .tag-success { background-color:#e8f5e9; color:#558b2f; }
        .tag-muted { background-color:#f5f5f5; color:#9e9e9e; }
        .actions { display:flex; gap:0.75rem; align-items:center; }
        .actions button { border:none; background:transparent; cursor:pointer; padding:0; font-weight:600; font-size:0.95rem; }
        .btn-open-edit { color:#c68b59; }
        .btn-open-edit:hover { color:var(--primary); text-decoration:underline; }
        .btn-delete-menu { color:var(--loss); }
        .btn-delete-menu:hover { color:#b71c1c; text-decoration:underline; }
        .alert { background:var(--bg-card); border-radius:12px; padding:1rem 1.1rem; box-shadow:0 4px 15px var(--shadow); margin-bottom:1rem; color:var(--text-muted); font-style:italic; }
        .menu-pagination { margin-top:1.25rem; }

        .drawer-backdrop { position:fixed; inset:0; background:rgba(56, 37, 30, 0.32); backdrop-filter:blur(2px); z-index:1200; opacity:0; visibility:hidden; transition:0.2s ease; }
        .drawer-backdrop.open { opacity:1; visibility:visible; }
        .menu-drawer { position:fixed; top:0; right:0; width:min(560px, 95vw); height:100vh; background:linear-gradient(180deg, #fffdfb 0%, #fff 100%); z-index:1201; transform:translateX(102%); transition:transform 0.2s ease; box-shadow:-10px 0 34px rgba(79, 53, 43, 0.18); display:grid; grid-template-rows:auto minmax(0, 1fr); }
        .menu-drawer.open { transform:translateX(0); }
        .drawer-head { padding:1.15rem 1.25rem; border-bottom:1px solid var(--accent); display:flex; justify-content:space-between; align-items:center; background:#fff; }
        .drawer-head h3 { margin:0; font-family:'Playfair Display', Georgia, serif; color:var(--primary); font-size:1.35rem; }
        .drawer-close { border:1px solid var(--accent); background:#fff; color:var(--primary); border-radius:12px; padding:0.45rem 0.85rem; cursor:pointer; font-weight:600; }
        .drawer-body { padding:1rem 1.25rem 1.25rem; overflow-y:auto; min-height:0; }
        .menu-form { display:grid; grid-template-rows:minmax(0, 1fr) auto; min-height:0; overflow:hidden; }
        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
        .drawer-field { display:flex; flex-direction:column; gap:0.4rem; }
        .drawer-field label { font-size:0.85rem; font-weight:600; color:var(--text-muted); }
        .drawer-field input[type="text"], .drawer-field input[type="number"], .drawer-field select, .drawer-field input[type="file"] { width:100%; padding:0.65rem 1rem; border:1px solid var(--accent); border-radius:12px; background:#fff; color:var(--text-main); }
        .drawer-field input[type="file"][data-cropper-input] { display:none; }
        .drawer-field select { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23795548' d='M6 8L1 3h10z'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 1rem center; padding-right:2.5rem; }
        .drawer-field input:focus, .drawer-field select:focus { border-color:var(--highlight); box-shadow:0 0 0 3px rgba(212, 163, 115, 0.15); outline:none; }
        .drawer-field.full { grid-column:1 / -1; }
        .photo-picker { display:flex; align-items:center; gap:0.8rem; flex-wrap:wrap; }
        .photo-picker-btn { border:1px solid var(--accent); border-radius:12px; padding:0.65rem 1rem; background:#fffaf5; color:var(--primary); cursor:pointer; font-weight:700; }
        .photo-name { color:var(--text-muted); font-size:0.85rem; font-style:italic; }
        .selected-photo { position:relative; width:150px; margin-top:0.45rem; }
        .selected-photo img { width:150px; height:150px; object-fit:cover; border-radius:16px; border:1px dashed var(--accent); background:#fffaf5; cursor:pointer; }
        .photo-clear { position:absolute; top:-8px; right:-8px; width:24px; height:24px; border-radius:999px; border:1px solid #FFCDD2; background:#fff; color:var(--loss); cursor:pointer; font-weight:800; line-height:1; }
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
        .cropper-close, .cropper-done { border:1px solid var(--accent); background:#fff; color:var(--primary); border-radius:12px; padding:.55rem 1rem; cursor:pointer; font-weight:700; }
        .cropper-done { background:var(--highlight); color:#fff; border-color:var(--highlight); }
        .form-error { font-size:0.82rem; color:var(--loss); }
        .drawer-foot { padding:1rem 1.25rem; border-top:1px solid var(--accent); display:flex; justify-content:flex-end; gap:0.7rem; background:#fff; }
        .btn-drawer-cancel { border:1px solid var(--accent); background:#fff; color:var(--primary); border-radius:12px; padding:0.6rem 1rem; cursor:pointer; font-weight:600; }
        .btn-drawer-cancel:hover { background:#fffaf5; }

        @media (max-width: 768px) {
            .content-toolbar { flex-direction: column; align-items: stretch; gap: 1rem; margin-bottom: 1.25rem; }
            
            /* Search box more compact */
            .search-box { position: relative; width: 100%; }
            .search-box input { padding-right: 4.5rem; width: 100%; min-width: auto; height: 50px; font-size: 0.95rem; border-radius: 16px; border: 1.5px solid var(--accent); }
            .search-box button { position: absolute; right: 5px; top: 5px; bottom: 5px; padding: 0 1.25rem; border-radius: 12px; font-size: 0.85rem; background: var(--highlight); }
            
            /* Buttons side by side */
            .toolbar-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; width: 100%; }
            .toolbar-actions form { width: 100%; margin: 0; }
            .danger-link, .primary-link { width: 100%; padding: 0.85rem 0.5rem; font-size: 0.85rem; justify-content: center; height: 48px; border-radius: 14px; }
            
            .panel { padding: 1.25rem; border-radius: 24px; }
            .panel-head { margin-bottom: 1.25rem; padding-bottom: 1rem; }
            .panel-head h2 { font-size: 1.6rem; }
            
            /* Menu card better layout */
            .menu-card { 
                display: grid;
                grid-template-columns: 100px 1fr; 
                gap: 1.15rem; 
                padding: 1.15rem; 
                align-items: start;
                border-radius: 20px;
                background: #ffffff;
                border: 1px solid #f0f0f0;
                box-shadow: 0 4px 12px var(--shadow);
                position: relative;
            }
            .menu-card::before { content: ''; position: absolute; top: 0; left: 0; width: 5px; height: 100%; background: var(--highlight); opacity: 0.6; }
            
            .menu-thumb { width: 100px; height: 100px; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
            .menu-meta { text-align: left; }
            .menu-meta h3 { font-size: 1.15rem; margin-bottom: 0.25rem; color: var(--primary); }
            .menu-meta p { font-size: 0.8rem; margin-bottom: 0.5rem; font-family: 'JetBrains Mono', monospace; background: #fdfaf8; display: inline-block; padding: 0.2rem 0.5rem; border-radius: 6px; }
            
            .menu-pricing { gap: 0.5rem; margin: 0.5rem 0; }
            .tag { padding: 0.3rem 0.75rem; font-size: 0.75rem; border-radius: 8px; }
            
            .actions { margin-top: 0.75rem; gap: 1.25rem; padding-top: 0.75rem; border-top: 1px solid #f5f5f5; width: 100%; }
            .actions button { font-size: 0.95rem; padding: 0.25rem 0; }
            .btn-open-edit { color: var(--highlight); }
            .btn-delete-menu { color: #e57373; }
            
            .form-grid { grid-template-columns: 1fr; gap: 1rem; }
            .drawer-head { padding: 1.25rem; }
            .drawer-head h3 { font-size: 1.25rem; }
            .drawer-body { padding: 1.25rem; }
            .drawer-foot { padding: 1.25rem; }
        }

        @media (max-width: 480px) {
            .menu-card { grid-template-columns: 85px 1fr; gap: 1rem; padding: 1rem; }
            .menu-thumb { width: 85px; height: 85px; }
            .menu-meta h3 { font-size: 1.05rem; }
            .toolbar-actions { grid-template-columns: 1fr; }
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
            <button type="submit">Cari</button>
        </form>

        <div class="toolbar-actions">
            <form method="POST" action="{{ route('superadmin.menus.destroy-all') }}" onsubmit="return confirm('Hapus semua menu? Tindakan ini tidak bisa dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="danger-link">Hapus Semua Menu</button>
            </form>
            <button class="primary-link" type="button" id="openCreateDrawer">+ Tambah Menu</button>
        </div>
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
                        ? (Storage::disk('public')->exists($menu->image_path) ? Storage::url($menu->image_path) : asset('images/menu-placeholder.svg'))
                        : asset('images/menu-placeholder.svg');
                @endphp
                <div class="menu-card" data-menu-id="{{ $menu->id }}">
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
                            <button
                                type="button"
                                class="btn-open-edit"
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

    <div id="drawerBackdrop" class="drawer-backdrop"></div>
    <aside id="menuDrawer" class="menu-drawer" aria-hidden="true">
        <div class="drawer-head">
            <h3 id="drawerTitle">Tambah Menu</h3>
            <button type="button" class="drawer-close" id="closeDrawerBtn">Tutup</button>
        </div>
        <form id="drawerForm" class="menu-form" method="POST" action="{{ route('superadmin.menus.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="methodSpoof" value="">
            <div class="drawer-body">
                <div class="form-grid">
                    <div class="drawer-field">
                        <label for="drawer_code">Kode Menu</label>
                        <input id="drawer_code" type="text" name="code" required>
                    </div>
                    <div class="drawer-field">
                        <label for="drawer_menu_category_id">Kategori</label>
                        <select id="drawer_menu_category_id" name="menu_category_id">
                            <option value="">Tanpa kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="drawer-field">
                        <label for="drawer_name">Nama Menu</label>
                        <input id="drawer_name" type="text" name="name" required>
                    </div>
                    <div class="drawer-field">
                        <label for="drawer_selling_price">Harga Jual</label>
                        <input id="drawer_selling_price" type="number" step="0.01" min="0" name="selling_price" required>
                    </div>
                    <div class="drawer-field">
                        <label for="drawer_cost_price">Harga Modal</label>
                        <input id="drawer_cost_price" type="number" step="0.01" min="0" name="cost_price" required>
                    </div>
                    <div class="drawer-field full" data-cropper data-cropper-size="600">
                        <label for="drawer_image">Foto Menu</label>
                        <div class="photo-picker">
                            <input id="drawer_image" type="file" name="image" accept="image/*" data-cropper-input>
                            <input type="hidden" name="cropped_image" id="drawer_cropped_image" data-cropper-output>
                            <label for="drawer_image" class="photo-picker-btn">Pilih Foto</label>
                            <span class="photo-name" id="drawerImageName" data-cropper-filename>Belum ada file dipilih</span>
                        </div>
                        <div class="selected-photo" data-cropper-preview-wrap hidden>
                            <button type="button" class="photo-clear" data-cropper-clear aria-label="Batal pilih foto">x</button>
                            <img src="" alt="Preview foto menu" data-cropper-preview title="Klik untuk crop ulang">
                        </div>
                        <div class="cropper-modal" data-cropper-panel hidden>
                            <div class="cropper-dialog">
                                <div class="cropper-head">
                                    <strong>Atur Crop Foto Menu</strong>
                                    <button type="button" class="cropper-close" data-cropper-close>Tutup</button>
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
                        <small class="form-error" id="drawerError"></small>
                    </div>
                </div>
            </div>
            <div class="drawer-foot">
                <button type="button" class="btn-drawer-cancel" id="cancelDrawerBtn">Batal</button>
                <button type="submit" class="primary-link" id="submitDrawerBtn">Buat Menu</button>
            </div>
        </form>
    </aside>

    <script src="{{ asset('js/cafe-image-cropper.js') }}?v=4"></script>
    <script>
        (function () {
            const searchForm = document.getElementById('menusSearchForm');
            const searchInput = document.getElementById('menusSearchInput');
            const drawer = document.getElementById('menuDrawer');
            const backdrop = document.getElementById('drawerBackdrop');
            const openCreateBtn = document.getElementById('openCreateDrawer');
            const closeBtn = document.getElementById('closeDrawerBtn');
            const cancelBtn = document.getElementById('cancelDrawerBtn');
            const title = document.getElementById('drawerTitle');
            const form = document.getElementById('drawerForm');
            const methodSpoof = document.getElementById('methodSpoof');
            const submitBtn = document.getElementById('submitDrawerBtn');
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const list = document.getElementById('menusList');
            const countEl = document.getElementById('menuCount');
            const imageInput = document.getElementById('drawer_image');
            const imageName = document.getElementById('drawerImageName');
            const croppedImageInput = document.getElementById('drawer_cropped_image');
            const cropperPanel = imageInput?.closest('[data-cropper]')?.querySelector('[data-cropper-panel]');
            const cropperPreviewWrap = imageInput?.closest('[data-cropper]')?.querySelector('[data-cropper-preview-wrap]');
            const cropperPreview = imageInput?.closest('[data-cropper]')?.querySelector('[data-cropper-preview]');
            const drawerError = document.getElementById('drawerError');
            const placeholder = @json(asset('images/menu-placeholder.svg'));

            if (searchForm && searchInput) {
                let searchTimer = null;
                searchInput.addEventListener('input', function () {
                    clearTimeout(searchTimer);
                    if (searchInput.value.trim() !== '') return;
                    searchTimer = setTimeout(function () {
                        window.location.href = searchForm.action;
                    }, 220);
                });
            }

            const openDrawer = () => {
                drawer.classList.add('open');
                backdrop.classList.add('open');
                drawer.setAttribute('aria-hidden', 'false');
            };
            const closeDrawer = () => {
                drawer.classList.remove('open');
                backdrop.classList.remove('open');
                drawer.setAttribute('aria-hidden', 'true');
            };
            const resetError = () => { drawerError.textContent = ''; };

            const formatRupiah = (num) => 'Rp ' + Number(num || 0).toLocaleString('id-ID');
            const esc = (str) => String(str ?? '').replace(/[&<>"']/g, (m) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));

            const updateCount = () => {
                const total = list.querySelectorAll('[data-menu-id]').length;
                countEl.textContent = total + ' menu';
            };

            const setCreateMode = () => {
                title.textContent = 'Tambah Menu';
                form.action = "{{ route('superadmin.menus.store') }}";
                methodSpoof.value = '';
                form.reset();
                imageName.textContent = 'Belum ada file dipilih';
                if (croppedImageInput) croppedImageInput.value = '';
                if (cropperPanel) cropperPanel.hidden = true;
                if (cropperPreview) cropperPreview.removeAttribute('src');
                if (cropperPreviewWrap) cropperPreviewWrap.hidden = true;
                submitBtn.textContent = 'Buat Menu';
                resetError();
            };

            const setEditMode = (btn) => {
                const id = btn.getAttribute('data-id');
                title.textContent = 'Edit Menu';
                form.action = "{{ url('superadmin/menus') }}/" + id;
                methodSpoof.value = 'PUT';
                document.getElementById('drawer_code').value = btn.getAttribute('data-code') || '';
                document.getElementById('drawer_name').value = btn.getAttribute('data-name') || '';
                document.getElementById('drawer_menu_category_id').value = btn.getAttribute('data-category-id') || '';
                document.getElementById('drawer_selling_price').value = btn.getAttribute('data-selling-price') || '';
                document.getElementById('drawer_cost_price').value = btn.getAttribute('data-cost-price') || '';
                const existingImageUrl = btn.getAttribute('data-image-url') || '';
                imageName.textContent = 'Kosongkan jika tidak ganti foto';
                if (croppedImageInput) croppedImageInput.value = '';
                if (cropperPanel) cropperPanel.hidden = true;
                if (cropperPreview && cropperPreviewWrap) {
                    if (existingImageUrl && existingImageUrl !== placeholder) {
                        cropperPreview.src = existingImageUrl;
                        cropperPreviewWrap.hidden = false;
                    } else {
                        cropperPreview.removeAttribute('src');
                        cropperPreviewWrap.hidden = true;
                    }
                }
                imageInput.value = '';
                submitBtn.textContent = 'Simpan Perubahan';
                resetError();
            };

            const rowHtml = (menu) => `
                <img class="menu-thumb" src="${esc(menu.image_url || placeholder)}" alt="${esc(menu.name)}">
                <div class="menu-meta">
                    <h3>${esc(menu.name)}</h3>
                    <p>${esc(menu.code)}</p>
                    <div class="menu-pricing">
                        <span class="tag">${esc(menu.category_name || 'Tanpa kategori')}</span>
                        <span class="tag tag-success">${esc(formatRupiah(menu.selling_price))}</span>
                        <span class="tag tag-muted">Modal ${esc(formatRupiah(menu.cost_price))}</span>
                    </div>
                    <div class="actions">
                        <button type="button" class="btn-open-edit"
                            data-id="${menu.id}"
                            data-code="${esc(menu.code)}"
                            data-name="${esc(menu.name)}"
                            data-category-id="${menu.menu_category_id ?? ''}"
                            data-selling-price="${menu.selling_price}"
                            data-cost-price="${menu.cost_price}"
                            data-image-url="${esc(menu.image_url || placeholder)}"
                        >Edit</button>
                        <button type="button" class="btn-delete-menu" data-id="${menu.id}">Hapus</button>
                    </div>
                </div>
            `;

            const bindCardActions = (root) => {
                root.querySelectorAll('.btn-open-edit').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        setEditMode(btn);
                        openDrawer();
                    });
                });
                root.querySelectorAll('.btn-delete-menu').forEach((btn) => {
                    btn.addEventListener('click', async () => {
                        const id = btn.getAttribute('data-id');
                        if (!id || !confirm('Hapus menu ini?')) return;
                        const res = await fetch("{{ url('superadmin/menus') }}/" + id, {
                            method: 'POST',
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
                            body: JSON.stringify({ _method: 'DELETE' })
                        });
                        const payload = await res.json();
                        if (!res.ok) return window.showToast?.(payload.message || 'Gagal hapus menu.', 'error');
                        list.querySelector('[data-menu-id="' + id + '"]')?.remove();
                        if (!list.querySelector('[data-menu-id]')) {
                            list.innerHTML = '<div class="alert" id="emptyState">Belum ada menu.</div>';
                        }
                        updateCount();
                        window.showToast?.(payload.message || 'Menu berhasil dihapus.', 'success');
                    });
                });
            };
            bindCardActions(document);

            imageInput?.addEventListener('change', (e) => {
                const file = e.target.files?.[0];
                imageName.textContent = file ? file.name : 'Belum ada file dipilih';
            });

            openCreateBtn?.addEventListener('click', () => { setCreateMode(); openDrawer(); });
            [closeBtn, cancelBtn, backdrop].forEach((el) => el?.addEventListener('click', closeDrawer));

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                resetError();
                submitBtn.disabled = true;
                const formData = new FormData(form);
                if (methodSpoof.value === 'PUT') formData.set('_method', 'PUT');
                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                        body: formData
                    });
                    const text = await res.text();
                    let payload = {};
                    try { payload = text ? JSON.parse(text) : {}; } catch (_) {}
                    if (!res.ok) {
                        const msg = payload.message || (payload.errors ? Object.values(payload.errors)[0][0] : 'Gagal menyimpan menu.');
                        throw new Error(msg);
                    }
                    const menu = payload.menu;
                    let card = list.querySelector('[data-menu-id="' + menu.id + '"]');
                    if (!card) {
                        list.querySelector('#emptyState')?.remove();
                        card = document.createElement('div');
                        card.className = 'menu-card';
                        card.setAttribute('data-menu-id', menu.id);
                        list.prepend(card);
                    }
                    card.innerHTML = rowHtml(menu);
                    bindCardActions(card);
                    updateCount();
                    closeDrawer();
                    window.showToast?.(payload.message || 'Menu tersimpan.', 'success');
                } catch (err) {
                    drawerError.textContent = err.message || 'Terjadi kesalahan.';
                    window.showToast?.(err.message || 'Terjadi kesalahan.', 'error');
                } finally {
                    submitBtn.disabled = false;
                }
            });
        })();
    </script>
@endsection

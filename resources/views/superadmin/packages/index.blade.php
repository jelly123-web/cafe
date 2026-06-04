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
            --line: rgba(121, 85, 72, 0.08);
        }

        .content-toolbar {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .search-box {
            display: flex;
            gap: 0.5rem;
        }

        .search-box input {
            min-width: 260px;
            border-radius: 12px;
            border: 1px solid var(--accent);
            background: var(--bg-card);
            color: var(--text-main);
            padding: 0.6rem 1rem;
        }

        .search-box button,
        .primary-link,
        .secondary-link,
        .danger-link {
            border: 0;
            border-radius: 12px;
            padding: 0.6rem 1.2rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .search-box button { background: var(--secondary); color: #fff; }
        .search-box button:hover { background: var(--primary); }

        /* Lightened Button Colors */
        .primary-link { background: #e2b68c; color: #fff; }
        .primary-link:hover { background: #d4a373; transform: translateY(-1px); }

        .secondary-link {
            background: rgba(255, 255, 255, 0.06);
            color: var(--text-main);
            border: 1px solid var(--accent);
        }

        .danger-link {
            background: transparent;
            color: #e57373;
            border: 1px solid #ffcdd2;
            padding: 0.6rem 1.1rem;
            font-weight: 700;
        }

        .danger-link:hover {
            background: #fff0f0;
            border-color: #e57373;
        }

        .panel {
            background: var(--bg-card);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 4px 15px var(--shadow);
        }

        .panel-head {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--accent);
        }

        .panel-head h2 {
            margin: 0;
            color: var(--primary);
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 1.9rem;
        }

        .panel-head span {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .package-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .package-card {
            display: flex;
            gap: 1.25rem;
            padding: 1.25rem;
            background: #fffaf5;
            border: 1px solid var(--accent);
            border-radius: 16px;
            align-items: center;
        }

        .package-thumb {
            width: 100px;
            height: 100px;
            border-radius: 12px;
            object-fit: cover;
            background-color: var(--bg-main);
            flex-shrink: 0;
            border: 1px solid var(--accent);
        }

        .package-meta {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .package-meta h3 {
            font-family: 'Playfair Display', Georgia, serif;
            color: var(--primary);
            font-size: 1.15rem;
            margin: 0 0 0.1rem;
        }

        .package-meta p {
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }

        .main-panel {
            padding: 1.5rem 1.5rem;
            background-color: #fbf8f4;
            color: var(--text-main);
            line-height: 1.6;
            background-image: radial-gradient(var(--accent) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        @media (min-width: 1024px) {
            .main-panel {
                padding: 2rem 2.5rem;
            }
        }

        .package-pricing {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 0.3rem;
        }

        .tag {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            background-color: #efebe9;
            color: var(--primary);
        }

        .tag-success {
            background-color: #e8f5e9;
            color: #558b2f;
        }

        .tag-muted {
            background-color: #f5f5f5;
            color: #9e9e9e;
        }

        .package-menu-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin: 0.4rem 0;
        }

        .package-menu-tag {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 6px;
            font-size: 0.7rem;
            background: rgba(121, 85, 72, 0.06);
            color: var(--primary);
            font-weight: 500;
            border: 1px solid rgba(121, 85, 72, 0.1);
        }

        .qty-control { display: flex; align-items: center; gap: 0.45rem; background: #fdfaf8; padding: 0.25rem; border-radius: 10px; border: 1px solid var(--accent); }
        .qty-btn { width: 28px; height: 28px; border-radius: 6px; border: none; background: #fff; color: var(--primary); cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .qty-btn:hover { background: var(--highlight); color: #fff; }
        .qty-input { width: 38px; text-align: center; border: none; background: transparent; font-weight: 700; color: var(--primary); -moz-appearance: textfield; }
        .qty-input::-webkit-outer-spin-button, .qty-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

        .drawer-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(56, 37, 30, 0.32);
            backdrop-filter: blur(2px);
            z-index: 1200;
            opacity: 0;
            visibility: hidden;
            transition: 0.2s ease;
        }
        .drawer-backdrop.open { opacity: 1; visibility: visible; }
        .package-drawer {
            position: fixed;
            top: 0;
            right: 0;
            width: min(620px, 95vw);
            height: 100vh;
            background: #fff;
            z-index: 1201;
            transform: translateX(102%);
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: -10px 0 34px rgba(79, 53, 43, 0.12);
            display: flex;
            flex-direction: column;
        }
        .package-drawer.open { transform: translateX(0); }
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
            padding: 1.75rem 1.9rem 1.5rem; 
            overflow-y: auto; 
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 1.15rem;
            scrollbar-width: thin;
            scrollbar-color: var(--accent) transparent;
        }
        .drawer-body::-webkit-scrollbar { width: 6px; }
        .drawer-body::-webkit-scrollbar-thumb { background: var(--accent); border-radius: 10px; }

        .drawer-foot { 
            padding: 1.15rem 1.9rem 1.35rem; 
            border-top: 1px solid var(--accent); 
            display: flex; 
            gap: 0.9rem; 
            justify-content: flex-end; 
            background: #fdfaf8;
            flex-shrink: 0;
        }
        .btn-drawer-cancel { border: 1.5px solid var(--accent); background: #fff; color: var(--primary); border-radius: 12px; padding: 0.75rem 1.5rem; cursor: pointer; font-weight: 700; transition: all 0.2s; min-width: 92px; }
        .btn-drawer-cancel:hover { border-color: #e57373; color: #e57373; }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.35rem 1.2rem; margin-bottom: 1.5rem; }
        .drawer-field { display: flex; flex-direction: column; gap: 0.55rem; }
        .drawer-field.full { grid-column: 1 / -1; }
        .drawer-field label { font-size: 0.9rem; font-weight: 700; color: var(--text-main); }
        .drawer-field input[type="text"], .drawer-field input[type="number"] {
            width: 100%;
            padding: 0.75rem 1.15rem;
            border-radius: 14px;
            border: 1.5px solid var(--accent);
            background: #fff;
            color: var(--text-main);
            font-size: 1rem;
            transition: all 0.2s;
        }
        .drawer-field input:focus { border-color: var(--highlight); box-shadow: 0 0 0 4px rgba(212, 163, 115, 0.12); outline: none; }
        .drawer-field select { min-height: 48px; }
        .drawer-field input[type="text"], .drawer-field input[type="number"] { min-height: 48px; }
        .package-form > div { display: grid; gap: 1rem; }
        
        .photo-picker { display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 0.5rem; }
        .photo-picker-btn { background: var(--primary); color: #fff; padding: 0.65rem 1.25rem; border-radius: 10px; cursor: pointer; font-weight: 700; font-size: 0.9rem; transition: all 0.2s; }
        .photo-picker-btn:hover { background: #5d3a31; transform: translateY(-1px); }
        .photo-name { font-size: 0.85rem; color: var(--text-muted); font-style: italic; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        
        .selected-photo { position: relative; width: 140px; margin-top: 0.75rem; border-radius: 18px; padding: 5px; background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border: 1px solid var(--accent); cursor: pointer; transition: all 0.2s; }
        .selected-photo:hover { border-color: var(--highlight); transform: scale(1.02); }
        .selected-photo img { width: 100%; height: 130px; object-fit: cover; border-radius: 14px; }
        .photo-clear { position: absolute; top: -10px; right: -10px; width: 28px; height: 28px; border-radius: 50%; border: none; background: #e57373; color: #fff; cursor: pointer; font-weight: 800; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(229, 115, 115, 0.4); z-index: 10; }

        .package-menu-selector { background: #fffaf5; border: 1.5px solid var(--accent); border-radius: 24px; padding: 1.5rem; margin-top: 0.5rem; }
        .package-menu-selector-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; gap: 1rem; }
        .package-menu-selector-header h3 { font-size: 1.4rem !important; }
        
        .menu-selector-box { border: none; padding: 0; max-height: 400px; overflow-y: auto; display: grid; grid-template-columns: 1fr; gap: 0.85rem; background: transparent; }
        .menu-option-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.15rem;
            border-radius: 18px;
            cursor: pointer;
            transition: all 0.2s;
            border: 1.5px solid transparent;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }
        .menu-option-item:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(121, 85, 72, 0.08); border-color: var(--accent); }
        .menu-option-item.selected { border-color: var(--highlight); background: #fffdfb; box-shadow: 0 8px 20px rgba(212, 163, 115, 0.12); }
        .menu-option-item input[type="checkbox"] { width: 22px; height: 22px; cursor: pointer; accent-color: var(--highlight); border-radius: 6px; }
        .menu-option-item strong { font-size: 1rem; color: var(--primary); display: block; margin-bottom: 2px; }
        .menu-option-item small { color: var(--text-muted); font-size: 0.85rem; font-weight: 500; }
        
        .qty-control { background: #fff; border: 1.5px solid var(--accent); padding: 0.35rem; border-radius: 12px; gap: 0.6rem; box-shadow: 0 2px 6px rgba(0,0,0,0.03); }
        .qty-btn { width: 32px; height: 32px; border-radius: 8px; font-size: 1.2rem; }
        .qty-input { width: 42px; font-size: 1.05rem; }

        .form-error { background: #fff5f5; color: #e57373; padding: 0.85rem 1.15rem; border-radius: 12px; border-left: 4px solid #e57373; font-weight: 600; }

        /* Cropper Modal */
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

        .package-actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            margin-top: 0.25rem;
        }

        .package-actions a,
        .package-actions button {
            border: none;
            background: transparent;
            cursor: pointer;
            padding: 0;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
        }

        .btn-open-edit {
            color: #c68b59;
        }

        .btn-open-edit:hover {
            color: var(--primary);
            text-decoration: underline;
        }

        .btn-delete-package {
            color: #e57373;
        }

        .btn-delete-package:hover {
            color: #b71c1c;
            text-decoration: underline;
        }

        .empty-state {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 1rem 1.1rem;
            box-shadow: 0 4px 15px var(--shadow);
            color: var(--text-muted);
            font-style: italic;
        }

        .package-drawer .drawer-body {
            padding: 1.75rem 1.95rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.15rem;
        }

        .package-drawer .form-grid {
            gap: 1.4rem 1.2rem;
        }

        .package-drawer .drawer-field {
            gap: 0.6rem;
        }

        .package-drawer .drawer-foot {
            padding: 1.2rem 1.95rem 1.4rem;
            gap: 0.95rem;
            background: #fff;
        }

        .package-drawer .btn-drawer-cancel,
        .package-drawer .primary-link {
            min-height: 46px;
            min-width: 108px;
            padding-left: 1.25rem;
            padding-right: 1.25rem;
        }

        .package-drawer .drawer-close {
            min-height: 42px;
            padding: 0.55rem 1rem;
            border-radius: 14px;
            background: linear-gradient(180deg, #fff 0%, #fffaf6 100%);
            box-shadow: 0 2px 8px rgba(121, 85, 72, 0.08);
        }

        .package-drawer .photo-picker-btn {
            min-height: 46px;
            padding: 0.75rem 1.15rem;
            border-radius: 14px;
            background: linear-gradient(180deg, #fff 0%, #fff7f0 100%);
            border: 1px solid var(--accent);
            box-shadow: 0 2px 8px rgba(121, 85, 72, 0.06);
        }

        .package-drawer .qty-control {
            padding: 0.45rem;
            border-radius: 14px;
        }

        .package-drawer .qty-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(180deg, #fff 0%, #fff8f3 100%);
            border: 1px solid rgba(121, 85, 72, 0.14);
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
            transition: transform .15s ease, background-color .2s ease, box-shadow .2s ease;
        }

        .package-drawer .qty-btn:hover {
            background: var(--highlight);
            color: #fff;
            box-shadow: 0 4px 10px rgba(212, 163, 115, 0.22);
            transform: translateY(-1px);
        }

        .package-drawer .btn-open-edit,
        .package-drawer .btn-delete-package {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.42rem 0.15rem;
        }

        .package-drawer .package-menu-selector {
            border-radius: 22px;
            padding: 1.35rem;
            box-shadow: inset 0 0 0 1px rgba(121, 85, 72, 0.05);
        }

        .package-drawer .package-menu-selector-header {
            margin-bottom: 1.1rem;
        }

        .package-drawer .menu-option-item {
            border-radius: 20px;
            padding: 1rem 1.05rem;
        }

        .package-drawer .menu-option-item input[type="checkbox"] {
            width: 20px;
            height: 20px;
        }

        @media (max-width: 900px) {
            .package-card {
                grid-template-columns: 1fr;
            }

            .package-thumb {
                width: 100%;
                max-width: 220px;
                height: 220px;
            }
        }
    </style>
@endpush

@section('title', 'Paket Makanan')
@section('kicker', 'Superadmin')
@section('page_title', 'Paket Makanan')
@section('page_description', 'Kelola paket makanan terpisah dari menu biasa.')

@section('content')
    <div class="content-toolbar">
        <form method="GET" action="{{ route('superadmin.packages.index') }}" class="search-box">
            <input type="text" name="search" placeholder="Cari paket atau kode" value="{{ request('search') }}">
            <button type="submit">Cari</button>
        </form>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <form method="POST" action="{{ route('superadmin.packages.destroy-all') }}" onsubmit="return confirm('Hapus semua paket? Tindakan ini tidak bisa dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="danger-link">Hapus Semua Paket</button>
            </form>
            <button type="button" class="primary-link" id="openCreateDrawer">+ Tambah Paket</button>
        </div>
    </div>

    <div class="panel">
        <div class="panel-head">
            <h2>Daftar Paket</h2>
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
                        </div>
                        <div class="package-menu-list">
                            @forelse ($package->menus as $menu)
                                <span class="package-menu-tag">({{ $menu->pivot->quantity }}x) {{ $menu->name }}</span>
                            @empty
                                <span class="package-menu-tag">Belum ada menu</span>
                            @endforelse
                        </div>
                        <div class="package-actions">
                            <button
                                type="button"
                                class="btn-open-edit"
                                data-id="{{ $package->id }}"
                                data-name="{{ $package->name }}"
                                data-selling-price="{{ (float) $package->selling_price }}"
                                data-cost-price="{{ (float) $package->cost_price }}"
                                data-free-item="{{ $package->free_item }}"
                                data-category-id="{{ $package->menu_category_id }}"
                                data-image-url="{{ $packageImage }}"
                                data-menu-ids='@json($package->menus->pluck("id")->values())'
                                data-menu-quantities='@json($package->menus->pluck("pivot.quantity", "id"))'
                            >Edit</button>
                            <button type="button" class="btn-delete-package" data-id="{{ $package->id }}">Hapus</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state" id="emptyState">Belum ada paket makanan.</div>
            @endforelse
        </div>

        <div style="margin-top:16px;">
            {{ $packages->links('components.pagination') }}
        </div>
    </div>

    <div class="drawer-backdrop" id="packageDrawerBackdrop"></div>
    <aside class="package-drawer" id="packageDrawer" aria-hidden="true">
        <div class="drawer-head">
            <div>
                <h3 id="drawerTitle">Tambah Paket</h3>
                <p style="margin:0.35rem 0 0;color:var(--text-muted);font-size:0.9rem;">Kelola paket tanpa pindah halaman.</p>
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
                            <label for="drawerImage" class="photo-picker-btn">Pilih Foto Paket</label>
                            <span class="photo-name" id="drawerImageName" data-cropper-filename>Belum ada file dipilih</span>
                        </div>
                        <input type="file" name="image" id="drawerImage" accept="image/*" data-cropper-input hidden>
                        <input type="hidden" name="cropped_image" id="drawer_cropped_image" data-cropper-output>
                        
                        <div class="selected-photo" id="drawerImageWrap" data-cropper-preview-wrap hidden>
                            <img id="drawerImagePreview" src="{{ asset('images/menu-placeholder.svg') }}" alt="Preview paket" data-cropper-preview title="Klik untuk crop ulang">
                            <button type="button" class="photo-clear" id="clearDrawerImage" data-cropper-clear title="Hapus pilihan foto">x</button>
                        </div>
                        <p style="margin:0.5rem 0 0;color:var(--text-muted);font-size:0.85rem;">Kosongkan jika tidak ganti foto.</p>

                        <!-- Cropper Modal -->
                        <div class="cropper-modal" data-cropper-panel hidden>
                            <div class="cropper-dialog">
                                <div class="cropper-head">
                                    <strong>Atur Crop Foto Paket</strong>
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
                            <h3 style="margin:0;color:var(--primary);font-family:'Playfair Display', Georgia, serif;font-size:1.4rem;">Isi Paket & Jumlah</h3>
                            <p style="color:var(--text-muted);font-size:0.85rem;margin-top:2px;">Pilih menu dan tentukan jumlah per porsinya.</p>
                        </div>
                        <span data-package-menu-count style="font-weight:700;color:var(--highlight);background:#fff;padding:0.4rem 1rem;border-radius:10px;border:1px solid var(--accent);">0 item terpilih</span>
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

                    @error('menus')
                        <small class="form-error" style="display:block;margin-top:0.75rem;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-error" id="drawerError" style="display:none;margin-top:0.9rem;"></div>
            </div>

            <div class="drawer-foot">
                <button type="button" class="btn-drawer-cancel" id="cancelDrawerBtn">Batal</button>
                <button type="submit" class="primary-link" id="submitDrawerBtn">Simpan Paket</button>
            </div>
        </form>
    </aside>

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
            const submitDrawerBtn = document.getElementById('submitDrawerBtn');
            const drawerError = document.getElementById('drawerError');
            const drawerName = document.getElementById('drawerName');
            const drawerSellingPrice = document.getElementById('drawerSellingPrice');
            const drawerFreeItem = document.getElementById('drawerFreeItem');
            const drawerCategory = document.getElementById('drawerCategory');
            const drawerImage = document.getElementById('drawerImage');
            const drawerImageName = document.getElementById('drawerImageName');
            const drawerImageWrap = document.getElementById('drawerImageWrap');
            const drawerImagePreview = document.getElementById('drawerImagePreview');
            const menuCountBadge = document.querySelector('[data-package-menu-count]');
            const menuSelector = document.getElementById('drawerMenuSelector');
            let currentPackageId = null;

            const escapeHtml = (value) => String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

            const formatMoney = (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(value || 0));

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
                document.querySelectorAll('#drawerMenuSelector .menu-option-item').forEach((option) => {
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
                menuCountBadge.textContent = `${selected.size} item terpilih`;
            };

            const resetDrawer = () => {
                drawerForm.reset();
                drawerForm.action = packagesStoreUrl;
                drawerMethod.disabled = true;
                drawerMethod.value = '';
                drawerTitle.textContent = 'Tambah Paket';
                submitDrawerBtn.textContent = 'Simpan Paket';
                currentPackageId = null;
                
                drawerName.value = '';
                drawerSellingPrice.value = '';
                drawerFreeItem.value = '';
                drawerCategory.value = '';
                drawerImageName.textContent = 'Belum ada file dipilih';
                drawerImageWrap.hidden = true;
                drawerImagePreview.src = defaultImage;
                const cropperOutput = document.getElementById('drawer_cropped_image');
                if (cropperOutput) cropperOutput.value = '';

                setMenuSelection([], {});
                resetDrawerError();
            };

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
                resetDrawerError();
            };

            const buildPackageHtml = (packageItem) => {
                const imageUrl = escapeHtml(packageItem.image_url || defaultImage);
                const chips = Array.isArray(packageItem.menus) && packageItem.menus.length
                    ? packageItem.menus.map((menu) => `<span class="package-menu-tag">(${escapeHtml(menu.quantity)}x) ${escapeHtml(menu.name)}</span>`).join('')
                    : '<span class="package-menu-tag">Belum ada menu</span>';

                return `
                    <div class="package-card" data-package-id="${escapeHtml(packageItem.id)}">
                        <img class="package-thumb" src="${imageUrl}" alt="${escapeHtml(packageItem.name)}">
                        <div class="package-meta">
                            <h3>${escapeHtml(packageItem.name)}</h3>
                            <div class="package-pricing">
                                <span class="tag tag-success">${formatMoney(packageItem.selling_price)}</span>
                                ${packageItem.free_item ? `<span class="tag" style="background:#fff3e0; color:#ef6c00;">Free: ${escapeHtml(packageItem.free_item)}</span>` : ''}
                            </div>
                            <div class="package-menu-list">${chips}</div>
                            <div class="package-actions">
                                <button
                                    type="button"
                                    class="btn-open-edit"
                                    data-id="${escapeHtml(packageItem.id)}"
                                    data-name="${escapeHtml(packageItem.name)}"
                                    data-selling-price="${escapeHtml(packageItem.selling_price)}"
                                    data-notes="${escapeHtml(packageItem.notes || '')}"
                                    data-free-item="${escapeHtml(packageItem.free_item || '')}"
                                    data-category-id="${escapeHtml(packageItem.menu_category_id || '')}"
                                    data-image-url="${imageUrl}"
                                    data-menu-ids='${escapeHtml(JSON.stringify((packageItem.menus || []).map((m) => m.id)))}'
                                    data-menu-quantities='${escapeHtml(JSON.stringify(Object.fromEntries((packageItem.menus || []).map((m) => [m.id, m.quantity]))))}'
                                >Edit</button>
                                <button type="button" class="btn-delete-package" data-id="${escapeHtml(packageItem.id)}">Hapus</button>
                            </div>
                        </div>
                    </div>
                `;
            };

            const bindCardActions = (root) => {
                root.querySelectorAll('.btn-open-edit').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        resetDrawer();
                        currentPackageId = btn.getAttribute('data-id');
                        drawerTitle.textContent = 'Edit Paket';
                        submitDrawerBtn.textContent = 'Simpan Perubahan';
                        drawerForm.action = `${packageBaseUrl}/${currentPackageId}`;
                        drawerMethod.disabled = false;
                        drawerMethod.value = 'PUT';

                        drawerName.value = btn.getAttribute('data-name') || '';
                        drawerSellingPrice.value = btn.getAttribute('data-selling-price') || '';
                        drawerFreeItem.value = btn.getAttribute('data-free-item') || '';
                        drawerCategory.value = btn.getAttribute('data-category-id') || '';

                        const imageUrl = btn.getAttribute('data-image-url') || defaultImage;
                        drawerImagePreview.src = imageUrl;
                        if (imageUrl && imageUrl !== defaultImage) {
                            drawerImageWrap.hidden = false;
                        }

                        setMenuSelection(
                            JSON.parse(btn.getAttribute('data-menu-ids') || '[]'),
                            JSON.parse(btn.getAttribute('data-menu-quantities') || '{}')
                        );

                        openDrawer();
                    });
                });

                root.querySelectorAll('.btn-delete-package').forEach((btn) => {
                    btn.addEventListener('click', async () => {
                        const id = btn.getAttribute('data-id');
                        if (!id || !confirm('Hapus paket ini?')) return;
                        const res = await fetch(`${packageBaseUrl}/${id}`, {
                            method: 'POST',
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Content-Type': 'application/json' },
                            body: JSON.stringify({ _method: 'DELETE' })
                        });

                        if (res.status === 404) {
                            window.showToast?.('Paket sudah tidak ada atau telah dihapus.', 'error');
                            list.querySelector(`[data-package-id="${id}"]`)?.remove();
                            updateCount();
                            return;
                        }

                        if (res.status === 419) {
                            window.showToast?.('Sesi telah berakhir, silakan refresh halaman.', 'error');
                            return;
                        }

                        const payload = await res.json();
                        if (!res.ok) return window.showToast?.(payload.message || 'Gagal hapus paket.', 'error');
                        list.querySelector(`[data-package-id="${id}"]`)?.remove();
                        if (!list.querySelector('[data-package-id]')) {
                            list.innerHTML = '<div class="empty-state" id="emptyState">Belum ada paket makanan.</div>';
                        }
                        updateCount();
                        window.showToast?.(payload.message || 'Paket berhasil dihapus.', 'success');
                    });
                });
            };

            const openCreateDrawer = () => {
                resetDrawer();
                openDrawer();
                drawerName.focus();
            };

            if (openCreateBtn) openCreateBtn.addEventListener('click', openCreateDrawer);
            [closeDrawerBtn, cancelDrawerBtn, backdrop].forEach((el) => {
                if (el) el.addEventListener('click', closeDrawer);
            });

            menuSelector.querySelectorAll('.menu-option-item').forEach((option) => {
                const checkbox = option.querySelector('.package-menu-checkbox');
                const qtyControl = option.querySelector('.qty-control');
                const qtyInput = option.querySelector('.qty-input');
                const plusBtn = option.querySelector('.plus');
                const minusBtn = option.querySelector('.minus');

                const syncOption = (state) => {
                    const checked = state !== undefined ? state : checkbox.checked;
                    checkbox.checked = checked;
                    option.classList.toggle('selected', checked);
                    qtyControl.style.display = checked ? 'flex' : 'none';
                    menuCountBadge.textContent = `${document.querySelectorAll('#drawerMenuSelector .package-menu-checkbox:checked').length} item terpilih`;
                };

                option.addEventListener('click', (event) => {
                    if (!event.target.closest('.qty-control') && event.target !== checkbox) {
                        syncOption(!checkbox.checked);
                    }
                });

                checkbox.addEventListener('change', () => syncOption());
                plusBtn.addEventListener('click', (event) => {
                    event.stopPropagation();
                    qtyInput.value = parseInt(qtyInput.value || '1', 10) + 1;
                });
                minusBtn.addEventListener('click', (event) => {
                    event.stopPropagation();
                    qtyInput.value = Math.max(1, parseInt(qtyInput.value || '1', 10) - 1);
                });
                qtyInput.addEventListener('click', (event) => event.stopPropagation());
            });

            drawerForm?.addEventListener('submit', async (event) => {
                event.preventDefault();
                resetDrawerError();
                submitDrawerBtn.disabled = true;
                submitDrawerBtn.textContent = 'Menyimpan...';

                try {
                    const res = await fetch(drawerForm.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken()
                        },
                        body: new FormData(drawerForm)
                    });

                    if (res.status === 419) {
                        throw new Error('Sesi telah berakhir, silakan refresh halaman.');
                    }

                    const payload = await res.json();
                    if (!res.ok) {
                        const messages = payload.errors ? Object.values(payload.errors).flat().join('<br>') : (payload.message || 'Gagal menyimpan paket.');
                        drawerError.innerHTML = messages;
                        drawerError.style.display = 'block';
                        return;
                    }

                    const packageItem = payload.package;
                    const existing = list.querySelector(`[data-package-id="${packageItem.id}"]`);
                    if (existing) {
                        existing.outerHTML = buildPackageHtml(packageItem);
                        const refreshed = list.querySelector(`[data-package-id="${packageItem.id}"]`);
                        if (refreshed) bindCardActions(refreshed);
                    } else if (document.getElementById('emptyState')) {
                        list.innerHTML = buildPackageHtml(packageItem);
                        const inserted = list.querySelector(`[data-package-id="${packageItem.id}"]`);
                        if (inserted) bindCardActions(inserted);
                    } else {
                        list.insertAdjacentHTML('afterbegin', buildPackageHtml(packageItem));
                        const inserted = list.querySelector(`[data-package-id="${packageItem.id}"]`);
                        if (inserted) bindCardActions(inserted);
                    }
                    updateCount();
                    window.showToast?.(payload.message || 'Paket berhasil disimpan.', 'success');
                    closeDrawer();
                } catch (error) {
                    drawerError.textContent = error.message || 'Gagal menyimpan paket.';
                    drawerError.style.display = 'block';
                } finally {
                    submitDrawerBtn.disabled = false;
                    submitDrawerBtn.textContent = currentPackageId ? 'Simpan Perubahan' : 'Simpan Paket';
                }
            });

            bindCardActions(document);
        })();
    </script>
@endsection

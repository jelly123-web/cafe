<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventory & Perlengkapan - Pastel Cafe Theme</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-main: #F9F5F0;
            --bg-card: #FFFFFF;
            --primary: #795548;
            --secondary: #BCAAA4;
            --accent: #D7CCC8;
            --highlight: #D4A373;
            --text-main: #6D4C41;
            --text-muted: #A1887F;
            --profit: #81C784;
            --loss: #E57373;
            --shadow: rgba(121, 85, 72, 0.08);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            line-height: 1.6;
            background-image: radial-gradient(var(--accent) 1px, transparent 1px);
            background-size: 24px 24px;
            padding-bottom: 24px;
        }

        .app-shell {
            display: grid;
            grid-template-columns: 290px minmax(0, 1fr);
            min-height: 100vh;
            transition: grid-template-columns .2s ease;
        }

        .sidebar-toggle { position: fixed; top: 16px; left: 16px; width: 44px; height: 44px; border: 1px solid var(--accent); border-radius: 12px; background: #fff; color: var(--primary); font-size: 24px; font-weight: 700; line-height: 1; cursor: pointer; z-index: 2100; box-shadow: 0 8px 20px rgba(121,85,72,.15); transition: left .2s ease, transform .2s ease; }
        body:not(.sidebar-collapsed) .sidebar-toggle { left: 306px; }
        .sidebar-toggle:hover { transform: translateY(-1px); }
        .sidebar-backdrop { position: fixed; inset: 0; background: rgba(62,39,35,.35); opacity: 0; pointer-events: none; transition: opacity .2s ease; z-index: 1500; }
        body.sidebar-open .sidebar-backdrop { opacity: 1; pointer-events: auto; }
        .sidebar { padding: 1.75rem 1.4rem; border-right: 1px solid rgba(121, 85, 72, 0.08); background: rgba(255, 255, 255, 0.94); backdrop-filter: blur(10px); display: flex; flex-direction: column; gap: 1.25rem; transition: transform .2s ease, opacity .2s ease; z-index: 1700; }
        .sidebar-brand { display: flex; flex-direction: column; }
        .sidebar-logo { width: 64px; height: 64px; object-fit: cover; border-radius: 18px; box-shadow: 0 4px 15px rgba(121, 85, 72, 0.12); margin-bottom: 0.5rem; }
        .sidebar-brand h2 { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.8rem; margin: 0.5rem 0 0.25rem; }
        .sidebar-brand p { color: var(--text-muted); font-size: 0.95rem; }
        .nav-menu { display: grid; gap: 0.75rem; }
        .nav-item { text-decoration: none; color: var(--text-main); background: var(--bg-card); border-radius: 16px; padding: 0.9rem 1rem; box-shadow: 0 4px 15px var(--shadow); border: 1px solid transparent; transition: all 0.2s ease; font-weight: 500; font-size: 0.95rem; }
        .nav-item.active, .nav-item:hover { border-color: rgba(212, 163, 115, 0.35); background: #fffaf5; }
        .sidebar-footer { margin-top: auto; display: grid; gap: 0.85rem; }
        .user-card { background: var(--bg-card); border-radius: 18px; padding: 1rem; box-shadow: 0 4px 15px var(--shadow); }
        .user-info-row { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; }
        .profile-photo { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent); }
        .user-info-text { display: flex; flex-direction: column; }
        .user-info-text span { color: var(--text-muted); font-size: 0.8rem; }
        .user-info-text strong { color: var(--primary); font-size: 0.95rem; }
        .logout-btn { background-color: #e2b68c; color: #fff; border: none; padding: 0.75rem 1.5rem; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3); width: 100%; font-size: 0.95rem; }
        .logout-btn:hover { background-color: #d4a373; transform: translateY(-2px); }

        .main-panel { padding: 2rem 2.5rem; overflow-y: auto; }
        .page-header { margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem; }
        .page-kicker { display: inline-flex; align-items: center; background-color: var(--highlight); color: #fff; font-size: 0.75rem; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase; }
        .page-header h1 { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.8rem; margin: 0.5rem 0 0.25rem; }
        .page-header p { color: var(--text-muted); font-size: 0.95rem; }

        .panel { background: var(--bg-card); border: 1px solid var(--accent); border-radius: 20px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; box-shadow: 0 4px 15px var(--shadow); }
        
        .btn { border: 1px solid transparent; border-radius: 12px; padding: 0.65rem 1.2rem; cursor: pointer; font-weight: 600; font-family: inherit; font-size: 0.9rem; transition: all 0.2s ease; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .btn-primary { background: #e2b68c; color: #fff; border: none; box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3); }
        .btn-primary:hover { background: #d4a373; transform: translateY(-2px); }

        .table-wrap { overflow-x: auto; margin: 0; }
        .inventory-table { width: 100%; border-collapse: collapse; }
        .inventory-table th, .inventory-table td { padding: 1rem 0.75rem; border-bottom: 1px dashed var(--accent); vertical-align: middle; text-align: left; font-size: 0.95rem; }
        .inventory-table th { background: var(--bg-main); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); font-weight: 600; border-bottom: 2px solid var(--highlight); }
        .inventory-table tbody tr:hover { background-color: #FFFAF5; }
        .inventory-table tbody tr:last-child td { border-bottom: none; }
        .item-name { font-weight: 600; color: var(--primary); }
        .amount { font-variant-numeric: tabular-nums; }
        .tag { display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; }
        .tag-danger { background: #FFEBEE; color: #C62828; }
        .tag-success { background: #E8F5E9; color: #558B2F; }
        .tag-in { background: #E8F5E9; color: #558B2F; }
        .tag-out { background: #FFF3E0; color: #E65100; }
        .section-title { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.2rem; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--accent); }
        .empty-state { color: var(--text-muted); font-style: italic; text-align: center; padding: 2.5rem 1rem; }
        
        .btn-delete { color: var(--loss); background: transparent; border: none; font-weight: 700; cursor: pointer; padding: 0.2rem 0.5rem; border-radius: 6px; }
        .btn-delete:hover { background: #FFEBEE; }

        /* Drawer Styles */
        .drawer-backdrop { position:fixed; inset:0; background:rgba(56, 37, 30, 0.32); backdrop-filter:blur(2px); z-index:1200; opacity:0; visibility:hidden; transition:0.2s ease; }
        .drawer-backdrop.open { opacity:1; visibility:visible; }
        .inventory-drawer {
            position: fixed;
            top: 0;
            right: 0;
            width: min(520px, 95vw);
            height: 100vh;
            background: #fff;
            z-index: 1201;
            transform: translateX(102%);
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: -10px 0 34px rgba(79, 53, 43, 0.12);
            display: flex;
            flex-direction: column;
        }
        .inventory-drawer.open { transform: translateX(0); }
        .drawer-head { padding: 1.5rem 1.75rem; border-bottom: 1px solid var(--accent); display: flex; justify-content: space-between; align-items: center; background: #fff; flex-shrink: 0; }
        .drawer-head h3 { margin: 0; font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.6rem; }
        .drawer-close { border: 1.5px solid var(--accent); background: #fff; color: var(--primary); border-radius: 12px; padding: 0.5rem 1rem; cursor: pointer; font-weight: 700; font-size: 0.9rem; transition: all 0.2s; }
        .drawer-close:hover { background: var(--bg-main); border-color: var(--highlight); }
        .drawer-body { padding: 1.75rem; overflow-y: auto; flex: 1; scrollbar-width: thin; scrollbar-color: var(--accent) transparent; }
        .drawer-body::-webkit-scrollbar { width: 6px; }
        .drawer-body::-webkit-scrollbar-thumb { background: var(--accent); border-radius: 10px; }
        .drawer-foot { padding: 1.25rem 1.75rem; border-top: 1px solid var(--accent); display: flex; gap: 1rem; justify-content: flex-end; background: #fdfaf8; flex-shrink: 0; }
        .btn-drawer-cancel { border: 1.5px solid var(--accent); background: #fff; color: var(--primary); border-radius: 12px; padding: 0.75rem 1.5rem; cursor: pointer; font-weight: 700; transition: all 0.2s; }
        .btn-drawer-cancel:hover { border-color: var(--loss); color: var(--loss); }
        .drawer-field { display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1.25rem; }
        .drawer-field label { font-size: 0.9rem; font-weight: 700; color: var(--text-main); }
        .drawer-field input, .drawer-field select, .drawer-field textarea { width: 100%; padding: 0.75rem 1.15rem; border-radius: 14px; border: 1.5px solid var(--accent); background: #fff; color: var(--text-main); font-size: 1rem; transition: all 0.2s; outline: none; }
        .drawer-field input:focus, .drawer-field select:focus, .drawer-field textarea:focus { border-color: var(--highlight); box-shadow: 0 0 0 4px rgba(212, 163, 115, 0.12); }
        .drawer-error { background: #fff5f5; color: var(--loss); padding: 0.85rem 1.15rem; border-radius: 12px; border-left: 4px solid var(--loss); font-weight: 600; margin-top: 0.5rem; display: none; }

        /* Toast system */
        .toast-wrap { position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; display: flex; flex-direction: column; gap: 10px; width: min(340px, 90vw); pointer-events: none; }
        .toast-item { background: #38251e; color: #fff; padding: 12px 20px; border-radius: 14px; box-shadow: 0 8px 25px rgba(0,0,0,0.2); font-size: 0.9rem; font-weight: 600; display: flex; justify-content: space-between; align-items: center; gap: 12px; pointer-events: auto; animation: toast-in 0.3s ease forwards; transition: opacity 0.2s, transform 0.2s; }
        .toast-item.success { border-left: 4px solid var(--profit); }
        .toast-item.error { border-left: 4px solid var(--loss); }
        .toast-item button { background: transparent; border: none; color: rgba(255,255,255,0.6); cursor: pointer; font-size: 1.1rem; padding: 0 4px; }
        @keyframes toast-in { from { opacity: 0; transform: translateY(-15px); } to { opacity: 1; transform: translateY(0); } }

        body.sidebar-collapsed .app-shell { grid-template-columns: 0 minmax(0, 1fr) !important; }
        body.sidebar-collapsed .sidebar { transform: translateX(-100%); opacity: 0; pointer-events: none; }
        @media (max-width: 1100px) {
            body:not(.sidebar-collapsed) .sidebar-toggle { left: 16px; }
            .app-shell { grid-template-columns: 1fr; }
            .sidebar { position: fixed; top: 0; left: 0; width: 290px; min-height: 100vh; border-right: 1px solid rgba(121, 85, 72, 0.08); border-bottom: 0; }
            body.sidebar-collapsed .sidebar { transform: translateX(-110%); opacity: 1; pointer-events: none; }
        }
        @media (max-width: 768px) {
            .main-panel { padding: 1.5rem 1rem; }
            .page-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
            .page-header h1 { font-size: 1.5rem; }
            .panel { padding: 1.25rem; }
            .inventory-table th, .inventory-table td { padding: 0.75rem 0.5rem; font-size: 0.85rem; }
        }
    </style>
</head>
<body>
    <button class="sidebar-toggle" type="button" id="sidebarToggle" aria-label="Toggle Sidebar">=</button>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
    <div class="app-shell">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <img src="{{ $cafeBrand['logo_url'] ?: 'https://placehold.co/64x64/D4A373/FFFFFF?text=BB' }}" alt="{{ $cafeBrand['name'] ?? 'Cafe' }}" class="sidebar-logo">
                <h2>{{ $cafeBrand['name'] ?? 'Cafe' }}</h2>
                <p>Panel Gudang</p>
            </div>
            <nav class="nav-menu">
                <a class="nav-item {{ ($activeTab ?? 'inventory') === 'inventory' ? 'active' : '' }}" href="{{ route('inventory.index') }}">Inventory</a>
                <a class="nav-item {{ ($activeTab ?? '') === 'movement' ? 'active' : '' }}" href="{{ route('inventory.in.page') }}">Barang Masuk/Keluar</a>
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
                    <small style="display:block;margin-bottom:0.5rem;">{{ auth()->user()->username ?? 'gudang' }}</small>
                    <a href="{{ route('profile.edit') }}" style="display: block; font-size: 0.8rem; color: var(--primary); text-decoration: none; font-weight: 600; margin-bottom: 0.5rem;">Edit Profil</a>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout-btn" type="submit">Logout</button>
                </form>
            </div>
        </aside>

        <main class="main-panel">
            <div class="page-header">
                <div>
                    <span class="page-kicker">ERP</span>
                    <h1>Inventory & Perlengkapan</h1>
                    <p>Kelola bahan baku dan perlengkapan (panci, sendok, dll) di satu tempat.</p>
                </div>
                @if (($activeTab ?? 'inventory') === 'inventory')
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <button class="btn btn-primary" onclick="openDrawer('category')">+ Kategori</button>
                        <button class="btn btn-primary" onclick="openDrawer('bahan')">+ Bahan Baku</button>
                        <button class="btn btn-primary" onclick="openDrawer('barang')">+ Barang/Perlengkapan</button>
                        <button class="btn btn-primary" onclick="openDrawer('opname')">Opname</button>
                    </div>
                @endif
            </div>

            @if (session('success'))<div class="panel" style="color:#558B2F; font-weight: 600; border-left: 4px solid #558B2F;">{{ session('success') }}</div>@endif

            @if (($activeTab ?? 'inventory') === 'inventory')
                <section class="panel">
                    <h3 class="section-title">Stok Bahan Baku </h3>
                    <div class="table-wrap">
                        <table class="inventory-table" id="tableBahan">
                            <thead>
                                <tr><th>Nama Bahan</th><th>Kategori</th><th>Baik</th><th>Kurang Baik</th><th>Rusak</th><th>Total</th><th>Min. Stok</th><th>Aksi</th></tr>
                            </thead>
                            <tbody>
                                @php $bahanItems = $allItems->where('type', 'bahan'); @endphp
                                @forelse($bahanItems as $item)
                                    @php $isLow = (float)$item->total_stock <= (float)$item->min_stock; @endphp
                                    <tr data-item-id="{{ $item->id }}">
                                        <td><span class="item-name">{{ $item->name }}</span></td>
                                        <td>{{ $item->category?->name ?? '-' }} ({{ $item->unit }})</td>
                                        <td><span class="amount">{{ rtrim(rtrim(number_format((float)$item->stock_good,2,'.',''),'0'),'.') }}</span></td>
                                        <td><span class="amount">{{ rtrim(rtrim(number_format((float)$item->stock_less_good,2,'.',''),'0'),'.') }}</span></td>
                                        <td><span class="amount">{{ rtrim(rtrim(number_format((float)$item->stock_damaged,2,'.',''),'0'),'.') }}</span></td>
                                        <td><span class="amount">{{ rtrim(rtrim(number_format((float)$item->total_stock,2,'.',''),'0'),'.') }} {{ $item->unit }}</span></td>
                                        <td><span class="amount">{{ rtrim(rtrim(number_format((float)$item->min_stock,2,'.',''),'0'),'.') }}</span></td>
                                        <td><button class="btn-delete" data-id="{{ $item->id }}">Hapus</button></td>
                                    </tr>
                                @empty
                                    <tr class="empty-row"><td colspan="8" class="empty-state">Belum ada data bahan baku.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="panel">
                    <h3 class="section-title">Stok Barang & Perlengkapan </h3>
                    <div class="table-wrap">
                        <table class="inventory-table" id="tableBarang">
                            <thead>
                                <tr><th>Nama Barang</th><th>Kategori</th><th>Baik</th><th>Kurang Baik</th><th>Rusak</th><th>Total</th><th>Satuan</th><th>Aksi</th></tr>
                            </thead>
                            <tbody>
                                @php $barangItems = $allItems->where('type', 'barang'); @endphp
                                @forelse($barangItems as $item)
                                    <tr data-item-id="{{ $item->id }}">
                                        <td><span class="item-name">{{ $item->name }}</span></td>
                                        <td>{{ $item->category?->name ?? '-' }}</td>
                                        <td><span class="amount">{{ (int)$item->stock_good }}</span></td>
                                        <td><span class="amount">{{ (int)$item->stock_less_good }}</span></td>
                                        <td><span class="amount">{{ (int)$item->stock_damaged }}</span></td>
                                        <td><span class="amount">{{ (int)$item->total_stock }}</span></td>
                                        <td>{{ $item->unit }}</td>
                                        <td><button class="btn-delete" data-id="{{ $item->id }}">Hapus</button></td>
                                    </tr>
                                @empty
                                    <tr class="empty-row"><td colspan="8" class="empty-state">Belum ada data barang/perlengkapan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif

            @if (($activeTab ?? '') === 'movement')
                <div style="margin-bottom: 1.5rem; display: flex; gap: 0.75rem;">
                    <button class="btn btn-primary" onclick="openDrawer('stock_in')">+ Barang Masuk</button>
                    <button class="btn btn-primary" onclick="openDrawer('stock_out')">+ Barang Keluar</button>
                </div>
                <section class="panel">
                    <h3 class="section-title">Riwayat Barang Masuk/Keluar</h3>
                    <div class="table-wrap">
                        <table class="inventory-table">
                            <thead>
                                <tr><th>Waktu</th><th>Bahan/Barang</th><th>Tipe</th><th>Kondisi</th><th>Qty</th><th>Catatan / Keterangan</th><th>User</th></tr>
                            </thead>
                            <tbody>
                                @php
                                    $filtered = $movements->getCollection()->filter(fn($x) => in_array($x->type, ['in', 'out'], true));
                                @endphp
                                @forelse($filtered as $m)
                                    <tr>
                                        <td>{{ $m->moved_at?->format('d M Y, H:i') }}</td>
                                        <td><span class="item-name">{{ $m->item?->name ?? '-' }}</span></td>
                                        <td>
                                            @if($m->type === 'in')
                                                <span class="tag tag-in">Masuk</span>
                                            @elseif($m->type === 'out')
                                                <span class="tag tag-out">Keluar</span>
                                            @else
                                                <span class="tag tag-success">Opname</span>
                                            @endif
                                        </td>
                                        <td>{{ $m->stock_condition ?: '-' }} @if($m->to_stock_condition) -> {{ $m->to_stock_condition }} @endif</td>
                                        <td><span class="amount">{{ rtrim(rtrim(number_format((float)$m->qty,2,'.',''),'0'),'.') }} {{ $m->item?->unit }}</span></td>
                                        <td>{!! $m->notes ? nl2br(e($m->notes)) : '-' !!}</td>
                                        <td>{{ $m->user?->username ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="empty-state">Belum ada mutasi stok.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination-area">{{ $movements->links('components.pagination') }}</div>
                </section>
            @endif
        </main>
    </div>

    <!-- Inventory Drawer -->
    <div class="drawer-backdrop" id="drawerBackdrop"></div>
    <aside class="inventory-drawer" id="inventoryDrawer" aria-hidden="true">
        <div class="drawer-head">
            <h3 id="drawerTitle">Form Inventory</h3>
            <button type="button" class="drawer-close" onclick="closeDrawer()">Tutup</button>
        </div>
        <div class="drawer-body">
            <!-- Form Kategori -->
            <form id="formCategory" method="POST" action="{{ route('inventory.categories.store') }}" style="display:none;">
                @csrf
                <div class="drawer-field">
                    <label>Tipe Kategori</label>
                    <select name="type" required>
                        <option value="bahan">Bahan Baku (Konsumsi)</option>
                        <option value="barang">Barang / Perlengkapan (Aset)</option>
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

            <!-- Form Bahan -->
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

            <!-- Form Barang -->
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

            <!-- Form Opname -->
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

            <!-- Form Stock In -->
            <form id="formStockIn" method="POST" action="{{ route('inventory.stock.in') }}" style="display:none;">
                @csrf
                <div class="drawer-field">
                    <label>Bahan/Barang</label>
                    <select name="inventory_item_id" required>
                        <option value="">Pilih bahan/barang</option>
                        @foreach ($allItems as $item)
                            <option value="{{ $item->id }}">{{ $item->name }} ({{ rtrim(rtrim(number_format((float)$item->total_stock,2,'.',''), '0'), '.') }} {{ $item->unit }})</option>
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

            <!-- Form Stock Out -->
            <form id="formStockOut" method="POST" action="{{ route('inventory.stock.out') }}" style="display:none;">
                @csrf
                <div class="drawer-field">
                    <label>Bahan/Barang</label>
                    <select name="inventory_item_id" required>
                        <option value="">Pilih bahan/barang</option>
                        @foreach ($allItems as $item)
                            <option value="{{ $item->id }}">{{ $item->name }} ({{ rtrim(rtrim(number_format((float)$item->total_stock,2,'.',''), '0'), '.') }} {{ $item->unit }})</option>
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
                    <label>Catatan / Alasan Keluar</label>
                    <textarea name="notes" rows="3" placeholder="Contoh: Panci rusak dibuang / Bahan untuk masak hari ini" required></textarea>
                </div>
                <input type="hidden" name="used_for" value="Pengeluaran Gudang">
                <input type="hidden" name="used_items" value="Cek catatan">
            </form>

            <div id="drawerError" class="drawer-error"></div>
        </div>
        <div class="drawer-foot">
            <button type="button" class="btn-drawer-cancel" onclick="closeDrawer()">Batal</button>
            <button type="button" class="btn btn-primary" id="btnSubmitDrawer" onclick="submitActiveForm()">Simpan Data</button>
        </div>
    </aside>

    <div id="toastWrap" class="toast-wrap"></div>

    <script>
        (function () {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');
            const inventoryDrawer = document.getElementById('inventoryDrawer');
            const drawerBackdrop = document.getElementById('drawerBackdrop');
            const toastWrap = document.getElementById('toastWrap');
            const btnSubmit = document.getElementById('btnSubmitDrawer');
            let activeFormId = null;

            window.showToast = function (message, type = 'success') {
                const el = document.createElement('div');
                el.className = 'toast-item ' + type;
                el.innerHTML = '<span>' + String(message) + '</span><button type="button">x</button>';
                el.querySelector('button').onclick = () => el.remove();
                toastWrap.appendChild(el);
                setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 200); }, 3500);
            };

            const openSidebar = () => { document.body.classList.remove('sidebar-collapsed'); document.body.classList.add('sidebar-open'); };
            const closeSidebar = () => { document.body.classList.add('sidebar-collapsed'); document.body.classList.remove('sidebar-open'); };
            
            if (window.innerWidth <= 1100) closeSidebar();
            sidebarToggle?.addEventListener('click', () => document.body.classList.contains('sidebar-collapsed') ? openSidebar() : closeSidebar());
            sidebarBackdrop?.addEventListener('click', closeSidebar);

            window.openDrawer = (type) => {
                const titleMap = {
                    'category': 'Input Kategori Baru',
                    'bahan': 'Tambah Bahan Baku Baru',
                    'barang': 'Tambah Barang/Perlengkapan',
                    'opname': 'Stok Opname',
                    'stock_in': 'Barang Masuk',
                    'stock_out': 'Barang Keluar'
                };
                const formMap = {
                    'category': 'formCategory',
                    'bahan': 'formBahan',
                    'barang': 'formBarang',
                    'opname': 'formOpname',
                    'stock_in': 'formStockIn',
                    'stock_out': 'formStockOut'
                };

                document.querySelectorAll('.inventory-drawer form').forEach(f => f.style.display = 'none');
                activeFormId = formMap[type];
                document.getElementById(activeFormId).style.display = 'block';
                document.getElementById('drawerTitle').textContent = titleMap[type];
                
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
                document.querySelectorAll('.inventory-drawer form').forEach(f => f.reset());
            };

            const formatNum = (n) => String(Number(n || 0).toLocaleString('id-ID'));

            const buildItemRow = (item) => {
                if (item.type === 'bahan') {
                    return `
                        <tr data-item-id="${item.id}">
                            <td><span class="item-name">${item.name}</span></td>
                            <td>${item.category?.name || '-'} (${item.unit})</td>
                            <td><span class="amount">${formatNum(item.stock_good)}</span></td>
                            <td><span class="amount">${formatNum(item.stock_less_good)}</span></td>
                            <td><span class="amount">${formatNum(item.stock_damaged)}</span></td>
                            <td><span class="amount">${formatNum(item.total_stock)} ${item.unit}</span></td>
                            <td><span class="amount">${formatNum(item.min_stock)}</span></td>
                            <td><button class="btn-delete" data-id="${item.id}">Hapus</button></td>
                        </tr>
                    `;
                } else {
                    return `
                        <tr data-item-id="${item.id}">
                            <td><span class="item-name">${item.name}</span></td>
                            <td>${item.category?.name || '-'}</td>
                            <td><span class="amount">${parseInt(item.stock_good)}</span></td>
                            <td><span class="amount">${parseInt(item.stock_less_good)}</span></td>
                            <td><span class="amount">${parseInt(item.stock_damaged)}</span></td>
                            <td><span class="amount">${parseInt(item.total_stock)}</span></td>
                            <td>${item.unit}</td>
                            <td><button class="btn-delete" data-id="${item.id}">Hapus</button></td>
                        </tr>
                    `;
                }
            };

            window.submitActiveForm = async () => {
                if (!activeFormId) return;
                const form = document.getElementById(activeFormId);
                const formData = new FormData(form);
                const drawerError = document.getElementById('drawerError');
                
                // Jika form stock in/out/opname, submit biasa (biar halaman refresh karena banyak perubahan data)
                if (activeFormId === 'formStockIn' || activeFormId === 'formStockOut' || activeFormId === 'formOpname') {
                    form.submit();
                    return;
                }

                btnSubmit.disabled = true;
                btnSubmit.textContent = 'Menyimpan...';
                drawerError.style.display = 'none';

                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: formData
                    });

                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Gagal menyimpan data');

                    if (activeFormId === 'formCategory') {
                        // Tambah kategori baru ke dropdown
                        const cat = data.category;
                        const selectId = cat.type === 'bahan' ? 'selectBahanCat' : 'selectBarangCat';
                        const select = document.getElementById(selectId);
                        const opt = new Option(`${cat.name} (${cat.unit})`, cat.id);
                        select.add(opt);
                        window.showToast(data.message);
                    } else {
                        // Tambah item baru ke tabel
                        const item = data.item;
                        const tableId = item.type === 'bahan' ? 'tableBahan' : 'tableBarang';
                        const tbody = document.querySelector(`#${tableId} tbody`);
                        const emptyRow = tbody.querySelector('.empty-row');
                        if (emptyRow) emptyRow.remove();
                        
                        tbody.insertAdjacentHTML('afterbegin', buildItemRow(item));
                        bindDeleteEvents();
                        window.showToast(data.message);
                    }
                    closeDrawer();
                } catch (err) {
                    drawerError.textContent = err.message;
                    drawerError.style.display = 'block';
                } finally {
                    btnSubmit.disabled = false;
                    btnSubmit.textContent = 'Simpan Data';
                }
            };

            const bindDeleteEvents = () => {
                document.querySelectorAll('.btn-delete').forEach(btn => {
                    btn.onclick = async () => {
                        const id = btn.dataset.id;
                        if (!confirm('Hapus item ini dari inventory?')) return;
                        
                        try {
                            const formData = new FormData();
                            formData.append('_method', 'DELETE');
                            const res = await fetch(`/gudang/items/${id}`, {
                                method: 'POST',
                                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                                body: formData
                            });
                            const data = await res.json();
                            if (res.ok) {
                                document.querySelector(`tr[data-item-id="${id}"]`).remove();
                                window.showToast(data.message);
                            } else {
                                window.showToast(data.message, 'error');
                            }
                        } catch (e) {
                            window.showToast('Gagal menghapus item', 'error');
                        }
                    };
                });
            };

            bindDeleteEvents();
            drawerBackdrop.addEventListener('click', closeDrawer);

            setInterval(async () => {
                try {
                    const res = await fetch('{{ route('inventory.live') }}');
                    const data = await res.json();
                    
                    data.items.forEach(item => {
                        const row = document.querySelector(`tr[data-item-id="${item.id}"]`);
                        if (row) {
                            row.querySelector('td:nth-child(3) .amount').textContent = item.stock_good;
                            row.querySelector('td:nth-child(4) .amount').textContent = item.stock_less_good;
                            row.querySelector('td:nth-child(5) .amount').textContent = item.stock_damaged;
                            row.querySelector('td:nth-child(6) .amount').textContent = item.total_stock;
                        }
                    });
                } catch (e) {
                    console.error('Failed to fetch live inventory data', e);
                }
            }, 1000);
        })();
    </script>
    @include('components.live-sync')
</body>
</html>

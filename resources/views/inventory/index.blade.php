<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventory Gudang - Pastel Cafe Theme</title>

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
        .logout-btn { background-color: var(--highlight); color: #fff; border: none; padding: 0.75rem 1.5rem; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3); width: 100%; font-size: 0.95rem; }
        .logout-btn:hover { background-color: #c68b59; transform: translateY(-2px); }

        .main-panel { padding: 2rem 2.5rem; overflow-y: auto; }
        .page-header { margin-bottom: 1.5rem; }
        .page-kicker { display: inline-flex; align-items: center; background-color: var(--highlight); color: #fff; font-size: 0.75rem; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase; }
        .page-header h1 { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.8rem; margin: 0.5rem 0 0.25rem; }
        .page-header p { color: var(--text-muted); font-size: 0.95rem; }

        .panel { background: var(--bg-card); border: 1px solid var(--accent); border-radius: 20px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; box-shadow: 0 4px 15px var(--shadow); }
        .form-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem; }
        .form-card { background: var(--bg-card); border: 1px solid var(--accent); border-radius: 20px; padding: 1.5rem; box-shadow: 0 4px 15px var(--shadow); display: flex; flex-direction: column; }
        .form-card h3 { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.2rem; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 2px solid var(--accent); }
        .field { display: flex; flex-direction: column; gap: 0.4rem; margin-bottom: 0.75rem; }
        .field:last-of-type { margin-bottom: 1.25rem; }
        .field label { font-size: 0.85rem; font-weight: 600; color: var(--text-muted); }
        .form-card input, .form-card select, .form-card textarea { width: 100%; border: 1px solid var(--accent); border-radius: 12px; padding: 0.65rem 1rem; background-color: var(--bg-main); color: var(--text-main); font-family: inherit; font-size: 0.9rem; outline: none; transition: border-color 0.2s ease, box-shadow 0.2s ease; }
        .form-card textarea { resize: vertical; min-height: 60px; }
        .form-card input:focus, .form-card select:focus, .form-card textarea:focus { border-color: var(--highlight); box-shadow: 0 0 0 3px rgba(212, 163, 115, 0.15); background-color: #fff; }
        .btn { border: 1px solid transparent; border-radius: 12px; padding: 0.65rem 1.2rem; cursor: pointer; font-weight: 600; font-family: inherit; font-size: 0.9rem; transition: all 0.2s ease; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; margin-top: auto; }
        .btn-primary { background: var(--highlight); color: #fff; border: none; box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3); width: 100%; }
        .btn-primary:hover { background: #c68b59; transform: translateY(-2px); }

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
        .pagination-area { margin-top: 1.5rem; }

        body.sidebar-collapsed .app-shell { grid-template-columns: 0 minmax(0, 1fr) !important; }
        body.sidebar-collapsed .sidebar { transform: translateX(-100%); opacity: 0; pointer-events: none; }
        @media (max-width: 1100px) {
            body:not(.sidebar-collapsed) .sidebar-toggle { left: 16px; }
            .app-shell { grid-template-columns: 1fr; }
            .sidebar { position: fixed; top: 0; left: 0; width: 290px; min-height: 100vh; border-right: 1px solid rgba(121, 85, 72, 0.08); border-bottom: 0; }
            body.sidebar-collapsed .sidebar { transform: translateX(-110%); opacity: 1; pointer-events: none; }
        }
        @media (max-width: 900px) { .form-grid { grid-template-columns: 1fr; } }
        @media (max-width: 768px) {
            .main-panel { padding: 1.5rem 1rem; }
            .page-header h1 { font-size: 1.5rem; }
            .panel { padding: 1.25rem; }
            .form-card { padding: 1.25rem; }
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
                <span class="page-kicker">ERP</span>
                <h1>Inventory Gudang</h1>
                <p>Mendata bahan baku: barang masuk dan barang keluar.</p>
            </div>

            @if (session('success'))<div class="panel" style="color:#558B2F;">{{ session('success') }}</div>@endif
            @if ($errors->any())<div class="panel" style="color:#C62828;">{{ $errors->first() }}</div>@endif

            @if (($activeTab ?? 'inventory') === 'inventory')
                <div class="form-grid">
                    <section class="form-card">
                        <h3>Input Kategori</h3>
                        <form method="POST" action="{{ route('inventory.categories.store') }}">
                            @csrf
                            <div class="field">
                                <label>Nama kategori</label>
                                <input name="name" placeholder="Contoh: Bahan Basah / Perlengkapan" required>
                            </div>
                            <div class="field">
                                <label>Unit kategori</label>
                                <select name="unit" required>
                                    <option value="">Pilih unit</option>
                                    <option value="kg">kg</option>
                                    <option value="pcs">pcs</option>
                                </select>
                            </div>
                            <button class="btn btn-primary" type="submit">Simpan Kategori</button>
                        </form>
                    </section>

                    <section class="form-card">
                        <h3>Tambah Bahan Baru</h3>
                        <form method="POST" action="{{ route('inventory.items.store') }}">
                            @csrf
                            <div class="field">
                                <label>Kategori</label>
                                <select name="inventory_category_id" required>
                                    <option value="">Pilih kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->unit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label>Nama bahan</label>
                                <input name="name" placeholder="Contoh: Biji Kopi Arabica" required>
                            </div>
                            <div class="field">
                                <label>Stok minimum (peringatan)</label>
                                <input type="number" step="0.01" min="0" name="min_stock" value="0">
                            </div>
                            <button class="btn btn-primary" type="submit">Simpan Bahan</button>
                        </form>
                    </section>

                    <section class="form-card">
                        <h3>Stok Opname</h3>
                        <form method="POST" action="{{ route('inventory.stock.opname') }}">
                            @csrf
                            <div class="field">
                                <label>Bahan</label>
                                <select name="inventory_item_id" required>
                                    <option value="">Pilih bahan</option>
                                    @foreach ($allItems as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label>Dari kondisi</label>
                                <select name="from_condition" required>
                                    <option value="">Pilih kondisi asal</option>
                                    <option value="good">Baik</option>
                                    <option value="less_good">Kurang Baik</option>
                                    <option value="damaged">Rusak</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>Ke kondisi</label>
                                <select name="to_condition" required>
                                    <option value="">Pilih kondisi tujuan</option>
                                    <option value="good">Baik</option>
                                    <option value="less_good">Kurang Baik</option>
                                    <option value="damaged">Rusak</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>Qty opname</label>
                                <input type="number" step="0.01" min="0.01" name="qty" required>
                            </div>
                            <div class="field">
                                <label>Catatan</label>
                                <textarea name="notes" rows="2" placeholder="Contoh: 2kg tomat busuk, pindah dari baik ke rusak"></textarea>
                            </div>
                            <button class="btn btn-primary" type="submit">Catat Opname</button>
                        </form>
                    </section>
                </div>

                <section class="panel">
                    <h3 class="section-title">Stok Bahan Saat Ini</h3>
                    <div class="table-wrap">
                        <table class="inventory-table">
                            <thead>
                                <tr><th>Nama Bahan</th><th>Kategori</th><th>Baik</th><th>Kurang Baik</th><th>Rusak</th><th>Total</th><th>Stok Minimum</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                    @php $isLow = (float)$item->total_stock <= (float)$item->min_stock; @endphp
                                    <tr>
                                        <td><span class="item-name">{{ $item->name }}</span></td>
                                        <td>{{ $item->category?->name ?? '-' }} ({{ $item->unit }})</td>
                                        <td><span class="amount">{{ rtrim(rtrim(number_format((float)$item->stock_good,2,'.',''),'0'),'.') }}</span></td>
                                        <td><span class="amount">{{ rtrim(rtrim(number_format((float)$item->stock_less_good,2,'.',''),'0'),'.') }}</span></td>
                                        <td><span class="amount">{{ rtrim(rtrim(number_format((float)$item->stock_damaged,2,'.',''),'0'),'.') }}</span></td>
                                        <td><span class="amount">{{ rtrim(rtrim(number_format((float)$item->total_stock,2,'.',''),'0'),'.') }} {{ $item->unit }}</span></td>
                                        <td><span class="amount">{{ rtrim(rtrim(number_format((float)$item->min_stock,2,'.',''),'0'),'.') }} {{ $item->unit }}</span></td>
                                        <td>{!! $isLow ? '<span class="tag tag-danger">Stok Menipis</span>' : '<span class="tag tag-success">Aman</span>' !!}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="empty-state">Belum ada data bahan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination-area">{{ $items->links('components.pagination') }}</div>
                </section>
            @endif

            @if (($activeTab ?? '') === 'movement')
                <div class="form-grid">
                    <section class="form-card">
                        <h3>Barang Masuk</h3>
                        <form method="POST" action="{{ route('inventory.stock.in') }}">
                            @csrf
                            <div class="field">
                                <label>Bahan</label>
                                <select name="inventory_item_id" required>
                                    <option value="">Pilih bahan</option>
                                    @foreach ($allItems as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }} ({{ rtrim(rtrim(number_format((float)$item->total_stock,2,'.',''), '0'), '.') }} {{ $item->unit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label>Kondisi stok masuk</label>
                                <select name="stock_condition" required>
                                    <option value="">Pilih kondisi</option>
                                    <option value="good">Baik</option>
                                    <option value="less_good">Kurang Baik</option>
                                    <option value="damaged">Rusak</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>Jumlah masuk</label>
                                <input type="number" step="0.01" min="0.01" name="qty" placeholder="Contoh: 10" required>
                            </div>
                            <div class="field">
                                <label>Catatan</label>
                                <textarea name="notes" rows="2" placeholder="Contoh: pembelian supplier A"></textarea>
                            </div>
                            <button class="btn btn-primary" type="submit">Catat Masuk</button>
                        </form>
                    </section>

                    <section class="form-card">
                        <h3>Barang Keluar</h3>
                        <form method="POST" action="{{ route('inventory.stock.out') }}">
                            @csrf
                            <div class="field">
                                <label>Bahan</label>
                                <select name="inventory_item_id" required>
                                    <option value="">Pilih bahan</option>
                                    @foreach ($allItems as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }} ({{ rtrim(rtrim(number_format((float)$item->total_stock,2,'.',''), '0'), '.') }} {{ $item->unit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label>Ambil dari kondisi</label>
                                <select name="stock_condition" required>
                                    <option value="">Pilih kondisi</option>
                                    <option value="good">Baik</option>
                                    <option value="less_good">Kurang Baik</option>
                                    <option value="damaged">Rusak</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>Jumlah keluar</label>
                                <input type="number" step="0.01" min="0.01" name="qty" placeholder="Contoh: 2" required>
                            </div>
                            <div class="field">
                                <label>Dipakai untuk</label>
                                <input name="used_for" placeholder="Contoh: Bahan-bahan hari ini" required>
                            </div>
                            <div class="field">
                                <label>Bahan dipakai apa aja</label>
                                <textarea name="used_items" rows="2" placeholder="Contoh: tepung 2kg, gula 1kg" required></textarea>
                            </div>
                            <div class="field">
                                <label>Catatan</label>
                                <textarea name="notes" rows="2" placeholder="Opsional"></textarea>
                            </div>
                            <button class="btn btn-primary" type="submit">Catat Keluar</button>
                        </form>
                    </section>
                </div>
            @endif

            @if (($activeTab ?? '') === 'movement')
                <section class="panel">
                    <h3 class="section-title">Riwayat Barang Masuk/Keluar</h3>
                    <div class="table-wrap">
                        <table class="inventory-table">
                            <thead>
                                <tr><th>Waktu</th><th>Bahan</th><th>Tipe</th><th>Kondisi</th><th>Qty</th><th>Dipakai untuk</th><th>Catatan / Keterangan</th><th>User</th></tr>
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
                                        <td>{{ $m->usage_title ?: '-' }}</td>
                                        <td>{!! $m->notes ? nl2br(e($m->notes)) : '-' !!}</td>
                                        <td>{{ $m->user?->username ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="empty-state">Belum ada mutasi stok.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination-area">{{ $movements->links('components.pagination') }}</div>
                </section>
            @endif
        </main>
    </div>
    <script>
        (function () {
            const toggle = document.getElementById('sidebarToggle');
            const backdrop = document.getElementById('sidebarBackdrop');
            const openSidebar = () => { document.body.classList.remove('sidebar-collapsed'); document.body.classList.add('sidebar-open'); };
            const closeSidebar = () => { document.body.classList.add('sidebar-collapsed'); document.body.classList.remove('sidebar-open'); };
            if (window.innerWidth <= 1100) closeSidebar();
            toggle?.addEventListener('click', () => document.body.classList.contains('sidebar-collapsed') ? openSidebar() : closeSidebar());
            backdrop?.addEventListener('click', closeSidebar);
            window.addEventListener('resize', () => { if (window.innerWidth > 1100) document.body.classList.remove('sidebar-open'); });
        })();
    </script>
</body>
</html>

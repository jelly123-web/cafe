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
        .toolbar-actions { display:flex; align-items:center; gap:0.65rem; flex-wrap:wrap; justify-content:flex-end; }
        
        .primary-link { display:inline-flex; align-items:center; background:#e2b68c; color:#fff; text-decoration:none; padding:0.6rem 1.2rem; border-radius:12px; font-weight:600; border:none; cursor:pointer; transition: all 0.2s; }
        .primary-link:hover { background:#d4a373; transform: translateY(-1px); }

        .panel { background:var(--bg-card); padding:2rem; border-radius:20px; box-shadow:0 4px 15px var(--shadow); }
        .panel-head { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:1.5rem; padding-bottom:0.75rem; border-bottom:2px solid var(--accent); }
        .panel-head h2 { font-family:'Playfair Display', Georgia, serif; color:var(--primary); font-size:1.9rem; margin:0; }
        
        .table-responsive { width: 100%; overflow-x: auto; }
        table { width: 100%; min-width: 1280px; border-collapse: separate; border-spacing: 0; table-layout: fixed; }
        th, td { padding: 1.15rem 1.2rem; text-align: left; border-bottom: 1px solid rgba(215, 204, 200, 0.9); vertical-align: top; }
        th { font-weight: 800; color: var(--primary); background: var(--bg-main); white-space: nowrap; letter-spacing: 0.02em; font-size: 0.94rem; }
        tbody tr:hover { background: transparent; }
        th.col-banner, td.col-banner { width: 150px; }
        th.col-name, td.col-name { width: 320px; }
        th.col-type, td.col-type { width: 160px; }
        th.col-value, td.col-value { width: 190px; }
        th.col-scope, td.col-scope { width: 190px; }
        th.col-status, td.col-status { width: 110px; }
        th.col-period, td.col-period { width: 140px; }
        th.col-actions, td.col-actions { width: 120px; }
        .promo-name-cell { display: grid; gap: 0.35rem; min-width: 230px; }
        .promo-name-title { color: var(--primary); font-size: 1.05rem; font-weight: 700; line-height: 1.35; }
        .promo-name-desc { color: var(--text-muted); font-size: 0.92rem; line-height: 1.45; }
        .promo-scope-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 126px;
            padding: 0.42rem 0.9rem;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
            line-height: 1.25;
            text-align: center;
            border: 1px solid rgba(215, 204, 200, 0.95);
            background: #efe7df;
            color: var(--primary);
        }
        .promo-scope-chip.specific {
            border-color: #c9def5;
            background: #e7f3ff;
            color: #1565c0;
        }
        .promo-scope-meta { margin-top: 0.65rem; color: var(--text-muted); font-size: 0.82rem; line-height: 1.45; }
        .promo-period-cell { display: grid; gap: 0.28rem; min-width: 118px; }
        .promo-period-date { color: var(--text-main); font-size: 0.92rem; font-weight: 600; white-space: nowrap; }
        .promo-period-note { color: var(--text-muted); font-size: 0.8rem; }
        
        .tag { display:inline-block; padding:0.25rem 0.75rem; border-radius:50px; font-size:0.75rem; font-weight:600; background-color:#efebe9; color:var(--primary); }
        .tag-success { background-color:#e8f5e9; color:#558b2f; }
        .tag-danger { background-color:#ffebee; color:#c62828; }
        
        .promo-banner { width: 124px; height: 66px; object-fit: cover; border-radius: 12px; border: 1px solid var(--accent); display:block; }
        .promo-banner-empty { width:124px;height:66px;background:#f3ece6;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:0.74rem;color:#999;border:1px dashed var(--accent); }
        
        .actions { display:flex; flex-direction:column; align-items:flex-start; gap:0.7rem; white-space: nowrap; }
        .btn-edit { color:#c68b59; background:none; border:none; cursor:pointer; font-weight: 600; font-family: inherit; }
        .btn-delete { color:var(--loss); background: none; border: none; cursor: pointer; font-weight: 600; font-family: inherit; }

        /* Drawer Styles */
        .drawer-backdrop { position:fixed; inset:0; background:rgba(56, 37, 30, 0.32); backdrop-filter:blur(2px); z-index:1200; opacity:0; visibility:hidden; transition:0.2s ease; }
        .drawer-backdrop.open { opacity:1; visibility:visible; }
        .promo-drawer {
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
        .promo-drawer.open { transform:translateX(0); }
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
        
        .drawer-body { padding: 1.75rem; overflow-y: auto; flex: 1; }
        .drawer-foot { padding: 1.25rem 1.75rem; border-top: 1px solid var(--accent); display: flex; gap: 1rem; justify-content: flex-end; background: #fdfaf8; flex-shrink: 0; }
        
        .drawer-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
        .drawer-field { display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1rem; }
        .drawer-field.full { grid-column: 1 / -1; }
        .drawer-field label { font-size: 0.9rem; font-weight: 700; color: var(--text-main); }
        .drawer-field input, .drawer-field select, .drawer-field textarea { 
            width: 100%; padding: 0.75rem 1.15rem; border: 1.5px solid var(--accent); border-radius: 14px; background: #fff; color: var(--text-main); font-size: 1rem; outline: none; transition: all 0.2s;
        }
        .scope-picker {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 0.5rem;
            background: #fcf8f4;
            padding: 1rem;
            border-radius: 16px;
            border: 1.5px solid var(--accent);
        }
        .scope-column {
            min-width: 0;
            background: #fff;
            border: 1px solid rgba(215, 204, 200, 0.9);
            border-radius: 14px;
            overflow: hidden;
        }
        .scope-column-title {
            display: block;
            padding: 0.85rem 1rem;
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            color: var(--primary);
            background: #f6efe8;
            border-bottom: 1px solid rgba(215, 204, 200, 0.85);
        }
        .scope-list {
            max-height: 220px;
            overflow-y: auto;
            display: grid;
            gap: 0.65rem;
            padding: 0.9rem;
        }
        .scope-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.8rem 0.9rem;
            border-radius: 12px;
            border: 1px solid rgba(215, 204, 200, 0.85);
            background: #fffdfa;
            cursor: pointer;
            transition: border-color .18s ease, background .18s ease, transform .18s ease;
        }
        .scope-item:hover {
            border-color: rgba(212, 163, 115, 0.8);
            background: #fff8f1;
            transform: translateY(-1px);
        }
        .scope-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin: 0.1rem 0 0;
            accent-color: #d4a373;
            flex-shrink: 0;
        }
        .scope-item-body {
            display: grid;
            gap: 0.18rem;
            min-width: 0;
        }
        .scope-item-name {
            font-size: 0.94rem;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1.35;
            word-break: break-word;
        }
        .scope-item-meta {
            font-size: 0.8rem;
            color: var(--text-muted);
            line-height: 1.4;
        }
        .scope-help {
            display: block;
            margin-top: 0.7rem;
            color: var(--text-muted);
            font-size: 0.82rem;
            line-height: 1.55;
        }
        .btn-drawer-cancel { border: 1.5px solid var(--accent); background:#fff; color:var(--primary); border-radius:12px; padding:0.75rem 1.5rem; cursor:pointer; font-weight:700; transition: all 0.2s; }
        .drawer-error { background: #fff5f5; color: var(--loss); padding: 0.85rem 1.15rem; border-radius: 12px; border-left: 4px solid var(--loss); font-weight: 600; margin-top: 0.5rem; display: none; }
        .promo-type-chip { white-space: nowrap; }
        @media (max-width: 768px) {
            .scope-picker { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@section('title', 'Manajemen Promo')
@section('page_title', 'Manajemen Promo')
@section('page_description', 'Kelola diskon, penawaran beli 1 gratis 1, dan promo lainnya.')

@section('content')
    <div class="content-toolbar">
        <div></div>
        <div class="toolbar-actions">
            <button type="button" class="primary-link" id="btnOpenCreate">+ Tambah Promo</button>
        </div>
    </div>

    <div class="panel">
        <div class="panel-head">
            <h2>Daftar Promo</h2>
            <span id="promoCount">{{ $promos->total() }} promo terdaftar</span>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th class="col-banner">Banner</th>
                        <th class="col-name">Nama Promo</th>
                        <th class="col-type">Tipe</th>
                        <th class="col-value">Nilai / Syarat</th>
                        <th class="col-scope">Berlaku Untuk</th>
                        <th class="col-status">Status</th>
                        <th class="col-period">Periode</th>
                        <th class="col-actions">Aksi</th>
                    </tr>
                </thead>
                <tbody id="promoTableBody">
                    @forelse ($promos as $promo)
                        <tr data-promo-id="{{ $promo->id }}">
                            <td class="col-banner" data-field="banner">
                                @if ($promo->banner_path)
                                    <img src="{{ Storage::disk('public')->url($promo->banner_path) }}" class="promo-banner" alt="Banner">
                                @else
                                    <div class="promo-banner-empty">No Image</div>
                                @endif
                            </td>
                            <td class="col-name" data-field="name_desc">
                                <div class="promo-name-cell">
                                    <strong class="promo-name-title" data-val="name">{{ $promo->name }}</strong>
                                    <small class="promo-name-desc" data-val="description">{{ Str::limit($promo->description, 70) }}</small>
                                </div>
                            </td>
                            <td class="col-type" data-field="type">
                                <span class="tag promo-type-chip" data-val="type_label">
                                    {{ $promo->type === 'percentage' ? 'Diskon %' : ($promo->type === 'fixed_discount' ? 'Potongan Tetap' : ($promo->type === 'buy_x_get_y' ? 'Beli X Gratis Y' : 'Gratis Ongkir')) }}
                                </span>
                            </td>
                            <td class="col-value" data-field="value_condition">
                                <span data-val="value_label">
                                    @if ($promo->type === 'percentage')
                                        {{ number_format($promo->value, 0) }}%
                                    @elseif ($promo->type === 'fixed_discount')
                                        Rp {{ number_format($promo->value, 0, ',', '.') }}
                                    @elseif ($promo->type === 'buy_x_get_y')
                                        Beli {{ $promo->buy_qty }} Gratis {{ $promo->get_qty }}
                                    @else
                                        -
                                    @endif
                                </span>
                                @if ($promo->min_spend > 0)
                                    <br><small style="color:var(--text-muted);">Min. Rp {{ number_format($promo->min_spend, 0, ',', '.') }}</small>
                                @endif
                            </td>
                            <td class="col-scope" data-field="scope">
                                @if ($promo->applies_to === 'all')
                                    <span class="promo-scope-chip all">Semua Produk</span>
                                @else
                                    <span class="promo-scope-chip specific">Produk Tertentu</span>
                                    <div class="promo-scope-meta">
                                        <span data-val="menu_count">{{ $promo->menus->count() }}</span> Menu, <span data-val="package_count">{{ $promo->foodPackages->count() }}</span> Paket
                                    </div>
                                @endif
                            </td>
                            <td class="col-status" data-field="status">
                                <span class="tag {{ $promo->is_active ? 'tag-success' : 'tag-danger' }}" data-val="status_label">
                                    {{ $promo->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="col-period" data-field="period">
                                <div class="promo-period-cell" data-val="period_label">
                                    @if ($promo->start_at && $promo->end_at)
                                        <span class="promo-period-date">{{ $promo->start_at->format('d/m/y') }}</span>
                                        <span class="promo-period-date">{{ $promo->end_at->format('d/m/y') }}</span>
                                    @else
                                        <span class="promo-period-date">Selamanya</span>
                                        <span class="promo-period-note">Tanpa batas tanggal</span>
                                    @endif
                                </div>
                            </td>
                            <td class="col-actions">
                                <div class="actions">
                                    <button type="button" class="btn-edit" 
                                        data-id="{{ $promo->id }}"
                                        data-name="{{ $promo->name }}"
                                        data-description="{{ $promo->description }}"
                                        data-type="{{ $promo->type }}"
                                        data-applies-to="{{ $promo->applies_to }}"
                                        data-value="{{ (float) $promo->value }}"
                                        data-buy-qty="{{ $promo->buy_qty }}"
                                        data-get-qty="{{ $promo->get_qty }}"
                                        data-is-active="{{ $promo->is_active ? '1' : '0' }}"
                                        data-start-at="{{ $promo->start_at?->format('Y-m-d') }}"
                                        data-end-at="{{ $promo->end_at?->format('Y-m-d') }}"
                                        data-menu-ids="{{ json_encode($promo->menus->pluck('id')->all()) }}"
                                        data-package-ids="{{ json_encode($promo->foodPackages->pluck('id')->all()) }}"
                                    >Edit</button>
                                    <button type="button" class="btn-delete" data-id="{{ $promo->id }}">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyState">
                            <td colspan="8" style="text-align:center;padding:2rem;color:var(--text-muted);font-style:italic;">Belum ada promo terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 1.5rem;" id="promoPagination">
            {{ $promos->links('components.pagination') }}
        </div>
    </div>

    <!-- SIDE MODAL (DRAWER) -->
    <div id="drawerBackdrop" class="drawer-backdrop"></div>
    <aside id="promoDrawer" class="promo-drawer" aria-hidden="true">
        <div class="drawer-head">
            <h3 id="drawerTitle">Tambah Promo</h3>
            <button type="button" class="drawer-close" id="btnCloseDrawer">Tutup</button>
        </div>
        <form id="drawerForm" method="POST" enctype="multipart/form-data" style="display: contents;">
            @csrf
            <input type="hidden" name="_method" id="drawerMethod" value="POST" disabled>
            <div class="drawer-body">
                @include('superadmin.promos.form', ['menus' => $menus, 'packages' => $packages, 'promo' => new \App\Models\Promo()])
                <div id="drawerError" class="drawer-error"></div>
            </div>
            <div class="drawer-foot">
                <button type="button" class="btn-drawer-cancel" id="btnCancelDrawer">Batal</button>
                <button type="submit" class="primary-link" id="btnSubmitDrawer">Simpan Promo</button>
            </div>
        </form>
    </aside>

    <script>
        (function () {
            const drawer = document.getElementById('promoDrawer');
            const backdrop = document.getElementById('drawerBackdrop');
            const form = document.getElementById('drawerForm');
            const drawerTitle = document.getElementById('drawerTitle');
            const drawerMethod = document.getElementById('drawerMethod');
            const btnSubmit = document.getElementById('btnSubmitDrawer');
            const tableBody = document.getElementById('promoTableBody');
            const countEl = document.getElementById('promoCount');
            
            const getCsrfToken = () => document.querySelector('input[name="_token"]')?.value || '';
            const storeUrl = @json(route('superadmin.promos.store'));
            const baseUrl = @json(url('superadmin/promos'));

            const openDrawer = () => { drawer.classList.add('open'); backdrop.classList.add('open'); drawer.setAttribute('aria-hidden', 'false'); };
            const closeDrawer = () => { drawer.classList.remove('open'); backdrop.classList.remove('open'); drawer.setAttribute('aria-hidden', 'true'); };

            const resetForm = () => {
                form.reset();
                form.action = storeUrl;
                drawerMethod.disabled = true;
                drawerTitle.textContent = 'Tambah Promo';
                btnSubmit.textContent = 'Simpan Promo';
                document.getElementById('drawerError').style.display = 'none';
                
                // Reset custom checkboxes
                form.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
                
                // Trigger field toggles
                if (window.togglePromoFields) window.togglePromoFields('percentage');
                const appliesSelect = document.getElementById('f_applies');
                if (appliesSelect) appliesSelect.value = 'all';
                if (window.toggleScopeFields) window.toggleScopeFields('all');
            };

            document.getElementById('btnOpenCreate').addEventListener('click', () => {
                resetForm();
                openDrawer();
            });

            [document.getElementById('btnCloseDrawer'), document.getElementById('btnCancelDrawer'), backdrop].forEach(el => {
                el.addEventListener('click', closeDrawer);
            });

            const escapeHtml = (v) => String(v ?? '').replace(/[&<>"']/g, (m) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
            const formatRp = (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(v || 0));

            const bindActions = (root) => {
                root.querySelectorAll('.btn-edit').forEach(btn => {
                    btn.addEventListener('click', () => {
                        resetForm();
                        const id = btn.getAttribute('data-id');
                        drawerTitle.textContent = 'Edit Promo';
                        btnSubmit.textContent = 'Simpan Perubahan';
                        form.action = `${baseUrl}/${id}`;
                        drawerMethod.disabled = false;
                        drawerMethod.value = 'PUT';
                        
                        document.getElementById('f_name').value = btn.getAttribute('data-name');
                        document.getElementById('f_description').value = btn.getAttribute('data-description');
                        
                        const type = btn.getAttribute('data-type');
                        document.getElementById('f_type').value = type;
                        if (window.togglePromoFields) window.togglePromoFields(type);
                        
                        document.getElementById('f_value').value = btn.getAttribute('data-value');
                        document.getElementById('f_buy').value = btn.getAttribute('data-buy-qty');
                        document.getElementById('f_get').value = btn.getAttribute('data-get-qty');
                        
                        const appliesTo = btn.getAttribute('data-applies-to') || 'all';
                        document.getElementById('f_applies').value = appliesTo;
                        if (window.toggleScopeFields) window.toggleScopeFields(appliesTo);
                        
                        document.getElementById('f_status').value = btn.getAttribute('data-is-active');
                        document.getElementById('f_start').value = btn.getAttribute('data-start-at') || '';
                        document.getElementById('f_end').value = btn.getAttribute('data-end-at') || '';
                        
                        // Handle checkboxes
                        const menuIds = JSON.parse(btn.getAttribute('data-menu-ids') || '[]');
                        const packageIds = JSON.parse(btn.getAttribute('data-package-ids') || '[]');
                        
                        menuIds.forEach(mid => {
                            const cb = form.querySelector(`input[name="menu_ids[]"][value="${mid}"]`);
                            if (cb) cb.checked = true;
                        });
                        packageIds.forEach(pid => {
                            const cb = form.querySelector(`input[name="package_ids[]"][value="${pid}"]`);
                            if (cb) cb.checked = true;
                        });
                        window.updateScopeSelectionMeta?.();

                        openDrawer();
                    });
                });

                root.querySelectorAll('.btn-delete').forEach(btn => {
                    btn.addEventListener('click', async () => {
                        const id = btn.getAttribute('data-id');
                        if (!confirm('Hapus promo ini?')) return;
                        
                        const res = await fetch(`${baseUrl}/${id}`, {
                            method: 'POST',
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                            body: new FormData(Object.assign(document.createElement('form'), { innerHTML: '<input name="_method" value="DELETE">' }))
                        });

                        if (res.ok) {
                            tableBody.querySelector(`tr[data-promo-id="${id}"]`).remove();
                            if (!tableBody.querySelector('tr[data-promo-id]')) {
                                tableBody.innerHTML = '<tr id="emptyState"><td colspan="8" style="text-align:center;padding:2rem;color:var(--text-muted);font-style:italic;">Belum ada promo terdaftar.</td></tr>';
                            }
                            countEl.textContent = tableBody.querySelectorAll('tr[data-promo-id]').length + ' promo terdaftar';
                            window.showToast?.('Promo berhasil dihapus', 'success');
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

                    const promo = data.promo;
                    let row = tableBody.querySelector(`tr[data-promo-id="${promo.id}"]`);
                    const isNew = !row;

                    const typeLabels = {
                        'percentage': 'Diskon %',
                        'fixed_discount': 'Potongan Tetap',
                        'buy_x_get_y': 'Beli X Gratis Y',
                        'free_shipping': 'Gratis Ongkir'
                    };
                    
                    let valueLabel = '-';
                    if (promo.type === 'percentage') valueLabel = Number(promo.value).toFixed(0) + '%';
                    else if (promo.type === 'fixed_discount') valueLabel = formatRp(promo.value);
                    else if (promo.type === 'buy_x_get_y') valueLabel = `Beli ${promo.buy_qty} Gratis ${promo.get_qty}`;

                    const periodLabel = (promo.start_at_label && promo.end_at_label) 
                        ? `${promo.start_at_label} - ${promo.end_at_label}` 
                        : 'Selamanya';

                    const html = `
                        <td>
                            ${promo.banner_url ? `<img src="${escapeHtml(promo.banner_url)}" class="promo-banner" alt="Banner">` : `<div class="promo-banner-empty">No Image</div>`}
                        </td>
                        <td>
                            <div class="promo-name-cell">
                                <strong class="promo-name-title">${escapeHtml(promo.name)}</strong>
                                <small class="promo-name-desc">${escapeHtml(promo.description || '').substring(0, 70)}</small>
                            </div>
                        </td>
                        <td>
                            <span class="tag promo-type-chip">${typeLabels[promo.type] || promo.type}</span>
                        </td>
                        <td>
                            <span>${valueLabel}</span>
                        </td>
                        <td>
                            ${promo.applies_to === 'all' 
                                ? `<span class="promo-scope-chip all">Semua Produk</span>` 
                                : `<span class="promo-scope-chip specific">Produk Tertentu</span><div class="promo-scope-meta">${promo.menu_count} Menu, ${promo.package_count} Paket</div>`}
                        </td>
                        <td>
                            <span class="tag ${promo.is_active ? 'tag-success' : 'tag-danger'}">${promo.is_active ? 'Aktif' : 'Nonaktif'}</span>
                        </td>
                        <td>
                            ${promo.start_at_label && promo.end_at_label
                                ? `<div class="promo-period-cell"><span class="promo-period-date">${periodLabel.split(' - ')[0]}</span><span class="promo-period-date">${periodLabel.split(' - ')[1]}</span></div>`
                                : `<div class="promo-period-cell"><span class="promo-period-date">Selamanya</span><span class="promo-period-note">Tanpa batas tanggal</span></div>`}
                        </td>
                        <td>
                            <div class="actions">
                                <button type="button" class="btn-edit" 
                                    data-id="${promo.id}" data-name="${escapeHtml(promo.name)}"
                                    data-description="${escapeHtml(promo.description)}" data-type="${promo.type}"
                                    data-applies-to="${promo.applies_to}" data-value="${promo.value}"
                                    data-buy-qty="${promo.buy_qty}" data-get-qty="${promo.get_qty}"
                                    data-is-active="${promo.is_active ? '1' : '0'}"
                                    data-start-at="${promo.start_at || ''}" data-end-at="${promo.end_at || ''}"
                                    data-menu-ids='${JSON.stringify(promo.menu_ids)}'
                                    data-package-ids='${JSON.stringify(promo.package_ids)}'
                                >Edit</button>
                                <button type="button" class="btn-delete" data-id="${promo.id}">Hapus</button>
                            </div>
                        </td>
                    `;

                    if (isNew) {
                        const empty = document.getElementById('emptyState');
                        if (empty) empty.remove();
                        row = document.createElement('tr');
                        row.setAttribute('data-promo-id', promo.id);
                        tableBody.prepend(row);
                    }

                    row.innerHTML = html;
                    bindActions(row);
                    
                    countEl.textContent = tableBody.querySelectorAll('tr[data-promo-id]').length + ' promo terdaftar';
                    window.showToast?.(data.message, 'success');
                    closeDrawer();
                } catch (err) {
                    const errEl = document.getElementById('drawerError');
                    errEl.textContent = err.message;
                    errEl.style.display = 'block';
                    if (String(err.message).toLowerCase().includes('menu') || String(err.message).toLowerCase().includes('paket')) {
                        document.getElementById('field_scope')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                } finally {
                    btnSubmit.disabled = false;
                    btnSubmit.textContent = drawerMethod.disabled ? 'Simpan Promo' : 'Simpan Perubahan';
                }
            });

            bindActions(tableBody);
        })();
    </script>
@endsection

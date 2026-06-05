@extends('superadmin.layout')

@section('title', 'Manajemen Promo — cafecaf')
@section('page_title', 'Manajemen Promo')
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
    .primary-link:hover { background: var(--accent-dark); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(217, 119, 6, 0.25); }

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
    }
    .panel-head h2 i { color: var(--accent); font-size: 16px; }
    .panel-head span { font-size: 12px; color: var(--muted); font-weight: 600; }

    /* ===== PROMO LIST ===== */
    .promo-list { display: flex; flex-direction: column; gap: 0; }

    .promo-card {
      display: flex; gap: 16px; padding: 18px 24px;
      border-bottom: 1px solid var(--border-light);
      align-items: center; transition: all var(--transition);
      animation: cardIn 0.3s ease;
    }
    @keyframes cardIn {
      from { opacity: 0; transform: translateY(8px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .promo-card:last-child { border-bottom: none; }
    .promo-card:hover { background: #FAFBFC; }

    .promo-thumb {
      width: 72px; height: 72px; border-radius: var(--radius-sm);
      object-fit: cover; background-color: var(--bg); flex-shrink: 0;
      border: 1px solid var(--border-light); transition: transform 0.3s ease;
    }
    .promo-card:hover .promo-thumb { transform: scale(1.05); }

    .promo-meta { flex: 1; display: flex; flex-direction: column; gap: 4px; min-width: 0; }
    .promo-meta h3 { font-size: 14px; font-weight: 700; color: var(--fg); margin: 0; letter-spacing: -0.2px; }
    .promo-meta p { color: var(--muted); font-size: 12px; margin: 0; font-weight: 500; }

    .promo-pricing { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 2px; }

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

    .promo-item-list { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 4px; }
    .promo-item-tag {
      display: inline-flex; align-items: center; padding: 2px 8px;
      border-radius: 6px; font-size: 11px; font-weight: 600;
      background: var(--bg); color: var(--fg-secondary);
      border: 1px solid var(--border-light);
    }

    /* ===== ACTIONS ===== */
    .actions { display: flex; gap: 12px; align-items: center; flex-shrink: 0; }
    .actions button {
      border: none; background: transparent; cursor: pointer; padding: 7px 0;
      font-weight: 700; font-size: 12px; text-decoration: none; font-family: var(--font);
      transition: all var(--transition); display: inline-flex; align-items: center; gap: 4px;
    }
    .btn-open-edit { color: var(--accent); }
    .btn-open-edit:hover { color: var(--accent-dark); text-decoration: underline; }
    .btn-delete-promo { color: var(--red); }
    .btn-delete-promo:hover { color: #991b1b; text-decoration: underline; }

    /* ===== EMPTY STATE ===== */
    .empty-state { padding: 40px 24px; text-align: center; color: var(--muted); font-size: 14px; background: #fff; border-radius: var(--radius-lg); }
    .empty-state::before { content: '\f56e'; font-family: 'Font Awesome 6 Free'; font-weight: 900; display: block; font-size: 32px; margin-bottom: 8px; color: var(--border); }

    /* ===== DRAWER ===== */
    .drawer-backdrop { position:fixed; inset:0; background:rgba(0,0,0,0.3); backdrop-filter:blur(2px); z-index:1200; opacity:0; visibility:hidden; transition:all 0.25s ease; }
    .drawer-backdrop.open { opacity: 1; visibility: visible; }

    .promo-drawer {
      position: fixed; top: 0; right: 0;
      width: min(580px, 95vw); height: 100vh;
      background: var(--white); z-index: 1201;
      transform: translateX(102%);
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: var(--shadow-xl);
      display: flex; flex-direction: column;
    }
    .promo-drawer.open { transform: translateX(0); }

    .drawer-head {
      padding: 18px 24px; border-bottom: 1px solid var(--border);
      display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;
    }
    .drawer-head h3 { margin: 0; font-size: 16px; font-weight: 800; color: var(--fg); display: flex; align-items: center; gap: 8px; }
    .drawer-head h3 i { color: var(--accent); }
    .drawer-close { border: 1px solid var(--border); background: var(--white); color: var(--fg-secondary); border-radius: 12px; padding: 8px 14px; cursor: pointer; font-weight: 700; font-size: 12px; font-family: var(--font); transition: all var(--transition); }
    .drawer-close:hover { border-color: var(--red); color: var(--red); background: var(--red-light); }

    .drawer-body { padding: 24px; overflow-y: auto; flex: 1; min-height: 0; scrollbar-width: thin; scrollbar-color: var(--border) transparent; }
    .drawer-body::-webkit-scrollbar { width: 5px; }
    .drawer-body::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }

    .drawer-foot { padding: 20px 24px; border-top: 1px solid var(--border); display: flex; gap: 12px; justify-content: flex-end; background: #FAFBFC; flex-shrink: 0; }
    .btn-drawer-cancel { border: 1.5px solid var(--border); background: var(--white); color: var(--fg-secondary); border-radius: var(--radius-sm); padding: 10px 20px; cursor: pointer; font-weight: 700; font-size: 13px; font-family: var(--font); transition: all var(--transition); }
    .btn-drawer-cancel:hover { border-color: var(--red); color: var(--red); background: var(--red-light); }

    .drawer-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
    .drawer-field { display: flex; flex-direction: column; gap: 6px; }
    .drawer-field.full { grid-column: 1 / -1; }
    .drawer-field label { font-size: 12px; font-weight: 700; color: var(--fg-secondary); text-transform: uppercase; letter-spacing: 0.5px; }
    .drawer-field input, .drawer-field select, .drawer-field textarea { width: 100%; padding: 10px 14px; border: 1.5px solid var(--border); border-radius: var(--radius-sm); background: var(--white); color: var(--fg); font-size: 14px; font-weight: 500; outline: none; transition: all var(--transition); font-family: var(--font); -webkit-appearance: none; }
    .drawer-field input:focus, .drawer-field select:focus, .drawer-field textarea:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1); }
    
    .scope-picker { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 4px; background: var(--bg); padding: 1rem; border-radius: 16px; border: 1.5px solid var(--border); }
    .scope-column { min-width: 0; background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
    .scope-column-title { display: block; padding: 0.85rem 1rem; font-size: 11px; font-weight: 800; letter-spacing: 0.04em; color: var(--fg-secondary); background: #f6f8fa; border-bottom: 1px solid var(--border); text-transform: uppercase; }
    .scope-list { max-height: 220px; overflow-y: auto; display: grid; gap: 0.65rem; padding: 0.9rem; }
    .scope-item { display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.8rem 0.9rem; border-radius: 12px; border: 1px solid var(--border); background: #fff; cursor: pointer; transition: all .18s ease; }
    .scope-item:hover { border-color: var(--accent); background: var(--accent-light); }
    .scope-item input[type="checkbox"] { width: 18px; height: 18px; margin: 0.1rem 0 0; accent-color: var(--accent); flex-shrink: 0; }
    .scope-item-name { font-size: 13px; font-weight: 700; color: var(--fg); line-height: 1.35; }
    .scope-item-meta { font-size: 11px; color: var(--muted); }
    .drawer-error { background: #fff5f5; color: var(--red); padding: 0.85rem 1.15rem; border-radius: 12px; border-left: 4px solid var(--red); font-weight: 600; margin-top: 0.5rem; display: none; }
    
    /* ===== CATEGORY FILTER BAR ===== */
    .category-filter-bar {
      display: flex;
      gap: 12px;
      margin-bottom: 20px;
      overflow-x: auto;
      padding-bottom: 8px;
      scrollbar-width: none;
    }
    .category-filter-bar::-webkit-scrollbar { display: none; }

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

    .filter-pill.active i { opacity: 1; }

    .filter-pill span {
      font-size: 12px;
      margin-left: 2px;
      opacity: 0.7;
      font-weight: 500;
    }

    .filter-pill.active span { opacity: 0.9; }

    .promo-date-badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 3px 10px;
      border-radius: var(--radius-full);
      font-size: 11px;
      font-weight: 600;
      background: var(--bg);
      color: var(--fg-secondary);
      border: 1px solid var(--border-light);
    }
    .promo-date-badge i { font-size: 10px; }

    </style>
@endpush

@section('content')
    <!-- TOOLBAR -->
    <div class="content-toolbar fade-in">
        <form method="GET" action="{{ route('superadmin.promos.index') }}" class="search-box" id="promoSearchForm" onsubmit="return false;">
            <input type="text" name="search" id="promoSearchInput" placeholder="Cari promo..." value="{{ request('search') }}" autocomplete="off">
            <button type="button" id="promoSearchBtn"><i class="fas fa-search"></i> Cari</button>
        </form>
        <div class="toolbar-actions">
            <form method="POST" action="{{ route('superadmin.promos.destroy-all') }}" onsubmit="return confirm('Hapus semua promo? Tindakan ini tidak bisa dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="danger-link"><i class="fas fa-trash-can"></i> Hapus Semua Promo</button>
            </form>
            <button type="button" class="primary-link" id="btnOpenCreate"><i class="fas fa-plus"></i> Tambah Promo</button>
        </div>
    </div>

    <!-- PANEL -->
    <div class="panel fade-in">
        <div class="panel-head">
            <h2><i class="fas fa-box-open" style="color: var(--accent);"></i> Daftar Promo</h2>
            <span id="promoCount">{{ $promos->total() }} promo</span>
        </div>

        <div class="promo-list" id="promoList">
            @forelse ($promos as $promo)
                @php
                    $bannerImage = $promo->banner_path
                        ? (Storage::disk('public')->exists($promo->banner_path) ? Storage::disk('public')->url($promo->banner_path) : asset('images/menu-placeholder.svg'))
                        : asset('images/menu-placeholder.svg');
                    
                    $valLabel = '-';
                    if ($promo->type === 'percentage') $valLabel = number_format($promo->value, 0) . '%';
                    else if ($promo->type === 'fixed_discount') $valLabel = 'Rp ' . number_format($promo->value, 0, ',', '.');
                    else if ($promo->type === 'buy_x_get_y') $valLabel = "Beli {$promo->buy_qty} Gratis {$promo->get_qty}";
                @endphp
                <div class="promo-card" data-promo-id="{{ $promo->id }}">
                    <img class="promo-thumb" src="{{ $bannerImage }}" alt="{{ $promo->name }}">
                    <div class="promo-meta">
                        <div class="promo-pricing">
                            <span class="tag tag-success">{{ $valLabel }}</span>
                            @if($promo->min_spend > 0)
                                <span class="tag tag-free"><i class="fas fa-gift" style="margin-right:3px;"></i> Min. Rp {{ number_format($promo->min_spend, 0, ',', '.') }}</span>
                            @endif
                            <span class="promo-date-badge"><i class="far fa-calendar-alt"></i> {{ $promo->start_at?->format('d/m/y') ?? '—' }} s/d {{ $promo->end_at?->format('d/m/y') ?? '—' }}</span>
                        </div>
                        <div class="promo-item-list">
                            @if($promo->applies_to === 'all')
                                <span class="promo-item-tag">Semua Produk</span>
                            @else
                                @foreach($promo->menus as $m) <span class="promo-item-tag">({{ $promo->type === 'buy_x_get_y' ? $promo->buy_qty : '1' }}x) {{ $m->name }}</span> @endforeach
                                @foreach($promo->foodPackages as $p) <span class="promo-item-tag">({{ $promo->type === 'buy_x_get_y' ? $promo->buy_qty : '1' }}x) {{ $p->name }}</span> @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="actions">
                        <button type="button" class="btn-open-edit" 
                            data-id="{{ $promo->id }}"
                            data-name="{{ $promo->name }}"
                            data-description="{{ $promo->description }}"
                            data-type="{{ $promo->type }}"
                            data-applies-to="{{ $promo->applies_to }}"
                            data-value="{{ (float) $promo->value }}"
                            data-min-spend="{{ (float) $promo->min_spend }}"
                            data-buy-qty="{{ $promo->buy_qty }}"
                            data-get-qty="{{ $promo->get_qty }}"
                            data-is-active="{{ $promo->is_active ? '1' : '0' }}"
                            data-start-at="{{ $promo->start_at?->format('Y-m-d') }}"
                            data-end-at="{{ $promo->end_at?->format('Y-m-d') }}"
                            data-image-url="{{ $bannerImage }}"
                            data-menu-ids="{{ json_encode($promo->menus->pluck('id')->all()) }}"
                            data-package-ids="{{ json_encode($promo->foodPackages->pluck('id')->all()) }}"
                        ><i class="fas fa-pen"></i> Edit</button>
                        <button type="button" class="btn-delete-promo" data-id="{{ $promo->id }}"><i class="fas fa-trash"></i> Hapus</button>
                    </div>
                </div>
            @empty
                <div class="empty-state" id="emptyState"><em>Belum ada promo terdaftar.</em></div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="menu-pagination">
            {{ $promos->links('components.pagination') }}
        </div>
    </div>

    <!-- SIDE MODAL (DRAWER) -->
    <div id="drawerBackdrop" class="drawer-backdrop"></div>
    <aside id="promoDrawer" class="promo-drawer" aria-hidden="true">
        <div class="drawer-head">
            <h3 id="drawerTitle"><i class="fas fa-plus-circle"></i> Tambah Promo</h3>
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
@endsection

@push('scripts')
    <script>
        (function () {
            const drawer = document.getElementById('promoDrawer');
            const backdrop = document.getElementById('drawerBackdrop');
            const form = document.getElementById('drawerForm');
            const drawerTitle = document.getElementById('drawerTitle');
            const drawerMethod = document.getElementById('drawerMethod');
            const btnSubmit = document.getElementById('btnSubmitDrawer');
            const list = document.getElementById('promoList');
            const countEl = document.getElementById('promoCount');
            
            const getCsrfToken = () => document.querySelector('input[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const storeUrl = @json(route('superadmin.promos.store'));
            const baseUrl = @json(url('superadmin/promos'));
            const defaultImage = @json(asset('images/menu-placeholder.svg'));

            const openDrawer = () => { drawer.classList.add('open'); backdrop.classList.add('open'); drawer.setAttribute('aria-hidden', 'false'); document.body.style.overflow='hidden'; };
            const closeDrawer = () => { drawer.classList.remove('open'); backdrop.classList.remove('open'); drawer.setAttribute('aria-hidden', 'true'); document.body.style.overflow=''; };

            const resetForm = () => {
                form.reset();
                form.action = storeUrl;
                drawerMethod.disabled = true;
                drawerTitle.innerHTML = '<i class="fas fa-plus-circle"></i> Tambah Promo';
                btnSubmit.innerHTML = 'Simpan Promo';
                document.getElementById('drawerError').style.display = 'none';
                form.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
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
            const formatMoney = (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(v || 0));

            const buildCardHtml = (p) => {
                const img = escapeHtml(p.banner_url || defaultImage);
                let valLabel = '-';
                if (p.type === 'percentage') valLabel = Number(p.value).toFixed(0) + '%';
                else if (p.type === 'fixed_discount') valLabel = formatMoney(p.value);
                else if (p.type === 'buy_x_get_y') valLabel = `Beli ${p.buy_qty} Gratis ${p.get_qty}`;

                const scopeTags = p.applies_to === 'all' 
                    ? '<span class="promo-item-tag">Semua Produk</span>'
                    : (p.items || []).map(i => `<span class="promo-item-tag">(1x) ${escapeHtml(i.name)}</span>`).join('');

                return `
                    <img class="promo-thumb" src="${img}" alt="${escapeHtml(p.name)}">
                    <div class="promo-meta">
                        <h3>${escapeHtml(p.name)}</h3>
                        <div class="promo-pricing">
                            <span class="tag tag-success">${valLabel}</span>
                            ${p.min_spend > 0 ? `<span class="tag tag-free"><i class="fas fa-gift" style="margin-right:3px;"></i> Min. ${formatMoney(p.min_spend)}</span>` : ''}
                        </div>
                        <div class="promo-item-list">${scopeTags}</div>
                    </div>
                    <div class="actions">
                        <button type="button" class="btn-open-edit" 
                            data-id="${p.id}" data-name="${escapeHtml(p.name)}"
                            data-description="${escapeHtml(p.description)}" data-type="${p.type}"
                            data-applies-to="${p.applies_to}" data-value="${p.value}"
                            data-min-spend="${p.min_spend}" data-buy-qty="${p.buy_qty}" data-get-qty="${p.get_qty}"
                            data-is-active="${p.is_active ? '1' : '0'}"
                            data-start-at="${p.start_at || ''}" data-end-at="${p.end_at || ''}"
                            data-image-url="${img}"
                            data-menu-ids='${JSON.stringify(p.menu_ids)}'
                            data-package-ids='${JSON.stringify(p.package_ids)}'
                        ><i class="fas fa-pen"></i> Edit</button>
                        <button type="button" class="btn-delete-promo" data-id="${p.id}"><i class="fas fa-trash"></i> Hapus</button>
                    </div>
                `;
            };

            const bindActions = (root) => {
                root.querySelectorAll('.btn-open-edit').forEach(btn => {
                    btn.addEventListener('click', () => {
                        resetForm();
                        const id = btn.getAttribute('data-id');
                        drawerTitle.innerHTML = '<i class="fas fa-pen-to-square"></i> Edit Promo';
                        btnSubmit.innerHTML = 'Simpan Perubahan';
                        form.action = `${baseUrl}/${id}`;
                        drawerMethod.disabled = false;
                        drawerMethod.value = 'PUT';
                        document.getElementById('f_name').value = btn.getAttribute('data-name');
                        document.getElementById('f_description').value = btn.getAttribute('data-description');
                        const type = btn.getAttribute('data-type');
                        document.getElementById('f_type').value = type;
                        if (window.togglePromoFields) window.togglePromoFields(type);
                        document.getElementById('f_value').value = btn.getAttribute('data-value');
                        document.getElementById('f_min').value = btn.getAttribute('data-min-spend');
                        document.getElementById('f_buy').value = btn.getAttribute('data-buy-qty');
                        document.getElementById('f_get').value = btn.getAttribute('data-get-qty');
                        const appliesTo = btn.getAttribute('data-applies-to') || 'all';
                        document.getElementById('f_applies').value = appliesTo;
                        if (window.toggleScopeFields) window.toggleScopeFields(appliesTo);
                        document.getElementById('f_status').value = btn.getAttribute('data-is-active');
                        document.getElementById('f_start').value = btn.getAttribute('data-start-at') || '';
                        document.getElementById('f_end').value = btn.getAttribute('data-end-at') || '';
                        const menuIds = JSON.parse(btn.getAttribute('data-menu-ids') || '[]');
                        const packageIds = JSON.parse(btn.getAttribute('data-package-ids') || '[]');
                        menuIds.forEach(mid => { const cb = form.querySelector(`input[name="menu_ids[]"][value="${mid}"]`); if (cb) cb.checked = true; });
                        packageIds.forEach(pid => { const cb = form.querySelector(`input[name="package_ids[]"][value="${pid}"]`); if (cb) cb.checked = true; });
                        window.updateScopeSelectionMeta?.();
                        openDrawer();
                    });
                });
                root.querySelectorAll('.btn-delete-promo').forEach(btn => {
                    btn.addEventListener('click', async () => {
                        const id = btn.getAttribute('data-id');
                        if (!confirm('Hapus promo ini?')) return;
                        const payload = new FormData();
                        payload.append('_method', 'DELETE');
                        payload.append('_token', getCsrfToken());
                        const res = await fetch(`${baseUrl}/${id}`, {
                            method: 'POST',
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                            body: payload
                        });
                        if (res.ok) {
                            const card = list.querySelector(`div[data-promo-id="${id}"]`);
                            if (card) card.remove();
                            if (!list.querySelector('div[data-promo-id]')) list.innerHTML = '<div class="empty-state" id="emptyState"><em>Belum ada promo terdaftar.</em></div>';
                            countEl.textContent = list.querySelectorAll('div[data-promo-id]').length + ' promo';
                            window.showToast?.('Promo berhasil dihapus', 'success');
                        }
                    });
                });
            };

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = 'Menyimpan...';
                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                        body: new FormData(form)
                    });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Terjadi kesalahan');
                    const p = data.promo;
                    let card = list.querySelector(`div[data-promo-id="${p.id}"]`);
                    const isNew = !card;
                    if (isNew) {
                        const empty = document.getElementById('emptyState');
                        if (empty) empty.remove();
                        card = document.createElement('div');
                        card.className = 'promo-card';
                        card.setAttribute('data-promo-id', p.id);
                        list.prepend(card);
                    }
                    card.innerHTML = buildCardHtml(p);
                    bindActions(card);
                    countEl.textContent = list.querySelectorAll('div[data-promo-id]').length + ' promo';
                    window.showToast?.(data.message, 'success');
                    closeDrawer();
                } catch (err) {
                    document.getElementById('drawerError').textContent = err.message;
                    document.getElementById('drawerError').style.display = 'block';
                } finally {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = 'Simpan Promo';
                }
            });

            closeDrawer();
            window.addEventListener('pageshow', closeDrawer);
            bindActions(list);

            /* ===== LIVE SEARCH ===== */
            const searchInput = document.getElementById('promoSearchInput');
            if (searchInput && list) {
                searchInput.addEventListener('input', (e) => {
                    const query = e.target.value.toLowerCase().trim();
                    const cards = list.querySelectorAll('.promo-card');
                    let hasVisible = false;

                    cards.forEach(card => {
                        const name = card.querySelector('.promo-title, h3')?.textContent.toLowerCase() || '';
                        
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
                            emptyMsg.innerHTML = `<em>Tidak ada promo ditemukan untuk "${escapeHtml(query)}"</em>`;
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

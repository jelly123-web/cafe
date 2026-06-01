@extends('kitchen.layout')

@section('title', ($cafeBrand['name'] ?? config('app.name')) . ' - Menu Habis Dapur')

@push('head')
    <style>
        .kitchen-shell { max-width: 100%; }
        .panel { background: var(--bg-card); border: 1px solid var(--accent); border-radius: 20px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; box-shadow: 0 4px 15px var(--shadow); }
        .page-title { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.5rem; margin: 0 0 0.5rem; }
        .page-desc { color: var(--text-muted); font-size: 0.95rem; margin: 0; }
        .alert-info { background: #E3F2FD; color: #5D4037; border: 1px solid #BBDEFB; border-left: 5px solid var(--secondary); padding: 0.85rem 1.25rem; border-radius: 14px; margin-top: 1rem; font-size: 0.9rem; }
        .table-wrap { overflow-x: auto; margin: 0; }
        .menu-table { width: 100%; border-collapse: collapse; }
        .menu-table th, .menu-table td { padding: 1rem 0.75rem; border-bottom: 1px dashed var(--accent); vertical-align: middle; text-align: left; font-size: 0.95rem; }
        .menu-table th { background: var(--bg-main); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); font-weight: 600; border-bottom: 2px solid var(--highlight); }
        .menu-table tbody tr:hover { background-color: #FFFAF5; }
        .menu-table tbody tr:last-child td { border-bottom: none; }
        .menu-name { font-weight: 600; color: var(--primary); font-family: 'Playfair Display', Georgia, serif; font-size: 1.05rem; }
        .menu-category { color: var(--text-muted); font-size: 0.9rem; }
        .tag { display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.85rem; border-radius: 999px; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.5px; }
        .tag-available { background: #E8F5E9; color: #558B2F; }
        .tag-sold-out { background: #FFEBEE; color: #C62828; }
        .tag::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }
        .tag-available::before { background-color: #81C784; }
        .tag-sold-out::before { background-color: #E57373; }
        .btn { border: 1px solid transparent; border-radius: 10px; padding: 0.5rem 1rem; cursor: pointer; font-weight: 700; font-family: inherit; font-size: 0.85rem; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 0.4rem; }
        .btn-danger { background: transparent; color: var(--loss); border-color: #FFCDD2; }
        .btn-danger:hover { background: #FFEBEE; border-color: var(--loss); }
        .btn-success { background: transparent; color: var(--profit); border-color: #C8E6C9; }
        .btn-success:hover { background: #E8F5E9; border-color: var(--profit); }
        .empty-state { color: var(--text-muted); font-style: italic; text-align: center; padding: 2.5rem 1rem; }
        .pagination-area { margin-top: 1.5rem; }
        @media (max-width: 768px) {
            .page-title { font-size: 1.3rem; }
            .panel { padding: 1.25rem; }
            .menu-table th, .menu-table td { padding: 0.75rem 0.5rem; font-size: 0.85rem; }
            .menu-name { font-size: 0.95rem; }
        }
    </style>
@endpush

@section('content')
    <div class="kitchen-shell">
        <section class="panel">
            <h2 class="page-title">Menandai Menu Habis</h2>
            <p class="page-desc">Kelola ketersediaan bahan baku di dapur.</p>
            <div class="alert-info">
                Jika menu ditandai <strong>habis</strong>, menu tersebut akan otomatis disembunyikan dan tidak bisa dipesan oleh pelanggan di halaman pemesanan.
            </div>
        </section>

        <section class="panel">
            <div class="table-wrap">
                <table class="menu-table">
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="kitchenMenusTbody">
                        @include('kitchen.partials.menu_rows', ['menus' => $menus])
                    </tbody>
                </table>
            </div>
            <div class="pagination-area" id="kitchenMenusPagination">{{ $menus->links('components.pagination') }}</div>
        </section>
    </div>
    <script>
        (function () {
            const tbody = document.getElementById('kitchenMenusTbody');
            const paginationWrap = document.getElementById('kitchenMenusPagination');
            if (!tbody || !paginationWrap) return;

            const currentPage = Number(@json((int) request()->query('page', 1)));
            let syncing = false;
            const sync = async () => {
                if (syncing) return;
                syncing = true;
                try {
                    const url = "{{ route('kitchen.menus.live') }}" + "?page=" + encodeURIComponent(currentPage);
                    const res = await fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin',
                    });
                    if (!res.ok) return;
                    const payload = await res.json();
                    tbody.innerHTML = payload.html || '';
                    paginationWrap.innerHTML = payload.pagination || '';
                } catch (e) {
                } finally {
                    syncing = false;
                }
            };

            tbody.addEventListener('submit', async (e) => {
                const form = e.target;
                if (!(form instanceof HTMLFormElement)) return;
                if (!form.matches('.js-toggle-menu-form')) return;
                e.preventDefault();

                const data = new FormData(form);
                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        body: data,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    });
                    const payload = await res.json();
                    if (!res.ok || payload.ok === false) throw new Error(payload.message || 'Gagal update menu.');
                    if (window.showToast) window.showToast(payload.message || 'Status menu diperbarui.', 'success');
                    await sync();
                } catch (err) {
                    if (window.showToast) window.showToast(err.message || 'Gagal update menu.', 'error');
                }
            });

            sync();
            setInterval(() => {
                if (document.visibilityState === 'visible') {
                    sync();
                }
            }, 4000);
            window.addEventListener('focus', sync);
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') sync();
            });
        })();
    </script>
@endsection

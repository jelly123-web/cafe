@extends('kitchen.layout')

@section('title', ($cafeBrand['name'] ?? config('app.name')) . ' - Menu Habis Dapur')

@push('head')
    <style>
        .page-body { padding: 0; }
        .page-shell {
            max-width: 1400px;
            margin: 0 auto;
            padding: 28px 32px;
        }

        .dashboard-topbar {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px 28px;
            margin-bottom: 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            box-shadow: var(--shadow-xs);
        }

        .dashboard-topbar h1 {
            font-size: 22px;
            font-weight: 900;
            color: var(--fg);
            margin: 0 0 4px;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: -0.3px;
        }

        .dashboard-topbar h1 i { color: var(--red); }

        .dashboard-topbar p {
            font-size: 13px;
            color: var(--muted);
            margin: 0;
            font-weight: 500;
        }

        .live-indicator {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: var(--radius-full);
            background: var(--green-light);
            color: var(--green);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.3px;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--green);
            animation: dotPulse 2s infinite;
        }

        @keyframes dotPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

        .alert-box {
            padding: 16px 22px;
            border-radius: var(--radius-md);
            margin-bottom: 24px;
            font-weight: 600;
            font-size: 13px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            box-shadow: var(--shadow-xs);
        }

        .alert-info-modern {
            background: var(--blue-light);
            border: 1px solid #BFDBFE;
            color: #1E40AF;
        }

        .alert-info-modern i { margin-top: 2px; color: var(--blue); }

        .section-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-xs);
        }

        .section-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 24px 26px 20px;
            border-bottom: 1px solid var(--border);
        }

        .section-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
            font-weight: 900;
            color: var(--fg);
        }

        .section-card-title i { color: var(--accent); }
        .section-card-body { overflow-x: auto; }

        .data-table { width: 100%; border-collapse: collapse; }
        .data-table thead th {
            padding: 16px 28px;
            background: #FBFBFC;
            border-bottom: 1px solid var(--border);
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            text-align: left;
            white-space: nowrap;
        }

        .data-table tbody td {
            padding: 18px 28px;
            border-bottom: 1px solid var(--border-light);
            vertical-align: middle;
            font-size: 14px;
            color: var(--fg-secondary);
            font-weight: 500;
        }

        .data-table tbody tr:last-child td { border-bottom: none; }
        .data-table tbody tr:hover { background: var(--bg); }
        .data-table tbody tr.row-sold-out { opacity: 0.65; }
        .data-table tbody tr.row-sold-out:hover { background: var(--red-light); }

        .cell-menu {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .menu-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--accent-light);
            color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .menu-icon.sold-out {
            background: var(--red-light);
            color: var(--red);
        }

        .menu-name {
            font-size: 15px;
            font-weight: 800;
            color: var(--fg);
        }

        .cell-category-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 12px;
            border-radius: var(--radius-full);
            background: #F3F4F6;
            color: var(--fg-secondary);
            font-size: 12px;
            font-weight: 700;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: var(--radius-full);
            font-size: 12px;
            font-weight: 800;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
        }

        .status-badge.available { background: var(--green-light); color: var(--green); }
        .status-badge.sold-out { background: var(--red-light); color: var(--red); }
        .status-badge.sold-out .status-dot { animation: dotPulse 1s infinite; }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 18px;
            border-radius: var(--radius-md);
            font-weight: 800;
            font-size: 12px;
            cursor: pointer;
            transition: all var(--transition);
            font-family: var(--font);
            border: 1.5px solid transparent;
        }

        .btn-action:hover { transform: translateY(-1px); }
        .btn-sold-out {
            background: var(--red-light);
            color: var(--red);
            border-color: rgba(220, 38, 38, 0.2);
        }

        .btn-sold-out:hover {
            background: var(--red);
            color: var(--white);
            border-color: var(--red);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
        }

        .btn-available {
            background: var(--green-light);
            color: var(--green);
            border-color: rgba(5, 150, 105, 0.2);
        }

        .btn-available:hover {
            background: var(--green);
            color: var(--white);
            border-color: var(--green);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
        }

        .pagination-area {
            padding: 20px 28px;
            border-top: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .pagination-area nav,
        .pagination-area .pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            flex-wrap: wrap;
        }

        .pagination-area a,
        .pagination-area span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            border: 1px solid var(--border);
            color: var(--fg-secondary);
            padding: 0 10px;
            background: var(--white);
            transition: all var(--transition);
            font-family: var(--font);
            cursor: pointer;
        }

        .pagination-area a:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-light); }
        .pagination-area .active,
        .pagination-area [aria-current="page"] span { background: var(--accent); border-color: var(--accent); color: white; }
        .pagination-area .disabled,
        .pagination-area [aria-disabled="true"] span { opacity: 0.35; pointer-events: none; }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            min-height: 220px;
            color: var(--muted);
            text-align: center;
            padding: 40px 24px;
        }

        .empty-state i { font-size: 44px; color: #E5E7EB; }
        .empty-state strong { font-size: 15px; color: var(--fg-secondary); }
        .empty-state span { font-size: 13px; max-width: 300px; }

        @media (max-width: 768px) {
            .page-shell { padding: 16px; }
            .dashboard-topbar { flex-direction: column; align-items: flex-start; padding: 20px; }
            .data-table { min-width: 600px; }
            .data-table thead th, .data-table tbody td { padding: 14px 18px; }
        }
    </style>
@endpush

@section('content')
    <div class="page-shell">
        <div class="dashboard-topbar">
            <div>
                <h1><i class="fas fa-ban"></i> Menandai Menu Habis</h1>
                <p>Kelola ketersediaan bahan baku di dapur.</p>
            </div>
            <div class="live-indicator">
                <span class="live-dot"></span> Auto-sync
            </div>
        </div>

        <div class="alert-box alert-info-modern">
            <i class="fas fa-circle-info"></i>
            <div>
                Jika menu ditandai <strong>habis</strong>, menu tersebut akan otomatis disembunyikan dan tidak bisa dipesan oleh pelanggan di halaman pemesanan.
            </div>
        </div>

        <div class="section-card">
            <div class="section-card-header">
                <div class="section-card-title">
                    <i class="fas fa-utensils"></i> Daftar Menu
                </div>
            </div>
            <div class="section-card-body">
                <table class="data-table">
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

            <div class="pagination-area" id="kitchenMenusPagination">
                {{ $menus->links('components.pagination') }}
            </div>
        </div>
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

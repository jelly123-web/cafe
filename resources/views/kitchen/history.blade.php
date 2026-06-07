@extends('kitchen.layout')

@section('title', ($cafeBrand['name'] ?? config('app.name')) . ' - Riwayat Pesanan Dapur')

@push('head')
    <style>
        .page-body {
            padding: 0;
        }

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

        .dashboard-topbar h1 i { color: var(--accent); }

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

        @keyframes dotPulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        .alert-box {
            padding: 14px 20px;
            border-radius: var(--radius-md);
            margin-bottom: 24px;
            font-weight: 600;
            font-size: 13px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            box-shadow: var(--shadow-xs);
        }

        .alert-warn-modern {
            background: #FFFBEB;
            border: 1px solid #FDE68A;
            color: #92400E;
        }

        .alert-warn-modern i { margin-top: 2px; color: var(--accent); }

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

        .section-card-body {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

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

        .cell-order {
            font-family: 'SF Mono', 'Fira Code', monospace;
            font-weight: 700;
            color: var(--fg);
            font-size: 13px;
            background: var(--border-light);
            padding: 4px 10px;
            border-radius: 6px;
            display: inline-block;
        }

        .cell-table-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 6px 12px;
            border-radius: var(--radius-full);
            background: var(--blue-light);
            color: var(--blue);
            font-size: 12px;
            font-weight: 800;
        }

        .cell-table-pill i { font-size: 10px; }

        .cell-time {
            font-size: 13px;
            color: var(--muted);
            font-weight: 600;
        }

        .cell-duration {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            font-weight: 800;
        }

        .cell-duration.fast { color: var(--green); }
        .cell-duration.normal { color: var(--accent-dark); }
        .cell-duration.slow { color: var(--red); }
        .cell-duration i { font-size: 12px; }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            min-height: 240px;
            color: var(--muted);
            text-align: center;
            padding: 40px 24px;
        }

        .empty-state i { font-size: 44px; color: #E5E7EB; }
        .empty-state strong { font-size: 15px; color: var(--fg-secondary); }
        .empty-state span { font-size: 13px; max-width: 300px; }

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

        .pagination-area a:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: var(--accent-light);
        }

        .pagination-area .active,
        .pagination-area [aria-current="page"] span {
            background: var(--accent);
            border-color: var(--accent);
            color: white;
        }

        .pagination-area .disabled,
        .pagination-area [aria-disabled="true"] span {
            opacity: 0.35;
            pointer-events: none;
        }

        @media (max-width: 768px) {
            .page-shell { padding: 16px; }
            .dashboard-topbar { flex-direction: column; align-items: flex-start; padding: 20px; }
            .data-table { min-width: 700px; }
            .data-table thead th, .data-table tbody td { padding: 14px 18px; }
            .pagination-area { padding: 16px 18px; }
        }
    </style>
@endpush

@section('content')
    <div class="page-shell">
        <div class="dashboard-topbar">
            <div>
                <h1><i class="fas fa-clock-rotate-left"></i> Riwayat Pesanan Selesai</h1>
                <p>Menampilkan pesanan selesai dan waktu pengerjaan.</p>
            </div>
            <div class="live-indicator">
                <span class="live-dot"></span> Auto-sync
            </div>
        </div>

        @if (!$hasStatus)
            <div class="alert-box alert-warn-modern">
                <i class="fas fa-triangle-exclamation"></i>
                <div>
                    <strong>Catatan:</strong> Kolom status belum tersedia di database, jadi data masih menampilkan semua pesanan. Jalankan migrasi untuk menambahkan kolom.
                </div>
            </div>
        @endif

        <div class="section-card">
            <div class="section-card-header">
                <div class="section-card-title">
                    <i class="fas fa-list-check"></i> Tabel Riwayat
                </div>
            </div>
            <div class="section-card-body">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nomor Pesanan</th>
                            <th>Nomor Meja</th>
                            <th>Waktu Pesanan</th>
                            <th>Waktu Selesai</th>
                            <th>Waktu Pengerjaan</th>
                        </tr>
                    </thead>
                    <tbody id="kitchenHistoryTbody">
                        @include('kitchen.partials.history_rows', ['history' => $history])
                    </tbody>
                </table>
            </div>

            <div class="pagination-area" id="kitchenHistoryPagination">
                {{ $history->links('components.pagination') }}
            </div>
        </div>
    </div>

    <script>
        (function () {
            const tbody = document.getElementById('kitchenHistoryTbody');
            const paginationWrap = document.getElementById('kitchenHistoryPagination');
            if (!tbody || !paginationWrap) return;

            const currentPage = Number(@json((int) request()->query('page', 1)));
            let syncing = false;
            const sync = async () => {
                if (syncing) return;
                syncing = true;
                try {
                    const url = "{{ route('kitchen.history.live') }}" + "?page=" + encodeURIComponent(currentPage);
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

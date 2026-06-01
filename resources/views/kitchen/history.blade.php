@extends('kitchen.layout')

@section('title', ($cafeBrand['name'] ?? config('app.name')) . ' - Riwayat Pesanan Dapur')

@push('head')
    <style>
        .kitchen-shell { max-width: 100%; }
        .panel { background: var(--bg-card); border: 1px solid var(--accent); border-radius: 20px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; box-shadow: 0 4px 15px var(--shadow); }
        .page-title { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.5rem; margin: 0 0 0.5rem; }
        .page-desc { color: var(--text-muted); font-size: 0.95rem; margin: 0; }
        .alert-warn { background: #FFF8E1; color: #8D6E63; border: 1px solid #FFE0B2; border-left: 5px solid var(--highlight); padding: 0.85rem 1.25rem; border-radius: 14px; margin-top: 1rem; font-size: 0.9rem; }
        .table-wrap { overflow-x: auto; margin: 0; }
        .history-table { width: 100%; border-collapse: collapse; }
        .history-table th, .history-table td { padding: 1rem 0.75rem; border-bottom: 1px dashed var(--accent); vertical-align: middle; text-align: left; font-size: 0.95rem; }
        .history-table th { background: var(--bg-main); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); font-weight: 600; border-bottom: 2px solid var(--highlight); }
        .history-table tbody tr:hover { background-color: #FFFAF5; }
        .history-table tbody tr:last-child td { border-bottom: none; }
        .pill { display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 8px; background: rgba(212, 163, 115, 0.15); color: var(--highlight); font-size: 0.9rem; font-weight: 700; letter-spacing: 0.5px; }
        .table-num { font-weight: 600; color: var(--primary); }
        .duration { font-weight: 700; color: var(--primary); }
        .empty-state { color: var(--text-muted); font-style: italic; text-align: center; padding: 2.5rem 1rem; }
        .pagination-area { margin-top: 1.5rem; }
        @media (max-width: 768px) {
            .page-title { font-size: 1.3rem; }
            .panel { padding: 1.25rem; }
            .history-table th, .history-table td { padding: 0.75rem 0.5rem; font-size: 0.85rem; }
        }
    </style>
@endpush

@section('content')
    <div class="kitchen-shell">
        <section class="panel">
            <h2 class="page-title">Riwayat Pesanan Selesai</h2>
            <p class="page-desc">Menampilkan pesanan selesai dan waktu pengerjaan.</p>
            @if (!$hasStatus)
                <div class="alert-warn">
                    Kolom status belum tersedia di database, jadi data masih menampilkan semua pesanan. Jalankan migrasi untuk menambahkan kolom.
                </div>
            @endif
        </section>

        <section class="panel">
            <div class="table-wrap">
                <table class="history-table">
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
        </section>
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

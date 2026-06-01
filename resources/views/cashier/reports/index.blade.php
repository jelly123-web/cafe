@extends('cashier.layout')

@section('title', ($cafeBrand['name'] ?? config('app.name')) . ' - Laporan Kasir')

@push('head')
    <style>
        .main-panel { padding: 2rem 2.5rem; overflow-y: auto; }
        .report-shell { max-width: 100%; }
        .panel { background: var(--bg-card); border: 1px solid var(--accent); border-radius: 20px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; box-shadow: 0 4px 15px var(--shadow); }
        .page-title { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.8rem; margin: 0 0 0.5rem; }
        .page-desc { color: var(--text-muted); font-size: 0.95rem; margin: 0; }
        .section-title { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.2rem; margin: 0 0 1.25rem; }
        .alert-warn { background: #FFF8E1; color: #8D6E63; border: 1px solid #FFE0B2; border-left: 5px solid var(--highlight); padding: 1rem 1.25rem; border-radius: 14px; margin-bottom: 1.5rem; font-size: 0.95rem; }
        .alert-warn strong { color: var(--primary); }
        .alert-warn code { background: rgba(121, 85, 72, 0.1); padding: 2px 6px; border-radius: 4px; font-size: 0.9em; }
        .summary-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem; }
        .summary-card { background: var(--bg-card); border: 1px solid var(--accent); border-radius: 16px; padding: 1.25rem; box-shadow: 0 4px 15px var(--shadow); display: flex; flex-direction: column; transition: transform 0.2s ease, box-shadow 0.2s ease; position: relative; overflow: hidden; }
        .summary-card::after { content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 4px; background: linear-gradient(90deg, var(--accent), var(--highlight)); }
        .summary-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px var(--shadow); }
        .summary-card span { color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 0.75rem; }
        .summary-card strong { font-family: 'Playfair Display', Georgia, serif; font-size: 1.75rem; color: var(--primary); margin-top: auto; }
        .table-wrap { overflow-x: auto; }
        .report-table { width: 100%; border-collapse: collapse; }
        .report-table th, .report-table td { padding: 0.85rem 0.75rem; border-bottom: 1px dashed var(--accent); vertical-align: middle; text-align: left; font-size: 0.95rem; }
        .report-table th { background: var(--bg-main); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); font-weight: 600; border-bottom: 2px solid var(--highlight); }
        .report-table tbody tr:hover { background-color: #FFFAF5; }
        .order-code { font-weight: 700; color: var(--primary); }
        .tag { display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; }
        .tag-paid { background: #E8F5E9; color: #558B2F; }
        .tag-pending { background: #FFF3E0; color: #E65100; }
        .tag-cancelled { background: #FFEBEE; color: #C62828; }
        .btn-danger { background: transparent; color: #C62828; border: 1px solid #FFCDD2; border-radius: 8px; padding: .35rem .65rem; font-size: .8rem; font-weight: 700; cursor: pointer; }
        .btn-danger:hover { background: #FFEBEE; }
        .pagination-wrap { margin-top: 1.5rem; }
        .pagination-meta { color: var(--text-muted); font-size: .9rem; margin-bottom: .75rem; text-align: center; }
        .pagination-links { display: flex; gap: .5rem; justify-content: center; flex-wrap: wrap; }
        .pagination-link, .pagination-dots { display: inline-flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; border-radius: 10px; font-size: .9rem; font-weight: 600; text-decoration: none; border: 1px solid var(--accent); color: var(--primary); padding: 0 .65rem; }
        .pagination-link:hover { background: #EFEBE9; }
        .pagination-link.active { background: var(--highlight); color: #fff; border-color: var(--highlight); box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3); }
        .pagination-link.disabled { color: var(--secondary); pointer-events: none; }
        @media (max-width: 900px) { .summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 768px) { .main-panel { padding: 1.5rem 1rem; } .page-title { font-size: 1.5rem; } .panel { padding: 1.25rem; } .summary-grid { grid-template-columns: 1fr; } }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            let interval;
            const fetchLiveReport = () => {
                const container = document.getElementById('live-report-container');
                if (!container) {
                    clearInterval(interval);
                    return;
                }
                fetch("{{ route('cashier.reports.live') }}")
                    .then(res => res.text())
                    .then(html => {
                        const target = document.getElementById('live-report-container');
                        if (target) target.innerHTML = html;
                    })
                    .catch(err => console.error('Error fetching live report:', err));
            };

            const startPolling = () => {
                clearInterval(interval);
                if (document.getElementById('live-report-container')) {
                    interval = setInterval(() => {
                        if (document.visibilityState === 'visible') {
                            fetchLiveReport();
                        }
                    }, 15000);
                }
            };

            document.addEventListener('turbo:load', startPolling);
            document.addEventListener('turbo:before-cache', () => clearInterval(interval));
            
            // Initial start if already loaded
            if (document.readyState === 'complete') startPolling();
        })();
    </script>
@endpush

@section('content')
    <div class="report-shell">
        <section class="panel">
            <h1 class="page-title">Laporan Kasir</h1>
            <p class="page-desc">Total transaksi hari ini, jumlah transaksi, riwayat transaksi, dan rekap uang masuk.</p>
        </section>

        @if (session('success'))
            <section class="panel" style="border-left:5px solid #81C784;">
                {{ session('success') }}
            </section>
        @endif

        @if (!($hasStatusColumn ?? false))
            <section class="alert-warn">
                <strong>Catatan:</strong> Kolom status transaksi belum ada di database, jadi angka laporan dihitung dari semua transaksi. Jalankan <code>php artisan migrate</code>.
            </section>
        @endif

        <div id="live-report-container">
            @include('cashier.reports.live')
        </div>
    </div>
@endsection

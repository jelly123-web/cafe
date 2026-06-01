@extends('kitchen.layout')

@section('title', ($cafeBrand['name'] ?? config('app.name')) . ' - Dashboard Dapur')

@push('head')
    <style>
        .kitchen-shell { max-width: 100%; }
        .panel { background: var(--bg-card); border: 1px solid var(--accent); border-radius: 20px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; box-shadow: 0 4px 15px var(--shadow); }
        .page-title { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.6rem; margin: 0 0 0.5rem; }
        .page-desc { color: var(--text-muted); font-size: 0.95rem; margin: 0; }
        .alert-warn { background: #FFF8E1; color: #8D6E63; border: 1px solid #FFE0B2; border-left: 5px solid var(--highlight); padding: 0.85rem 1.25rem; border-radius: 14px; margin-top: 1rem; font-size: 0.9rem; }
        .summary-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1.25rem; }
        .summary-card { background: var(--bg-card); border: 1px solid var(--accent); border-radius: 16px; padding: 1.25rem; box-shadow: 0 4px 15px var(--shadow); position: relative; overflow: hidden; }
        .summary-card::after { content: ''; position: absolute; left: 0; bottom: 0; width: 100%; height: 4px; background: linear-gradient(90deg, var(--accent), var(--highlight)); }
        .summary-card span { display: block; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.4px; font-weight: 600; margin-bottom: 0.6rem; }
        .summary-card strong { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.8rem; }
        @media (max-width: 900px) { .summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 768px) { .page-title { font-size: 1.35rem; } .panel { padding: 1.25rem; } .summary-grid { grid-template-columns: 1fr; } }
    </style>
@endpush

@section('content')
    <div class="kitchen-shell">
        <section class="panel">
            <h1 class="page-title">Dashboard Dapur</h1>
            <p class="page-desc">Ringkasan operasional dapur hari ini.</p>
            @if (! $hasStatus)
                <div class="alert-warn">Kolom status belum tersedia di database, jadi ringkasan status masih 0. Jalankan migrasi untuk data status lengkap.</div>
            @endif
        </section>

        <section class="summary-grid">
            <article class="summary-card">
                <span>Total pesanan hari ini</span>
                <strong id="kpiTotal">{{ $totalToday }}</strong>
            </article>
            <article class="summary-card">
                <span>Pesanan menunggu</span>
                <strong id="kpiPending">{{ $pendingCount }}</strong>
            </article>
            <article class="summary-card">
                <span>Pesanan sedang dibuat</span>
                <strong id="kpiProcessing">{{ $processingCount }}</strong>
            </article>
            <article class="summary-card">
                <span>Pesanan selesai</span>
                <strong id="kpiCompleted">{{ $completedCount }}</strong>
            </article>
        </section>
    </div>
    <script>
        (function () {
            const totalEl = document.getElementById('kpiTotal');
            const pendingEl = document.getElementById('kpiPending');
            const processingEl = document.getElementById('kpiProcessing');
            const completedEl = document.getElementById('kpiCompleted');
            if (!totalEl || !pendingEl || !processingEl || !completedEl) return;

            let syncing = false;
            const sync = async () => {
                if (syncing) return;
                syncing = true;
                try {
                    const res = await fetch("{{ route('kitchen.dashboard.live') }}", {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin',
                    });
                    if (!res.ok) return;
                    const payload = await res.json();
                    totalEl.textContent = payload.totalToday ?? 0;
                    pendingEl.textContent = payload.pendingCount ?? 0;
                    processingEl.textContent = payload.processingCount ?? 0;
                    completedEl.textContent = payload.completedCount ?? 0;
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

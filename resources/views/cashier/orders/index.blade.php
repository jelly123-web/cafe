@extends('cashier.layout')

@section('title', ($cafeBrand['name'] ?? config('app.name')) . ' - Pesanan Kasir')

@push('head')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { line-height: 1.6; background-image: radial-gradient(var(--accent) 1px, transparent 1px); background-size: 24px 24px; }
        .shell { max-width: 900px; margin: 0 auto; padding: 2rem 1.5rem; }
        .panel { background: var(--bg-card); border: 1px solid var(--accent); border-radius: 20px; padding: 2rem; margin-bottom: 1.5rem; box-shadow: 0 4px 15px var(--shadow); }
        .title { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.8rem; margin: 0 0 0.5rem; }
        .muted { color: var(--text-muted); font-size: 0.95rem; }
        .alert { padding: 0.85rem 1.25rem; border-radius: 14px; margin-bottom: 1rem; font-weight: 500; font-size: 0.95rem; border: 1px solid transparent; }
        .ok { background: #E8F5E9; color: #558B2F; border-color: #C8E6C9; }
        .err { background: #FFEBEE; color: #C62828; border-color: #FFCDD2; }
        .order { border: 1px solid var(--accent); border-radius: 16px; padding: 1.25rem; margin-bottom: 1.25rem; background: #FFFAF5; transition: all 0.2s ease; box-shadow: 0 2px 8px var(--shadow); }
        .order:hover { border-color: rgba(212, 163, 115, 0.4); transform: translateY(-2px); box-shadow: 0 6px 15px var(--shadow); }
        .row { display: flex; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
        .pill { display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 999px; background: var(--highlight); color: #fff; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; }
        .order strong { display: block; margin-top: 8px; color: var(--primary); font-size: 1.1rem; font-family: 'Playfair Display', Georgia, serif; }
        .order .muted div { font-size: 0.9rem; }
        .order .muted strong { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 0.9rem; color: var(--text-main); }
        .items { margin-top: 1rem; padding-top: 0.75rem; border-top: 1px dashed var(--accent); }
        .item { display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px dashed var(--accent); font-size: 0.95rem; }
        .item:last-child { border-bottom: none; }
        .item span:last-child { font-weight: 600; color: var(--primary); }
        .order-card { background: #FFFAF5; box-shadow: 0 2px 8px var(--shadow); }
        .order-head { align-items: flex-start; }
        .order-title { margin-top: 0.45rem; }
        .order-meta { display: grid; gap: 0.2rem; color: var(--text-muted); font-size: 0.9rem; margin-top: 0.2rem; }
        .order-items { margin-top: 1rem; padding-top: 0.85rem; border-top: 1px dashed var(--accent); display: grid; gap: 0.5rem; }
        .order-item { font-size: 0.95rem; }
        .order-footer { display: flex; justify-content: space-between; gap: 1rem; margin-top: 1rem; padding-top: 0.85rem; border-top: 1px solid var(--accent); flex-wrap: wrap; }
        .order-code { margin-bottom: 0.25rem; }
        .status-pill { display: inline-flex; align-items: center; padding: 0.25rem 0.65rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; }
        .status-pending { background: #FFF3E0; color: #E65100; }
        .status-processing { background: #E3F2FD; color: #1565C0; }
        .status-ready { background: #E8F5E9; color: #2E7D32; }
        .status-completed { background: #E8F5E9; color: #2E7D32; }
        .status-cancelled { background: #FFEBEE; color: #C62828; }
        .empty-orders { text-align: center; padding: 2rem 0; }
        .btn { border: 1px solid transparent; border-radius: 12px; padding: 0.5rem 1.2rem; cursor: pointer; font-weight: 600; font-family: inherit; font-size: 0.9rem; transition: all 0.2s ease; }
        .btn-cancel { background: transparent; color: #E57373; border-color: #F8D7DA; margin-top: 1rem; }
        .btn-cancel:hover { background: #FFF0F0; border-color: #E57373; }
        .pagination-area { margin-top: 1.5rem; }
        .order.flash-new { animation: flashNew 0.9s ease; }
        @keyframes flashNew {
            0% { box-shadow: 0 0 0 0 rgba(129, 199, 132, 0.6); border-color: #81C784; }
            100% { box-shadow: 0 2px 8px var(--shadow); border-color: var(--accent); }
        }
        @media (max-width: 768px) { .shell { padding: 1rem; } .title { font-size: 1.5rem; } .panel { padding: 1.25rem; } .row { flex-direction: column; gap: 0.5rem; } }
    </style>
@endpush

@section('content')
    <main class="shell">
        <section class="panel">
            <span style="display: block; color: var(--highlight); font-weight: 700; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.25rem;">Halo, {{ auth()->user()->name ?? 'User' }} 👋</span>
            <h1 class="title">Pesanan Kasir</h1>
            <p class="muted">Melihat pesanan masuk pelanggan, status pesanan, dan membatalkan pesanan jika diizinkan.</p>
        </section>

        @if (session('success'))
            <div class="alert ok">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert err">{{ session('error') }}</div>
        @endif

        <section class="panel">
            <div id="cashierOrdersList">
                @include('cashier.orders._list', ['orders' => $orders, 'canCancelOrders' => $canCancelOrders])
            </div>
        </section>
    </main>
@endsection

@push('scripts')
<script>
    (function () {
        const listEl = document.getElementById('cashierOrdersList');
        if (!listEl) return;

        let lastTopOrderId = Number(listEl.querySelector('.order[data-order-id]')?.dataset.orderId || 0);
        let isLoading = false;

        const showNotif = (message, type = 'success') => {
            if (typeof window.showToast === 'function') {
                window.showToast(message, type);
                return;
            }
            console.log(type.toUpperCase() + ': ' + message);
        };

        const bindCancelForms = () => {
            listEl.querySelectorAll('.cancel-order-form').forEach((form) => {
                if (form.dataset.bound === '1') return;
                form.dataset.bound = '1';
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    if (!confirm('Batalkan pesanan ini?')) return;

                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) submitBtn.disabled = true;
                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            body: new FormData(form),
                            credentials: 'same-origin',
                        });
                        const data = await response.json();
                        if (!response.ok || !data.ok) {
                            throw new Error(data.message || 'Gagal membatalkan pesanan.');
                        }
                        showNotif(data.message || 'Pesanan dibatalkan.');
                        await reloadOrders(false);
                    } catch (err) {
                        showNotif(err.message || 'Terjadi kesalahan.', 'error');
                    } finally {
                        if (submitBtn) submitBtn.disabled = false;
                    }
                });
            });
        };

        const reloadOrders = async (notifyNew = true) => {
            if (isLoading) return;
            isLoading = true;
            try {
                const url = new URL(@json(route('cashier.orders.live')), window.location.origin);
                const currentParams = new URLSearchParams(window.location.search);
                if (currentParams.has('page')) {
                    url.searchParams.set('page', currentParams.get('page'));
                }

                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                });
                if (!response.ok) throw new Error('Gagal memuat pesanan terbaru.');
                const data = await response.json();
                const prevTopOrderId = lastTopOrderId;

                listEl.innerHTML = data.html || '';
                bindCancelForms();

                const topOrderEl = listEl.querySelector('.order[data-order-id]');
                const topId = Number(topOrderEl?.dataset.orderId || 0);
                lastTopOrderId = topId;

                if (notifyNew && data.latest && Number(data.latest.id) > Number(prevTopOrderId)) {
                    topOrderEl?.classList.add('flash-new');
                    showNotif('Pesanan baru masuk: ' + data.latest.code, 'success');
                }
            } catch (err) {
                console.error(err);
            } finally {
                isLoading = false;
            }
        };

        const startPolling = () => {
            const listEl = document.getElementById('cashierOrdersList');
            if (!listEl) return;

            // Handle pagination clicks via AJAX
            listEl.addEventListener('click', (e) => {
                const link = e.target.closest('.pagination-link');
                if (link && link.href) {
                    e.preventDefault();
                    const url = new URL(link.href);
                    const newPage = url.searchParams.get('page');
                    if (newPage) {
                        // Update URL without reloading or scrolling
                        const newUrl = window.location.pathname + '?page=' + newPage;
                        window.history.pushState({ path: newUrl }, '', newUrl);
                        reloadOrders(false);
                    }
                }
            });

            setInterval(() => {
                if (document.visibilityState === 'visible') {
                    reloadOrders(true);
                }
            }, 3000);
        };

        bindCancelForms();
        startPolling();
    })();
</script>
@endpush

@extends('cashier.layout')

@section('title', ($cafeBrand['name'] ?? config('app.name')) . ' - Pembayaran Kasir')

@push('head')
    <style>
        :root { --cash-color:#6D4C41; --qris-color:#4DB6AC; --transfer-color:#7E57C2; }
        .main-panel { padding: 2rem 2.5rem; overflow-y: auto; }
        .pos-shell { max-width: 100%; }
        .panel { background: var(--bg-card); border: 1px solid var(--accent); border-radius: 20px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; box-shadow: 0 4px 15px var(--shadow); }
        .page-title { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.8rem; margin: 0 0 0.5rem; }
        .page-desc { color: var(--text-muted); font-size: 0.95rem; margin: 0; }
        .alert { padding: 0.85rem 1.25rem; border-radius: 14px; margin-bottom: 1.25rem; font-weight: 500; font-size: 0.95rem; border: 1px solid transparent; }
        .ok { background: #E8F5E9; color: #558B2F; border-color: #C8E6C9; }
        .err { background: #FFEBEE; color: #C62828; border-color: #FFCDD2; }
        .order { border: 1px solid var(--accent); border-radius: 16px; padding: 1.25rem; margin-bottom: 1.25rem; background: #FFFAF5; transition: all 0.2s ease; box-shadow: 0 2px 8px var(--shadow); }
        .order:hover { border-color: rgba(212, 163, 115, 0.4); box-shadow: 0 6px 15px var(--shadow); }
        .order-head { display: flex; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
        .order-code { font-family: 'Playfair Display', Georgia, serif; font-size: 1.2rem; color: var(--primary); font-weight: 700; display: block; margin-bottom: 0.25rem; }
        .order-meta { color: var(--text-muted); font-size: 0.9rem; }
        .order-total { color: var(--text-main); font-weight: 600; font-size: 0.95rem; display: block; margin-top: 0.25rem; }
        .tag { display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem; }
        .tag-unpaid { background: #FFF3E0; color: #E65100; }
        .tag-paid { background: #E8F5E9; color: #558B2F; }
        .tag-cancelled { background: #FFEBEE; color: #C62828; }
        .payment-actions { display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 1.25rem; padding-top: 1rem; border-top: 1px dashed var(--accent); }
        .payment-actions form { display: inline-flex; }
        .btn { border: none; border-radius: 12px; padding: 0.65rem 1.25rem; cursor: pointer; font-weight: 700; font-family: inherit; font-size: 0.9rem; color: #fff; transition: all 0.2s ease; }
        .btn-cash { background-color: var(--cash-color); box-shadow: 0 2px 8px rgba(109, 76, 65, 0.25); }
        .btn-cash:hover { background-color: #5D4037; transform: translateY(-2px); }
        .btn-qris { background-color: var(--qris-color); box-shadow: 0 2px 8px rgba(77, 182, 172, 0.25); }
        .btn-qris:hover { background-color: #009688; transform: translateY(-2px); }
        .btn-transfer { background-color: var(--transfer-color); box-shadow: 0 2px 8px rgba(126, 87, 194, 0.25); }
        .btn-transfer:hover { background-color: #673AB7; transform: translateY(-2px); }
        .pagination-area { margin-top: 1.5rem; }
        .pagination-wrap { margin-top: 1.5rem; }
        .pagination-meta { color: var(--text-muted); font-size: .9rem; margin-bottom: .75rem; text-align: center; }
        .pagination-links { display: flex; gap: .5rem; justify-content: center; flex-wrap: wrap; }
        .pagination-link, .pagination-dots { display: inline-flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; border-radius: 10px; font-size: .9rem; font-weight: 600; text-decoration: none; border: 1px solid var(--accent); color: var(--primary); padding: 0 .65rem; }
        .pagination-link:hover { background: var(--highlight); color: #fff; border-color: var(--highlight); }
        .pagination-link.active { background: var(--highlight); color: #fff; border-color: var(--highlight); box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3); }
        .pagination-link.disabled { color: var(--secondary); pointer-events: none; }
        .toolbar { display:flex; justify-content:flex-end; margin-bottom:1rem; }
        .btn-delete-all { background: transparent; color: #C62828; border: 1px solid #FFCDD2; border-radius: 10px; padding: .55rem .9rem; font-weight: 700; cursor: pointer; }
        .btn-delete-all:hover { background: #FFEBEE; }
        @media (max-width: 768px) {
            .main-panel { padding: 1.5rem 1rem; }
            .page-title { font-size: 1.5rem; }
            .panel { padding: 1.25rem; }
            .payment-actions { flex-direction: column; }
            .payment-actions form { width: 100%; }
            .payment-actions .btn { width: 100%; justify-content: center; }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            let interval;
            const fetchLivePayment = () => {
                const container = document.getElementById('live-payment-container');
                if (!container) {
                    clearInterval(interval);
                    return;
                }
                
                // Get current page from URL to keep pagination consistent
                const urlParams = new URLSearchParams(window.location.search);
                const currentPage = urlParams.get('page') || 1;
                const fetchUrl = `{{ route('cashier.payments.live') }}?page=${currentPage}`;

                fetch(fetchUrl)
                    .then(res => {
                        // If session expired, redirect to login
                        if (res.redirected && res.url.includes('/login')) {
                            window.location.href = '{{ route("login") }}';
                            return;
                        }
                        return res.text();
                    })
                    .then(html => {
                        if (!html) return;

                        // Security check: if response contains login form, redirect full page
                        if (html.includes('id="username"') || html.includes('name="username"')) {
                            window.location.href = '{{ route("login") }}';
                            return;
                        }

                        const target = document.getElementById('live-payment-container');
                        if (target) target.innerHTML = html;
                    })
                    .catch(err => console.error('Error fetching live payment:', err));
            };

            const startPolling = () => {
                clearInterval(interval);
                const container = document.getElementById('live-payment-container');
                if (container) {
                    // Handle pagination clicks via AJAX
                    container.addEventListener('click', (e) => {
                        const link = e.target.closest('.pagination-link');
                        if (link && link.href) {
                            e.preventDefault();
                            const url = new URL(link.href);
                            const newPage = url.searchParams.get('page');
                            if (newPage) {
                                // Update URL without reloading or scrolling
                                const newUrl = window.location.pathname + '?page=' + newPage;
                                window.history.pushState({ path: newUrl }, '', newUrl);
                                fetchLivePayment();
                            }
                        }
                    });

                    interval = setInterval(() => {
                        if (document.visibilityState === 'visible') {
                            fetchLivePayment();
                        }
                    }, 15000);
                }
            };

            document.addEventListener('turbo:load', startPolling);
            document.addEventListener('turbo:before-cache', () => clearInterval(interval));

            if (document.readyState === 'complete') startPolling();
        })();
    </script>
@endpush

@section('content')
    <div class="pos-shell">
        <section class="panel">
            <h1 class="page-title">Pembayaran Kasir</h1>
            <p class="page-desc">Menerima pembayaran tunai, QRIS, transfer/e-wallet, lalu ubah status menjadi Lunas.</p>
        </section>

        @if (session('success'))
            <div class="alert ok">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert err">{{ session('error') }}</div>
        @endif

        <section class="panel" id="live-payment-container">
            @include('cashier.payments.live')
        </section>
    </div>
@endsection

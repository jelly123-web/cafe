@extends('cashier.layout')

@section('title', ($cafeBrand['name'] ?? config('app.name')) . ' - Pesanan Kasir')
@section('page_title', 'Pesanan Kasir')
@section('page_description', 'Melihat pesanan masuk pelanggan, status pesanan, dan membatalkan pesanan jika diizinkan.')

@push('head')
    <link rel="stylesheet" href="{{ asset('css/leader-cashier.css') }}">
    <style>
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
            --radius-sm: 8px;
            --radius-md: 12px;
        }

        /* ===== STATS STRIP ===== */
        .stats-strip {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 12px; margin-bottom: 24px;
        }
        .strip-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: var(--radius-md); padding: 14px 18px;
            display: flex; align-items: center; gap: 14px;
            transition: all 0.25s ease; position: relative; overflow: hidden;
        }
        .strip-card .strip-icon {
            width: 40px; height: 40px; border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; flex-shrink: 0;
        }
        /* Warna Stat Card */
        .strip-card:nth-child(1) .strip-icon { background: var(--accent-light); color: var(--accent); }
        .strip-card:nth-child(2) .strip-icon { background: var(--blue-light); color: var(--blue); }
        .strip-card:nth-child(3) .strip-icon { background: var(--green-light); color: var(--green); }
        .strip-card:nth-child(4) .strip-icon { background: var(--red-light); color: var(--red); }
        
        .strip-info strong {
            font-size: 18px; font-weight: 900; color: var(--fg);
            letter-spacing: -0.3px; line-height: 1.1; display: block;
        }
        .strip-info span {
            font-size: 11px; color: var(--muted); font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.4px;
        }

        /* ===== ORDER LIST ===== */
        .order-list { display: grid; gap: 12px; }

        /* ===== ORDER CARD ===== */
        .order {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 0;
            transition: all 0.25s ease;
            overflow: hidden;
            position: relative;
        }
        .order-head {
            display: flex; justify-content: space-between; align-items: flex-start;
            gap: 12px; padding: 18px 20px 0; flex-wrap: wrap;
        }
        .order-head-left { display: flex; align-items: flex-start; gap: 14px; min-width: 0; }
        .order-icon {
            width: 44px; height: 44px; border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; flex-shrink: 0;
            background: var(--accent-light); color: var(--accent);
        }
        .order-head-info { min-width: 0; }
        .order-code {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 2px 8px; border-radius: 4px;
            background: var(--accent-light); color: var(--accent-dark);
            font-size: 12px; font-weight: 800; font-family: monospace;
        }
        .order-title { font-size: 14px; font-weight: 800; color: var(--fg); margin-top: 4px; }
        .order-meta { display: flex; align-items: center; gap: 12px; color: var(--muted); font-size: 12px; font-weight: 500; margin-top: 3px; }
        
        .status-pill {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 12px; border-radius: 999px;
            font-size: 11px; font-weight: 700; text-transform: uppercase;
        }
        .status-dot { width: 5px; height: 5px; border-radius: 50%; background: currentColor; }
        .status-pending { background: var(--accent-light); color: var(--accent-dark); }
        .status-processing { background: var(--blue-light); color: var(--blue); }
        .status-ready { background: var(--green-light); color: var(--green); }
        .status-cancelled { background: var(--red-light); color: var(--red); }

        .order-items { padding: 14px 20px; display: grid; gap: 6px; }
        .order-item {
            display: flex; justify-content: space-between; align-items: center;
            font-size: 13px; padding: 6px 0; border-bottom: 1px solid var(--border-light);
        }
        .item-left { display: flex; align-items: center; gap: 8px; }
        .item-qty {
            width: 24px; height: 24px; border-radius: 5px;
            background: var(--bg); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 800; color: var(--fg-secondary);
        }
        .item-name { font-weight: 600; color: var(--fg); }
        .item-price { font-weight: 700; color: var(--fg); }

        .order-footer {
            display: flex; justify-content: space-between; align-items: center;
            gap: 12px; padding: 14px 20px; border-top: 1px solid var(--border-light);
            background: var(--bg);
        }
        .order-total { font-size: 16px; font-weight: 900; color: var(--fg); }
        .order-total small { font-size: 11px; color: var(--muted); font-weight: 600; text-transform: uppercase; display: block; }
        .btn-cancel {
            background: transparent; color: var(--red); border: 1.5px solid #FECACA;
            border-radius: 8px; padding: 7px 14px; font-size: 12px; font-weight: 700; cursor: pointer;
        }
        .btn-cancel:hover { background: var(--red-light); border-color: var(--red); }
        .empty-state { text-align: center; padding: 48px 20px; color: var(--muted); font-size: 14px; }
    </style>
@endpush

@section('content')
    @if (session('success'))
        <div class="alert alert-ok fade-in">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-err fade-in">{{ session('error') }}</div>
    @endif

    <section class="stats-strip fade-in">
        <div class="strip-card">
            <div class="strip-icon"><i class="fas fa-clipboard-list"></i></div>
            <div class="strip-info">
                <strong id="cashierOrdersTotal">{{ $orders->count() }}</strong>
                <span>Total Pesanan</span>
            </div>
        </div>
        <div class="strip-card">
            <div class="strip-icon"><i class="fas fa-spinner"></i></div>
            <div class="strip-info">
                <strong id="cashierOrdersProcessing">{{ $orders->where('status','processing')->count() }}</strong>
                <span>Diproses</span>
            </div>
        </div>
        <div class="strip-card">
            <div class="strip-icon"><i class="fas fa-check-circle"></i></div>
            <div class="strip-info">
                <strong id="cashierOrdersReady">{{ $orders->where('status','ready')->count() }}</strong>
                <span>Siap Saji</span>
            </div>
        </div>
        <div class="strip-card">
            <div class="strip-icon"><i class="fas fa-ban"></i></div>
            <div class="strip-info">
                <strong id="cashierOrdersCancelled">{{ $orders->where('status','cancelled')->count() }}</strong>
                <span>Dibatalkan</span>
            </div>
        </div>
    </section>

    <div id="cashierOrdersList" class="order-list">
        @include('cashier.orders._list', ['orders' => $orders, 'canCancelOrders' => $canCancelOrders])
    </div>
@endsection

@push('scripts')
<script>
    (() => {
        const wrap = document.getElementById('cashierOrdersList');
        if (!wrap) return;

        const totalEl = document.getElementById('cashierOrdersTotal');
        const processingEl = document.getElementById('cashierOrdersProcessing');
        const readyEl = document.getElementById('cashierOrdersReady');
        const cancelledEl = document.getElementById('cashierOrdersCancelled');
        const storageKey = 'cafe_live_sync_last_order_id';
        const channel = window.BroadcastChannel ? new BroadcastChannel('cafe-order-sync') : null;
        let currentSignature = @json(sha1($orders->getCollection()->map(function ($order) {
            return implode(':', [
                $order->id,
                $order->status,
                optional($order->updated_at)->timestamp ?? 0,
            ]);
        })->implode('|')));
        let busy = false;

        const hydrateFadeIn = () => {
            wrap.querySelectorAll('.fade-in').forEach((el) => el.classList.add('visible'));
        };

        const refreshOrders = async () => {
            if (busy || document.visibilityState !== 'visible') return;
            busy = true;
            try {
                const res = await fetch("{{ route('cashier.orders.live') }}", {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                });
                if (!res.ok) return;
                const payload = await res.json();
                if ((payload.signature || '') !== currentSignature) {
                    wrap.innerHTML = payload.html || '';
                    currentSignature = payload.signature || '';
                    hydrateFadeIn();
                }
                if (payload.summary) {
                    if (totalEl) totalEl.textContent = payload.summary.total ?? '0';
                    if (processingEl) processingEl.textContent = payload.summary.processing ?? '0';
                    if (readyEl) readyEl.textContent = payload.summary.ready ?? '0';
                    if (cancelledEl) cancelledEl.textContent = payload.summary.cancelled ?? '0';
                }
            } catch (e) {
            } finally {
                busy = false;
            }
        };

        wrap.addEventListener('submit', async (event) => {
            const form = event.target.closest('.cancel-order-form');
            if (!(form instanceof HTMLFormElement)) return;
            event.preventDefault();

            const formData = new FormData(form);
            currentSignature = '';

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                    credentials: 'same-origin',
                });
                const payload = await res.json();
                if (!res.ok || payload.ok === false) {
                    throw new Error(payload.message || 'Gagal membatalkan pesanan.');
                }
                if (window.showToast) window.showToast(payload.message || 'Pesanan dibatalkan.', 'success');
                refreshOrders();
            } catch (error) {
                if (window.showToast) window.showToast(error.message || 'Gagal membatalkan pesanan.', 'error');
            }
        });

        const requestRefresh = () => {
            if (document.visibilityState === 'visible') refreshOrders();
        };

        hydrateFadeIn();
        setInterval(requestRefresh, 4000);
        window.addEventListener('cafe:order-sync', requestRefresh);
        window.addEventListener('storage', (event) => {
            if (event.key === storageKey) requestRefresh();
        });
        channel?.addEventListener('message', requestRefresh);
    })();
</script>
@endpush

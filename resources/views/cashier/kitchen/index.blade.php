@extends('cashier.layout')

@section('title', 'Dapur - MakanYuk')

@push('head')
    <style>
        /* ===== VARIABLES (Already defined in base layout, but ensuring consistency) ===== */
        :root {
            --white: #ffffff;
            --border: #E5E7EB;
            --border-light: #F3F4F6;
            --radius-lg: 16px;
            --radius-md: 10px;
            --radius-sm: 8px;
            --radius-full: 9999px;
            --accent: #D97706;
            --accent-dark: #B45309;
            --accent-light: #FFFBEB;
            --fg: #111827;
            --fg-secondary: #374151;
            --muted: #6B7280;
            --green: #059669;
            --green-light: #D1FAE5;
            --red: #DC2626;
            --red-light: #FEE2E2;
            --blue: #2563EB;
            --blue-light: #DBEAFE;
            --indigo: #4F46E5;
            --indigo-light: #EEF2FF;
            --bg: #F9FAFB;
            --shadow-xs: 0 1px 2px rgba(0,0,0,0.05);
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
            --font: 'Inter', sans-serif;
            --transition: 0.2s ease;
        }

        .page-shell {
            max-width: 1400px;
            margin: 0 auto;
            padding: 28px 32px;
        }

        /* ===== DASHBOARD TOPBAR ===== */
        .dashboard-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }

        .dashboard-topbar-left h1 {
            font-size: 22px;
            font-weight: 900;
            color: var(--fg);
            letter-spacing: -0.3px;
            margin-bottom: 4px;
        }

        .dashboard-topbar-left p {
            font-size: 13px;
            color: var(--muted);
            font-weight: 500;
        }

        /* ===== STATS GRID ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 26px;
            box-shadow: var(--shadow-xs);
            transition: all 0.25s ease;
        }

        .stat-card .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
        }

        .stat-card .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .stat-card .stat-icon.amber { background: #FEF3C7; color: #D97706; }
        .stat-card .stat-icon.blue { background: #DBEAFE; color: #2563EB; }
        .stat-card .stat-icon.green { background: #D1FAE5; color: #059669; }
        .stat-card .stat-icon.red { background: #FEE2E2; color: #DC2626; }

        .stat-card .stat-value {
            font-size: 28px;
            font-weight: 900;
            line-height: 1.1;
            color: var(--fg);
            margin-bottom: 8px;
        }

        .stat-card .stat-label {
            color: var(--muted);
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }

        /* ===== ORDER GRID ===== */
        .order-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 20px;
        }

        /* ===== ORDER CARD ===== */
        .order-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            transition: all 0.25s ease;
            position: relative;
        }

        .order-card[data-status="pending"]::before { background: #E65100; content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; }
        .order-card[data-status="processing"]::before { background: var(--accent); content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; }
        .order-card[data-status="ready"]::before { background: var(--indigo); content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; }
        
        .order-card-head {
            padding: 20px 20px 0;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }

        .order-card-head h3 {
            font-size: 17px;
            font-weight: 900;
            color: var(--fg);
            letter-spacing: -0.3px;
            margin-top: 6px;
            font-family: 'SF Mono', 'Fira Code', monospace;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: var(--radius-full);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: currentColor;
        }

        .status-badge.pending { background: #FFF3E0; color: #E65100; }
        .status-badge.cooking { background: var(--accent-light); color: var(--accent); }
        .status-badge.ready { background: var(--indigo-light); color: var(--indigo); }

        .order-items { padding: 16px 20px; }
        .item-row { display: flex; align-items: center; gap: 10px; padding: 8px 0; border-bottom: 1px solid var(--border-light); font-size: 14px; color: var(--fg-secondary); font-weight: 500; }
        .item-qty { display: inline-flex; align-items: center; justify-content: center; min-width: 28px; height: 28px; border-radius: 8px; background: var(--accent-light); color: var(--accent-dark); font-weight: 900; font-size: 12px; flex-shrink: 0; }
        
        .order-actions { padding: 16px 20px; border-top: 1px solid var(--border-light); display: flex; gap: 8px; flex-wrap: wrap; background: #FAFBFC; }
        .btn-action { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 18px; border-radius: var(--radius-md); font-weight: 800; font-size: 13px; cursor: pointer; border: 1.5px solid transparent; }
        .btn-cook { background: var(--accent-light); color: var(--accent-dark); }
        .btn-ready { background: var(--indigo-light); color: var(--indigo); }
    </style>
@endpush

@section('content')
<!-- ===== TOPBAR ===== -->
<div class="dashboard-topbar">
    <div class="dashboard-topbar-left">
        <h1>Dashboard Dapur</h1>
        <p>Ringkasan operasional dapur hari ini.</p>
    </div>
</div>

<!-- ===== STATS GRID ===== -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value">{{ $pendingCount + $cookingCount + $readyCount + 47 }}</div> <!-- Total today -->
        <div class="stat-label">TOTAL PESANAN HARI INI</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $pendingCount }}</div>
        <div class="stat-label">PESANAN MENUNGGU</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $cookingCount }}</div>
        <div class="stat-label">PESANAN SEDANG DIBUAT</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">47</div> <!-- Placeholder -->
        <div class="stat-label">PESANAN SELESAI</div>
    </div>
</div>

    <div class="order-grid">
        @foreach($orders as $order)
            <article class="order-card" data-status="{{ $order->status }}">
                <div class="order-card-head">
                    <h3>{{ $order->code }}</h3>
                    <span class="status-badge {{ $order->status }}"><span class="status-dot"></span> {{ ucfirst($order->status) }}</span>
                </div>
                <div class="order-items">
                    @foreach($order->items as $item)
                        <div class="item-row">
                            <span class="item-qty">{{ $item->qty }}x</span>
                            <span class="item-name">{{ $item->menu->name }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="order-actions">
                    @if($order->status == 'pending')
                        <form action="{{ route('kitchen.orders.update', [$order, 'status' => 'processing']) }}" method="POST">
                            @csrf @method('PUT')
                            <button type="submit" class="btn-action btn-cook"><i class="fas fa-fire"></i> Masak</button>
                        </form>
                    @elseif($order->status == 'processing')
                        <form action="{{ route('kitchen.orders.update', [$order, 'status' => 'ready']) }}" method="POST">
                            @csrf @method('PUT')
                            <button type="submit" class="btn-action btn-ready"><i class="fas fa-bell"></i> Siap Saji</button>
                        </form>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
</div>
@endsection

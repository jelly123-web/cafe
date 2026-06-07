@extends('kitchen.layout')

@section('title', 'Dashboard Dapur')

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

        .dashboard-topbar h1 i { color: var(--accent); }

        .dashboard-topbar p {
            font-size: 13px;
            color: var(--muted);
            margin: 0;
            font-weight: 500;
        }

        .dashboard-topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dashboard-hello-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 40px;
            padding: 0 18px;
            border: 1px solid var(--border);
            border-radius: var(--radius-full);
            background: var(--bg);
            color: var(--fg-secondary);
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
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
            padding: 24px 26px;
            box-shadow: var(--shadow-xs);
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            box-shadow: var(--shadow-sm);
            transform: translateY(-3px);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .stat-icon.amber { background: #FEF3C7; color: #D97706; }
        .stat-icon.orange { background: #FFF3E0; color: #E65100; }
        .stat-icon.blue { background: #DBEAFE; color: #2563EB; }
        .stat-icon.green { background: #D1FAE5; color: #059669; }

        .stat-trend {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 12px;
            border-radius: var(--radius-full);
            font-size: 12px;
            font-weight: 800;
        }

        .stat-trend.urgent { background: var(--red-light); color: var(--red); }
        .stat-trend.progress { background: var(--blue-light); color: var(--blue); }
        .stat-trend.success { background: var(--green-light); color: var(--green); }
        .stat-trend.neutral { background: #F3F4F6; color: var(--muted); }

        .stat-value {
            font-size: 32px;
            font-weight: 900;
            line-height: 1.1;
            color: var(--fg);
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .stat-label {
            color: var(--muted);
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        @media (max-width: 1280px) {
            .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 768px) {
            .page-shell { padding: 16px; }
            .dashboard-topbar { flex-direction: column; align-items: flex-start; padding: 20px; }
            .dashboard-hello-pill { display: none; }
            .stats-grid { grid-template-columns: 1fr; gap: 16px; }
            .stat-card { padding: 20px; }
            .stat-value { font-size: 26px; }
        }
    </style>
@endpush

@section('content')
    <div class="page-shell">
        <div class="dashboard-topbar">
            <div>
                <h1><i class="fas fa-fire-burner"></i> Dashboard Dapur</h1>
                <p>Ringkasan operasional dapur hari ini.</p>
            </div>
            <div class="dashboard-topbar-right">
                <div class="dashboard-hello-pill">
                    <span>Halo, {{ auth()->user()->name ?? 'Koki' }}</span>
                </div>
                <div class="live-indicator">
                    <span class="live-dot"></span> Live
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon amber">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <span class="stat-trend neutral">
                        <i class="fas fa-calendar-day"></i> Hari ini
                    </span>
                </div>
                <div class="stat-value">{{ $pendingCount + $processingCount + $completedCount }}</div>
                <div class="stat-label">Total Pesanan</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon orange">
                        <i class="fas fa-clock"></i>
                    </div>
                    <span class="stat-trend {{ $pendingCount > 0 ? 'urgent' : 'neutral' }}">
                        <i class="fas {{ $pendingCount > 0 ? 'fa-exclamation-circle' : 'fa-minus-circle' }}"></i>
                        {{ $pendingCount > 0 ? 'Butuh Aksi' : 'Aman' }}
                    </span>
                </div>
                <div class="stat-value">{{ $pendingCount }}</div>
                <div class="stat-label">Pesanan Menunggu</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon blue">
                        <i class="fas fa-fire"></i>
                    </div>
                    <span class="stat-trend {{ $processingCount > 0 ? 'progress' : 'neutral' }}">
                        <i class="fas {{ $processingCount > 0 ? 'fa-spinner' : 'fa-minus-circle' }}"></i>
                        {{ $processingCount > 0 ? 'Proses' : 'Kosong' }}
                    </span>
                </div>
                <div class="stat-value">{{ $processingCount }}</div>
                <div class="stat-label">Sedang Dibuat</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon green">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <span class="stat-trend success">
                        <i class="fas fa-arrow-up"></i> Lancar
                    </span>
                </div>
                <div class="stat-value">{{ $completedCount }}</div>
                <div class="stat-label">Pesanan Selesai</div>
            </div>
        </div>
    </div>
@endsection

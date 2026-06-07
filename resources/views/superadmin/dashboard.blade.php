@extends('superadmin.layout')

@push('head')
    <style>
        .dashboard-topbar-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dashboard-hello-pill {
            display: inline-flex;
            align-items: center;
            min-height: 38px;
            padding: 0 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius-full);
            background: var(--bg);
            color: var(--fg-secondary);
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
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
            padding: 26px;
            box-shadow: var(--shadow-xs);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .stat-icon.amber { background: #FEF3C7; color: #D97706; }
        .stat-icon.blue { background: #DBEAFE; color: #2563EB; }
        .stat-icon.green { background: #D1FAE5; color: #059669; }
        .stat-icon.teal { background: #CCFBF1; color: #0D9488; }

        .stat-trend {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: var(--radius-full);
            font-size: 13px;
            font-weight: 800;
        }

        .stat-trend.up {
            background: #D1FAE5;
            color: #059669;
        }

        .stat-trend.down {
            background: #FEE2E2;
            color: #DC2626;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 900;
            line-height: 1.1;
            color: var(--fg);
            margin-bottom: 8px;
        }

        .stat-label {
            color: var(--muted);
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }

        .two-col {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(0, 0.85fr);
            gap: 24px;
        }

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

        .section-card-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-card-body {
            padding: 0;
        }

        .live-indicator {
            display: inline-flex;
            align-items: center;
            gap: 6px;
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
        }

        .filter-pills {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px;
            border-radius: var(--radius-full);
            background: #F3F4F6;
        }

        .filter-pill {
            border: none;
            background: transparent;
            color: var(--fg-secondary);
            border-radius: var(--radius-full);
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 800;
            font-family: inherit;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .filter-pill.active {
            background: var(--white);
            color: var(--fg);
            box-shadow: var(--shadow-sm);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead th {
            padding: 18px 28px;
            background: #FBFBFC;
            border-bottom: 1px solid var(--border);
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            text-align: left;
        }

        .data-table tbody td {
            padding: 18px 28px;
            border-bottom: 1px solid var(--border-light);
            vertical-align: middle;
        }

        .cell-branch {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .cell-branch-dot {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 20px;
            font-weight: 900;
            flex-shrink: 0;
        }

        .cell-branch-name {
            font-size: 15px;
            font-weight: 800;
            color: var(--fg);
        }

        .cell-branch-loc {
            color: var(--muted);
            font-size: 12px;
            margin-top: 2px;
        }

        .cell-money {
            font-size: 14px;
            font-weight: 800;
        }

        .cell-money.positive { color: var(--green); }

        .cell-bar {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .cell-bar-track {
            width: 180px;
            max-width: 100%;
            height: 6px;
            border-radius: 999px;
            background: #EEF0F4;
            overflow: hidden;
        }

        .cell-bar-fill {
            height: 100%;
            border-radius: inherit;
        }

        .cell-bar-value {
            font-size: 14px;
            font-weight: 800;
            min-width: 42px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: var(--radius-full);
            font-size: 12px;
            font-weight: 800;
        }

        .status-badge.active {
            background: #D1FAE5;
            color: #059669;
        }

        .status-badge.inactive {
            background: #FEE2E2;
            color: #DC2626;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
        }

        .btn-sm {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            background: var(--white);
            color: var(--fg-secondary);
            font-size: 13px;
            font-weight: 800;
            text-decoration: none;
        }

        .bestseller-list {
            list-style: none;
            padding: 10px 22px 18px;
            margin: 0;
        }

        .bestseller-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 4px;
            border-bottom: 1px solid var(--border-light);
        }

        .bestseller-item:last-child {
            border-bottom: none;
        }

        .bestseller-rank {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 900;
            flex-shrink: 0;
            background: #EEF2FF;
            color: #4F46E5;
        }

        .bestseller-rank.gold { background: #FEF3C7; color: #D97706; }
        .bestseller-rank.silver { background: #E5E7EB; color: #4B5563; }
        .bestseller-rank.bronze { background: #FDE7D8; color: #C2410C; }

        .bestseller-info {
            flex: 1;
            min-width: 0;
        }

        .bestseller-name {
            font-size: 15px;
            font-weight: 800;
            color: var(--fg);
        }

        .bestseller-meta {
            color: var(--muted);
            font-size: 12px;
            margin-top: 2px;
        }

        .bestseller-qty {
            text-align: right;
            flex-shrink: 0;
        }

        .bestseller-qty-num {
            font-size: 18px;
            font-weight: 900;
            color: var(--accent-dark);
            line-height: 1;
        }

        .bestseller-qty-label {
            color: var(--muted);
            font-size: 11px;
            margin-top: 3px;
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 240px;
            color: var(--muted);
            text-align: center;
        }

        .empty-state i {
            font-size: 44px;
            color: #E5E7EB;
        }

        @media (max-width: 1280px) {
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .two-col {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .dashboard-topbar-right {
                width: 100%;
                justify-content: flex-end;
            }

            .dashboard-hello-pill {
                display: none;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .section-card-header {
                padding: 18px 18px 16px;
                flex-direction: column;
                align-items: flex-start;
            }

            .data-table {
                min-width: 760px;
            }

            .section-card-body {
                overflow-x: auto;
            }
        }
    </style>
@endpush

@section('title', 'Dashboard Superadmin')
@section('kicker', $cafeBrand['name'] ?? 'cafecaf')
@section('page_title', 'Kontrol Semua Cabang')
@section('topbar_right')
    <div class="topbar-right dashboard-topbar-right">
        <div class="dashboard-hello-pill">
            <span>Hallo, {{ auth()->user()->name ?? 'Super Admin' }}</span>
        </div>
        <button class="topbar-btn" title="Notifikasi">
            <i class="far fa-bell"></i>
            <span class="notif-dot"></span>
        </button>
        <a href="{{ route('superadmin.settings.index') }}" class="topbar-btn" title="Pengaturan"><i class="fas fa-gear"></i></a>
    </div>
@endsection


@section('content')
    <div id="live-dashboard-container">
        @include('superadmin.live-dashboard')
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            let interval;
            let currentFetchController = null;
            const periodCache = new Map();
            const pendingRequests = new Map();

            const getCurrentPeriod = () => new URLSearchParams(window.location.search).get('period') || 'today';
            const getCurrentPage = () => new URLSearchParams(window.location.search).get('page') || '1';
            const cacheKey = (period, page = '1') => `${period}:${page}`;
            const buildFetchUrl = (period, page = '1') => `{{ route('superadmin.dashboard.live.fragment') }}?page=${page}&period=${period}`;

            const renderLiveHtml = (html) => {
                if (!html) return;
                const target = document.getElementById('live-dashboard-container');
                if (!target) return;
                target.innerHTML = html;
                document.querySelectorAll('.fade-in').forEach((el) => el.classList.add('visible'));
                bindPeriodPills();
            };

            const requestLiveHtml = (period, page = '1', abortPrevious = false) => {
                const key = cacheKey(period, page);

                if (periodCache.has(key)) {
                    return Promise.resolve(periodCache.get(key));
                }

                if (pendingRequests.has(key)) {
                    return pendingRequests.get(key);
                }

                if (abortPrevious && currentFetchController) {
                    currentFetchController.abort();
                }

                const controller = new AbortController();
                if (abortPrevious) {
                    currentFetchController = controller;
                }

                const request = fetch(buildFetchUrl(period, page), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html',
                    },
                    signal: controller.signal,
                })
                    .then((res) => {
                        if (res.redirected && res.url.includes('/login')) {
                            window.location.href = '{{ route("login") }}';
                            return null;
                        }
                        return res.text();
                    })
                    .then((html) => {
                        if (!html) return null;
                        if (html.includes('id="username"') || html.includes('name="username"')) {
                            window.location.href = '{{ route("login") }}';
                            return null;
                        }
                        periodCache.set(key, html);
                        return html;
                    })
                    .finally(() => {
                        pendingRequests.delete(key);
                        if (abortPrevious && currentFetchController === controller) {
                            currentFetchController = null;
                        }
                    });

                pendingRequests.set(key, request);
                return request;
            };

            const fetchLiveData = () => {
                const container = document.getElementById('live-dashboard-container');
                if (!container) {
                    clearInterval(interval);
                    return;
                }

                requestLiveHtml(getCurrentPeriod(), getCurrentPage(), true)
                    .then(renderLiveHtml)
                    .catch((err) => {
                        if (err?.name !== 'AbortError') {
                            console.error('Error fetching live dashboard data:', err);
                        }
                    });
            };

            const bindPeriodPills = () => {
                const container = document.getElementById('dashboardPeriodPills');
                if (!container || container.dataset.bound === '1') return;
                container.dataset.bound = '1';
                container.addEventListener('click', (e) => {
                    const btn = e.target.closest('[data-period]');
                    if (!btn) return;
                    e.preventDefault();
                    const period = btn.getAttribute('data-period') || 'today';
                    container.querySelectorAll('[data-period]').forEach((item) => item.classList.remove('active'));
                    btn.classList.add('active');
                    const url = new URL(window.location.href);
                    url.searchParams.set('period', period);
                    url.searchParams.delete('page');
                    history.replaceState({}, '', url.toString());
                    requestLiveHtml(period, '1', true)
                        .then(renderLiveHtml)
                        .catch((err) => {
                            if (err?.name !== 'AbortError') {
                                console.error('Error switching dashboard period:', err);
                            }
                        });
                });
            };

            const warmPeriodCache = () => {
                ['today', 'week', 'month'].forEach((period) => {
                    requestLiveHtml(period, '1', false).catch(() => {});
                });
            };

            const startPolling = () => {
                clearInterval(interval);
                const container = document.getElementById('live-dashboard-container');
                if (container) {
                    bindPeriodPills();
                    warmPeriodCache();
                    container.addEventListener('click', (e) => {
                        const link = e.target.closest('.pagination-link');
                        if (link && link.href) {
                            e.preventDefault();
                            const url = new URL(link.href);
                            const newPage = url.searchParams.get('page');
                            if (newPage) {
                                const currentUrl = new URL(window.location.href);
                                currentUrl.searchParams.set('page', newPage);
                                const newUrl = currentUrl.toString();
                                window.history.pushState({ path: newUrl }, '', newUrl);
                                requestLiveHtml(getCurrentPeriod(), newPage, true)
                                    .then(renderLiveHtml)
                                    .catch((err) => {
                                        if (err?.name !== 'AbortError') {
                                            console.error('Error fetching dashboard page:', err);
                                        }
                                    });
                            }
                        }
                    });

                    interval = setInterval(() => {
                        if (document.visibilityState === 'visible') {
                            fetchLiveData();
                        }
                    }, 30000);
                }
            };

            document.addEventListener('turbo:load', startPolling);
            document.addEventListener('turbo:before-cache', () => clearInterval(interval));

            if (document.readyState === 'complete') startPolling();
        })();
    </script>
@endpush

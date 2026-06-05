@extends('superadmin.layout')

@section('title', 'Dashboard Superadmin')
@section('kicker', $cafeBrand['name'] ?? 'cafecaf')
@section('page_title', 'Kontrol Semua Cabang')
@section('topbar_right')
    <div class="topbar-right dashboard-topbar-right">
        <div class="topbar-search dashboard-hello-pill">
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
            const fetchLiveData = () => {
                const container = document.getElementById('live-dashboard-container');
                if (!container) {
                    clearInterval(interval);
                    return;
                }
                
                // Get current page from URL to keep pagination consistent
                const urlParams = new URLSearchParams(window.location.search);
                const currentPage = urlParams.get('page') || 1;
                const period = urlParams.get('period') || 'today';
                const fetchUrl = `{{ route('superadmin.dashboard.live.fragment') }}?page=${currentPage}&period=${period}`;

                fetch(fetchUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html',
                    },
                })
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

                        const target = document.getElementById('live-dashboard-container');
                        if (target) {
                            target.innerHTML = html;
                            document.querySelectorAll('.fade-in').forEach(el => el.classList.add('visible'));
                            bindPeriodPills();
                        }
                    })
                    .catch(err => console.error('Error fetching live dashboard data:', err));
            };

            const bindPeriodPills = () => {
                const container = document.getElementById('dashboardPeriodPills');
                if (!container || container.dataset.bound === '1') return;
                container.dataset.bound = '1';
                container.addEventListener('click', (e) => {
                    const btn = e.target.closest('[data-period]');
                    if (!btn) return;
                    const period = btn.getAttribute('data-period') || 'today';
                    const url = new URL(window.location.href);
                    url.searchParams.set('period', period);
                    url.searchParams.delete('page');
                    history.replaceState({}, '', url.toString());
                    fetchLiveData();
                });
            };

            const startPolling = () => {
                clearInterval(interval);
                const container = document.getElementById('live-dashboard-container');
                if (container) {
                    bindPeriodPills();
                    // Handle pagination clicks via AJAX
                    container.addEventListener('click', (e) => {
                        const link = e.target.closest('.pagination-link');
                        if (link && link.href) {
                            e.preventDefault();
                            const url = new URL(link.href);
                            const newPage = url.searchParams.get('page');
                            if (newPage) {
                                // Update URL without reloading or scrolling
                                const currentUrl = new URL(window.location.href);
                                currentUrl.searchParams.set('page', newPage);
                                const newUrl = currentUrl.toString();
                                window.history.pushState({ path: newUrl }, '', newUrl);
                                fetchLiveData();
                            }
                        }
                    });

                    interval = setInterval(() => {
                        if (document.visibilityState === 'visible') {
                            fetchLiveData();
                        }
                    }, 30000); // 30 seconds for superadmin dashboard to reduce load
                }
            };

            document.addEventListener('turbo:load', startPolling);
            document.addEventListener('turbo:before-cache', () => clearInterval(interval));

            if (document.readyState === 'complete') startPolling();
        })();
    </script>
@endpush

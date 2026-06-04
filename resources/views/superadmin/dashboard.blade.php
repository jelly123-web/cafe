@extends('superadmin.layout')

@section('title', 'Dashboard Superadmin')
@section('kicker', 'Halo, ' . (auth()->user()->name ?? 'User') . ' 👋')
@section('page_title', 'Kontrol Semua Cabang')
@section('page_description', 'Ringkasan penjualan, transaksi hari ini, laba/rugi, dan menu terlaris.')

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
                const fetchUrl = `{{ route('superadmin.dashboard.live.fragment') }}?page=${currentPage}`;

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
                        if (target) target.innerHTML = html;
                    })
                    .catch(err => console.error('Error fetching live dashboard data:', err));
            };

            const startPolling = () => {
                clearInterval(interval);
                const container = document.getElementById('live-dashboard-container');
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
                                fetchLiveData();
                            }
                        }
                    });

                    interval = setInterval(() => {
                        if (document.visibilityState === 'visible') {
                            fetchLiveData();
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

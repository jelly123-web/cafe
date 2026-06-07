@extends('kitchen.layout')

@section('title', 'Dapur - MakanYuk')

@push('head')
<style>
    .page-body { padding: 0; }
    .page-shell { max-width: 1400px; margin: 0 auto; padding: 28px 32px; }
    .dashboard-topbar { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 28px; flex-wrap: wrap; }
    .dashboard-topbar-left h1 { font-size: 22px; font-weight: 900; color: var(--fg); letter-spacing: -0.3px; margin-bottom: 4px; }
    .dashboard-topbar-left p { font-size: 13px; color: var(--muted); font-weight: 500; }
    .dashboard-topbar-right { display: flex; align-items: center; gap: 8px; }
    .dashboard-hello-pill { display: inline-flex; align-items: center; gap: 8px; min-height: 38px; padding: 0 16px; border: 1px solid var(--border); border-radius: var(--radius-full); background: var(--white); color: var(--fg-secondary); font-size: 13px; font-weight: 700; white-space: nowrap; }
    .live-indicator { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: var(--radius-full); background: var(--green-light); color: var(--green); font-size: 12px; font-weight: 800; letter-spacing: 0.3px; }
    .live-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--green); animation: dotPulse 2s infinite; }
    @keyframes dotPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

    .stats-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 20px; margin-bottom: 28px; }
    .stat-card { background: var(--white); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 26px; box-shadow: var(--shadow-xs); transition: all 0.25s ease; }
    .stat-card:hover { box-shadow: var(--shadow-sm); transform: translateY(-2px); }
    .stat-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
    .stat-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
    .stat-icon.amber { background: #FEF3C7; color: #D97706; }
    .stat-icon.blue { background: #DBEAFE; color: #2563EB; }
    .stat-icon.green { background: #D1FAE5; color: #059669; }
    .stat-icon.red { background: #FEE2E2; color: #DC2626; }
    .stat-trend { display: inline-flex; align-items: center; gap: 4px; padding: 6px 12px; border-radius: var(--radius-full); font-size: 12px; font-weight: 800; }
    .stat-trend.up { background: #D1FAE5; color: #059669; }
    .stat-trend.down { background: #FEE2E2; color: #DC2626; }
    .stat-trend.neutral { background: #F3F4F6; color: var(--muted); }
    .stat-value { font-size: 28px; font-weight: 900; line-height: 1.1; color: var(--fg); margin-bottom: 8px; }
    .stat-label { color: var(--muted); font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.2px; }

    .section-card { background: var(--white); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-xs); margin-bottom: 24px; }
    .section-card-header { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 24px 26px 20px; border-bottom: 1px solid var(--border); }
    .section-card-title { display: flex; align-items: center; gap: 10px; font-size: 18px; font-weight: 900; color: var(--fg); }
    .section-card-title i { color: var(--accent); }
    .section-card-actions { display: flex; align-items: center; gap: 10px; }
    .section-card-body { padding: 20px 26px 26px; }

    .filter-pills { display: inline-flex; align-items: center; gap: 4px; padding: 4px; border-radius: var(--radius-full); background: #F3F4F6; }
    .filter-pill { border: none; background: transparent; color: var(--fg-secondary); border-radius: var(--radius-full); padding: 10px 16px; font-size: 13px; font-weight: 800; font-family: var(--font); cursor: pointer; transition: var(--transition); display: inline-flex; align-items: center; justify-content: center; }
    .filter-pill.active { background: var(--white); color: var(--fg); box-shadow: var(--shadow-sm); }
    .filter-pill:hover:not(.active) { color: var(--fg); }

    .order-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 20px; }
    .order-card { background: var(--white); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; transition: all 0.25s ease; animation: cardIn 0.35s ease; position: relative; }
    @keyframes cardIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .order-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; transition: background 0.2s ease; }
    .order-card[data-status="pending"]::before { background: #E65100; }
    .order-card[data-status="processing"]::before { background: var(--accent); }
    .order-card[data-status="ready"]::before { background: #4F46E5; }
    .order-card[data-status="completed"]::before { background: var(--green); }
    .order-card:hover { border-color: transparent; box-shadow: var(--shadow-lg); transform: translateY(-3px); }
    .order-card-head { padding: 20px 20px 0; display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; }
    .order-card-head h3 { font-size: 17px; font-weight: 900; color: var(--fg); letter-spacing: -0.3px; margin-top: 6px; font-family: 'SF Mono', 'Fira Code', monospace; }
    .order-meta { display: flex; flex-direction: column; gap: 3px; text-align: right; font-size: 12px; color: var(--muted); font-weight: 600; }
    .order-meta i { margin-right: 4px; width: 14px; text-align: center; color: var(--fg-secondary); }

    .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: var(--radius-full); font-size: 11px; font-weight: 800; letter-spacing: 0.3px; text-transform: uppercase; }
    .status-dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; }
    .status-badge.pending { background: #FFF3E0; color: #E65100; }
    .status-badge.pending .status-dot { animation: dotPulse 1.2s infinite; }
    .status-badge.cooking { background: #FFFBEB; color: var(--accent); }
    .status-badge.cooking .status-dot { animation: dotPulse 0.8s infinite; }
    .status-badge.ready { background: #EEF2FF; color: #4F46E5; }
    .status-badge.done { background: var(--green-light); color: var(--green); }

    .order-items { padding: 16px 20px; }
    .items-title { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: var(--muted); margin-bottom: 10px; display: flex; align-items: center; gap: 6px; }
    .items-title i { color: var(--accent); font-size: 12px; }
    .item-row { display: flex; align-items: center; gap: 10px; padding: 8px 0; border-bottom: 1px solid var(--border-light); font-size: 14px; color: var(--fg-secondary); font-weight: 500; }
    .item-row:last-child { border-bottom: none; }
    .item-qty { display: inline-flex; align-items: center; justify-content: center; min-width: 28px; height: 28px; border-radius: 8px; background: #FFFBEB; color: var(--accent-dark); font-weight: 900; font-size: 12px; flex-shrink: 0; }
    .item-name { flex: 1; }

    .order-note { margin: 0 20px; padding: 14px 16px; background: #FFFBEB; border-left: 4px solid var(--accent); border-radius: 0 var(--radius-sm) var(--radius-sm) 0; font-size: 13px; color: #92400E; font-style: italic; display: flex; flex-direction: column; gap: 10px; }
    .note-speak-btn { display: inline-flex; align-items: center; gap: 6px; border: 1px solid rgba(217, 119, 6, 0.3); background: var(--white); color: var(--accent-dark); border-radius: var(--radius-full); padding: 6px 14px; font-family: var(--font); font-size: 11px; font-weight: 800; cursor: pointer; transition: all var(--transition); width: fit-content; }
    .note-speak-btn:hover { background: #FFFBEB; border-color: var(--accent); transform: translateY(-1px); }
    .note-speak-btn.is-playing { background: var(--accent); color: var(--white); border-color: var(--accent); }

    .order-actions { padding: 16px 20px; border-top: 1px solid var(--border-light); display: flex; gap: 8px; flex-wrap: wrap; background: #FAFBFC; }
    .order-actions form { display: inline-flex; }
    .btn-action { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 18px; border-radius: var(--radius-md); font-weight: 800; font-size: 13px; cursor: pointer; transition: all var(--transition); font-family: var(--font); border: 1.5px solid transparent; }
    .btn-action:hover { transform: translateY(-1px); }
    .btn-cook { background: #FFFBEB; color: var(--accent-dark); border-color: rgba(217, 119, 6, 0.2); }
    .btn-cook:hover { background: var(--accent); color: var(--white); border-color: var(--accent); box-shadow: 0 4px 12px rgba(217, 119, 6, 0.3); }
    .btn-ready { background: #EEF2FF; color: #4F46E5; border-color: rgba(79, 70, 229, 0.2); }
    .btn-ready:hover { background: #4F46E5; color: var(--white); border-color: #4F46E5; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); }
    .btn-done { background: var(--green-light); color: var(--green); border-color: rgba(5, 150, 105, 0.2); }
    .btn-done:hover { background: var(--green); color: var(--white); border-color: var(--green); box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3); }

    .alert-box { padding: 14px 20px; border-radius: var(--radius-md); margin-bottom: 20px; font-weight: 600; font-size: 13px; display: flex; align-items: center; gap: 10px; animation: cardIn 0.3s ease; }
    .alert-ok { background: var(--green-light); color: var(--green); border: 1px solid #A7F3D0; }
    .alert-err { background: var(--red-light); color: var(--red); border: 1px solid #FECACA; }

    .empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px; min-height: 280px; color: var(--muted); text-align: center; padding: 40px 24px; grid-column: 1 / -1; }
    .empty-state i { font-size: 48px; color: #E5E7EB; }
    .empty-state strong { font-size: 15px; color: var(--fg-secondary); }
    .empty-state span { font-size: 13px; max-width: 300px; }

    .pagination-area { margin-top: 24px; display: flex; align-items: center; justify-content: center; gap: 4px; }
    .pagination-area nav,
    .pagination-area .pagination { display: flex; align-items: center; justify-content: center; gap: 4px; flex-wrap: wrap; }
    .pagination-area a,
    .pagination-area span { display: inline-flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 700; text-decoration: none; border: 1px solid var(--border); color: var(--fg-secondary); padding: 0 10px; background: var(--white); transition: all var(--transition); font-family: var(--font); }
    .pagination-area a:hover { border-color: var(--accent); color: var(--accent); background: #FFFBEB; }
    .pagination-area .active,
    .pagination-area [aria-current="page"] span { background: var(--accent); border-color: var(--accent); color: white; }
    .pagination-area .disabled,
    .pagination-area [aria-disabled="true"] span { opacity: 0.35; pointer-events: none; }

    @media (max-width: 1280px) {
        .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 768px) {
        .page-shell { padding: 16px; }
        .dashboard-topbar { flex-direction: column; align-items: flex-start; }
        .dashboard-hello-pill { display: none; }
        .stats-grid { grid-template-columns: 1fr; gap: 12px; }
        .stat-card { padding: 20px; }
        .stat-value { font-size: 22px; }
        .order-grid { grid-template-columns: 1fr; }
        .section-card-header { padding: 18px 18px 16px; flex-direction: column; align-items: flex-start; }
        .section-card-body { padding: 16px; }
        .order-actions { flex-direction: column; }
        .order-actions form { width: 100%; }
        .btn-action { width: 100%; justify-content: center; }
    }
</style>
@endpush

@section('content')
    <div class="page-shell">
        <div class="dashboard-topbar">
            <div class="dashboard-topbar-left">
                <h1><i class="fas fa-fire-burner" style="color: var(--accent);"></i> Pesanan Masuk</h1>
                <p>Nomor pesanan, meja, pelanggan, dan waktu pesanan.</p>
            </div>
            <div class="dashboard-topbar-right">
                <div class="dashboard-hello-pill">
                    <i class="fas fa-hand-paper" style="color: var(--accent);"></i>
                    Halo, {{ auth()->user()->name ?? 'User' }}
                </div>
                <div class="live-indicator" id="liveIndicator">
                    <span class="live-dot"></span>
                    <span id="kitchenLiveText">Live</span>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert-box alert-ok"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert-box alert-err"><i class="fas fa-circle-xmark"></i> {{ session('error') }}</div>
        @endif

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon amber"><i class="fas fa-clock"></i></div>
                    <span class="stat-trend up"><i class="fas fa-arrow-up"></i> {{ $pendingCount }}</span>
                </div>
                <div class="stat-value">{{ $pendingCount }}</div>
                <div class="stat-label">Menunggu</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon blue"><i class="fas fa-fire"></i></div>
                    <span class="stat-trend neutral"><i class="fas fa-minus"></i> {{ $processingCount }}</span>
                </div>
                <div class="stat-value">{{ $processingCount }}</div>
                <div class="stat-label">Dimasak</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon green"><i class="fas fa-bell-concierge"></i></div>
                    <span class="stat-trend up"><i class="fas fa-arrow-up"></i> {{ $orders->where('status', \App\Models\SaleTransaction::STATUS_READY)->count() }}</span>
                </div>
                <div class="stat-value">{{ $orders->where('status', \App\Models\SaleTransaction::STATUS_READY)->count() }}</div>
                <div class="stat-label">Siap Saji</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon red"><i class="fas fa-check-double"></i></div>
                    <span class="stat-trend up"><i class="fas fa-arrow-up"></i> {{ $completedCount }}</span>
                </div>
                <div class="stat-value">{{ $completedCount }}</div>
                <div class="stat-label">Selesai Hari Ini</div>
            </div>
        </div>

        <div class="section-card">
            <div class="section-card-header">
                <div class="section-card-title">
                    <i class="fas fa-list-check"></i> Antrean Dapur
                </div>
                <div class="section-card-actions">
                    <div class="filter-pills">
                        <button class="filter-pill active" type="button" data-filter="all">Semua</button>
                        <button class="filter-pill" type="button" data-filter="pending">Menunggu</button>
                        <button class="filter-pill" type="button" data-filter="processing">Dimasak</button>
                        <button class="filter-pill" type="button" data-filter="ready">Siap Saji</button>
                    </div>
                </div>
            </div>
            <div class="section-card-body">
                <div class="order-grid" id="kitchenOrdersWrap">
                    @include('kitchen.partials.orders', [
                        'orders' => $orders,
                        'hasCustomerName' => $hasCustomerName,
                        'hasStatus' => $hasStatus,
                        'kitchenStatuses' => $kitchenStatuses,
                    ])
                </div>
                <div class="pagination-area">
                    {{ $orders->links('components.pagination') }}
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const wrap = document.getElementById('kitchenOrdersWrap');
            const liveText = document.getElementById('kitchenLiveText');
            if (!wrap) return;

            const onPageOne = Number(@json((int) request()->query('page', 1))) === 1;
            let lastTs = Number(@json(optional($orders->first()?->sold_at)?->timestamp ?? 0));
            let lastSignature = @json(sha1($orders->getCollection()->map(function ($order) {
                return implode(':', [
                    $order->id,
                    $order->status,
                    optional($order->updated_at)->timestamp ?? 0,
                ]);
            })->implode('|')));
            let activeFilter = 'all';
            const storageKey = 'cafe_live_sync_last_order_id';
            const channel = window.BroadcastChannel ? new BroadcastChannel('cafe-order-sync') : null;

            const statusClassMap = {
                pending: 'pending',
                processing: 'cooking',
                ready: 'ready',
                completed: 'done',
            };

            const statusLabelMap = {
                pending: 'Menunggu',
                processing: 'Dimasak',
                ready: 'Siap Saji',
                completed: 'Selesai',
            };

            const applyFilter = () => {
                wrap.querySelectorAll('.order-card').forEach((card) => {
                    card.style.display = activeFilter === 'all' || card.dataset.status === activeFilter ? '' : 'none';
                });
            };

            const poll = async () => {
                if (!onPageOne) return;
                liveText.textContent = 'Sync...';
                try {
                    const res = await fetch("{{ route('kitchen.orders.live') }}", {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin',
                    });
                    if (!res.ok) throw new Error('network');
                    const payload = await res.json();
                    if ((payload.signature || '') !== lastSignature) {
                        wrap.innerHTML = payload.html || '';
                        lastSignature = payload.signature || '';
                        applyFilter();
                    }
                    if (Number(payload.latest_ts || 0) > lastTs) {
                        lastTs = Number(payload.latest_ts || 0);
                        if (window.showToast) window.showToast('Pesanan baru masuk ke dapur.', 'success');
                    }
                    liveText.textContent = 'Live';
                } catch (e) {
                    liveText.textContent = 'Offline';
                }
            };

            const requestPoll = () => {
                if (document.visibilityState !== 'visible') return;
                poll();
            };

            let noteUtterance = null;
            let cachedKitchenVoice = null;

            const pickKitchenVoice = () => {
                if (cachedKitchenVoice) return cachedKitchenVoice;
                if (!window.speechSynthesis) return null;

                const voices = window.speechSynthesis.getVoices();
                if (!voices.length) return null;

                const indonesianVoices = voices.filter((voice) => {
                    const lang = String(voice.lang || '').toLowerCase();
                    const name = String(voice.name || '').toLowerCase();
                    return lang === 'id-id'
                        || lang === 'id'
                        || lang.startsWith('id-')
                        || name.includes('indonesia')
                        || name.includes('indonesian');
                });

                if (!indonesianVoices.length) {
                    cachedKitchenVoice = null;
                    return null;
                }

                const scoreVoice = (voice) => {
                    const name = String(voice.name || '').toLowerCase();
                    const lang = String(voice.lang || '').toLowerCase();
                    let score = 0;

                    if (lang === 'id-id') score += 120;
                    else if (lang.startsWith('id')) score += 100;
                    else if (lang === 'id') score += 95;
                    else if (lang.startsWith('ms')) score += 20;

                    if (name.includes('child')) score += 90;
                    if (name.includes('kid')) score += 90;
                    if (name.includes('children')) score += 90;
                    if (name.includes('little')) score += 45;
                    if (name.includes('cute')) score += 35;
                    if (name.includes('female')) score += 60;
                    if (name.includes('woman')) score += 60;
                    if (name.includes('girl')) score += 60;
                    if (name.includes('indonesia')) score += 80;
                    if (name.includes('indonesian')) score += 80;
                    if (name.includes('zira')) score += 40;
                    if (name.includes('katja')) score += 35;
                    if (name.includes('aria')) score += 25;
                    if (name.includes('google') && name.includes('indonesia')) score += 50;
                    if (name.includes('microsoft')) score += 15;
                    if (voice.default) score += 10;

                    return score;
                };

                cachedKitchenVoice = [...indonesianVoices].sort((a, b) => scoreVoice(b) - scoreVoice(a))[0] || null;
                return cachedKitchenVoice;
            };

            const stopNoteSpeech = () => {
                if (window.speechSynthesis) window.speechSynthesis.cancel();
                noteUtterance = null;
                document.querySelectorAll('.note-speak-btn.is-playing').forEach((btn) => {
                    btn.classList.remove('is-playing');
                    btn.innerHTML = '<i class="fas fa-volume-up"></i> Dengar catatan';
                });
            };

            const speakNote = (button) => {
                const note = String(button?.dataset?.note || '');
                if (!note) {
                    if (window.showToast) window.showToast('Catatan kosong.', 'error');
                    return;
                }
                if (!window.speechSynthesis || !window.SpeechSynthesisUtterance) {
                    if (window.showToast) window.showToast('Browser tidak mendukung TTS.', 'error');
                    return;
                }
                stopNoteSpeech();
                const utterance = new SpeechSynthesisUtterance(note);
                const selectedVoice = pickKitchenVoice();
                if (selectedVoice) {
                    utterance.voice = selectedVoice;
                    utterance.lang = selectedVoice.lang || 'id-ID';
                } else {
                    if (window.showToast) {
                        window.showToast('Voice Bahasa Indonesia belum tersedia di browser ini.', 'error');
                    }
                    return;
                }
                utterance.rate = 0.92;
                utterance.pitch = 1.35;
                utterance.volume = 1;
                utterance.text = note;
                utterance.onend = () => stopNoteSpeech();
                utterance.onerror = () => stopNoteSpeech();
                noteUtterance = utterance;
                button.classList.add('is-playing');
                button.innerHTML = '<i class="fas fa-stop"></i> Hentikan';
                window.speechSynthesis.speak(utterance);
            };

            if (window.speechSynthesis) {
                window.speechSynthesis.onvoiceschanged = () => {
                    cachedKitchenVoice = null;
                    pickKitchenVoice();
                };
                pickKitchenVoice();
            }

            document.querySelector('.filter-pills')?.addEventListener('click', (e) => {
                const btn = e.target.closest('.filter-pill');
                if (!btn) return;
                document.querySelectorAll('.filter-pill').forEach((pill) => pill.classList.remove('active'));
                btn.classList.add('active');
                activeFilter = btn.dataset.filter || 'all';
                applyFilter();
            });

            wrap.addEventListener('click', async (e) => {
                const noteButton = e.target.closest('button[data-speak-note]');
                if (noteButton) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (noteButton.classList.contains('is-playing')) stopNoteSpeech();
                    else speakNote(noteButton);
                    return;
                }

                const button = e.target.closest('button[type="submit"][name="status"]');
                if (!button) return;
                const form = button.closest('form.action-group');
                if (!(form instanceof HTMLFormElement)) return;
                e.preventDefault();

                const card = button.closest('.order-card');
                const badge = card ? card.querySelector('.status-badge') : null;
                const actions = card ? card.querySelector('.order-actions') : null;
                const nextStatus = button.value;

                if (badge) {
                    badge.className = 'status-badge ' + (statusClassMap[nextStatus] || 'pending');
                    badge.innerHTML = '<span class="status-dot"></span> ' + (statusLabelMap[nextStatus] || nextStatus);
                }
                if (card) {
                    card.setAttribute('data-status', nextStatus);
                    card.style.opacity = '0.72';
                }
                if (actions) {
                    if (nextStatus === 'processing') {
                        actions.innerHTML = '<form method="POST" action="' + form.action + '" class="action-group"><input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="PUT"><button type="submit" name="status" value="ready" class="btn-action btn-ready"><i class="fas fa-bell"></i> Siap Saji</button></form>';
                    } else if (nextStatus === 'ready') {
                        actions.innerHTML = '<form method="POST" action="' + form.action + '" class="action-group"><input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="PUT"><button type="submit" name="status" value="completed" class="btn-action btn-done"><i class="fas fa-check-circle"></i> Selesai</button></form>';
                    }
                }

                if (nextStatus === 'completed' && card) {
                    card.style.transition = 'all .4s ease';
                    card.style.maxHeight = card.offsetHeight + 'px';
                    requestAnimationFrame(() => {
                        card.style.opacity = '0';
                        card.style.maxHeight = '0px';
                        card.style.marginTop = '0';
                        card.style.marginBottom = '0';
                        card.style.paddingTop = '0';
                        card.style.paddingBottom = '0';
                        card.style.overflow = 'hidden';
                        card.style.borderWidth = '0';
                    });
                    setTimeout(() => {
                        if (card.parentNode) card.parentNode.removeChild(card);
                    }, 450);
                }

                try {
                const data = new FormData(form);
                data.set('status', button.value);
                const res = await fetch(form.action, {
                    method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: data,
                        credentials: 'same-origin',
                    });
                const payload = await res.json();
                if (!res.ok || payload.ok === false) throw new Error(payload.message || 'Gagal update status.');
                lastSignature = '';
                if (window.showToast) window.showToast(payload.message || 'Status pesanan diperbarui!', 'success');
                applyFilter();
                poll();
                } catch (err) {
                    poll();
                    if (window.showToast) window.showToast(err.message || 'Gagal update status.', 'error');
                }
            });

            setInterval(() => {
                if (document.visibilityState === 'visible') poll();
            }, 4000);

            window.addEventListener('beforeunload', stopNoteSpeech);
            window.addEventListener('cafe:order-sync', requestPoll);
            window.addEventListener('storage', (event) => {
                if (event.key === storageKey) requestPoll();
            });
            channel?.addEventListener('message', requestPoll);

            applyFilter();
        })();
    </script>
@endsection

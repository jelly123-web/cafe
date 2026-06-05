@extends('kitchen.layout')

@section('title', ($cafeBrand['name'] ?? config('app.name')) . ' - Dapur')

@push('head')
    <style>
        .main-panel { padding: 2rem 2.5rem; overflow-y: auto; }
        .kitchen-shell { max-width: 100%; }
        .panel { background: var(--bg-card); border: 1px solid var(--accent); border-radius: 20px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; box-shadow: 0 4px 15px var(--shadow); }
        .page-title { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.8rem; margin: 0 0 0.5rem; }
        .page-desc { color: var(--text-muted); font-size: 0.95rem; margin: 0; }
        .alert { padding: 0.85rem 1.25rem; border-radius: 14px; margin-bottom: 1.25rem; font-weight: 500; font-size: 0.95rem; border: 1px solid transparent; }
        .ok { background: #E8F5E9; color: #558B2F; border-color: #C8E6C9; }
        .err { background: #FFEBEE; color: #C62828; border-color: #FFCDD2; }
        .panel-head { display: flex; justify-content: space-between; gap: 1rem; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; border-bottom: 1px solid var(--accent); padding-bottom: 1rem; }
        .panel-head h2 { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.4rem; margin: 0; }
        .panel-head p { color: var(--text-muted); margin: 0; font-size: 0.9rem; }
        .orders { display: grid; gap: 1.25rem; }
        .order-card { background: #FFFAF5; border: 1px solid var(--accent); border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px var(--shadow); transition: all 0.2s ease; }
        .order-card:hover { border-color: var(--highlight); box-shadow: 0 8px 25px var(--shadow); }
        .order-top { display: flex; justify-content: space-between; gap: 1rem; flex-wrap: wrap; align-items: flex-start; margin-bottom: 1rem; }
        .order-top h3 { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.2rem; margin: 0.5rem 0; }
        .pill { display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 8px; background: rgba(212, 163, 115, 0.15); color: var(--highlight); font-size: 0.85rem; font-weight: 700; letter-spacing: 0.5px; }
        .order-meta { color: var(--text-muted); font-size: 0.9rem; display: flex; flex-direction: column; gap: 0.25rem; text-align: right; }
        .order-meta strong { color: var(--primary); }
        .item-list { padding-top: 1rem; border-top: 1px dashed var(--accent); margin-bottom: 1rem; }
        .detail-title { color: var(--primary); font-weight: 700; margin-bottom: 0.5rem; }
        .detail-meta { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.7rem; }
        .item-row { display: flex; justify-content: space-between; padding: 0.6rem 0; border-bottom: 1px dashed var(--accent); font-size: 0.95rem; }
        .item-row:last-child { border-bottom: none; }
        .item-qty { font-weight: 700; color: var(--highlight); margin-right: 0.5rem; }
        .order-note { padding: 0.75rem 1rem; background: #FFF8E1; border-left: 4px solid var(--highlight); border-radius: 0 8px 8px 0; margin-bottom: 1.25rem; font-size: 0.9rem; color: #8D6E63; font-style: italic; }
        .order-note-text { margin-bottom: 0.55rem; }
        .note-speak-btn { display: inline-flex; align-items: center; gap: 0.35rem; border: 1px solid rgba(212, 163, 115, 0.45); background: #fff; color: var(--primary); border-radius: 999px; padding: 0.4rem 0.75rem; font: inherit; font-size: 0.82rem; font-weight: 700; cursor: pointer; transition: all 0.18s ease; }
        .note-speak-btn:hover { background: rgba(212, 163, 115, 0.1); border-color: var(--highlight); transform: translateY(-1px); }
        .note-speak-btn.is-playing { background: var(--highlight); color: #fff; border-color: var(--highlight); }
        .action-group { display: flex; gap: 0.75rem; flex-wrap: wrap; }
        .btn { border: 1px solid transparent; border-radius: 12px; padding: 0.65rem 1.2rem; cursor: pointer; font-weight: 600; font-family: inherit; font-size: 0.9rem; transition: all 0.2s ease; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .btn-primary { background: var(--highlight); color: #fff; border: none; box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3); }
        .btn-primary:hover { background: #c68b59; transform: translateY(-2px); }
        .btn-success { background: #81C784; color: #fff; border: none; box-shadow: 0 2px 8px rgba(129, 199, 132, 0.3); }
        .btn-success:hover { background: #66BB6A; transform: translateY(-2px); }
        .btn-secondary { background: #8b6b5c; color: #fff; border: none; }
        .btn-secondary:hover { background: #76584a; transform: translateY(-2px); }
        .tag { display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; margin-bottom: 0.25rem; }
        .tag-pending { background: #FFF3E0; color: #E65100; }
        .tag-cooking { background: #E3F2FD; color: #1565C0; }
        .tag-ready { background: #E8EAF6; color: #3949AB; }
        .tag-done { background: #E8F5E9; color: #558B2F; }
        .empty-state { color: var(--text-muted); font-style: italic; text-align: center; padding: 2rem 0; }
        .live-pill { display:inline-flex; align-items:center; gap:0.4rem; border:1px solid var(--accent); border-radius:999px; padding:0.25rem 0.7rem; font-size:0.8rem; color:var(--text-muted); }
        .live-dot { width:8px; height:8px; border-radius:50%; background:#81C784; }
        @media (max-width: 768px) {
            .main-panel { padding: 1.5rem 1rem; }
            .page-title { font-size: 1.5rem; }
            .panel { padding: 1.25rem; }
            .order-top { flex-direction: column; }
            .order-meta { text-align: left; }
        }
    </style>
@endpush

@section('content')
    <div class="kitchen-shell">
        <section class="panel">
            <span style="display: block; color: var(--highlight); font-weight: 700; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.25rem;">Halo, {{ auth()->user()->name ?? 'User' }} 👋</span>
            <h1 class="page-title">Pesanan Masuk</h1>
            <p class="page-desc">Menampilkan nomor pesanan, nomor meja, nama pelanggan (jika ada), dan waktu pesanan.</p>
        </section>

        @if (session('success'))
            <div class="alert ok">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert err">{{ session('error') }}</div>
        @endif

        <section class="panel">
            <div class="panel-head">
                <div>
                    <h2>Antrean Dapur</h2>
                    <p>Urutan terbaru di atas.</p>
                </div>
                <span class="live-pill"><span class="live-dot"></span><span id="kitchenLiveText">Live</span></span>
            </div>

            <div class="orders" id="kitchenOrdersWrap">
                @include('kitchen.partials.orders', [
                    'orders' => $orders,
                    'hasCustomerName' => $hasCustomerName,
                    'hasStatus' => $hasStatus,
                    'kitchenStatuses' => $kitchenStatuses,
                ])
            </div>
            <div class="pagination-wrap">
                {{ $orders->links('components.pagination') }}
            </div>
        </section>
    </div>
    <script>
        (function () {
            const wrap = document.getElementById('kitchenOrdersWrap');
            const liveText = document.getElementById('kitchenLiveText');
            if (!wrap) return;
            const onPageOne = Number(@json((int) request()->query('page', 1))) === 1;
            let lastTs = Number(@json(optional($orders->first()?->sold_at)?->timestamp ?? 0));
            const storageKey = 'cafe_live_sync_last_order_id';
            const channel = window.BroadcastChannel ? new BroadcastChannel('cafe-order-sync') : null;
            const poll = async () => {
                if (!onPageOne) return;
                liveText.textContent = 'Sync...';
                try {
                    const res = await fetch("{{ route('kitchen.orders.live') }}", { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    if (!res.ok) throw new Error('network');
                    const payload = await res.json();
                    wrap.innerHTML = payload.html || '';
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

            const statusClassMap = {
                pending: 'tag-pending',
                processing: 'tag-cooking',
                ready: 'tag-ready',
                completed: 'tag-done',
            };

            let noteUtterance = null;
            const stopNoteSpeech = () => {
                if (window.speechSynthesis) {
                    window.speechSynthesis.cancel();
                }
                noteUtterance = null;
                document.querySelectorAll('.note-speak-btn.is-playing').forEach((btn) => {
                    btn.classList.remove('is-playing');
                    btn.textContent = '🔊 Dengar catatan';
                });
            };

            const speakNote = (button) => {
                const note = String(button?.dataset?.note || '').trim();
                if (!note) {
                    if (window.showToast) window.showToast('Catatan kosong.', 'error');
                    return;
                }
                const SpeechSynthesisUtteranceCtor = window.SpeechSynthesisUtterance;
                if (!window.speechSynthesis || !SpeechSynthesisUtteranceCtor) {
                    if (window.showToast) window.showToast('Browser ini tidak mendukung pembacaan suara.', 'error');
                    return;
                }

                stopNoteSpeech();
                const utterance = new SpeechSynthesisUtteranceCtor(note);
                utterance.lang = 'id-ID';
                utterance.rate = 1;
                utterance.pitch = 1;
                utterance.volume = 1;
                utterance.onend = () => stopNoteSpeech();
                utterance.onerror = () => stopNoteSpeech();
                noteUtterance = utterance;
                button.classList.add('is-playing');
                button.textContent = '⏹ Hentikan bacaan';
                window.speechSynthesis.speak(utterance);
            };

            wrap.addEventListener('click', async (e) => {
                const noteButton = e.target.closest('button[data-speak-note]');
                if (noteButton) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (noteButton.classList.contains('is-playing')) {
                        stopNoteSpeech();
                    } else {
                        speakNote(noteButton);
                    }
                    return;
                }

                const button = e.target.closest('button[type="submit"][name="status"]');
                if (!button) return;

                const form = button.closest('form.action-group');
                if (!(form instanceof HTMLFormElement)) return;

                e.preventDefault();

                const card = button.closest('.order-card');
                const tag = card ? card.querySelector('.tag') : null;
                const nextStatus = button.value;
                const nextLabel = button.textContent.trim();

                if (tag) {
                    tag.className = 'tag ' + (statusClassMap[nextStatus] || 'tag-pending');
                    tag.textContent = nextLabel;
                }
                if (card) {
                    card.style.opacity = '0.72';
                }
                if (nextStatus === 'completed' && card) {
                    card.style.transition = 'all .18s ease';
                    card.style.maxHeight = card.offsetHeight + 'px';
                    requestAnimationFrame(() => {
                        card.style.opacity = '0';
                        card.style.maxHeight = '0px';
                        card.style.margin = '0';
                        card.style.paddingTop = '0';
                        card.style.paddingBottom = '0';
                        card.style.overflow = 'hidden';
                    });
                    setTimeout(() => {
                        if (card.parentNode) card.parentNode.removeChild(card);
                    }, 200);
                }

                const data = new FormData(form);
                data.set('status', button.value);

                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: data,
                        credentials: 'same-origin',
                    });

                    let payload = {};
                    try { payload = await res.json(); } catch (_) {}
                    if (!res.ok || payload.ok === false) {
                        throw new Error(payload.message || 'Gagal update status.');
                    }

                    if (window.showToast) {
                        window.showToast(payload.message || 'Status diperbarui.', 'success');
                    }
                    poll();
                } catch (err) {
                    poll();
                    if (window.showToast) {
                        window.showToast(err.message || 'Gagal update status.', 'error');
                    }
                }
            });

            setInterval(() => {
                if (document.visibilityState === 'visible') {
                    poll();
                }
            }, 1000);

            window.addEventListener('beforeunload', stopNoteSpeech);
            window.addEventListener('cafe:order-sync', requestPoll);
            window.addEventListener('storage', (event) => {
                if (event.key === storageKey) requestPoll();
            });
            channel?.addEventListener('message', requestPoll);
        })();
    </script>
@endsection

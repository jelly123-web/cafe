@once
    <style>
        .live-sync-toast-wrap {
            position: fixed;
            top: 16px;
            right: 16px;
            z-index: 2400;
            display: grid;
            gap: 10px;
            width: min(360px, calc(100vw - 32px));
        }

        .live-sync-toast {
            border-radius: 12px;
            padding: 0.85rem 1rem;
            background: #4caf50;
            color: #fff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .16);
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: flex-start;
            font-weight: 600;
            animation: live-sync-toast-in .22s ease;
        }

        .live-sync-toast small {
            display: block;
            margin-top: 3px;
            font-weight: 500;
            opacity: .9;
        }

        .live-sync-toast button {
            border: 0;
            background: transparent;
            color: #fff;
            font-weight: 800;
            cursor: pointer;
            line-height: 1;
        }

        @keyframes live-sync-toast-in {
            from { transform: translateY(-10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>

    <script>
        (function () {
            const liveUrl = @json(route('live-sync.orders'));
            const storageKey = 'cafe_live_sync_last_order_id';
            const pollDelay = 1500;
            let timer = null;
            let busy = false;

            const ensureToast = () => {
                let wrap = document.getElementById('liveSyncToastWrap');
                if (!wrap) {
                    wrap = document.createElement('div');
                    wrap.id = 'liveSyncToastWrap';
                    wrap.className = 'live-sync-toast-wrap';
                    document.body.appendChild(wrap);
                }
                return wrap;
            };

            const fallbackToast = (title, detail) => {
                const wrap = ensureToast();
                const el = document.createElement('div');
                el.className = 'live-sync-toast';
                el.innerHTML = '<span>' + title + (detail ? '<small>' + detail + '</small>' : '') + '</span><button type="button">x</button>';
                const close = () => {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(-8px)';
                    setTimeout(() => el.remove(), 180);
                };
                el.querySelector('button')?.addEventListener('click', close);
                wrap.appendChild(el);
                setTimeout(close, 3500);
            };

            const notify = (order) => {
                const message = 'Pesanan baru masuk: ' + order.code;
                const detail = [order.table_label, order.items_label].filter(Boolean).join(' - ');
                if (typeof window.showToast === 'function') {
                    window.showToast(detail ? message + ' | ' + detail : message, 'success', 4200);
                } else {
                    fallbackToast(message, detail);
                }
            };

            const poll = async () => {
                if (busy || document.visibilityState !== 'visible') {
                    return;
                }

                busy = true;
                try {
                    const res = await fetch(liveUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    });

                    if (!res.ok) {
                        return;
                    }

                    const payload = await res.json();
                    const latest = payload.latest;
                    if (!latest || !latest.id) {
                        return;
                    }

                    const previousId = Number(localStorage.getItem(storageKey) || 0);
                    const latestId = Number(latest.id);

                    if (!previousId) {
                        localStorage.setItem(storageKey, String(latestId));
                        return;
                    }

                    if (latestId > previousId) {
                        localStorage.setItem(storageKey, String(latestId));
                        notify(latest);
                        window.dispatchEvent(new CustomEvent('cafe:order-sync', {
                            detail: {
                                order: latest,
                                activeCount: payload.active_count || 0,
                            },
                        }));
                    }
                } catch (e) {
                    // Live sync is best-effort; page actions keep working if polling fails.
                } finally {
                    busy = false;
                }
            };

            const start = () => {
                clearInterval(timer);
                poll();
                timer = setInterval(poll, pollDelay);
            };

            document.addEventListener('turbo:load', start);
            document.addEventListener('DOMContentLoaded', start);
            document.addEventListener('turbo:before-cache', () => clearInterval(timer));
        })();
    </script>
@endonce

@extends('cashier.layout')

@section('title', ($cafeBrand['name'] ?? config('app.name')) . ' - Pembayaran Kasir')

@push('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    /* ===== VARIABEL DESAIN ===== */
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
      --red-light: #FEE2E9;
      --blue: #2563EB;
      --blue-light: #DBEAFE;
      --shadow-xs: 0 1px 2px rgba(0,0,0,0.03);
      --shadow-md: 0 4px 12px rgba(0,0,0,0.05);
      --radius-sm: 8px;
      --radius-md: 12px;
      --radius-lg: 16px;
      --radius-full: 999px;
      --font: 'Plus Jakarta Sans', -apple-system, sans-serif;
      --transition: 0.2s ease;
      --cash-color: #795548;
      --cash-light: #EFEBE9;
      --qris-color: #009688;
      --qris-light: #E0F2F1;
      --transfer-color: #7E57C2;
      --transfer-light: #EDE7F6;
    }

    /* ===== PANEL ===== */
    .panel { background: var(--white); border: 1px solid var(--border); border-radius: var(--radius-lg); margin-bottom: 20px; padding: 24px; box-shadow: var(--shadow-xs); }

    /* ===== PAYMENT GRID ===== */
    .payment-grid { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 24px; align-items: stretch; }
    .payment-col { display: flex; flex-direction: column; gap: 18px; }
    
    /* ===== SECTION HEAD ===== */
    .section-head { padding-bottom: 14px; border-bottom: 1px solid var(--border-light); }
    .section-head h2 { font-size: 15px; font-weight: 800; display: flex; align-items: center; gap: 8px; margin: 0; color: var(--fg); }
    .section-head h2 i { color: var(--accent); }

    /* ===== FIELD ===== */
    .field { display: flex; flex-direction: column; gap: 5px; }
    .field label { font-size: 12px; font-weight: 700; color: var(--fg-secondary); text-transform: uppercase; }
    .field input, .field select { width: 100%; border: 1.5px solid var(--border); border-radius: var(--radius-sm); padding: 10px 14px; background: var(--white); font-size: 14px; outline: none; transition: all var(--transition); }
    .field input:focus, .field select:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(217,119,6,0.1); }
    .scan-row { display:grid; grid-template-columns: 1fr 100px auto; gap: 10px; align-items: end; }
    .scan-actions { display:flex; gap:10px; flex-wrap:wrap; }
    .camera-box { display:none; margin-top: 12px; padding: 14px; border: 1px solid var(--border); border-radius: var(--radius-md); background: #FAFBFC; }
    .camera-box.open { display:block; }
    .camera-preview { position: relative; overflow: hidden; border-radius: var(--radius-md); background: #111827; aspect-ratio: 4 / 3; }
    .camera-preview video { width: 100%; height: 100%; object-fit: cover; display:block; }
    .camera-overlay { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; pointer-events:none; }
    .camera-frame { width:min(72%, 320px); height:min(42%, 180px); border:2px solid rgba(255,255,255,0.95); border-radius: 18px; box-shadow: 0 0 0 9999px rgba(0,0,0,0.2); }
    .camera-status { margin-top: 10px; font-size: 12px; font-weight: 700; color: var(--fg-secondary); }
    
    /* ===== BUTTONS ===== */
    .btn-soft { border: 1.5px solid var(--border); background: var(--white); color: var(--fg-secondary); border-radius: var(--radius-sm); padding: 10px 18px; cursor: pointer; font-weight: 700; font-size: 13px; transition: all var(--transition); }
    .btn-soft:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-light); }
    .btn-primary-wide { width: 100%; background: var(--accent); color: white; border: none; border-radius: var(--radius-md); padding: 13px 20px; font-weight: 800; cursor: pointer; transition: all var(--transition); }
    .btn-primary-wide:hover { background: var(--accent-dark); }
    
    /* ===== CART ITEMS ===== */
    .cart-list { display: grid; gap: 8px; margin-top: 10px; }
    .cart-item { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; padding: 14px 16px; border-radius: var(--radius-sm); border: 1px solid var(--border-light); }
    .cart-item div { display: block; }
    .cart-item strong { display: block; font-weight: 800; color: var(--fg); }
    .cart-item small { display: block; font-size: 11px; color: var(--muted); }
    .cart-total { display: flex; justify-content: space-between; align-items: center; margin-top: 16px; padding-top: 16px; border-top: 2px solid var(--border); font-weight: 800; font-size: 18px; color: var(--fg); }
    .cart-total span:last-child { color: var(--accent); }
    
    /* Order Cards */
    .order { border: 1px solid var(--border-light); border-radius: var(--radius-md); padding: 18px 20px; margin-bottom: 12px; }
    .order-code { display: block; font-size: 15px; font-weight: 800; color: var(--fg); }
    .order-meta { display: block; font-size: 12px; color: var(--muted); }
    .order-total { display: block; color: var(--fg); font-weight: 700; font-size: 14px; margin-top: 4px; }
    </style>
@endpush

@section('content')
    <h1 style="font-size:22px; font-weight:900; margin-bottom:20px;">Pembayaran Kasir</h1>
    <p style="color:var(--muted); margin-bottom:20px;">Scan barcode menu, masukkan ke keranjang pembayaran, lalu buat tagihan dan proses pembayaran.</p>

    <section class="panel">
        <div class="payment-grid">
            <div class="payment-col">
                <div class="section-head"><h2><i class="fas fa-barcode"></i> Scan Barcode Pembayaran</h2></div>
                <div class="scan-row">
                    <div class="field"><label>Barcode</label><input id="barcodeInput" type="text" autofocus></div>
                    <div class="field"><label>Qty</label><input id="qtyInput" type="number" min="1" step="1" value="1"></div>
                    <div class="scan-actions">
                        <button id="scanBtn" class="btn-soft" type="button"><i class="fas fa-barcode"></i> Scan</button>
                        <button id="cameraBtn" class="btn-soft" type="button"><i class="fas fa-camera"></i> Scan Kamera</button>
                        <button id="stopCameraBtn" class="btn-soft" type="button" hidden><i class="fas fa-circle-stop"></i> Tutup Kamera</button>
                    </div>
                </div>
                <div id="scanResult" style="margin-top:10px; padding: 1rem; border: 1px dashed var(--border); border-radius: var(--radius-md); background: #FAFBFC;">Siap untuk scan barcode.</div>
                <div id="cameraBox" class="camera-box">
                    <div class="camera-preview">
                        <video id="cameraVideo" playsinline muted></video>
                        <div class="camera-overlay"><div class="camera-frame"></div></div>
                    </div>
                    <div id="cameraStatus" class="camera-status">Arahkan kamera HP ke barcode menu.</div>
                </div>
            </div>

            <div class="payment-col">
                <div class="section-head"><h2><i class="fas fa-cart-shopping"></i> Keranjang Pembayaran</h2></div>
                <form method="POST" action="{{ route('cashier.payments.checkout') }}">
                    @csrf
                    <div id="cartList" class="cart-list">
                        @forelse ($cart['items'] as $row)
                            <div class="cart-item" data-menu-id="{{ $row['menu_id'] }}">
                                <div><strong>{{ $row['name'] }}</strong><br><small>{{ $row['code'] }} | Barcode: {{ $row['barcode'] }}<br>Qty: {{ $row['qty'] }} x Rp {{ number_format((float) $row['unit_price'], 0, ',', '.') }}</small></div>
                                <div>
                                    <strong>Rp {{ number_format((float) $row['line_total'], 0, ',', '.') }}</strong>
                                    <button type="button" class="btn-soft" data-remove-cart="{{ $row['menu_id'] }}" style="margin-top:8px; padding:.45rem .75rem; font-size:.8rem;"><i class="fas fa-trash"></i> Hapus</button>
                                </div>
                            </div>
                        @empty
                            <div style="text-align:center; padding: 20px; color:var(--muted);">Keranjang kosong.</div>
                        @endforelse
                    </div>
                    <div class="cart-total"><span>Total</span><span id="cartTotal">Rp {{ number_format((float) $cart['total'], 0, ',', '.') }}</span></div>
                    <button id="checkoutCartBtn" type="submit" class="btn-primary-wide" style="margin-top:20px;" {{ empty($cart['items']) ? 'disabled' : '' }}>Buat Tagihan</button>
                </form>
            </div>
        </div>
    </section>

    <section class="panel" id="live-payment-container">
        @include('cashier.payments.live')
    </section>
@endsection

@push('scripts')
<script>
    (() => {
        const csrfRefreshRoute = @json(route('csrf.token'));
        const scanRoute = @json(route('cashier.scanner.cart'));
        const removeCartBase = @json(route('cashier.payments.cart.destroy', ['menu' => '__MENU_ID__']));
        const barcodeInput = document.getElementById('barcodeInput');
        const qtyInput = document.getElementById('qtyInput');
        const scanBtn = document.getElementById('scanBtn');
        const cameraBtn = document.getElementById('cameraBtn');
        const stopCameraBtn = document.getElementById('stopCameraBtn');
        const cameraBox = document.getElementById('cameraBox');
        const cameraVideo = document.getElementById('cameraVideo');
        const cameraStatus = document.getElementById('cameraStatus');
        const scanResult = document.getElementById('scanResult');
        const cartList = document.getElementById('cartList');
        const cartTotal = document.getElementById('cartTotal');
        const checkoutBtn = document.getElementById('checkoutCartBtn');
        const initialCart = @json($cart['items']);
        const cartState = new Map((initialCart || []).map((item) => [Number(item.menu_id), { ...item }]));
        let barcodeDetector = null;
        let cameraStream = null;
        let cameraFrame = null;
        let cameraBusy = false;

        const wrap = document.getElementById('live-payment-container');
        if (!wrap) return;

        const storageKey = 'cafe_live_sync_last_order_id';
        const channel = window.BroadcastChannel ? new BroadcastChannel('cafe-order-sync') : null;
        let lastHtml = wrap.innerHTML.trim();
        let busy = false;

        const esc = (value) => String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');

        const formatRp = (value) => 'Rp ' + new Intl.NumberFormat('id-ID', {
            maximumFractionDigits: 0,
        }).format(Number(value || 0));

        const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const syncCsrfToken = (token) => {
            if (!token) return;
            const meta = document.querySelector('meta[name="csrf-token"]');
            if (meta) meta.setAttribute('content', token);
            document.querySelectorAll('input[name="_token"]').forEach((input) => {
                input.value = token;
            });
        };

        const refreshCsrfToken = async () => {
            const response = await fetch(csrfRefreshRoute, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            });

            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.token) {
                throw new Error('Sesi halaman kedaluwarsa. Muat ulang halaman lalu scan lagi.');
            }

            syncCsrfToken(data.token);
            return data.token;
        };

        const showResult = (html, error = false) => {
            if (!scanResult) return;
            scanResult.innerHTML = html;
            scanResult.style.borderColor = error ? '#ef9a9a' : '';
            scanResult.style.background = error ? '#fff5f5' : '#FAFBFC';
        };

        const renderCart = () => {
            if (!cartList || !cartTotal) return;
            const rows = Array.from(cartState.values()).sort((a, b) => a.menu_id - b.menu_id);
            cartList.innerHTML = '';

            if (!rows.length) {
                cartList.innerHTML = '<div style="text-align:center; padding: 20px; color:var(--muted);">Keranjang kosong.</div>';
                cartTotal.textContent = formatRp(0);
                if (checkoutBtn) checkoutBtn.disabled = true;
                return;
            }

            rows.forEach((row) => {
                const item = document.createElement('div');
                item.className = 'cart-item';
                item.setAttribute('data-menu-id', String(row.menu_id));
                item.innerHTML = `
                    <div>
                        <strong>${esc(row.name)}</strong>
                        <small>${esc(row.code)} | Barcode: ${esc(row.barcode)}</small>
                        <small>Qty: ${row.qty} x ${formatRp(row.unit_price)}</small>
                    </div>
                    <div>
                        <strong>${formatRp(row.line_total)}</strong>
                        <button type="button" class="btn-soft" data-remove-cart="${row.menu_id}" style="margin-top:8px; padding:.45rem .75rem; font-size:.8rem;"><i class="fas fa-trash"></i> Hapus</button>
                    </div>
                `;
                cartList.appendChild(item);
            });

            const total = rows.reduce((sum, row) => sum + Number(row.line_total || 0), 0);
            cartTotal.textContent = formatRp(total);
            if (checkoutBtn) checkoutBtn.disabled = false;
        };

        const upsertCartItem = (item, qty) => {
            const menuId = Number(item.id);
            const existing = cartState.get(menuId);
            const unitPrice = Number(item.selling_price || 0);
            if (existing) {
                existing.qty += qty;
                existing.line_total = existing.qty * existing.unit_price;
            } else {
                cartState.set(menuId, {
                    menu_id: menuId,
                    name: item.name,
                    code: item.code,
                    barcode: item.barcode || item.code,
                    unit_price: unitPrice,
                    qty: qty,
                    line_total: unitPrice * qty,
                });
            }
            renderCart();
        };

        const postJson = async (url, payload, retry = true) => {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload),
            });

            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                if (response.status === 419 && retry) {
                    await refreshCsrfToken();
                    return postJson(url, payload, false);
                }
                const err = new Error(data.message || 'Gagal memproses barcode.');
                err.status = response.status;
                throw err;
            }
            return data;
        };

        const focusBarcode = () => setTimeout(() => barcodeInput?.focus(), 60);

        const scanBarcode = async () => {
            const barcode = barcodeInput?.value?.trim();
            const qty = Math.max(1, parseInt(qtyInput?.value || '1', 10) || 1);
            if (!barcode) {
                showResult('<span>Barcode masih kosong.</span>', true);
                focusBarcode();
                return;
            }

            scanBtn.disabled = true;
            scanBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            try {
                const data = await postJson(scanRoute, { barcode, qty });
                showResult(`
                    <strong>${esc(data.item.name)}</strong><br>
                    <span>${formatRp(data.item.selling_price || 0)}</span><br>
                    <small>Berhasil masuk ke keranjang pembayaran.</small><br>
                    <small>${esc(data.message || '')}</small>
                `);
                upsertCartItem(data.item, qty);
                barcodeInput.value = '';
                qtyInput.value = '1';
            } catch (error) {
                showResult(`<span>${esc(error.message)}</span>`, true);
            } finally {
                scanBtn.disabled = false;
                scanBtn.innerHTML = '<i class="fas fa-barcode"></i> Scan';
                focusBarcode();
            }
        };

        const ensureDetector = async () => {
            if (barcodeDetector) return barcodeDetector;
            if (!window.isSecureContext) {
                throw new Error('Scan kamera butuh HTTPS atau localhost yang aman.');
            }
            if (!('mediaDevices' in navigator) || !navigator.mediaDevices.getUserMedia) {
                throw new Error('Browser ini tidak mendukung akses kamera.');
            }
            if (!('BarcodeDetector' in window)) {
                throw new Error('Browser ini belum mendukung scan barcode kamera.');
            }

            const preferredFormats = ['ean_13', 'ean_8', 'code_128', 'code_39', 'upc_a', 'upc_e', 'itf', 'codabar', 'qr_code'];
            let formats = preferredFormats;
            if (typeof window.BarcodeDetector.getSupportedFormats === 'function') {
                const supported = await window.BarcodeDetector.getSupportedFormats();
                const filtered = preferredFormats.filter((format) => supported.includes(format));
                if (filtered.length) formats = filtered;
            }

            barcodeDetector = new window.BarcodeDetector({ formats });
            return barcodeDetector;
        };

        const stopCamera = () => {
            if (cameraFrame) {
                cancelAnimationFrame(cameraFrame);
                cameraFrame = null;
            }
            if (cameraStream) {
                cameraStream.getTracks().forEach((track) => track.stop());
                cameraStream = null;
            }
            cameraBusy = false;
            if (cameraVideo) {
                cameraVideo.pause?.();
                cameraVideo.srcObject = null;
            }
            cameraBox?.classList.remove('open');
            if (cameraBtn) cameraBtn.hidden = false;
            if (stopCameraBtn) stopCameraBtn.hidden = true;
        };

        const detectFromCamera = async () => {
            if (!cameraVideo || !barcodeDetector || cameraBusy) {
                cameraFrame = requestAnimationFrame(detectFromCamera);
                return;
            }

            cameraBusy = true;
            try {
                const results = await barcodeDetector.detect(cameraVideo);
                const rawValue = results?.find((result) => String(result.rawValue || '').trim())?.rawValue?.trim();
                if (rawValue) {
                    barcodeInput.value = rawValue;
                    if (cameraStatus) cameraStatus.textContent = `Barcode terdeteksi: ${rawValue}`;
                    stopCamera();
                    await scanBarcode();
                    return;
                }
            } catch (error) {
                if (cameraStatus) cameraStatus.textContent = 'Kamera aktif. Arahkan ke barcode dengan pencahayaan cukup.';
            } finally {
                cameraBusy = false;
            }

            cameraFrame = requestAnimationFrame(detectFromCamera);
        };

        const startCamera = async () => {
            try {
                await ensureDetector();
                cameraStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: { ideal: 'environment' },
                    },
                    audio: false,
                });
                if (cameraVideo) {
                    cameraVideo.srcObject = cameraStream;
                    await cameraVideo.play();
                }
                cameraBox?.classList.add('open');
                if (cameraBtn) cameraBtn.hidden = true;
                if (stopCameraBtn) stopCameraBtn.hidden = false;
                if (cameraStatus) cameraStatus.textContent = 'Kamera aktif. Arahkan barcode ke dalam kotak.';
                cameraFrame = requestAnimationFrame(detectFromCamera);
            } catch (error) {
                stopCamera();
                showResult(`<span>${esc(error.message || 'Tidak bisa membuka kamera.')}</span>`, true);
            }
        };

        const refreshPayments = async () => {
            if (busy || document.visibilityState !== 'visible') return;
            busy = true;
            try {
                const res = await fetch("{{ route('cashier.payments.live') }}?page={{ (int) request()->query('page', 1) }}", {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html',
                    },
                    credentials: 'same-origin',
                });
                if (!res.ok) return;
                const html = (await res.text()).trim();
                if (html !== lastHtml) {
                    wrap.innerHTML = html;
                    lastHtml = html;
                }
            } catch (e) {
            } finally {
                busy = false;
            }
        };

        const requestRefresh = () => {
            if (document.visibilityState === 'visible') refreshPayments();
        };

        setInterval(requestRefresh, 4000);
        window.addEventListener('cafe:order-sync', requestRefresh);
        window.addEventListener('storage', (event) => {
            if (event.key === storageKey) requestRefresh();
        });
        channel?.addEventListener('message', requestRefresh);
        scanBtn?.addEventListener('click', scanBarcode);
        cameraBtn?.addEventListener('click', startCamera);
        stopCameraBtn?.addEventListener('click', stopCamera);
        barcodeInput?.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                scanBarcode();
            }
        });
        cartList?.addEventListener('click', async (event) => {
            const btn = event.target.closest('[data-remove-cart]');
            if (!btn) return;
            const menuId = Number(btn.getAttribute('data-remove-cart'));
            if (!menuId) return;

            btn.disabled = true;
            btn.textContent = 'Menghapus...';
            try {
                const response = await fetch(removeCartBase.replace('__MENU_ID__', String(menuId)), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                });
                if (response.status === 419) {
                    await refreshCsrfToken();
                    throw new Error('Token halaman diperbarui. Coba hapus item sekali lagi.');
                }
                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(data.message || 'Gagal menghapus item.');
                }
                cartState.delete(menuId);
                renderCart();
                showResult(`<strong>${esc(data.message || 'Item dihapus.')}</strong>`);
            } catch (error) {
                showResult(`<span>${esc(error.message)}</span>`, true);
            } finally {
                focusBarcode();
            }
        });
        window.addEventListener('focus', focusBarcode);
        document.addEventListener('turbo:load', focusBarcode);
        document.addEventListener('turbo:before-cache', stopCamera);
        renderCart();
        focusBarcode();
    })();
</script>
@endpush

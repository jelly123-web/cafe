@extends('cashier.layout')

@section('title', ($cafeBrand['name'] ?? config('app.name')) . ' - Pembayaran Kasir')

@push('head')
    <style>
        :root { --cash-color:#6D4C41; --qris-color:#4DB6AC; --transfer-color:#7E57C2; --loss:#C62828; }
        .main-panel { padding: 2rem 2.5rem; overflow-y: auto; }
        .pos-shell { max-width: 100%; }
        .panel { background: var(--bg-card); border: 1px solid var(--accent); border-radius: 20px; padding: 1.75rem 2.15rem; margin-bottom: 1.5rem; box-shadow: 0 4px 15px var(--shadow); }
        .page-title { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.8rem; margin: 0 0 0.5rem; }
        .page-desc { color: var(--text-muted); font-size: 0.95rem; margin: 0; }
        .payment-grid { display: grid; grid-template-columns: minmax(0, 1.08fr) minmax(360px, 0.92fr); gap: 2.25rem; align-items: start; }
        .payment-col { display: grid; gap: 1.25rem; min-width: 0; }
        .split-section {
            display: grid;
            gap: 1.35rem;
            padding: 0 0.1rem;
        }
        .split-section + .split-section {
            border-left: 1px solid rgba(212, 163, 115, 0.2);
            padding-left: 2rem;
        }
        .section-head {
            display: grid;
            gap: 0.65rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(212, 163, 115, 0.45);
        }
        .scan-box { display: grid; gap: 1.25rem; }
        .barcode-camera-box {
            display: grid;
            gap: 0.85rem;
            padding: 1rem;
            border: 1px solid rgba(212, 163, 115, 0.22);
            border-radius: 18px;
            background: linear-gradient(180deg, #fffdf9 0%, #fff7ef 100%);
        }
        .barcode-camera-preview {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            min-height: 220px;
            background: #120d0b;
            border: 1px solid rgba(121, 85, 72, 0.12);
        }
        .barcode-camera-video {
            width: 100%;
            height: 100%;
            min-height: 220px;
            object-fit: cover;
            display: block;
            background: #120d0b;
        }
        .barcode-camera-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            text-align: center;
            color: #fff;
            background: linear-gradient(180deg, rgba(18,13,11,0.12), rgba(18,13,11,0.35));
            font-weight: 700;
            letter-spacing: 0.2px;
        }
        .barcode-camera-controls {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            align-items: center;
        }
        .barcode-camera-status {
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 700;
        }
        .barcode-camera-btn {
            border: 1px solid rgba(212, 163, 115, 0.45);
            background: #fff;
            color: var(--primary);
            border-radius: 999px;
            padding: 0.65rem 1rem;
            font-weight: 800;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.18s ease;
        }
        .barcode-camera-btn:hover { background: rgba(212, 163, 115, 0.1); border-color: var(--highlight); }
        .barcode-camera-btn:disabled { opacity: 0.55; cursor: not-allowed; }
        .scan-row { display: grid; grid-template-columns: minmax(0, 1fr) 130px auto; gap: 1rem; align-items: end; }
        .field { display: flex; flex-direction: column; gap: 0.35rem; }
        .field label { color: var(--text-muted); font-size: 0.85rem; font-weight: 700; }
        .field input, .field select {
            width: 100%;
            border: 1px solid var(--accent);
            border-radius: 12px;
            padding: 0.9rem 1rem;
            background: #fff;
            color: var(--text-main);
            font-family: inherit;
            outline: none;
            font-size: 1rem;
        }
        .field input:focus, .field select:focus { border-color: var(--highlight); box-shadow: 0 0 0 3px rgba(212, 163, 115, 0.15); }
        .btn-soft {
            border: 1px solid var(--accent);
            background: #fff;
            color: var(--primary);
            border-radius: 12px;
            padding: 0.9rem 1.15rem;
            cursor: pointer;
            font-weight: 700;
            font-family: inherit;
        }
        .btn-soft:hover { background: #FFF8F1; }
        .btn-primary-wide {
            width: 100%;
            background: var(--highlight);
            color: #fff;
            border: none;
            border-radius: 14px;
            padding: 0.9rem 1rem;
            font-weight: 800;
            font-family: inherit;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3);
        }
        .btn-primary-wide:disabled { opacity: 0.55; cursor: not-allowed; }
        .scan-result { border: 1px dashed var(--accent); border-radius: 18px; padding: 1.25rem 1.3rem; background: #FFFBF6; min-height: 108px; line-height: 1.65; display: grid; align-content: center; }
        .register-box {
            display: none;
            gap: 0.95rem;
            border: 1px solid rgba(212, 163, 115, 0.35);
            border-radius: 18px;
            background: #fffaf5;
            padding: 1.15rem 1.2rem;
        }
        .register-box.open { display: grid; }
        .register-head { display: grid; gap: 0.2rem; }
        .register-title { margin: 0; color: var(--primary); font-size: 1rem; font-weight: 800; }
        .register-subtitle { margin: 0; color: var(--text-muted); font-size: 0.88rem; }
        .register-grid { display: grid; grid-template-columns: 1.3fr 0.8fr 0.8fr; gap: 0.85rem; }
        .register-actions { display: flex; gap: 0.75rem; justify-content: flex-end; }
        .register-link-btn {
            border: none;
            background: transparent;
            padding: 0.35rem 0.15rem;
            font-weight: 700;
            font-family: inherit;
            font-size: 1rem;
            cursor: pointer;
            transition: opacity 0.18s ease;
        }
        .register-link-btn:hover { opacity: 0.74; }
        .register-link-btn.cancel { color: #B07A4A; }
        .register-link-btn.submit { color: #FF6F61; }
        .cart-list { display: grid; gap: 1rem; margin-top: 0.35rem; }
        .cart-item {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.05rem 1.15rem;
            border-radius: 16px;
            border: 1px solid rgba(212, 163, 115, 0.22);
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }
        .cart-item h4 { margin: 0 0 0.25rem; font-size: 1rem; color: var(--primary); }
        .cart-item small { display: block; color: var(--text-muted); }
        .cart-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--accent);
            font-weight: 800;
            font-size: 1.2rem;
            color: var(--primary);
        }
        .cart-empty { color: var(--text-muted); font-style: italic; padding: 1.25rem 0; text-align: center; }
        .alert { padding: 0.85rem 1.25rem; border-radius: 14px; margin-bottom: 1.25rem; font-weight: 500; font-size: 0.95rem; border: 1px solid transparent; }
        .ok { background: #E8F5E9; color: #558B2F; border-color: #C8E6C9; }
        .err { background: #FFEBEE; color: #C62828; border-color: #FFCDD2; }
        .order { border: 1px solid var(--accent); border-radius: 16px; padding: 1.25rem; margin-bottom: 1.25rem; background: #FFFAF5; transition: all 0.2s ease; box-shadow: 0 2px 8px var(--shadow); }
        .order:hover { border-color: rgba(212, 163, 115, 0.4); box-shadow: 0 6px 15px var(--shadow); }
        .order-head { display: flex; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
        .order-code { font-family: 'Playfair Display', Georgia, serif; font-size: 1.2rem; color: var(--primary); font-weight: 700; display: block; margin-bottom: 0.25rem; }
        .order-meta { color: var(--text-muted); font-size: 0.9rem; }
        .order-total { color: var(--text-main); font-weight: 600; font-size: 0.95rem; display: block; margin-top: 0.25rem; }
        .tag { display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem; }
        .tag-unpaid { background: #FFF3E0; color: #E65100; }
        .tag-paid { background: #E8F5E9; color: #558B2F; }
        .tag-cancelled { background: #FFEBEE; color: #C62828; }
        .payment-actions { display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 1.25rem; padding-top: 1rem; border-top: 1px dashed var(--accent); }
        .payment-actions form { display: inline-flex; }
        .btn { border: none; border-radius: 12px; padding: 0.65rem 1.25rem; cursor: pointer; font-weight: 700; font-family: inherit; font-size: 0.9rem; color: #fff; transition: all 0.2s ease; }
        .btn-cash { background-color: var(--cash-color); box-shadow: 0 2px 8px rgba(109, 76, 65, 0.25); }
        .btn-cash:hover { background-color: #5D4037; transform: translateY(-2px); }
        .btn-qris { background-color: var(--qris-color); box-shadow: 0 2px 8px rgba(77, 182, 172, 0.25); }
        .btn-qris:hover { background-color: #009688; transform: translateY(-2px); }
        .btn-transfer { background-color: var(--transfer-color); box-shadow: 0 2px 8px rgba(126, 87, 194, 0.25); }
        .btn-transfer:hover { background-color: #673AB7; transform: translateY(-2px); }
        .toolbar { display:flex; justify-content:flex-end; margin-bottom:1rem; }
        .btn-delete-all { background: transparent; color: #C62828; border: 1px solid #FFCDD2; border-radius: 10px; padding: .55rem .9rem; font-weight: 700; cursor: pointer; }
        .btn-delete-all:hover { background: #FFEBEE; }
        .history-panel { display: grid; gap: 1.25rem; }
        .history-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(212, 163, 115, 0.24);
        }
        .history-title {
            margin: 0;
            font-family: 'Playfair Display', Georgia, serif;
            color: var(--primary);
            font-size: 1.4rem;
        }
        .history-subtitle {
            margin: 0.35rem 0 0;
            color: var(--text-muted);
            font-size: 0.92rem;
        }
        .history-empty {
            min-height: 180px;
            border: 1px dashed rgba(212, 163, 115, 0.38);
            border-radius: 18px;
            background: linear-gradient(180deg, #fffdfb 0%, #fff8f2 100%);
            display: grid;
            place-items: center;
            text-align: center;
            padding: 2rem 1.25rem;
            color: var(--text-muted);
            font-size: 1rem;
        }
        .history-empty strong {
            display: block;
            color: var(--primary);
            font-size: 1.1rem;
            margin-bottom: 0.35rem;
        }
        .history-list {
            display: grid;
            gap: 1rem;
        }
        @media (max-width: 900px) {
            .payment-grid { grid-template-columns: 1fr; }
            .split-section + .split-section {
                border-left: none;
                border-top: 1px solid rgba(212, 163, 115, 0.2);
                padding-left: 0.1rem;
                padding-top: 1.5rem;
            }
        }
        @media (max-width: 768px) {
            .main-panel { padding: 1.5rem 1rem; }
            .page-title { font-size: 1.5rem; }
            .panel { padding: 1.25rem; }
            .scan-row { grid-template-columns: 1fr; gap: 0.85rem; }
            .register-grid { grid-template-columns: 1fr; }
            .history-head { align-items: stretch; flex-direction: column; }
            .payment-actions { flex-direction: column; }
            .payment-actions form { width: 100%; }
            .payment-actions .btn { width: 100%; justify-content: center; }
            .register-actions { flex-direction: column; }
            .register-actions button { width: 100%; }
            .scan-result { min-height: 84px; }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            const csrfRefreshRoute = @json(route('csrf.token'));
            const scanRoute = @json(route('cashier.scanner.cart'));
            const saveRoute = @json(route('cashier.scanner.save'));
            const removeCartBase = @json(url('/kasir/pembayaran/cart'));
            const menuCategories = @json($menuCategories->map(fn($row) => ['id' => $row->id, 'name' => $row->name])->values());
            const barcodeInput = document.getElementById('barcodeInput');
            const qtyInput = document.getElementById('qtyInput');
            const scanBtn = document.getElementById('scanBtn');
            const scanResult = document.getElementById('scanResult');
            const barcodeVideo = document.getElementById('barcodeVideo');
            const startCameraBtn = document.getElementById('startCameraBtn');
            const stopCameraBtn = document.getElementById('stopCameraBtn');
            const barcodeCameraStatus = document.getElementById('barcodeCameraStatus');
            const barcodeCameraOverlay = document.getElementById('barcodeCameraOverlay');
            const registerBox = document.getElementById('registerBox');
            const registerBarcode = document.getElementById('registerBarcode');
            const registerName = document.getElementById('registerName');
            const registerCategory = document.getElementById('registerCategory');
            const registerPrice = document.getElementById('registerPrice');
            const saveNewItemBtn = document.getElementById('saveNewItemBtn');
            const cancelRegisterBtn = document.getElementById('cancelRegisterBtn');
            const cartList = document.getElementById('cartList');
            const cartTotal = document.getElementById('cartTotal');
            const checkoutBtn = document.getElementById('checkoutCartBtn');
            const initialCart = @json($cart['items']);
            const cartState = new Map((initialCart || []).map((item) => [Number(item.menu_id), { ...item }]));
            let pendingBarcode = '';
            let cameraStream = null;
            let barcodeDetector = null;
            let cameraScanning = false;
            let cameraBusy = false;
            let lastCameraBarcode = '';
            let cameraFrameHandle = null;

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

            const focusBarcode = () => setTimeout(() => barcodeInput?.focus(), 60);

            const setCameraStatus = (message, type = 'info') => {
                if (barcodeCameraStatus) {
                    barcodeCameraStatus.textContent = message;
                    barcodeCameraStatus.style.color = type === 'error' ? '#c62828' : 'var(--text-muted)';
                }
                if (barcodeCameraOverlay) {
                    barcodeCameraOverlay.textContent = message;
                }
            };

            const stopCameraScanner = async () => {
                cameraScanning = false;
                cameraBusy = false;
                lastCameraBarcode = '';
                if (cameraFrameHandle) {
                    cancelAnimationFrame(cameraFrameHandle);
                    cameraFrameHandle = null;
                }
                if (barcodeVideo) {
                    barcodeVideo.srcObject = null;
                }
                if (cameraStream) {
                    cameraStream.getTracks().forEach((track) => track.stop());
                    cameraStream = null;
                }
                if (startCameraBtn) startCameraBtn.disabled = false;
                if (stopCameraBtn) stopCameraBtn.disabled = true;
                setCameraStatus('Kamera belum aktif.');
            };

            const ensureBarcodeDetector = async () => {
                if (barcodeDetector) return barcodeDetector;
                if (!('BarcodeDetector' in window)) return null;
                try {
                    barcodeDetector = new BarcodeDetector({
                        formats: ['ean_13', 'ean_8', 'code_128', 'code_39', 'upc_a', 'upc_e', 'qr_code'],
                    });
                } catch (_) {
                    barcodeDetector = null;
                }
                return barcodeDetector;
            };

            const startCameraScanner = async () => {
                if (!navigator.mediaDevices?.getUserMedia) {
                    setCameraStatus('Kamera tidak didukung browser ini.', 'error');
                    return;
                }

                const detector = await ensureBarcodeDetector();
                if (!detector) {
                    setCameraStatus('BarcodeDetector tidak tersedia. Gunakan Chrome di HP atau input manual.', 'error');
                    return;
                }

                try {
                    cameraStream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: { ideal: 'environment' } },
                        audio: false,
                    });
                    if (!barcodeVideo) throw new Error('Video scanner tidak ditemukan.');
                    barcodeVideo.srcObject = cameraStream;
                    await barcodeVideo.play();
                    cameraScanning = true;
                    cameraBusy = false;
                    lastCameraBarcode = '';
                    if (startCameraBtn) startCameraBtn.disabled = true;
                    if (stopCameraBtn) stopCameraBtn.disabled = false;
                    setCameraStatus('Kamera aktif. Arahkan barcode ke kotak kamera.');

                    const detectFrame = async () => {
                        if (!cameraScanning || !barcodeDetector || !barcodeVideo) return;
                        cameraFrameHandle = requestAnimationFrame(detectFrame);
                        if (cameraBusy || barcodeVideo.readyState < 2) return;
                        try {
                            cameraBusy = true;
                            const codes = await barcodeDetector.detect(barcodeVideo);
                            const raw = String(codes?.[0]?.rawValue || '').trim();
                            if (!raw || raw === lastCameraBarcode) return;
                            lastCameraBarcode = raw;
                            barcodeInput.value = raw;
                            setCameraStatus('Barcode terbaca: ' + raw);
                            await scanBarcode(raw);
                            await stopCameraScanner();
                        } catch (error) {
                            setCameraStatus(error?.message || 'Gagal membaca barcode.', 'error');
                        } finally {
                            cameraBusy = false;
                        }
                    };

                    detectFrame();
                } catch (error) {
                    await stopCameraScanner();
                    setCameraStatus(error?.message || 'Gagal membuka kamera.', 'error');
                }
            };

            const showResult = (html, error = false) => {
                if (!scanResult) return;
                scanResult.innerHTML = html;
                scanResult.style.borderColor = error ? '#ef9a9a' : '';
                scanResult.style.background = error ? '#fff5f5' : '#FFFBF6';
            };

            const hideRegisterBox = () => {
                if (!registerBox) return;
                registerBox.classList.remove('open');
                pendingBarcode = '';
            };

            const showRegisterBox = (barcode) => {
                if (!registerBox) return;
                pendingBarcode = barcode;
                if (registerBarcode) registerBarcode.value = barcode;
                if (registerName) registerName.value = '';
                if (registerPrice) registerPrice.value = '';
                if (registerCategory && registerCategory.options.length && !registerCategory.value) {
                    registerCategory.value = registerCategory.options[0].value;
                }
                registerBox.classList.add('open');
                setTimeout(() => registerName?.focus(), 40);
            };

            const renderCart = () => {
                if (!cartList || !cartTotal) return;

                const rows = Array.from(cartState.values()).sort((a, b) => a.menu_id - b.menu_id);
                cartList.innerHTML = '';

                if (!rows.length) {
                    cartList.innerHTML = '<div class="cart-empty">Belum ada item discan.</div>';
                    cartTotal.textContent = formatRp(0);
                    if (checkoutBtn) checkoutBtn.disabled = true;
                    return;
                }

                rows.forEach((row) => {
                    const item = document.createElement('div');
                    item.className = 'cart-item';
                    item.setAttribute('data-menu-id', String(row.menu_id));
                    item.setAttribute('data-unit-price', String(row.unit_price));
                    item.setAttribute('data-qty', String(row.qty));
                    item.innerHTML = `
                        <div>
                            <h4>${esc(row.name)}</h4>
                            <small>${esc(row.code)} | Barcode: ${esc(row.barcode)}</small>
                            <small>Qty: <span data-cart-qty>${row.qty}</span> x ${formatRp(row.unit_price)}</small>
                        </div>
                        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.5rem;">
                            <strong data-cart-line-total>${formatRp(row.line_total)}</strong>
                            <button type="button" class="btn-soft" data-remove-cart="${row.menu_id}" style="padding:.45rem .75rem;font-size:.8rem;">Hapus</button>
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
                    err.data = data;
                    err.status = response.status;
                    throw err;
                }
                return data;
            };

            const saveAndAddBarcodeItem = async () => {
                const barcode = pendingBarcode || registerBarcode?.value?.trim() || barcodeInput?.value?.trim();
                const qty = Math.max(1, parseInt(qtyInput?.value || '1', 10) || 1);
                const name = registerName?.value?.trim() || '';
                const menuCategoryId = registerCategory?.value || '';
                const sellingPrice = Number(registerPrice?.value || 0);

                if (!barcode || !name || !menuCategoryId || sellingPrice < 0) {
                    showResult('<span>Lengkapi nama barang, kategori, dan harga.</span>', true);
                    return;
                }

                saveNewItemBtn.disabled = true;
                saveNewItemBtn.textContent = 'Menyimpan...';
                try {
                    await postJson(saveRoute, {
                        record_type: 'menu',
                        barcode,
                        name,
                        menu_category_id: Number(menuCategoryId),
                        selling_price: sellingPrice,
                        cost_price: 0,
                    });

                    const data = await postJson(scanRoute, { barcode, qty });
                    hideRegisterBox();
                    showResult(`
                        <strong>${esc(data.item.name)}</strong><br>
                        <span>${esc(data.item.selling_price_label || formatRp(data.item.selling_price))}</span><br>
                        <small>Barang baru disimpan dan langsung masuk ke pembayaran.</small>
                    `);
                    upsertCartItem(data.item, qty);
                    barcodeInput.value = '';
                    qtyInput.value = '1';
                } catch (error) {
                    showResult(`<span>${esc(error.message)}</span>`, true);
                } finally {
                    saveNewItemBtn.disabled = false;
                    saveNewItemBtn.textContent = 'Simpan & Masukkan';
                    focusBarcode();
                }
            };

            const scanBarcode = async (barcodeValue = null) => {
                const barcode = String(barcodeValue ?? barcodeInput?.value?.trim() ?? '').trim();
                const qty = Math.max(1, parseInt(qtyInput?.value || '1', 10) || 1);
                if (!barcode) {
                    showResult('<span>Barcode masih kosong.</span>', true);
                    focusBarcode();
                    return;
                }

                scanBtn.disabled = true;
                scanBtn.textContent = 'Memproses...';
                try {
                    const data = await postJson(scanRoute, { barcode, qty });
                    hideRegisterBox();
                    showResult(`
                        <strong>${esc(data.item.name)}</strong><br>
                        <span>Rp ${esc(Number(data.item.selling_price || 0).toLocaleString('id-ID'))}</span><br>
                        <small>Berhasil masuk ke keranjang pembayaran.</small><br>
                        <small>${esc(data.message || '')}</small>
                    `);
                    upsertCartItem(data.item, qty);
                    barcodeInput.value = '';
                    qtyInput.value = '1';
                } catch (error) {
                    if (error.status === 404) {
                        showRegisterBox(barcode);
                    } else {
                        hideRegisterBox();
                    }
                    showResult(`<span>${esc(error.message)}</span>`, true);
                } finally {
                    scanBtn.disabled = false;
                    scanBtn.textContent = 'Scan';
                    focusBarcode();
                }
            };

            renderCart();
            scanBtn?.addEventListener('click', () => scanBarcode());
            startCameraBtn?.addEventListener('click', startCameraScanner);
            stopCameraBtn?.addEventListener('click', stopCameraScanner);
            saveNewItemBtn?.addEventListener('click', saveAndAddBarcodeItem);
            cancelRegisterBtn?.addEventListener('click', () => {
                hideRegisterBox();
                showResult('<span>Input barang baru dibatalkan.</span>');
                focusBarcode();
            });
            window.addEventListener('beforeunload', () => {
                stopCameraScanner();
            });
            cartList?.addEventListener('click', async (event) => {
                const btn = event.target.closest('[data-remove-cart]');
                if (!btn) return;
                const menuId = Number(btn.getAttribute('data-remove-cart'));
                if (!menuId) return;

                btn.disabled = true;
                btn.textContent = 'Menghapus...';
                try {
                    const response = await fetch(`${removeCartBase}/${menuId}`, {
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
            barcodeInput?.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    scanBarcode();
                }
            });
            window.addEventListener('focus', focusBarcode);
            document.addEventListener('turbo:load', focusBarcode);
            focusBarcode();

            let interval;
            const fetchLivePayment = () => {
                const container = document.getElementById('live-payment-container');
                if (!container) {
                    clearInterval(interval);
                    return;
                }

                const urlParams = new URLSearchParams(window.location.search);
                const currentPage = urlParams.get('page') || 1;
                const fetchUrl = `{{ route('cashier.payments.live') }}?page=${currentPage}`;

                fetch(fetchUrl)
                    .then(res => {
                        if (res.redirected && res.url.includes('/login')) {
                            window.location.href = '{{ route("login") }}';
                            return;
                        }
                        return res.text();
                    })
                    .then(html => {
                        if (!html) return;
                        if (html.includes('id="username"') || html.includes('name="username"')) {
                            window.location.href = '{{ route("login") }}';
                            return;
                        }

                        const target = document.getElementById('live-payment-container');
                        if (target) target.innerHTML = html;
                    })
                    .catch(err => console.error('Error fetching live payment:', err));
            };

            const startPolling = () => {
                clearInterval(interval);
                const container = document.getElementById('live-payment-container');
                if (container) {
                    container.addEventListener('click', (e) => {
                        const link = e.target.closest('.pagination-link');
                        if (link && link.href) {
                            e.preventDefault();
                            const url = new URL(link.href);
                            const newPage = url.searchParams.get('page');
                            if (newPage) {
                                const newUrl = window.location.pathname + '?page=' + newPage;
                                window.history.pushState({ path: newUrl }, '', newUrl);
                                fetchLivePayment();
                            }
                        }
                    });

                    interval = setInterval(() => {
                        if (document.visibilityState === 'visible') {
                            fetchLivePayment();
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

@section('content')
    <div class="pos-shell">
        <section class="panel">
            <h1 class="page-title">Pembayaran Kasir</h1>
            <p class="page-desc">Scan barcode menu di sini, masukkan ke keranjang pembayaran, lalu buat tagihan dan proses pembayaran.</p>
        </section>

        @if (session('success'))
            <div class="alert ok">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert err">{{ session('error') }}</div>
        @endif

        <section class="panel" style="margin-bottom: 1.5rem;">
            <div class="payment-grid">
                <div class="payment-col split-section">
                    <div class="section-head">
                        <h2 class="page-title" style="font-size: 1.35rem; margin: 0;">Scan Barcode Pembayaran</h2>
                    </div>

                    <div class="scan-box">
                        <div class="barcode-camera-box">
                            <div class="barcode-camera-preview">
                                <video id="barcodeVideo" class="barcode-camera-video" playsinline muted></video>
                                <div id="barcodeCameraOverlay" class="barcode-camera-overlay">Arahkan barcode ke kamera HP lalu tekan &quot;Buka Kamera&quot;.</div>
                            </div>
                            <div class="barcode-camera-controls">
                                <button id="startCameraBtn" class="barcode-camera-btn" type="button">Buka Kamera HP</button>
                                <button id="stopCameraBtn" class="barcode-camera-btn" type="button" disabled>Tutup Kamera</button>
                                <span id="barcodeCameraStatus" class="barcode-camera-status">Kamera belum aktif.</span>
                            </div>
                        </div>
                        <div class="scan-row">
                            <div class="field" style="flex: 1;">
                                <label for="barcodeInput">Barcode</label>
                                <input id="barcodeInput" type="text" inputmode="none" autocomplete="off" placeholder="Arahkan scanner ke barcode..." autofocus>
                            </div>
                            <div class="field">
                                <label for="qtyInput">Qty</label>
                                <input id="qtyInput" type="number" min="1" step="1" value="1">
                            </div>
                            <button id="scanBtn" class="btn-soft" type="button">Scan</button>
                        </div>
                        <div id="scanResult" class="scan-result">
                            <span>Siap untuk scan barcode.</span>
                        </div>
                        <div id="registerBox" class="register-box">
                            <div class="register-head">
                                <p class="register-title">Barcode belum terdaftar</p>
                                <p class="register-subtitle">Isi data barang sekali, setelah itu scan berikutnya langsung masuk ke pembayaran.</p>
                            </div>
                            <div class="register-grid">
                                <div class="field">
                                    <label for="registerName">Nama barang</label>
                                    <input id="registerName" type="text" placeholder="Contoh: Teh Botol">
                                </div>
                                <div class="field">
                                    <label for="registerCategory">Kategori</label>
                                    <select id="registerCategory">
                                        @foreach ($menuCategories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="field">
                                    <label for="registerPrice">Harga jual</label>
                                    <input id="registerPrice" type="number" min="0" step="1" placeholder="0">
                                </div>
                            </div>
                            <input id="registerBarcode" type="hidden">
                            <div class="register-actions">
                                <button id="cancelRegisterBtn" class="register-link-btn cancel" type="button">Batal</button>
                                <button id="saveNewItemBtn" class="register-link-btn submit" type="button">Simpan & Masukkan</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="payment-col split-section">
                    <div class="section-head">
                        <h2 class="page-title" style="font-size: 1.35rem; margin: 0;">Keranjang Pembayaran</h2>
                    </div>

                    <form method="POST" action="{{ route('cashier.payments.checkout') }}" id="checkoutCartForm">
                        @csrf
                        <div id="cartList" class="cart-list">
                            @forelse ($cart['items'] as $row)
                                <div class="cart-item" data-menu-id="{{ $row['menu_id'] }}" data-unit-price="{{ $row['unit_price'] }}" data-qty="{{ $row['qty'] }}">
                                    <div>
                                        <h4>{{ $row['name'] }}</h4>
                                        <small>{{ $row['code'] }} | Barcode: {{ $row['barcode'] }}</small>
                                        <small>Qty: <span data-cart-qty>{{ $row['qty'] }}</span> x Rp {{ number_format((float) $row['unit_price'], 0, ',', '.') }}</small>
                                    </div>
                                    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.5rem;">
                                        <strong data-cart-line-total>Rp {{ number_format((float) $row['line_total'], 0, ',', '.') }}</strong>
                                        <button type="button" class="btn-soft" data-remove-cart="{{ $row['menu_id'] }}" style="padding:.45rem .75rem;font-size:.8rem;">Hapus</button>
                                    </div>
                                </div>
                            @empty
                                <div class="cart-empty">Belum ada item discan.</div>
                            @endforelse
                        </div>
                        <div class="cart-total">
                            <span>Total</span>
                            <span id="cartTotal">Rp {{ number_format((float) $cart['total'], 0, ',', '.') }}</span>
                        </div>
                        <div style="margin-top: 1rem;">
                            <button id="checkoutCartBtn" class="btn-primary-wide" type="submit" {{ empty($cart['items']) ? 'disabled' : '' }}>
                                Buat Tagihan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section class="panel" id="live-payment-container">
            @include('cashier.payments.live')
        </section>
    </div>
@endsection

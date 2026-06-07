@extends('leader_cashier.layout')

@section('title', ($cafeBrand['name'] ?? config('app.name')) . ' - Pembayaran Kasir')
@section('page_title', 'Pembayaran Kasir')
@section('page_description', 'Scan barcode menu, masukkan ke keranjang pembayaran, lalu buat tagihan dan proses pembayaran.')

@push('head')
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
      --red-light: #FEE2E2;
      --blue: #2563EB;
      --blue-light: #DBEAFE;
      --purple: #7C3AED;
      --purple-light: #EDE9FE;
      --teal: #0D9488;
      --teal-light: #CCFBF1;
      --cash-color: #78350F;
      --cash-bg: #FEF3C7;
      --qris-color: #0F766E;
      --qris-bg: #CCFBF1;
      --transfer-color: #6D28D9;
      --transfer-bg: #EDE9FE;
      --shadow-xs: 0 1px 2px rgba(0,0,0,0.03);
      --shadow-sm: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.02);
      --shadow-md: 0 4px 12px rgba(0,0,0,0.05);
      --shadow-lg: 0 8px 30px rgba(0,0,0,0.07);
      --radius-sm: 8px;
      --radius-md: 12px;
      --radius-lg: 16px;
      --radius-xl: 20px;
      --radius-full: 999px;
      --font: 'Plus Jakarta Sans', -apple-system, sans-serif;
      --transition: 0.2s ease;
    }

    /* ===== ALERT ===== */
    .alert {
      padding: 12px 18px; border-radius: var(--radius-md);
      margin-bottom: 20px; font-size: 13px; font-weight: 600;
      display: flex; align-items: center; gap: 10px;
      border: 1px solid transparent;
    }
    .alert.ok { background: var(--green-light); color: var(--green); border-color: #A7F3D0; }
    .alert.ok::before { content: '\f058'; font-family: 'Font Awesome 6 Free'; font-weight: 900; }
    .alert.err { background: var(--red-light); color: var(--red); border-color: #FECACA; }
    .alert.err::before { content: '\f06a'; font-family: 'Font Awesome 6 Free'; font-weight: 900; }

    /* ===== SECTION CARD (mengganti .panel) ===== */
    .panel {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      margin-bottom: 20px;
      overflow: hidden;
      box-shadow: none;
    }

    /* ===== PAYMENT GRID ===== */
    .payment-grid {
      display: grid;
      grid-template-columns: 1.1fr 0.9fr;
      gap: 0;
      align-items: stretch;
    }

    .payment-col {
      padding: 22px 24px;
      display: flex;
      flex-direction: column;
      gap: 18px;
    }

    /* Garis pemisah kolom */
    .split-section + .split-section {
      border-left: 1px solid var(--border);
    }

    /* ===== SECTION HEAD ===== */
    .section-head {
      padding-bottom: 14px;
      border-bottom: 1px solid var(--border-light);
    }

    .section-head h2 {
      font-size: 15px;
      font-weight: 800;
      letter-spacing: -0.2px;
      display: flex;
      align-items: center;
      gap: 8px;
      margin: 0;
      color: var(--fg);
    }

    .section-head h2 i {
      color: var(--accent);
      font-size: 16px;
    }

    /* ===== SCAN BOX ===== */
    .scan-box { display: flex; flex-direction: column; gap: 14px; }

    .scan-row {
      display: grid;
      grid-template-columns: 1fr 100px auto;
      gap: 10px;
      align-items: end;
    }

    /* ===== FORM FIELDS ===== */
    .field { display: flex; flex-direction: column; gap: 5px; }

    .field label {
      font-size: 12px;
      font-weight: 700;
      color: var(--fg-secondary);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .field input,
    .field select {
      width: 100%;
      border: 1.5px solid var(--border);
      border-radius: var(--radius-sm);
      padding: 10px 14px;
      background: var(--white);
      color: var(--fg);
      font-family: var(--font);
      font-size: 14px;
      font-weight: 500;
      outline: none;
      transition: all var(--transition);
      -webkit-appearance: none;
    }

    .field input::placeholder { color: var(--muted); font-weight: 400; }

    .field input:focus,
    .field select:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1);
    }

    .field select {
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%239CA3B4' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 12px center;
      padding-right: 32px;
    }

    /* ===== SCAN RESULT ===== */
    .scan-result {
      border: 1.5px dashed var(--border);
      border-radius: var(--radius-md);
      padding: 16px 18px;
      background: #FAFBFC;
      min-height: 80px;
      line-height: 1.7;
      font-size: 13px;
      color: var(--fg-secondary);
      display: grid;
      align-content: center;
      transition: all 0.25s ease;
    }

    .scan-result strong { color: var(--fg); font-size: 14px; }

    .scan-result.error-state {
      border-color: #FECACA;
      background: #FEF2F2;
      color: var(--red);
    }

    .scan-result.success-state {
      border-color: #A7F3D0;
      background: #F0FDF4;
      color: var(--green);
    }

    /* ===== REGISTER BOX ===== */
    .register-box {
      display: none;
      flex-direction: column;
      gap: 14px;
      border: 1.5px solid var(--accent);
      border-radius: var(--radius-md);
      background: var(--accent-light);
      padding: 18px 20px;
      animation: slideDown 0.25s ease;
    }

    @keyframes slideDown {
      from { opacity: 0; transform: translateY(-8px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .register-box.open { display: flex; }

    .register-head { display: flex; flex-direction: column; gap: 2px; }

    .register-title {
      font-size: 14px;
      font-weight: 800;
      color: var(--accent-dark);
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .register-title i { font-size: 14px; }

    .register-subtitle {
      font-size: 12px;
      color: var(--fg-secondary);
    }

    .register-grid {
      display: grid;
      grid-template-columns: 1.3fr 0.8fr 0.8fr;
      gap: 10px;
    }

    .register-actions {
      display: flex;
      gap: 8px;
      justify-content: flex-end;
    }

    .register-link-btn {
      border: none;
      background: transparent;
      padding: 8px 16px;
      font-weight: 700;
      font-family: var(--font);
      font-size: 13px;
      cursor: pointer;
      border-radius: var(--radius-sm);
      transition: all var(--transition);
    }

    .register-link-btn.cancel {
      color: var(--fg-secondary);
    }

    .register-link-btn.cancel:hover {
      background: rgba(0,0,0,0.05);
    }

    .register-link-btn.submit {
      background: var(--accent);
      color: white;
    }

    .register-link-btn.submit:hover {
      background: var(--accent-dark);
    }

    /* ===== BUTTONS ===== */
    .btn-soft {
      border: 1.5px solid var(--border);
      background: var(--white);
      color: var(--fg-secondary);
      border-radius: var(--radius-sm);
      padding: 10px 18px;
      cursor: pointer;
      font-weight: 700;
      font-family: var(--font);
      font-size: 13px;
      transition: all var(--transition);
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .btn-soft:hover {
      border-color: var(--accent);
      color: var(--accent);
      background: var(--accent-light);
    }

    .btn-primary-wide {
      width: 100%;
      background: var(--accent);
      color: white;
      border: none;
      border-radius: var(--radius-md);
      padding: 13px 20px;
      font-weight: 800;
      font-family: var(--font);
      font-size: 14px;
      cursor: pointer;
      transition: all var(--transition);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .btn-primary-wide:hover {
      background: var(--accent-dark);
      transform: translateY(-1px);
      box-shadow: 0 4px 14px rgba(217, 119, 6, 0.3);
    }

    .btn-primary-wide:disabled {
      opacity: 0.45;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    /* ===== CART LIST ===== */
    .cart-list {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .cart-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      padding: 14px 16px;
      border-radius: var(--radius-sm);
      border: 1px solid var(--border-light);
      background: var(--white);
      transition: all var(--transition);
      animation: cartItemIn 0.25s ease;
    }

    @keyframes cartItemIn {
      from { opacity: 0; transform: translateX(10px); }
      to { opacity: 1; transform: translateX(0); }
    }

    .cart-item:hover {
      border-color: var(--border);
      box-shadow: var(--shadow-xs);
    }

    .cart-item h4 {
      font-size: 13px;
      font-weight: 700;
      color: var(--fg);
      margin-bottom: 2px;
    }

    .cart-item small {
      display: block;
      font-size: 11px;
      color: var(--muted);
      line-height: 1.5;
    }

    .cart-item-right {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 6px;
      flex-shrink: 0;
    }

    .cart-item-right strong {
      font-size: 14px;
      font-weight: 800;
      color: var(--fg);
      font-variant-numeric: tabular-nums;
    }

    .cart-item .btn-soft {
      padding: 5px 10px;
      font-size: 11px;
      border-radius: 6px;
    }

    .cart-item .btn-soft:hover {
      border-color: var(--red);
      color: var(--red);
      background: var(--red-light);
    }

    .cart-total {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 16px;
      padding-top: 16px;
      border-top: 2px solid var(--border);
      font-weight: 800;
      font-size: 16px;
      color: var(--fg);
    }

    .cart-total span:last-child {
      color: var(--accent);
      font-size: 20px;
    }

    .cart-empty {
      color: var(--muted);
      font-size: 13px;
      text-align: center;
      padding: 28px 16px;
      border: 1.5px dashed var(--border);
      border-radius: var(--radius-md);
      background: #FAFBFC;
    }

    .cart-empty::before {
      content: '\f07a';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      display: block;
      font-size: 28px;
      margin-bottom: 8px;
      color: var(--border);
    }

    /* ===== ORDER CARDS (riwayat/live) ===== */
    .history-section { padding: 22px 24px; }

    .history-head {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      padding-bottom: 16px;
      border-bottom: 1px solid var(--border-light);
      margin-bottom: 18px;
      flex-wrap: wrap;
    }

    .history-head-left { display: flex; flex-direction: column; gap: 2px; }

    .history-title {
      font-size: 15px;
      font-weight: 800;
      color: var(--fg);
      display: flex;
      align-items: center;
      gap: 8px;
      letter-spacing: -0.2px;
      margin: 0;
    }

    .history-title i { color: var(--accent); font-size: 16px; }

    .history-subtitle {
      font-size: 12px;
      color: var(--muted);
      margin: 0;
    }

    .toolbar { display: flex; gap: 8px; }

    .btn-delete-all {
      background: transparent;
      color: var(--red);
      border: 1px solid #FECACA;
      border-radius: var(--radius-sm);
      padding: 7px 14px;
      font-weight: 700;
      font-size: 12px;
      font-family: var(--font);
      cursor: pointer;
      transition: all var(--transition);
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .btn-delete-all:hover { background: var(--red-light); }

    .history-list {
      display: grid;
      gap: 12px;
    }

    .history-empty {
      min-height: 200px;
      border: 1.5px dashed var(--border);
      border-radius: var(--radius-md);
      background: #FAFBFC;
      display: grid;
      place-items: center;
      text-align: center;
      padding: 32px 20px;
    }

    .history-empty i { font-size: 36px; color: var(--border); margin-bottom: 8px; }
    .history-empty strong { display: block; font-size: 14px; color: var(--fg-secondary); margin-bottom: 4px; }
    .history-empty p { font-size: 13px; color: var(--muted); }

    /* ===== ORDER CARD ===== */
    .order {
      border: 1px solid var(--border-light);
      border-radius: var(--radius-md);
      padding: 18px 20px;
      background: var(--white);
      transition: all 0.25s ease;
    }

    .order:hover {
      border-color: var(--border);
      box-shadow: var(--shadow-md);
      transform: translateY(-1px);
    }

    .order-head {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 12px;
      flex-wrap: wrap;
    }

    .order-code {
      font-size: 15px;
      font-weight: 800;
      color: var(--fg);
      display: block;
      margin-bottom: 3px;
      letter-spacing: -0.2px;
    }

    .order-meta {
      font-size: 12px;
      color: var(--muted);
    }

    .order-total {
      color: var(--fg);
      font-weight: 700;
      font-size: 14px;
      display: block;
      margin-top: 4px;
      font-variant-numeric: tabular-nums;
    }

    /* ===== STATUS TAG ===== */
    .tag {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 4px 12px;
      border-radius: var(--radius-full);
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.3px;
      white-space: nowrap;
    }

    .tag .tag-dot {
      width: 6px; height: 6px;
      border-radius: 50%;
      background: currentColor;
    }

    .tag-unpaid { background: var(--accent-light); color: var(--accent-dark); }
    .tag-unpaid .tag-dot { animation: dotBlink 1.5s infinite; }

    .tag-paid { background: var(--green-light); color: var(--green); }
    .tag-cancelled { background: var(--red-light); color: var(--red); }

    @keyframes dotBlink {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.3; }
    }

    /* ===== PAYMENT ACTION BUTTONS ===== */
    .payment-actions {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      margin-top: 14px;
      padding-top: 14px;
      border-top: 1px dashed var(--border);
    }

    .payment-actions form { display: inline-flex; }

    .btn {
      border: none;
      border-radius: var(--radius-sm);
      padding: 9px 18px;
      cursor: pointer;
      font-weight: 700;
      font-family: var(--font);
      font-size: 12px;
      color: white;
      transition: all 0.25s ease;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }

    .btn:hover { transform: translateY(-2px); }
    .btn:active { transform: translateY(0); }

    .btn-cash {
      background: var(--cash-color);
      box-shadow: 0 2px 8px rgba(120, 53, 15, 0.2);
    }
    .btn-cash:hover { background: #92400E; }

    .btn-qris {
      background: var(--qris-color);
      box-shadow: 0 2px 8px rgba(15, 118, 110, 0.2);
    }
    .btn-qris:hover { background: #115E59; }

    .btn-transfer {
      background: var(--transfer-color);
      box-shadow: 0 2px 8px rgba(109, 40, 217, 0.2);
    }
    .btn-transfer:hover { background: #5B21B6; }

    /* ===== PAGINATION ===== */
    .pagination-area { margin-top: 18px; }
    .pagination-wrap { margin-top: 18px; }

    .pagination-meta {
      color: var(--muted);
      font-size: 12px;
      margin-bottom: 10px;
      text-align: center;
    }

    .pagination-links {
      display: flex;
      gap: 4px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .pagination-link, .pagination-dots {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 34px;
      height: 34px;
      border-radius: var(--radius-sm);
      font-size: 12px;
      font-weight: 600;
      text-decoration: none;
      border: 1px solid var(--border);
      color: var(--fg-secondary);
      padding: 0 8px;
      background: var(--white);
      transition: all var(--transition);
      font-family: var(--font);
      cursor: pointer;
    }

    .pagination-link:hover {
      border-color: var(--accent);
      color: var(--accent);
      background: var(--accent-light);
    }

    .pagination-link.active {
      background: var(--accent);
      border-color: var(--accent);
      color: white;
      box-shadow: 0 2px 8px rgba(217, 119, 6, 0.25);
    }

    .pagination-link.disabled {
      opacity: 0.35;
      pointer-events: none;
    }

    /* ===== LIVE INDICATOR ===== */
    .live-indicator {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 11px;
      font-weight: 700;
      color: var(--green);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .live-dot {
      width: 7px; height: 7px;
      background: var(--green);
      border-radius: 50%;
      animation: livePulse 1.5s infinite;
    }

    @keyframes livePulse {
      0% { box-shadow: 0 0 0 0 rgba(5,150,105,0.4); }
      70% { box-shadow: 0 0 0 6px rgba(5,150,105,0); }
      100% { box-shadow: 0 0 0 0 rgba(5,150,105,0); }
    }

    /* ===== ENTRANCE ANIMATION ===== */
    .fade-in {
      opacity: 0;
      transform: translateY(12px);
      transition: opacity 0.4s ease, transform 0.4s ease;
    }

    .fade-in.visible {
      opacity: 1;
      transform: translateY(0);
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1024px) {
      .payment-grid { grid-template-columns: 1fr; }
      .split-section + .split-section { border-left: none; border-top: 1px solid var(--border); }
    }

    @media (max-width: 768px) {
      .page-body { padding: 16px; }
      .payment-col { padding: 16px; }
      .scan-row { grid-template-columns: 1fr; }
      .register-grid { grid-template-columns: 1fr; }
      .register-actions { flex-direction: column; }
      .register-actions button { width: 100%; justify-content: center; }
      .payment-actions { flex-direction: column; }
      .payment-actions form { width: 100%; }
      .payment-actions .btn { width: 100%; justify-content: center; }
      .history-head { flex-direction: column; align-items: stretch; }
    }

    @media (max-width: 480px) {
      .cart-item { flex-direction: column; align-items: flex-start; }
      .cart-item-right { flex-direction: row; width: 100%; justify-content: space-between; align-items: center; }
    }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            const csrfRefreshRoute = @json(route('csrf.token'));
            const scanRoute = @json(route('superadmin.scanner.cart'));
            const saveRoute = @json(route('superadmin.scanner.save'));
            const removeCartBase = @json(route('superadmin.payments.cart.destroy', ['menu' => '__MENU_ID__']));
            const menuCategories = @json($menuCategories->map(fn($row) => ['id' => $row->id, 'name' => $row->name])->values());
            const barcodeInput = document.getElementById('barcodeInput');
            const qtyInput = document.getElementById('qtyInput');
            const scanBtn = document.getElementById('scanBtn');
            const scanResult = document.getElementById('scanResult');
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
                        <div class="cart-item-right">
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

            const scanBarcode = async () => {
                const barcode = barcodeInput?.value?.trim();
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
            scanBtn?.addEventListener('click', scanBarcode);
            saveNewItemBtn?.addEventListener('click', saveAndAddBarcodeItem);
            cancelRegisterBtn?.addEventListener('click', () => {
                hideRegisterBox();
                showResult('<span>Input barang baru dibatalkan.</span>');
                focusBarcode();
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
                const fetchUrl = `{{ route('superadmin.payments.live') }}?page=${currentPage}`;

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
    @if (session('success'))
        <div class="alert ok">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert err">{{ session('error') }}</div>
    @endif

    <!-- SCAN + CART PANEL -->
    <div class="panel fade-in">
        <div class="payment-grid">
            <!-- KOLOM KIRI: SCAN -->
            <div class="payment-col split-section">
                <div class="section-head">
                    <h2><i class="fas fa-barcode"></i> Scan Barcode Pembayaran</h2>
                </div>
                <div class="scan-box">
                    <div class="scan-row">
                        <div class="field">
                            <label for="barcodeInput">Barcode</label>
                            <input id="barcodeInput" type="text" inputmode="none" autocomplete="off" placeholder="Arahkan scanner ke barcode..." autofocus>
                        </div>
                        <div class="field">
                            <label for="qtyInput">Qty</label>
                            <input id="qtyInput" type="number" min="1" step="1" value="1">
                        </div>
                        <button id="scanBtn" class="btn-soft" type="button"><i class="fas fa-barcode"></i> Scan</button>
                    </div>

                    <div id="scanResult" class="scan-result">
                        <span>Siap untuk scan barcode.</span>
                    </div>

                    <div id="registerBox" class="register-box">
                        <div class="register-head">
                            <p class="register-title"><i class="fas fa-circle-plus"></i> Barcode belum terdaftar</p>
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

            <!-- KOLOM KANAN: KERANJANG -->
            <div class="payment-col split-section">
                <div class="section-head">
                    <h2><i class="fas fa-cart-shopping"></i> Keranjang Pembayaran</h2>
                </div>

                <form method="POST" action="{{ route('superadmin.payments.checkout') }}" id="checkoutCartForm">
                    @csrf
                    <div id="cartList" class="cart-list">
                        @forelse ($cart['items'] as $row)
                            <div class="cart-item" data-menu-id="{{ $row['menu_id'] }}" data-unit-price="{{ $row['unit_price'] }}" data-qty="{{ $row['qty'] }}">
                                <div>
                                    <h4>{{ $row['name'] }}</h4>
                                    <small>{{ $row['code'] }} | Barcode: {{ $row['barcode'] }}</small>
                                    <small>Qty: <span data-cart-qty>{{ $row['qty'] }}</span> x Rp {{ number_format((float) $row['unit_price'], 0, ',', '.') }}</small>
                                </div>
                                <div class="cart-item-right">
                                    <strong data-cart-line-total>Rp {{ number_format((float) $row['line_total'], 0, ',', '.') }}</strong>
                                    <button type="button" class="btn-soft" data-remove-cart="{{ $row['menu_id'] }}"><i class="fas fa-trash"></i> Hapus</button>
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
                    <div style="margin-top:14px;">
                        <button id="checkoutCartBtn" class="btn-primary-wide" type="submit" {{ empty($cart['items']) ? 'disabled' : '' }}>
                            <i class="fas fa-receipt"></i> Buat Tagihan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- HISTORY / LIVE PAYMENTS -->
    <div class="panel fade-in" id="live-payment-container">
        @include('superadmin.payments.live')
    </div>
@endsection

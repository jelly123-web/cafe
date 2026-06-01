<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $cafeBrand['name'] ?? config('app.name') }} - Meja {{ $table->number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        :root {
            --bg-main: #F9F5F0;
            --bg-card: #FFFFFF;
            --primary: #795548;
            --secondary: #BCAAA4;
            --accent: #D7CCC8;
            --highlight: #D4A373;
            --text-main: #6D4C41;
            --text-muted: #A1887F;
            --profit: #81C784;
            --loss: #E57373;
            --shadow: rgba(121, 85, 72, 0.08);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            line-height: 1.6;
            background-image: radial-gradient(var(--accent) 1px, transparent 1px);
            background-size: 24px 24px;
            padding-bottom: 24px;
        }

        .shell { max-width: 900px; margin: 0 auto; padding: 2rem 1.5rem; }

        .hero, .section, .card {
            background: var(--bg-card);
            border: 1px solid var(--accent);
            border-radius: 20px;
            box-shadow: 0 4px 15px var(--shadow);
        }

        .hero {
            padding: 2rem;
            margin-bottom: 1.5rem;
            text-align: center;
            border-bottom: 4px solid var(--highlight);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(212, 163, 115, 0.15);
            color: var(--highlight);
            padding: 0.4rem 1rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.8rem;
            letter-spacing: .5px;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        h1, h2 { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); margin: 0; }
        .hero h1 { font-size: 2.5rem; margin-bottom: 0.5rem; }
        .meta { color: var(--text-muted); margin-bottom: 0.5rem; font-size: 1.1rem; }
        .meta strong { color: var(--primary); }
        .hero-desc { color: var(--text-muted); font-size: 0.95rem; margin: 0; }

        .section { padding: 2rem; margin-bottom: 2rem; }
        .section h2 { font-size: 1.5rem; margin-bottom: 1rem; }

        .status-wrap {
            display: grid;
            gap: 0.9rem;
        }

        .order-status-card {
            border: 1px solid var(--accent);
            border-radius: 14px;
            background: #fff;
            padding: 0.9rem 1rem;
        }

        .order-status-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.3rem;
        }

        .order-status-head strong {
            color: var(--primary);
            font-size: 1rem;
        }

        .order-status-meta {
            color: var(--text-muted);
            font-size: 0.84rem;
            margin-bottom: 0.45rem;
        }

        .order-status-items {
            color: var(--text-main);
            font-size: 0.9rem;
            display: grid;
            gap: 0.15rem;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.7rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.78rem;
            letter-spacing: 0.3px;
        }
        .status-pending { background: #FFF3E0; color: #ef6c00; }
        .status-processing { background: #E3F2FD; color: #1565c0; }
        .status-ready { background: #E8F5E9; color: #2e7d32; }
        .status-completed { background: #E8F5E9; color: #1b5e20; }
        .status-cancelled { background: #FFEBEE; color: #c62828; }

        .alert-ok, .alert-err {
            border-radius: 12px;
            padding: 0.85rem 1.25rem;
            margin-bottom: 1rem;
            font-weight: 500;
            font-size: 0.95rem;
            border: 1px solid transparent;
        }
        .alert-ok { background: #E8F5E9; border-color: #C8E6C9; color: #558B2F; }
        .alert-err { background: #FFEBEE; border-color: #FFCDD2; color: #C62828; }

        .category-nav {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--accent);
            padding-bottom: 1rem;
        }
        .category-btn {
            border: 1px solid var(--accent);
            background: #fff;
            color: var(--primary);
            padding: 0.5rem 1.25rem;
            border-radius: 999px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        .category-btn:hover { background: var(--bg-main); }
        .category-btn.active {
            background: var(--highlight);
            color: #fff;
            border-color: var(--highlight);
            box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3);
        }

        .menu-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.25rem; }
        .card { padding: 1.25rem; border-radius: 16px; position: relative; overflow: hidden; }
        .menu-card { cursor: pointer; transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
        .menu-card:hover {
            transform: translateY(-4px);
            border-color: var(--highlight);
            box-shadow: 0 8px 25px rgba(121, 85, 72, 0.12);
        }
        .menu-card.in-cart {
            border-color: var(--highlight);
            background: #FFFAF5;
        }

        .menu-title { display: block; color: var(--primary); font-family: 'Playfair Display', Georgia, serif; font-size: 1.15rem; font-weight: 700; margin-bottom: 0.25rem; }
        .menu-category { color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem; }
        .price { font-weight: 700; color: var(--highlight); font-size: 1.1rem; }

        .qty-badge {
            margin-top: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border: 1px solid var(--accent);
            background: #fff;
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .in-cart .qty-badge { background: rgba(212, 163, 115, 0.15); color: var(--highlight); border-color: var(--highlight); }

        .empty { color: var(--text-muted); font-style: italic; padding: 2rem 0; text-align: center; }

        .btn {
            border: 0; border-radius: 12px; padding: 0.65rem 1.5rem;
            background: var(--highlight); color: #fff; font-weight: 700;
            cursor: pointer; font-family: inherit; font-size: 0.95rem;
            text-decoration: none; transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3);
        }
        .btn:hover { background: #c68b59; transform: translateY(-1px); }
        .btn:disabled { opacity: .5; cursor: not-allowed; transform: none; background: var(--secondary); box-shadow: none; }

        .modal-backdrop {
            position: fixed; inset: 0; background: rgba(62, 39, 35, 0.5);
            backdrop-filter: blur(4px); opacity: 0; visibility: hidden; transition: .25s ease; z-index: 1000;
        }
        .modal-backdrop.open { opacity: 1; visibility: visible; }
        .order-modal {
            position: fixed; left: 50%; top: 50%;
            transform: translate(-50%, -45%);
            width: min(480px, 92vw); max-height: min(84vh, 760px); background: #fff;
            border: 1px solid var(--accent); border-radius: 24px;
            box-shadow: 0 20px 50px rgba(62, 39, 35, 0.25);
            opacity: 0; visibility: hidden; transition: .25s ease; z-index: 1001;
            display: grid; grid-template-rows: auto minmax(0, 1fr) auto;
        }
        .order-modal.open { opacity: 1; visibility: visible; transform: translate(-50%, -50%); }

        .modal-head, .modal-foot { padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; gap: 10px; }
        .modal-head { border-bottom: 1px solid var(--accent); }
        .modal-head strong { font-family: 'Playfair Display', Georgia, serif; font-size: 1.3rem; color: var(--primary); }
        .modal-foot { border-top: 1px solid var(--accent); justify-content: flex-end; gap: 0.75rem; }
        .modal-body { padding: 1.5rem; display: grid; gap: 1rem; overflow-y: auto; min-height: 0; }
        .modal-body label { font-size: 0.85rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .modal-close { border: 1px solid var(--accent); background: #fff; color: var(--primary); border-radius: 8px; padding: 0.4rem 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.2s ease; }
        .modal-close:hover { background: var(--bg-main); }

        .qty-input, .modal-note, .modal-select {
            width: 100%; border: 1px solid var(--accent); border-radius: 12px;
            padding: 0.75rem 1rem; font-family: inherit; font-size: 1rem; color: var(--text-main);
            background: var(--bg-main); outline: none; transition: all 0.2s ease;
        }
        .qty-input:focus, .modal-note:focus, .modal-select:focus { border-color: var(--highlight); box-shadow: 0 0 0 3px rgba(212, 163, 115, 0.15); background: #fff; }
        .modal-note {
            min-height: 80px;
            resize: vertical;
        }
        .field-hint { color: var(--text-muted); font-size: 0.85rem; margin-top: -0.35rem; }
        .btn-light { border: 1px solid var(--loss); background: #fff; color: var(--loss); border-radius: 12px; padding: 0.65rem 1.5rem; font-weight: 700; cursor: pointer; transition: all 0.2s ease; }
        .btn-light:hover { background: #FFEBEE; }
        .total-line { display: flex; justify-content: space-between; font-weight: 700; color: var(--primary); font-size: 1.1rem; margin-top: 0.5rem; }
        @media (max-width: 700px) {
            .order-modal {
                width: min(94vw, 480px);
                max-height: 78vh;
            }
            .modal-head, .modal-foot { padding: 1rem 1.1rem; }
            .modal-body { padding: 1rem 1.1rem; gap: 0.85rem; }
        }

        @media (max-width: 700px) {
            .shell { padding: 1rem; }
            .hero, .section { padding: 1.25rem; }
            .menu-grid { grid-template-columns: 1fr; }
            .hero h1 { font-size: 2rem; }
        }
    </style>
</head>
<body>
    <main class="shell">
        <section class="hero">
            <span class="badge">Scan dari Meja {{ $table->number }}</span>
            <h1>{{ $cafeBrand['name'] ?? config('app.name') }}</h1>
            <div class="meta">Meja: <strong>{{ $table->name }}</strong> | Nomor meja: <strong>{{ $table->number }}</strong></div>
            <p class="hero-desc">Pilih menu lalu klik kartu menu untuk isi jumlah pesanan.</p>
        </section>

        <section class="section">
            <h2>Status Pesanan Meja Ini</h2>
            <div class="status-wrap" id="tableOrderStatusWrap">
                @include('public.partials.table-order-status', ['orders' => $orders])
            </div>
        </section>

        <section class="section">
            <h2>Menu Tersedia</h2>
            <div id="orderClientAlert" class="alert-ok" style="display:none;"></div>
            @if (session('success'))
                <div class="alert-ok">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert-err">{{ $errors->first() }}</div>
            @endif

            <div class="category-nav" id="categoryNav">
                <button class="category-btn active" type="button" data-filter="all">Semua</button>
                @foreach ($categories as $category)
                    <button class="category-btn" type="button" data-filter="cat-{{ $category->id }}">{{ $category->name }}</button>
                @endforeach
            </div>

            <div class="menu-grid" id="menuGrid">
                @forelse ($menus as $menu)
                    <article class="card menu-card"
                        data-menu-id="{{ $menu->id }}"
                        data-menu-name="{{ $menu->name }}"
                        data-menu-category="{{ $menu->category?->name ?? 'Tanpa kategori' }}"
                        data-menu-category-key="{{ $menu->menu_category_id ? 'cat-'.$menu->menu_category_id : 'all' }}"
                        data-menu-price="{{ (float) $menu->selling_price }}"
                    >
                        <strong class="menu-title">{{ $menu->name }}</strong>
                        <div class="menu-category">{{ $menu->category?->name ?? 'Tanpa kategori' }}</div>
                        <div class="price">Rp {{ number_format((float) $menu->selling_price, 0, ',', '.') }}</div>
                        <div class="qty-badge">Qty dipilih: <span class="qty-selected" data-qty-for="{{ $menu->id }}">0</span></div>
                    </article>
                @empty
                    <div class="empty">Belum ada menu.</div>
                @endforelse
            </div>

            <form method="POST" action="{{ route('tables.order', $table->qr_token) }}" id="orderForm">
                @csrf
                <div id="orderItemsContainer"></div>
                <input type="hidden" id="globalNotesInput" name="notes" value="{{ old('notes') }}">
            </form>
        </section>
    </main>

    <div class="modal-backdrop" id="modalBackdrop"></div>
    <section class="order-modal" id="orderModal" aria-hidden="true">
        <div class="modal-head">
            <strong id="modalMenuName">Menu</strong>
            <button type="button" class="modal-close" id="closeModalBtn">Tutup</button>
        </div>
        <div class="modal-body">
            <div id="modalMenuCategory" style="color:var(--text-muted);"></div>
            <div class="price" id="modalMenuPrice"></div>
            <label for="modalQty"><strong>Jumlah Pesan</strong></label>
            <input class="qty-input" id="modalQty" type="number" min="0" step="1" value="1">
            <label for="modalAddonSelect"><strong>Tambahan</strong></label>
            <select class="modal-select" id="modalAddonSelect">
                <option value="">Tanpa tambahan</option>
                @foreach ($toppings as $topping)
                    <option value="{{ $topping['key'] }}"
                        data-name="{{ $topping['name'] }}"
                        data-price="{{ (float) $topping['price'] }}"
                        data-cost="{{ (float) $topping['cost'] }}">
                        {{ $topping['name'] }} (+Rp {{ number_format((float) $topping['price'], 0, ',', '.') }})
                    </option>
                @endforeach
            </select>
            <div class="field-hint">Contoh: Mie Goreng + 2 telur tambahan.</div>
            <label for="modalAddonQty"><strong>Jumlah tambahan</strong></label>
            <input class="qty-input" id="modalAddonQty" type="number" min="0" step="1" value="0">
            <label for="modalNote"><strong>Catatan Menu (opsional)</strong></label>
            <textarea id="modalNote" class="modal-note" placeholder="Contoh: tanpa bawang, pedas level 1"></textarea>
            <div class="total-line"><span>Subtotal</span><span id="modalSubtotal">Rp 0</span></div>
        </div>
        <div class="modal-foot">
            <button type="button" class="btn-light" id="removeFromCartBtn">Hapus dari Pesanan</button>
            <button type="button" class="btn" id="saveToCartBtn">Pesan Menu Ini</button>
        </div>
    </section>

    <script>
        (function () {
            const categoryButtons = document.querySelectorAll('#categoryNav .category-btn');
            const menuCards = document.querySelectorAll('#menuGrid .menu-card');
            const orderItemsContainer = document.getElementById('orderItemsContainer');
            const globalNotesInput = document.getElementById('globalNotesInput');
            const modal = document.getElementById('orderModal');
            const modalBackdrop = document.getElementById('modalBackdrop');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const saveBtn = document.getElementById('saveToCartBtn');
            const removeBtn = document.getElementById('removeFromCartBtn');
            const modalQty = document.getElementById('modalQty');
            const modalAddonSelect = document.getElementById('modalAddonSelect');
            const modalAddonQty = document.getElementById('modalAddonQty');
            const modalSubtotal = document.getElementById('modalSubtotal');
            const modalNote = document.getElementById('modalNote');
            const modalMenuName = document.getElementById('modalMenuName');
            const modalMenuCategory = document.getElementById('modalMenuCategory');
            const modalMenuPrice = document.getElementById('modalMenuPrice');
            const tableOrderStatusWrap = document.getElementById('tableOrderStatusWrap');
            const orderClientAlert = document.getElementById('orderClientAlert');
            const orderForm = document.getElementById('orderForm');
            const statusLiveUrl = @json(route('tables.orders.live', $table->qr_token));

            const formatRupiah = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID');
            const cart = new Map();
            let activeMenu = null;

            const openModal = () => {
                modal.classList.add('open');
                modalBackdrop.classList.add('open');
                modal.setAttribute('aria-hidden', 'false');
            };
            const closeModal = () => {
                modal.classList.remove('open');
                modalBackdrop.classList.remove('open');
                modal.setAttribute('aria-hidden', 'true');
            };

            const showClientAlert = (message, type = 'ok') => {
                if (!orderClientAlert) return;
                orderClientAlert.className = type === 'err' ? 'alert-err' : 'alert-ok';
                orderClientAlert.textContent = message;
                orderClientAlert.style.display = 'block';
                window.clearTimeout(orderClientAlert._hideTimer);
                orderClientAlert._hideTimer = window.setTimeout(() => {
                    orderClientAlert.style.display = 'none';
                    orderClientAlert.textContent = '';
                }, 2500);
            };

            const updateQtyBadges = () => {
                document.querySelectorAll('.qty-selected').forEach((el) => {
                    const id = Number(el.getAttribute('data-qty-for'));
                    const qty = cart.get(id)?.qty || 0;
                    el.textContent = qty;
                    const card = el.closest('.menu-card');
                    if (card) {
                        card.classList.toggle('in-cart', qty > 0);
                    }
                });
            };

            const rebuildHiddenInputs = () => {
                orderItemsContainer.innerHTML = '';
                let i = 0;
                cart.forEach((row, menuId) => {
                    const menuInput = document.createElement('input');
                    menuInput.type = 'hidden';
                    menuInput.name = `items[${i}][menu_id]`;
                    menuInput.value = String(menuId);
                    orderItemsContainer.appendChild(menuInput);

                    const qtyInput = document.createElement('input');
                    qtyInput.type = 'hidden';
                    qtyInput.name = `items[${i}][qty]`;
                    qtyInput.value = String(row.qty);
                    orderItemsContainer.appendChild(qtyInput);

                    const addonNameInput = document.createElement('input');
                    addonNameInput.type = 'hidden';
                    addonNameInput.name = `items[${i}][addon_name]`;
                    addonNameInput.value = String(row.addonName || '');
                    orderItemsContainer.appendChild(addonNameInput);

                    const addonQtyInput = document.createElement('input');
                    addonQtyInput.type = 'hidden';
                    addonQtyInput.name = `items[${i}][addon_qty]`;
                    addonQtyInput.value = String(row.addonQty || 0);
                    orderItemsContainer.appendChild(addonQtyInput);

                    const addonPriceInput = document.createElement('input');
                    addonPriceInput.type = 'hidden';
                    addonPriceInput.name = `items[${i}][addon_price]`;
                    addonPriceInput.value = String(row.addonPrice || 0);
                    orderItemsContainer.appendChild(addonPriceInput);

                    const addonCostInput = document.createElement('input');
                    addonCostInput.type = 'hidden';
                    addonCostInput.name = `items[${i}][addon_cost]`;
                    addonCostInput.value = String(row.addonCost || 0);
                    orderItemsContainer.appendChild(addonCostInput);
                    i += 1;
                });
            };

            const rebuildSummary = () => {
                if (!cart.size) {
                    globalNotesInput.value = '';
                    return;
                }
                let total = 0;
                let itemCount = 0;
                const notes = [];
                cart.forEach((row) => {
                    const addonSub = Number(row.addonPrice || 0) * Number(row.addonQty || 0);
                    const sub = (Number(row.price) * Number(row.qty)) + addonSub;
                    total += sub;
                    itemCount += Number(row.qty);
                    if (row.note && row.note.trim() !== '') {
                        notes.push(`${row.name}: ${row.note.trim()}`);
                    }
                    if (row.addonName && Number(row.addonQty || 0) > 0) {
                        notes.push(`${row.name}: ${row.addonName} x${row.addonQty}`);
                    }
                });
                globalNotesInput.value = notes.join(' | ');
            };

            const refreshOrderView = () => {
                updateQtyBadges();
                rebuildHiddenInputs();
                rebuildSummary();
            };

            const updateModalSubtotal = () => {
                if (!activeMenu) return;
                const qty = Math.max(0, Number(modalQty.value || 0));
                const addonOpt = modalAddonSelect?.selectedOptions?.[0];
                const addonPrice = Number(addonOpt?.dataset.price || 0);
                const addonQty = Math.max(0, Number(modalAddonQty?.value || 0));
                modalSubtotal.textContent = formatRupiah((activeMenu.price * qty) + (addonPrice * addonQty));
            };

            categoryButtons.forEach((btn) => {
                btn.addEventListener('click', () => {
                    const filter = btn.getAttribute('data-filter');
                    categoryButtons.forEach((x) => x.classList.remove('active'));
                    btn.classList.add('active');
                    menuCards.forEach((card) => {
                        const key = card.getAttribute('data-menu-category-key');
                        card.style.display = (filter === 'all' || key === filter) ? '' : 'none';
                    });
                });
            });

            menuCards.forEach((card) => {
                card.addEventListener('click', () => {
                    activeMenu = {
                        id: Number(card.getAttribute('data-menu-id')),
                        name: card.getAttribute('data-menu-name'),
                        category: card.getAttribute('data-menu-category'),
                        price: Number(card.getAttribute('data-menu-price')),
                    };
                    const existing = cart.get(activeMenu.id);
                    modalMenuName.textContent = activeMenu.name;
                    modalMenuCategory.textContent = activeMenu.category;
                    modalMenuPrice.textContent = formatRupiah(activeMenu.price);
                    modalQty.value = existing ? existing.qty : 1;
                    modalAddonSelect.value = existing?.addonKey || '';
                    modalAddonQty.value = existing?.addonQty || 0;
                    modalNote.value = existing?.note || '';
                    updateModalSubtotal();
                    openModal();
                });
            });

            modalQty.addEventListener('input', updateModalSubtotal);
            modalAddonSelect.addEventListener('change', updateModalSubtotal);
            modalAddonQty.addEventListener('input', updateModalSubtotal);

            saveBtn.addEventListener('click', async () => {
                if (!activeMenu) return;
                const qty = Math.max(0, Number(modalQty.value || 0));
                const addonOption = modalAddonSelect?.selectedOptions?.[0] || null;
                const addonKey = modalAddonSelect?.value || '';
                const addonName = addonOption?.dataset.name || '';
                const addonPrice = Number(addonOption?.dataset.price || 0);
                const addonCost = Number(addonOption?.dataset.cost || 0);
                const addonQty = addonKey ? Math.max(0, Number(modalAddonQty?.value || 0)) : 0;
                if (qty <= 0) {
                    cart.delete(activeMenu.id);
                } else {
                    cart.set(activeMenu.id, {
                        name: activeMenu.name,
                        qty: qty,
                        price: activeMenu.price,
                        note: modalNote.value || '',
                        addonKey: addonKey,
                        addonName: addonName,
                        addonPrice: addonPrice,
                        addonCost: addonCost,
                        addonQty: addonQty,
                    });
                }
                refreshOrderView();
                closeModal();

                const formData = new FormData(orderForm);
                saveBtn.disabled = true;
                try {
                    const res = await fetch(orderForm.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: formData,
                        credentials: 'same-origin',
                    });

                    let payload = {};
                    try { payload = await res.json(); } catch (_) {}

                    if (!res.ok || payload.ok === false) {
                        throw new Error(payload.message || 'Gagal mengirim pesanan.');
                    }

                    cart.clear();
                    refreshOrderView();
                    if (typeof payload.html === 'string' && tableOrderStatusWrap) {
                        tableOrderStatusWrap.innerHTML = payload.html;
                    } else {
                        refreshOrderStatuses().catch(() => {});
                    }
                    showClientAlert(payload.message || 'Pesanan berhasil dikirim.');
                } catch (err) {
                    showClientAlert(err.message || 'Gagal mengirim pesanan.', 'err');
                } finally {
                    saveBtn.disabled = false;
                }
            });

            removeBtn.addEventListener('click', () => {
                if (!activeMenu) return;
                cart.delete(activeMenu.id);
                refreshOrderView();
                closeModal();
            });

            [modalBackdrop, closeModalBtn].forEach((el) => el.addEventListener('click', closeModal));

            refreshOrderView();

            let lastStatusTs = 0;
            const refreshOrderStatuses = async () => {
                const res = await fetch(statusLiveUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                if (!res.ok) return;
                const data = await res.json();
                if (!data || typeof data.html !== 'string') return;
                tableOrderStatusWrap.innerHTML = data.html;
                if ((Number(data.latest_ts || 0) > lastStatusTs) && lastStatusTs !== 0) {
                    if (typeof window.showToast === 'function') {
                        window.showToast('Status pesanan meja diperbarui.', 'success');
                    }
                }
                lastStatusTs = Number(data.latest_ts || 0);
            };

            refreshOrderStatuses().catch(() => {});
            setInterval(() => {
                if (document.visibilityState === 'visible') {
                    refreshOrderStatuses().catch(() => {});
                }
            }, 5000);
        })();
    </script>
</body>
</html>


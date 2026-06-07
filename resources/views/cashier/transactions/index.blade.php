@extends('cashier.layout')

@section('title', ($cafeBrand['name'] ?? config('app.name')) . ' - Transaksi Kasir')
@section('page_title', 'Transaksi Kasir')
@section('page_description', 'Buat pesanan langsung dari kasir, atur meja, tambah catatan, lalu simpan ke antrean pesanan.')

@php
    $cartItems = collect($cart ?? [])->values();
    $cartCount = $cartItems->sum('qty');
    $cartTotal = $cartItems->sum('line_total');
@endphp

@push('head')
<style>
    .stats-strip {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 24px;
    }

    .strip-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .strip-icon {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .strip-card:nth-child(1) .strip-icon { background: var(--accent-light); color: var(--accent); }
    .strip-card:nth-child(2) .strip-icon { background: var(--blue-light); color: var(--blue); }
    .strip-card:nth-child(3) .strip-icon { background: var(--green-light); color: var(--green); }
    .strip-card:nth-child(4) .strip-icon { background: #F3E8FF; color: #7C3AED; }

    .strip-info strong {
        display: block;
        font-size: 18px;
        font-weight: 900;
        color: var(--fg);
        line-height: 1.1;
        letter-spacing: -0.3px;
    }

    .strip-info span {
        font-size: 11px;
        color: var(--muted);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .transaction-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(340px, 0.8fr);
        gap: 20px;
        align-items: start;
    }

    .panel {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-xs);
    }

    .panel-head {
        padding: 18px 20px;
        border-bottom: 1px solid var(--border-light);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .panel-head h2 {
        margin: 0;
        font-size: 15px;
        font-weight: 800;
        color: var(--fg);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .panel-head h2 i { color: var(--accent); }

    .panel-body {
        padding: 20px;
    }

    .catalog-search {
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 10px 14px;
        margin-bottom: 16px;
        background: var(--white);
    }

    .catalog-search i { color: var(--muted); }

    .catalog-search input {
        width: 100%;
        border: 0;
        outline: none;
        background: transparent;
        font: inherit;
        color: var(--fg);
    }

    .catalog-search input::placeholder { color: var(--muted); }

    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 12px;
    }

    .menu-card {
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 14px;
        background: var(--white);
        display: flex;
        flex-direction: column;
        gap: 12px;
        transition: all var(--transition);
    }

    .menu-card:hover {
        border-color: var(--accent);
        box-shadow: var(--shadow-sm);
        transform: translateY(-1px);
    }

    .menu-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
    }

    .menu-code {
        display: inline-flex;
        align-items: center;
        padding: 3px 8px;
        border-radius: 999px;
        background: var(--accent-light);
        color: var(--accent-dark);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.4px;
    }

    .menu-name {
        font-size: 14px;
        font-weight: 800;
        color: var(--fg);
        line-height: 1.4;
    }

    .menu-meta {
        display: grid;
        gap: 4px;
    }

    .menu-price {
        font-size: 15px;
        font-weight: 900;
        color: var(--accent-dark);
    }

    .menu-cost {
        font-size: 11px;
        color: var(--muted);
        font-weight: 600;
    }

    .menu-form,
    .row-form {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .qty-input,
    .field-control {
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        background: var(--white);
        color: var(--fg);
        font: inherit;
        min-height: 40px;
        padding: 0 12px;
        outline: none;
        transition: all var(--transition);
    }

    .qty-input:focus,
    .field-control:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(217,119,6,0.1);
    }

    .qty-input {
        width: 68px;
        text-align: center;
        font-weight: 700;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border-radius: var(--radius-sm);
        min-height: 40px;
        padding: 0 16px;
        border: 1.5px solid transparent;
        cursor: pointer;
        font: inherit;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
        transition: all var(--transition);
        white-space: nowrap;
    }

    .btn:hover { transform: translateY(-1px); }

    .btn-primary {
        background: var(--accent);
        color: #fff;
        border-color: var(--accent);
    }

    .btn-primary:hover { background: var(--accent-dark); border-color: var(--accent-dark); }

    .btn-outline {
        background: var(--white);
        color: var(--fg-secondary);
        border-color: var(--border);
    }

    .btn-outline:hover {
        background: var(--accent-light);
        color: var(--accent);
        border-color: var(--accent);
    }

    .btn-danger {
        background: transparent;
        color: var(--red);
        border-color: #FECACA;
    }

    .btn-danger:hover {
        background: var(--red-light);
        border-color: var(--red);
    }

    .cart-summary {
        display: grid;
        gap: 16px;
    }

    .cart-list {
        display: grid;
        gap: 10px;
    }

    .cart-row {
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 14px;
        background: #FCFCFD;
        display: grid;
        gap: 12px;
    }

    .cart-row-head {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        align-items: flex-start;
    }

    .cart-row-name {
        font-size: 14px;
        font-weight: 800;
        color: var(--fg);
    }

    .cart-row-code {
        font-size: 11px;
        color: var(--muted);
        font-weight: 700;
    }

    .cart-row-total {
        font-size: 14px;
        font-weight: 900;
        color: var(--fg);
        white-space: nowrap;
    }

    .row-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }

    .cart-empty {
        border: 1px dashed var(--border);
        border-radius: var(--radius-md);
        padding: 28px 18px;
        text-align: center;
        color: var(--muted);
        font-size: 13px;
        background: #FAFBFC;
    }

    .checkout-fields {
        display: grid;
        gap: 14px;
        margin-top: 4px;
    }

    .field {
        display: grid;
        gap: 6px;
    }

    .field label {
        font-size: 11px;
        font-weight: 800;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .addon-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .checkout-total {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 18px;
        border-radius: var(--radius-md);
        background: var(--accent-light);
        color: var(--accent-dark);
    }

    .checkout-total strong {
        font-size: 20px;
        font-weight: 900;
        letter-spacing: -0.3px;
    }

    .alert {
        margin-bottom: 16px;
        padding: 14px 16px;
        border-radius: var(--radius-md);
        font-size: 13px;
        font-weight: 700;
    }

    .alert-ok {
        background: var(--green-light);
        color: var(--green);
        border: 1px solid #A7F3D0;
    }

    .alert-err {
        background: var(--red-light);
        color: var(--red);
        border: 1px solid #FECACA;
    }

    @media (max-width: 1200px) {
        .stats-strip {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .transaction-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .stats-strip,
        .menu-grid,
        .addon-grid {
            grid-template-columns: 1fr;
        }

        .panel-body,
        .panel-head {
            padding: 16px;
        }

        .menu-form,
        .row-form,
        .row-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .qty-input,
        .btn,
        .field-control {
            width: 100%;
        }

        .cart-row-head,
        .checkout-total {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endpush

@section('content')
    @if (session('success'))
        <div class="alert alert-ok fade-in">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-err fade-in">{{ session('error') }}</div>
    @endif

    <section class="stats-strip fade-in">
        <article class="strip-card">
            <div class="strip-icon"><i class="fas fa-bowl-food"></i></div>
            <div class="strip-info">
                <strong>{{ $menus->count() }}</strong>
                <span>Menu Reguler</span>
            </div>
        </article>
        <article class="strip-card">
            <div class="strip-icon"><i class="fas fa-plus-circle"></i></div>
            <div class="strip-info">
                <strong>{{ $addonMenus->count() }}</strong>
                <span>Menu Tambahan</span>
            </div>
        </article>
        <article class="strip-card">
            <div class="strip-icon"><i class="fas fa-cart-shopping"></i></div>
            <div class="strip-info">
                <strong>{{ $cartCount }}</strong>
                <span>Item Di Keranjang</span>
            </div>
        </article>
        <article class="strip-card">
            <div class="strip-icon"><i class="fas fa-wallet"></i></div>
            <div class="strip-info">
                <strong>Rp {{ number_format((float) $cartTotal, 0, ',', '.') }}</strong>
                <span>Total Sementara</span>
            </div>
        </article>
    </section>

    <div class="transaction-grid">
        <section class="panel fade-in">
            <div class="panel-head">
                <h2><i class="fas fa-utensils"></i> Pilih Menu</h2>
            </div>
            <div class="panel-body">
                <div class="catalog-search">
                    <i class="fas fa-search"></i>
                    <input type="text" id="menuSearchInput" placeholder="Cari menu...">
                </div>

                <div class="menu-grid" id="menuGrid">
                    @foreach ($menus as $menu)
                        <article class="menu-card" data-menu-search="{{ strtolower($menu->name . ' ' . $menu->code) }}">
                            <div class="menu-card-head">
                                <span class="menu-code">{{ $menu->code }}</span>
                            </div>
                            <div class="menu-meta">
                                <div class="menu-name">{{ $menu->name }}</div>
                                <div class="menu-price">Rp {{ number_format((float) $menu->selling_price, 0, ',', '.') }}</div>
                                <div class="menu-cost">Modal: Rp {{ number_format((float) $menu->cost_price, 0, ',', '.') }}</div>
                            </div>
                            <form method="POST" action="{{ route('cashier.transactions.items.store') }}" class="menu-form">
                                @csrf
                                <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                                <input type="number" name="qty" value="1" min="1" class="qty-input">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Tambah
                                </button>
                            </form>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="panel fade-in">
            <div class="panel-head">
                <h2><i class="fas fa-receipt"></i> Ringkasan Transaksi</h2>
            </div>
            <div class="panel-body">
                <div class="cart-summary">
                    <div class="cart-list">
                        @forelse ($cartItems as $row)
                            <article class="cart-row">
                                <div class="cart-row-head">
                                    <div>
                                        <div class="cart-row-name">{{ $row['name'] }}</div>
                                        <div class="cart-row-code">{{ $row['code'] ?? '-' }}</div>
                                    </div>
                                    <div class="cart-row-total">
                                        Rp {{ number_format((float) $row['line_total'], 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="row-actions">
                                    <form method="POST" action="{{ route('cashier.transactions.items.update', $row['menu_id']) }}" class="row-form">
                                        @csrf
                                        @method('PUT')
                                        <input type="number" name="qty" value="{{ $row['qty'] }}" min="1" class="qty-input">
                                        <button type="submit" class="btn btn-outline">
                                            <i class="fas fa-rotate"></i> Update
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('cashier.transactions.items.destroy', $row['menu_id']) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </article>
                        @empty
                            <div class="cart-empty">
                                Belum ada item di keranjang. Tambahkan menu dari panel kiri.
                            </div>
                        @endforelse
                    </div>

                    <form method="POST" action="{{ route('cashier.transactions.checkout') }}">
                        @csrf
                        <div class="checkout-fields">
                            <div class="field">
                                <label for="table_id">Pilih Meja</label>
                                <select name="table_id" id="table_id" class="field-control">
                                    <option value="">Tanpa meja / bungkus</option>
                                    @foreach ($tables as $table)
                                        <option value="{{ $table->id }}">Meja {{ $table->number }}{{ $table->name ? ' - ' . $table->name : '' }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="field">
                                <label for="notes">Catatan Pesanan</label>
                                <textarea name="notes" id="notes" class="field-control" rows="3" placeholder="Contoh: tanpa pedas, tambah sambal, bungkus rapi."></textarea>
                            </div>

                            @if ($addonMenus->isNotEmpty())
                                <div class="addon-grid">
                                    <div class="field">
                                        <label for="addon_menu_id">Menu Tambahan</label>
                                        <select name="addon_menu_id" id="addon_menu_id" class="field-control">
                                            <option value="">Tanpa tambahan</option>
                                            @foreach ($addonMenus as $addonMenu)
                                                <option value="{{ $addonMenu->id }}">{{ $addonMenu->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="field">
                                        <label for="addon_qty">Jumlah Tambahan</label>
                                        <input type="number" name="addon_qty" id="addon_qty" min="1" value="1" class="field-control">
                                    </div>
                                </div>
                            @endif

                            <div class="checkout-total">
                                <span>Total Pesanan</span>
                                <strong>Rp {{ number_format((float) $cartTotal, 0, ',', '.') }}</strong>
                            </div>

                            <button type="submit" class="btn btn-primary" {{ $cartItems->isEmpty() ? 'disabled' : '' }}>
                                <i class="fas fa-floppy-disk"></i> Simpan Ke Pesanan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
    (() => {
        const input = document.getElementById('menuSearchInput');
        const cards = Array.from(document.querySelectorAll('#menuGrid .menu-card'));

        if (!input || !cards.length) {
            return;
        }

        input.addEventListener('input', () => {
            const keyword = input.value.trim().toLowerCase();

            cards.forEach((card) => {
                const haystack = card.dataset.menuSearch || '';
                card.style.display = haystack.includes(keyword) ? '' : 'none';
            });
        });
    })();
</script>
@endpush

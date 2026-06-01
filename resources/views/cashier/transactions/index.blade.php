@extends('cashier.layout')

@section('title', ($cafeBrand['name'] ?? config('app.name')) . ' - Transaksi Kasir')

@push('head')
    <style>
        .main-panel { padding: 2rem 2.5rem; overflow-y: auto; }
        .pos-shell { max-width: 100%; }
        .panel { background: var(--bg-card); border: 1px solid var(--accent); border-radius: 20px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; box-shadow: 0 4px 15px var(--shadow); }
        .page-title { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.8rem; margin: 0 0 0.5rem; }
        .page-desc { color: var(--text-muted); font-size: 0.95rem; margin: 0; }
        .section-title { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.2rem; margin: 0 0 1.25rem; }
        .alert { padding: 0.85rem 1.25rem; border-radius: 14px; margin-bottom: 1.25rem; font-weight: 500; font-size: 0.95rem; border: 1px solid transparent; }
        .ok { background: #E8F5E9; color: #558B2F; border-color: #C8E6C9; }
        .err { background: #FFEBEE; color: #C62828; border-color: #FFCDD2; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .field { margin-bottom: 1rem; display: flex; flex-direction: column; gap: 0.4rem; }
        .field label { font-size: 0.85rem; font-weight: 600; color: var(--text-muted); }
        .field-hint { font-size: 0.8rem; color: var(--text-muted); margin-top: -0.1rem; }
        input, select, textarea { width: 100%; border: 1px solid var(--accent); border-radius: 12px; padding: 0.65rem 1rem; background-color: var(--bg-card); color: var(--text-main); font-family: inherit; font-size: 0.95rem; outline: none; transition: border-color 0.2s ease, box-shadow 0.2s ease; }
        input:focus, select:focus, textarea:focus { border-color: var(--highlight); box-shadow: 0 0 0 3px rgba(212, 163, 115, 0.15); }
        textarea { resize: vertical; min-height: 80px; }
        .btn { border: 1px solid transparent; border-radius: 12px; padding: 0.65rem 1.2rem; cursor: pointer; font-weight: 600; font-family: inherit; font-size: 0.95rem; transition: all 0.2s ease; }
        .btn-primary { background: var(--highlight); color: #fff; border: none; box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3); }
        .btn-primary:hover { background: #c68b59; transform: translateY(-2px); }
        .btn-danger { background: transparent; color: var(--loss); border-color: #F8D7DA; }
        .btn-danger:hover { background: #FFF0F0; border-color: var(--loss); }
        .btn-sm { padding: 0.4rem 0.8rem; font-size: 0.85rem; border-radius: 8px; }
        .input-qty { width: 70px !important; text-align: center; padding: 0.4rem; border-radius: 8px; }
        .row-actions { display: flex; gap: 0.5rem; align-items: center; }
        .mini-badge { display: inline-flex; align-items: center; padding: 0.2rem 0.55rem; border-radius: 999px; background: #E8F5E9; color: #558B2F; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 0.35rem; }
        .order-feed { display: grid; gap: 1rem; }
        .order-card { border: 1px solid var(--accent); border-radius: 16px; padding: 1rem 1.1rem; background: #FFFAF5; box-shadow: 0 2px 8px var(--shadow); }
        .order-head { display: flex; justify-content: space-between; gap: 1rem; flex-wrap: wrap; align-items: flex-start; }
        .order-code { display: inline-flex; align-items: center; padding: 0.25rem 0.7rem; border-radius: 999px; background: var(--highlight); color: #fff; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; }
        .order-title { margin-top: 0.55rem; font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.15rem; }
        .order-meta { color: var(--text-muted); font-size: 0.9rem; display: grid; gap: 0.2rem; }
        .order-items { margin-top: 0.9rem; padding-top: 0.75rem; border-top: 1px dashed var(--accent); display: grid; gap: 0.5rem; }
        .order-item { display: flex; justify-content: space-between; gap: 1rem; font-size: 0.95rem; }
        .order-item strong { color: var(--primary); }
        .order-footer { display: flex; justify-content: space-between; gap: 1rem; margin-top: 0.9rem; padding-top: 0.75rem; border-top: 1px solid var(--accent); flex-wrap: wrap; }
        .status-pill { display: inline-flex; align-items: center; padding: 0.25rem 0.65rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; }
        .status-pending { background: #FFF3E0; color: #E65100; }
        .status-processing { background: #E3F2FD; color: #1565C0; }
        .status-ready { background: #E8F5E9; color: #2E7D32; }
        .status-completed { background: #E8F5E9; color: #2E7D32; }
        .status-cancelled { background: #FFEBEE; color: #C62828; }
        .empty-orders { color: var(--text-muted); font-style: italic; text-align: center; padding: 2rem 0; }
        .form-section-right { border-left: 1px dashed var(--accent); padding-left: 1.5rem; }
        @media (max-width: 900px) { 
            .grid { grid-template-columns: 1fr; } 
            .form-section-right { border-left: none; padding-left: 0; border-top: 1px dashed var(--accent); padding-top: 1.5rem; margin-top: 1.5rem; }
        }
        @media (max-width: 768px) { .main-panel { padding: 1.5rem 1rem; } .page-title { font-size: 1.5rem; } .panel { padding: 1.25rem; } }
    </style>
@endpush

@section('content')
    <div class="pos-shell">
        <section class="panel">
            <h1 class="page-title">Transaksi Kasir</h1>
            <p class="page-desc">Membuat pesanan langsung di kasir: tambah menu, ubah jumlah, hapus menu, lalu simpan.</p>
        </section>

        @if (session('success'))
            <div class="alert ok">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert err">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert err">{{ $errors->first() }}</div>
        @endif

        <section class="panel">
            <form method="POST" action="{{ route('cashier.transactions.checkout') }}">
                @csrf
                <div class="grid" style="margin-bottom: 0;">
                    <div class="form-section">
                        <h2 class="section-title">Detail Menu</h2>
                        <div class="field">
                            <label>Menu</label>
                            <select name="menu_id">
                                <option value="">Pilih menu (atau simpan item di bawah)</option>
                                @foreach ($menus as $menu)
                                    <option value="{{ $menu->id }}">{{ $menu->name }} - Rp {{ number_format((float) $menu->selling_price, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Jumlah</label>
                            <input type="number" name="qty" min="1" value="1">
                        </div>
                    </div>

                    <div class="form-section-right">
                        <h2 class="section-title">Detail Pesanan</h2>
                        <div class="field">
                            <label>Tambahan</label>
                            <select name="addon_menu_id">
                                <option value="">Tanpa tambahan</option>
                                @foreach ($addonMenus as $addon)
                                    <option value="{{ $addon->id }}">{{ $addon->name }} - Rp {{ number_format((float) $addon->selling_price, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                            <div class="field-hint">Contoh: Mie Goreng + 2 telur tambahan.</div>
                        </div>
                        <div class="field">
                            <label>Jumlah tambahan</label>
                            <input type="number" name="addon_qty" min="1" value="1">
                        </div>
                        <div class="field">
                            <label>Tambahan manual</label>
                            <input type="text" name="addon_custom_name" placeholder="Contoh: Telur, Keju, Sosis">
                        </div>
                        <div class="grid" style="gap: 0.75rem; margin-bottom: 0;">
                            <div class="field">
                                <label>Harga tambahan manual</label>
                                <input type="number" name="addon_custom_price" min="0" step="0.01" placeholder="3000">
                            </div>
                            <div class="field">
                                <label>Jumlah manual</label>
                                <input type="number" name="addon_custom_qty" min="1" value="1">
                            </div>
                        </div>
                        <div class="field">
                            <label>Meja (opsional)</label>
                            <select name="table_id">
                                <option value="">Tanpa meja</option>
                                @foreach ($tables as $table)
                                    <option value="{{ $table->id }}">Meja {{ $table->number }} - {{ $table->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Catatan</label>
                            <textarea name="notes" rows="3" placeholder="Contoh: tanpa gula, extra espresso"></textarea>
                        </div>
                    </div>
                </div>
                <div style="margin-top: 1.5rem; text-align: center; border-top: 1px solid var(--accent); padding-top: 1.5rem;">
                    <button class="btn btn-primary" type="submit" style="min-width: 200px; font-size: 1.1rem; padding: 0.8rem 2rem;">
                        Simpan Pesanan
                    </button>
                </div>
            </form>
        </section>

        <section class="panel">
            <div class="row" style="align-items: center; margin-bottom: 1rem;">
                <div>
                    <h2 class="section-title" style="margin-bottom: 0.2rem;">Pesanan Masuk Pelanggan</h2>
                    <p class="field-hint" style="margin: 0;">Pesanan dari meja pelanggan akan muncul otomatis di sini.</p>
                </div>
                <a href="{{ route('cashier.orders.index') }}" class="btn btn-primary btn-sm">Buka Halaman Pesanan</a>
            </div>

            <div id="cashierOrdersList" class="order-feed">
                <div class="empty-orders">Memuat pesanan...</div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const listEl = document.getElementById('cashierOrdersList');
        if (!listEl) return;

        let isLoading = false;

        const reloadOrders = async () => {
            if (isLoading) return;
            isLoading = true;
            try {
                const url = new URL(@json(route('cashier.orders.live')), window.location.origin);
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                });
                if (!response.ok) return;
                const data = await response.json();
                listEl.innerHTML = data.html || '';
            } catch (e) {
                console.error(e);
            } finally {
                isLoading = false;
            }
        };

        reloadOrders();
        setInterval(() => {
            if (document.visibilityState === 'visible') {
                reloadOrders();
            }
        }, 3000);
    })();
</script>
@endpush

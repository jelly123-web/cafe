@extends('leader_cashier.layout')

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
        .cart-table { width: 100%; border-collapse: collapse; }
        .cart-table th, .cart-table td { padding: 0.85rem 0.75rem; border-bottom: 1px dashed var(--accent); vertical-align: middle; text-align: left; font-size: 0.95rem; }
        .cart-table th { background: var(--bg-main); border-bottom: 2px solid var(--highlight); font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; letter-spacing: 0.5px; }
        .cart-table tfoot td { border-bottom: none; border-top: 2px solid var(--primary); padding-top: 1rem; }
        .text-right { text-align: right; }
        .total-label { font-family: 'Playfair Display', Georgia, serif; font-size: 1.2rem; color: var(--primary); }
        .total-amount { font-family: 'Playfair Display', Georgia, serif; font-size: 1.4rem; color: var(--highlight); font-weight: 700; }
        .empty-cart { color: var(--text-muted); font-style: italic; text-align: center; padding: 2rem 0; }
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
            <h2 class="section-title">Item Pesanan Saat Ini</h2>
            @php $total = collect($cart)->sum('line_total'); @endphp
            @if (empty($cart))
                <p class="empty-cart">Belum ada item.</p>
            @else
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Menu</th><th>Harga</th><th>Qty</th><th class="text-right">Subtotal</th><th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cart as $row)
                            <tr>
                                <td>
                                    @if ($row['is_addon'] ?? false)
                                        <span class="mini-badge">Tambahan</span>
                                    @endif
                                    <div>{{ $row['name'] }}</div>
                                </td>
                                <td>Rp {{ number_format((float) $row['unit_price'], 0, ',', '.') }}</td>
                                <td>
                                    <form method="POST" action="{{ route('cashier.transactions.items.update', $row['menu_id']) }}" class="row-actions">
                                        @csrf
                                        @method('PUT')
                                        <input type="number" name="qty" min="1" value="{{ $row['qty'] }}" class="input-qty">
                                        <button class="btn btn-primary btn-sm" type="submit">Ubah</button>
                                    </form>
                                </td>
                                <td class="text-right">Rp {{ number_format((float) $row['line_total'], 0, ',', '.') }}</td>
                                <td>
                                    <form method="POST" action="{{ route('cashier.transactions.items.destroy', $row['menu_id']) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="total-label">Total</td>
                            <td class="text-right total-amount">Rp {{ number_format((float) $total, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            @endif
        </section>
    </div>
@endsection

@extends('leader_cashier.layout')

@section('title', 'Transaksi Kasir — MakanYuk')
@section('page_title', 'Transaksi Kasir')
@section('page_description', 'Membuat pesanan langsung di kasir: tambah menu, ubah jumlah, hapus menu, lalu simpan.')

@push('head')
    <link rel="stylesheet" href="{{ asset('css/leader-cashier.css') }}">
    <style>
        /* Cart Strip */
        .cart-strip {
            background: var(--white); border: 1px solid var(--border);
            border-radius: var(--radius-md); padding: 14px 20px;
            margin-bottom: 20px; display: flex; align-items: center;
            justify-content: space-between; gap: 14px;
        }
        .cart-info { display: flex; align-items: center; gap: 12px; }
        .cart-icon {
            width: 42px; height: 42px; border-radius: var(--radius-sm);
            background: var(--accent-light); color: var(--accent);
            display: flex; align-items: center; justify-content: center;
            font-size: 17px;
        }
        .cart-count { font-size: 18px; font-weight: 900; color: var(--fg); }
        .cart-label { font-size: 11px; color: var(--muted); font-weight: 600; text-transform: uppercase; }
        .cart-total { font-size: 20px; font-weight: 900; color: var(--accent); }

        /* Panel Styling */
        .panel { background: var(--white); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 24px; margin-bottom: 20px; }
        .pos-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 28px; }
        .section-title { font-size: 13px; font-weight: 800; color: var(--fg); text-transform: uppercase; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        
        /* Menu Grid */
        .menu-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 16px; }
        .menu-chip { border: 1.5px solid var(--border); border-radius: var(--radius-sm); padding: 10px; cursor: pointer; text-align: center; }
        .menu-chip .chip-name { font-size: 12px; font-weight: 700; color: var(--fg); display: block; }
        .menu-chip .chip-price { font-size: 11px; font-weight: 700; color: var(--accent-dark); }
        
        /* Field Styling */
        .field { margin-bottom: 16px; display: flex; flex-direction: column; gap: 6px; }
        .field label { font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; }
        .field input, .field select { width: 100%; border: 1.5px solid var(--border); border-radius: var(--radius-sm); padding: 9px; font-size: 13px; }
        
        .btn-primary { background: var(--accent); color: white; padding: 12px; border-radius: var(--radius-md); font-weight: 800; text-align: center; }
        
        /* Order Feed */
        .order-card { border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 20px; margin-bottom: 16px; }
        .order-header { display: flex; justify-content: space-between; align-items: flex-start; }
        .status-badge { font-size: 11px; padding: 4px 12px; border-radius: var(--radius-full); font-weight: 800; text-transform: uppercase; }
        
        .status-pending { background: var(--accent-light); color: var(--accent-dark); }
        .status-processing { background: var(--blue-light); color: var(--blue); }
        .status-ready { background: var(--green-light); color: var(--green); }
        .status-cancelled { background: var(--red-light); color: var(--red); }
    </style>
@endpush

@section('content')
    <!-- Cart Strip -->
    <div class="cart-strip fade-in">
        <div class="cart-info">
            <div class="cart-icon"><i class="fas fa-cart-shopping"></i></div>
            <div>
                <div class="cart-count">0</div>
                <div class="cart-label">Item di keranjang</div>
            </div>
        </div>
        <div class="cart-total">Rp 0</div>
    </div>

    <!-- POS Panel -->
    <section class="panel fade-in">
        <h2 class="section-title"><i class="fas fa-plus-circle"></i> Buat Pesanan</h2>
        <div class="pos-grid">
            <div>
                <h3 class="section-title"><i class="fas fa-utensils"></i> Pilih Menu</h3>
                <div class="menu-grid">
                    @foreach($menus as $menu)
                        <div class="menu-chip">
                            <span class="chip-name">{{ $menu->name }}</span>
                            <span class="chip-price">Rp {{ number_format((float)$menu->price, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div>
                <h3 class="section-title"><i class="fas fa-file-lines"></i> Detail Pesanan</h3>
                <div class="field">
                    <label>Tambahan Menu</label>
                    <select><option>Tanpa tambahan</option></select>
                </div>
                <div class="field">
                    <label>Jumlah Tambahan</label>
                    <input type="number" value="1">
                </div>
                <button class="btn-primary" style="width:100%">+ Tambah</button>
            </div>
        </div>
    </section>

    <!-- Order Feed -->
    <section class="panel fade-in">
        <h2 class="section-title"><i class="fas fa-clipboard-list"></i> Pesanan Masuk Pelanggan <span style="float:right; color:var(--green); font-size:12px;">● LIVE</span></h2>
        @foreach ($orders as $order)
            @php
                $statusClass = match ($order->status) {
                    \App\Models\SaleTransaction::STATUS_PROCESSING => 'status-processing',
                    \App\Models\SaleTransaction::STATUS_READY => 'status-ready',
                    \App\Models\SaleTransaction::STATUS_CANCELLED => 'status-cancelled',
                    default => 'status-pending',
                };
            @endphp
            <article class="order-card">
                <div class="order-header">
                    <div>
                        <span class="order-code" style="background:var(--accent-light);color:var(--accent-dark);">{{ $order->code }}</span>
                        <div class="order-info">Meja {{ $order->table?->number ?? '-' }}</div>
                    </div>
                    <span class="status-badge {{ $statusClass }}">{{ $order->statusLabel() }}</span>
                </div>
                <div class="order-items">
                    @foreach ($order->items as $item)
                        <div class="order-item"><span>{{ $item->qty }}x {{ $item->menu?->name }}</span> <strong>Rp {{ number_format((float)$item->line_total, 0, ',', '.') }}</strong></div>
                    @endforeach
                </div>
                <div class="order-footer">
                    <strong>Total: Rp {{ number_format((float)$order->total_amount, 0, ',', '.') }}</strong>
                    @if($order->canBeCancelled()) <button class="btn-cancel">Batalkan</button> @endif
                </div>
            </article>
        @endforeach
    </section>
@endsection

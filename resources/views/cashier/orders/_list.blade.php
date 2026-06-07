@forelse ($orders as $order)
    @php
        $statusClass = match ($order->status) {
            \App\Models\SaleTransaction::STATUS_PENDING => 'status-pending',
            \App\Models\SaleTransaction::STATUS_PROCESSING => 'status-processing',
            \App\Models\SaleTransaction::STATUS_READY => 'status-ready',
            \App\Models\SaleTransaction::STATUS_COMPLETED,
            \App\Models\SaleTransaction::STATUS_PAID => 'status-ready',
            \App\Models\SaleTransaction::STATUS_CANCELLED => 'status-cancelled',
            default => 'status-pending',
        };

        $iconStyle = match ($order->status) {
            \App\Models\SaleTransaction::STATUS_PENDING => 'background:var(--accent-light);color:var(--accent);',
            \App\Models\SaleTransaction::STATUS_PROCESSING => 'background:var(--blue-light);color:var(--blue);',
            \App\Models\SaleTransaction::STATUS_READY,
            \App\Models\SaleTransaction::STATUS_COMPLETED,
            \App\Models\SaleTransaction::STATUS_PAID => 'background:var(--green-light);color:var(--green);',
            \App\Models\SaleTransaction::STATUS_CANCELLED => 'background:var(--red-light);color:var(--red);',
            default => 'background:var(--accent-light);color:var(--accent);',
        };
    @endphp

    <article class="order fade-in" data-order-id="{{ $order->id }}">
        <div class="order-head">
            <div class="order-head-left">
                <div class="order-icon" style="{{ $iconStyle }}">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="order-head-info">
                    <span class="order-code">{{ $order->code }}</span>
                    <div class="order-title">Meja {{ $order->table?->number ?? '-' }}</div>
                    <div class="order-meta">
                        <span><i class="far fa-clock"></i> {{ $order->sold_at?->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>
            <span class="status-pill {{ $statusClass }}">
                <span class="status-dot"></span> {{ $order->statusLabel() }}
            </span>
        </div>

        <div class="order-items">
            @foreach ($order->items as $item)
                @php
                    $itemName = $item->food_package_id
                        ? ($item->foodPackage?->name ?? 'Paket')
                        : ($item->menu?->name ?? 'Menu');
                @endphp
                <div class="order-item">
                    <div class="item-left">
                        <span class="item-qty">{{ $item->qty }}</span>
                        <span class="item-name">{{ $itemName }}</span>
                    </div>
                    <span class="item-price">Rp {{ number_format((float) $item->line_total, 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>

        <div class="order-footer">
            <div class="order-total">
                <small>Total</small>
                Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}
            </div>
            @if ($canCancelOrders && $order->canBeCancelled())
                <form method="POST" action="{{ route('cashier.orders.cancel', $order) }}" class="cancel-order-form">
                    @csrf
                    <button type="submit" class="btn-cancel"><i class="fas fa-xmark"></i> Batalkan</button>
                </form>
            @endif
        </div>
    </article>
@empty
    <div class="empty-state">Belum ada pesanan.</div>
@endforelse

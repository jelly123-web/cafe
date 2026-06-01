@forelse ($orders as $order)
    @php
        $statusClass = match ($order->status) {
            \App\Models\SaleTransaction::STATUS_PENDING => 'status-pending',
            \App\Models\SaleTransaction::STATUS_PROCESSING => 'status-processing',
            \App\Models\SaleTransaction::STATUS_READY => 'status-ready',
            \App\Models\SaleTransaction::STATUS_COMPLETED,
            \App\Models\SaleTransaction::STATUS_PAID => 'status-completed',
            \App\Models\SaleTransaction::STATUS_CANCELLED => 'status-cancelled',
            default => 'status-pending',
        };
    @endphp

    <article class="order order-card" data-order-id="{{ $order->id }}">
        <div class="row order-head">
            <div>
                <span class="pill order-code">{{ $order->code }}</span>
                <strong class="order-title">
                    Meja {{ $order->table?->number ?? '-' }} - {{ $order->table?->name ?? 'Tanpa meja' }}
                </strong>
                <div class="muted order-meta">
                    <span>Waktu: {{ $order->sold_at?->format('d M Y H:i') ?? '-' }}</span>
                    <span>Total item: {{ $order->items_count }}</span>
                </div>
            </div>

            <div style="text-align: right;">
                <span class="status-pill {{ $statusClass }}">{{ $order->statusLabel() }}</span>
                <div style="margin-top: 0.65rem; color: var(--text-muted); font-size: 0.9rem;">
                    Total: <strong style="color: var(--primary);">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        <div class="items order-items">
            @foreach ($order->items as $item)
                <div class="item order-item">
                    <span>{{ $item->qty }}x {{ $item->menu?->name ?? 'Menu' }}</span>
                    <strong>Rp {{ number_format((float) $item->line_total, 0, ',', '.') }}</strong>
                </div>
            @endforeach
        </div>

        <div class="order-footer">
            <div class="muted order-meta">
                <span>Branch: {{ $order->branch?->name ?? 'Utama' }}</span>
            </div>

            @if ($canCancelOrders && $order->canBeCancelled())
                <form method="POST" action="{{ route('cashier.orders.cancel', $order) }}" style="margin: 0;" class="cancel-order-form">
                    @csrf
                    <button type="submit" class="btn btn-cancel">Batalkan Pesanan</button>
                </form>
            @endif
        </div>
    </article>
@empty
    <div class="muted empty-orders">Belum ada pesanan.</div>
@endforelse

<div class="pagination-area">
    {{ $orders->links() }}
</div>

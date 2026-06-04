@forelse ($orders as $order)
    @php
        $status = (string) $order->status;
        $tagClass = match ($status) {
            \App\Models\SaleTransaction::STATUS_PROCESSING => 'status-processing',
            \App\Models\SaleTransaction::STATUS_READY => 'status-ready',
            \App\Models\SaleTransaction::STATUS_COMPLETED, \App\Models\SaleTransaction::STATUS_PAID => 'status-completed',
            \App\Models\SaleTransaction::STATUS_CANCELLED => 'status-cancelled',
            default => 'status-pending',
        };
    @endphp
    <article class="order-status-card">
        <div class="order-status-head">
            <strong>{{ $order->code }}</strong>
            <span class="status-pill {{ $tagClass }}">{{ $order->statusLabel() }}</span>
        </div>
        <div class="order-status-meta">
            Waktu: {{ $order->sold_at?->format('d M Y H:i') }} | {{ (int) $order->items->sum('qty') }} item
        </div>
        <div class="order-status-items">
            @foreach ($order->items as $item)
                @php
                    $itemName = $item->food_package_id ? ($item->foodPackage?->name ?? 'Paket') : ($item->menu?->name ?? 'Menu');
                @endphp
                <div>{{ $item->qty }}x {{ $itemName }}</div>
            @endforeach
        </div>
    </article>
@empty
    <div class="empty">Belum ada pesanan dari meja ini.</div>
@endforelse

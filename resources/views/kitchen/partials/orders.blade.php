@forelse ($orders as $order)
    @php
        $tagClass = match ($order->status) {
            \App\Models\SaleTransaction::STATUS_PROCESSING => 'tag-cooking',
            \App\Models\SaleTransaction::STATUS_READY => 'tag-ready',
            \App\Models\SaleTransaction::STATUS_COMPLETED => 'tag-done',
            default => 'tag-pending',
        };
    @endphp
    <article class="order-card">
        <div class="order-top">
            <div>
                <span class="pill">{{ $order->code }}</span>
                <h3>Meja {{ $order->table?->number ?? '-' }}</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">Pelanggan: {{ $hasCustomerName ? ($order->customer_name ?: '-') : '-' }}</p>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">Waktu: {{ $order->sold_at?->format('d M Y, H:i') }}</p>
            </div>
            <div class="order-meta">
                <span class="tag {{ $tagClass }}">{{ $order->statusLabel() }}</span>
            </div>
        </div>

        <div class="item-list">
            <div class="detail-title">Detail Pesanan</div>
            <div class="detail-meta">
                Daftar menu yang dipesan, jumlah menu: <strong>{{ (int) $order->items->sum('qty') }}</strong>
            </div>
            @foreach ($order->items as $item)
                <div class="item-row">
                    <span><span class="item-qty">{{ $item->qty }}x</span>{{ $item->menu?->name ?? 'Menu' }}</span>
                </div>
            @endforeach
        </div>

        <div class="order-note">
            Catatan: {{ $order->notes ?: 'Tidak ada catatan.' }}
        </div>

        @if ($hasStatus)
            <form method="POST" action="{{ route('kitchen.orders.status', $order) }}" class="action-group">
                @csrf
                @method('PUT')
                @foreach ($kitchenStatuses as $value => $label)
                    @php
                        $btnClass = match ($value) {
                            \App\Models\SaleTransaction::STATUS_PENDING => 'btn-secondary',
                            \App\Models\SaleTransaction::STATUS_PROCESSING => 'btn-primary',
                            \App\Models\SaleTransaction::STATUS_READY,
                            \App\Models\SaleTransaction::STATUS_COMPLETED => 'btn-success',
                            default => 'btn-secondary',
                        };
                    @endphp
                    <button class="btn {{ $btnClass }}" type="submit" name="status" value="{{ $value }}">{{ $label }}</button>
                @endforeach
            </form>
        @endif
    </article>
@empty
    <div class="empty-state">Belum ada pesanan masuk hari ini.</div>
@endforelse

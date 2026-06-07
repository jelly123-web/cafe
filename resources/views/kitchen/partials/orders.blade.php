@forelse ($orders as $order)
    @php
        $statusValue = $order->status ?? \App\Models\SaleTransaction::STATUS_PENDING;
        $statusClass = match ($statusValue) {
            \App\Models\SaleTransaction::STATUS_PROCESSING => 'cooking',
            \App\Models\SaleTransaction::STATUS_READY => 'ready',
            \App\Models\SaleTransaction::STATUS_COMPLETED => 'done',
            default => 'pending',
        };
        $customerName = $hasCustomerName ? ($order->customer_name ?: '-') : '-';
        $soldAtText = $order->sold_at?->diffForHumans() ?? '-';
        $nextAction = match ($statusValue) {
            \App\Models\SaleTransaction::STATUS_PENDING => [
                'value' => \App\Models\SaleTransaction::STATUS_PROCESSING,
                'label' => 'Masak',
                'icon' => 'fas fa-fire',
                'class' => 'btn-cook',
            ],
            \App\Models\SaleTransaction::STATUS_PROCESSING => [
                'value' => \App\Models\SaleTransaction::STATUS_READY,
                'label' => 'Siap Saji',
                'icon' => 'fas fa-bell',
                'class' => 'btn-ready',
            ],
            \App\Models\SaleTransaction::STATUS_READY => [
                'value' => \App\Models\SaleTransaction::STATUS_COMPLETED,
                'label' => 'Selesai',
                'icon' => 'fas fa-check-circle',
                'class' => 'btn-done',
            ],
            default => null,
        };
    @endphp
    <article class="order-card" data-status="{{ $statusValue }}">
        <div class="order-card-head">
            <div>
                <span class="status-badge {{ $statusClass }}"><span class="status-dot"></span> {{ $order->statusLabel() }}</span>
                <h3>{{ $order->code }}</h3>
            </div>
            <div class="order-meta">
                <span><i class="fas fa-chair"></i> Meja {{ $order->table?->number ?? '-' }}</span>
                <span><i class="fas fa-user"></i> {{ $customerName }}</span>
                <span><i class="fas fa-clock"></i> {{ $soldAtText }}</span>
            </div>
        </div>

        <div class="order-items">
            <div class="items-title"><i class="fas fa-utensils"></i> Item Pesanan</div>
            @foreach ($order->items as $item)
                @php
                    $itemName = $item->food_package_id
                        ? ($item->foodPackage?->name ?? 'Paket')
                        : ($item->menu?->name ?? 'Menu');
                @endphp
                <div class="item-row">
                    <span class="item-qty">{{ $item->qty }}x</span>
                    <span class="item-name">{{ $itemName }}</span>
                </div>
            @endforeach
        </div>

        @if (!empty($order->notes))
            <div class="order-note">
                <div>{{ $order->notes }}</div>
                <button
                    type="button"
                    class="note-speak-btn"
                    data-speak-note
                    data-note=@json($order->notes)
                >
                    <i class="fas fa-volume-up"></i> Dengar catatan
                </button>
            </div>
        @endif

        @if ($hasStatus && $nextAction)
            <div class="order-actions">
                <form method="POST" action="{{ route('kitchen.orders.status', $order) }}" class="action-group">
                    @csrf
                    @method('PUT')
                    <button type="submit" name="status" value="{{ $nextAction['value'] }}" class="btn-action {{ $nextAction['class'] }}">
                        <i class="{{ $nextAction['icon'] }}"></i> {{ $nextAction['label'] }}
                    </button>
                </form>
            </div>
        @endif
    </article>
@empty
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <strong>Belum ada pesanan masuk</strong>
        <span>Pesanan baru dari pelanggan akan muncul otomatis di sini.</span>
    </div>
@endforelse

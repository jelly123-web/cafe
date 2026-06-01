<div class="toolbar">
    <form method="POST" action="{{ route('cashier.payments.destroy-all') }}" onsubmit="return confirm('Hapus semua data pembayaran/transaksi?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-delete-all">Hapus Data</button>
    </form>
</div>

@forelse ($orders as $order)
    <article class="order">
        <div class="order-head">
            <div>
                <span class="order-code">{{ $order->code }}</span>
                <small class="order-meta">Meja {{ $order->table?->number ?? '-' }} - {{ $order->table?->name ?? 'Tanpa meja' }}</small>
            </div>
            <div style="text-align:right;">
                @php
                    $isPaid = $order->status === \App\Models\SaleTransaction::STATUS_PAID;
                    $isCancelled = $order->status === \App\Models\SaleTransaction::STATUS_CANCELLED;
                    $tagClass = $isCancelled ? 'tag-cancelled' : ($isPaid ? 'tag-paid' : 'tag-unpaid');
                @endphp
                <span class="tag {{ $tagClass }}">{{ $order->statusLabel() }}</span>
                <span class="order-total">Total: Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</span>
                @if ($order->payment_method)
                    <small class="order-meta" style="display:block; margin-top:0.25rem;">Metode: {{ strtoupper(str_replace('_', '/', $order->payment_method)) }}</small>
                @endif
            </div>
        </div>

        @if (!$isPaid && !$isCancelled)
            <div class="payment-actions">
                <form method="POST" action="{{ route('cashier.payments.pay', $order) }}">
                    @csrf
                    <input type="hidden" name="payment_method" value="cash">
                    <button type="submit" class="btn btn-cash">Bayar Tunai</button>
                </form>
                <form method="POST" action="{{ route('cashier.payments.pay', $order) }}">
                    @csrf
                    <input type="hidden" name="payment_method" value="qris">
                    <button type="submit" class="btn btn-qris">Bayar QRIS</button>
                </form>
                <form method="POST" action="{{ route('cashier.payments.pay', $order) }}">
                    @csrf
                    <input type="hidden" name="payment_method" value="transfer_ewallet">
                    <button type="submit" class="btn btn-transfer">Transfer / E-Wallet</button>
                </form>
            </div>
        @endif
    </article>
@empty
    <p class="order-meta">Belum ada transaksi.</p>
@endforelse

<div class="pagination-area">
    {{ $orders->links('components.pagination') }}
</div>

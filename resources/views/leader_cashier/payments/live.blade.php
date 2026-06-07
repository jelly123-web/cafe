<div class="history-section">
    <div class="history-head">
        <div class="history-head-left">
            <h2 class="history-title"><i class="fas fa-clock-rotate-left"></i> Riwayat Pembayaran <span class="live-indicator"><span class="live-dot"></span> Live</span></h2>
            <p class="history-subtitle">Daftar tagihan yang menunggu atau sudah diproses.</p>
        </div>
        <div class="toolbar">
            <form method="POST" action="{{ route('leader-cashier.payments.destroy-all') }}" onsubmit="return confirm('Hapus semua data pembayaran/transaksi?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-delete-all"><i class="fas fa-trash-can"></i> Hapus Semua</button>
            </form>
        </div>
    </div>

    @if ($orders->count())
        <div class="history-list">
            @foreach ($orders as $order)
                @php
                    $isPaid = $order->status === \App\Models\SaleTransaction::STATUS_PAID;
                    $isCancelled = $order->status === \App\Models\SaleTransaction::STATUS_CANCELLED;
                    $tagClass = $isCancelled ? 'tag-cancelled' : ($isPaid ? 'tag-paid' : 'tag-unpaid');
                @endphp
                <div class="order">
                    <div class="order-head">
                        <div>
                            <span class="order-code">{{ $order->code }}</span>
                            <span class="order-meta">
                                <i class="far fa-clock"></i> {{ optional($order->sold_at)->format('d M Y, H:i') }} 
                                · Meja {{ $order->table?->number ?? '-' }} 
                                · Kasir: {{ $order->user?->name ?? 'System' }}
                            </span>
                            <span class="order-total">Total: Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</span>
                            @if ($order->payment_method)
                                <small class="order-meta" style="display:block; margin-top:0.25rem;">Metode: {{ strtoupper(str_replace('_', '/', $order->payment_method)) }}</small>
                            @endif
                        </div>
                        <span class="tag {{ $tagClass }}"><span class="tag-dot"></span> {{ $order->statusLabel() }}</span>
                    </div>

                    @if (!$isPaid && !$isCancelled)
                        <div class="payment-actions">
                            <form method="POST" action="{{ route('leader-cashier.payments.pay', $order) }}">
                                @csrf
                                <input type="hidden" name="payment_method" value="cash">
                                <button type="submit" class="btn btn-cash"><i class="fas fa-money-bill-wave"></i> Tunai</button>
                            </form>
                            <form method="POST" action="{{ route('leader-cashier.payments.pay', $order) }}">
                                @csrf
                                <input type="hidden" name="payment_method" value="qris">
                                <button type="submit" class="btn btn-qris"><i class="fas fa-qrcode"></i> QRIS</button>
                            </form>
                            <form method="POST" action="{{ route('leader-cashier.payments.pay', $order) }}">
                                @csrf
                                <input type="hidden" name="payment_method" value="transfer_ewallet">
                                <button type="submit" class="btn btn-transfer"><i class="fas fa-building-columns"></i> Transfer</button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="history-empty">
            <div>
                <i class="fas fa-clock-rotate-left"></i>
                <strong>Belum ada transaksi</strong>
                <p>Tagihan hasil scan barcode atau pembayaran pelanggan akan muncul di sini.</p>
            </div>
        </div>
    @endif

    <div class="pagination-area">
        {{ $orders->links('components.pagination') }}
    </div>
</div>


@forelse ($history as $order)
    @php
        $started = $order->sold_at;
        $finished = $order->updated_at;
        $duration = ($started && $finished) ? $started->diffForHumans($finished, true) : '-';
    @endphp
    <tr>
        <td><span class="pill">{{ $order->code }}</span></td>
        <td><span class="table-num">{{ $order->table?->number ? 'Meja ' . $order->table->number : 'Takeaway' }}</span></td>
        <td>{{ $started?->format('d M Y, H:i') }}</td>
        <td>{{ $finished?->format('d M Y, H:i') }}</td>
        <td><span class="duration">{{ $duration }}</span></td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="empty-state">Belum ada riwayat pesanan selesai.</td>
    </tr>
@endforelse

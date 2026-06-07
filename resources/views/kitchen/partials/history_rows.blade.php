@forelse ($history as $order)
    @php
        $started = $order->sold_at;
        $finished = $order->updated_at;
        $durationMinutes = ($started && $finished) ? max(0, $started->diffInMinutes($finished)) : null;
        $durationText = $durationMinutes === null ? '-' : ($durationMinutes . ' Menit');
        $durationClass = match (true) {
            $durationMinutes !== null && $durationMinutes <= 15 => 'fast',
            $durationMinutes !== null && $durationMinutes <= 30 => 'normal',
            default => 'slow',
        };
        $durationIcon = match ($durationClass) {
            'fast' => 'fas fa-bolt',
            'normal' => 'fas fa-clock',
            default => 'fas fa-hourglass-half',
        };
        $tableLabel = $order->table?->number ? 'Meja ' . $order->table->number : 'Takeaway';
    @endphp
    <tr>
        <td><span class="cell-order">{{ $order->code }}</span></td>
        <td>
            <span class="cell-table-pill">
                <i class="fas fa-chair"></i> {{ $tableLabel }}
            </span>
        </td>
        <td class="cell-time">{{ $started?->format('d M Y, H:i') ?? '-' }}</td>
        <td class="cell-time">{{ $finished?->format('d M Y, H:i') ?? '-' }}</td>
        <td>
            @if($durationMinutes === null)
                <span class="cell-duration normal"><i class="fas fa-clock"></i> -</span>
            @else
                <span class="cell-duration {{ $durationClass }}"><i class="{{ $durationIcon }}"></i> {{ $durationText }}</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5">
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <strong>Belum ada riwayat pesanan selesai</strong>
                <span>Pesanan yang sudah selesai akan tampil otomatis di tabel ini.</span>
            </div>
        </td>
    </tr>
@endforelse

@forelse($riwayatTransaksi as $trx)
    <tr>
        <td><span class="trx-code">{{ $trx->code }}</span></td>
        <td style="font-size:12px;color:var(--fg-secondary);">{{ optional($trx->sold_at)->format('d M Y, H:i') }}</td>
        <td>
            <div class="branch-cell">
                @if($trx->table_id)
                    <div class="branch-dot" style="background:linear-gradient(135deg, var(--accent), #F59E0B);"><i class="fas fa-chair"></i></div>
                    <span style="font-weight:600;font-size:13px;">Meja {{ $trx->table->number }}</span>
                @else
                    <div class="branch-dot" style="background:linear-gradient(135deg, #2563EB, #60A5FA);"><i class="fas fa-bag-shopping"></i></div>
                    <span style="font-weight:600;font-size:13px;">Bungkus</span>
                @endif
            </div>
        </td>
        <td>
            @php
                $status = $hasStatus ? $trx->status : 'paid';
                $tagClass = 'tag-paid';
                $icon = 'fa-check-circle';
                $label = 'Lunas';
                
                if ($status === 'cancelled' || $status === 'void') {
                    $tagClass = 'tag-cancelled';
                    $icon = 'fa-xmark-circle';
                    $label = 'Batal';
                } elseif ($status === 'pending') {
                    $tagClass = 'tag-pending';
                    $icon = 'fa-clock';
                    $label = 'Pending';
                }
            @endphp
            <span class="tag {{ $tagClass }}"><i class="fas {{ $icon }}" style="font-size:9px;"></i> {{ $label }}</span>
        </td>
        <td class="amount-cell" style="text-align:right; {{ $status === 'cancelled' ? 'color:var(--muted); text-decoration:line-through;' : '' }}">
            Rp {{ number_format((float) $trx->total_amount, 0, ',', '.') }}
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="empty-state">Belum ada riwayat transaksi.</td>
    </tr>
@endforelse

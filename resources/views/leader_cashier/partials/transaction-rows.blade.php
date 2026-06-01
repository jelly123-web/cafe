@forelse($riwayatTransaksi as $trx)
    <tr>
        <td>{{ $trx->code }}</td>
        <td>{{ optional($trx->sold_at)->format('d M Y H:i') }}</td>
        <td>{{ $trx->table?->number ? 'Meja ' . $trx->table->number : '-' }}</td>
        <td>{{ $hasStatus ? $trx->statusLabel() : '-' }}</td>
        <td>Rp {{ number_format((float) $trx->total_amount, 0, ',', '.') }}</td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="empty-state">Belum ada riwayat transaksi.</td>
    </tr>
@endforelse

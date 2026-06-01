<table>
    <tr>
        <td colspan="6">{{ $period_label }}</td>
    </tr>
    <tr>
        <td colspan="6">{{ $date_from->format('d M Y') }} - {{ $date_to->format('d M Y') }}</td>
    </tr>
    <tr>
        <td colspan="6"></td>
    </tr>
    <tr>
        <td><strong>Transaksi</strong></td>
        <td><strong>{{ $transaction_count }}</strong></td>
        <td><strong>Penjualan</strong></td>
        <td><strong>{{ $total_sales }}</strong></td>
        <td><strong>Modal</strong></td>
        <td><strong>{{ $total_cost }}</strong></td>
    </tr>
    <tr>
        <td colspan="6"></td>
    </tr>
    <tr>
        <th>Kode</th>
        <th>Cabang</th>
        <th>Tanggal</th>
        <th>Total Penjualan</th>
        <th>Total Modal</th>
        <th>Laba / Rugi</th>
    </tr>
    @forelse ($transactions as $row)
        <tr>
            <td>{{ $row['code'] }}</td>
            <td>{{ $row['branch_name'] }}</td>
            <td>{{ $row['sold_at']->format('d M Y H:i') }}</td>
            <td>{{ $row['total_amount'] }}</td>
            <td>{{ $row['total_cost'] }}</td>
            <td>{{ $row['profit_loss'] }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6">Belum ada transaksi pada periode ini.</td>
        </tr>
    @endforelse
</table>

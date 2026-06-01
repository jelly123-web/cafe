<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
        }
        h1, h2, p {
            margin: 0 0 8px;
        }
        .meta {
            margin-bottom: 18px;
            color: #4b5563;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background: #f3f4f6;
            font-size: 11px;
            text-transform: uppercase;
        }
        .amount {
            white-space: nowrap;
        }
        .profit {
            color: #15803d;
        }
        .loss {
            color: #b91c1c;
        }
        .summary {
            margin: 14px 0 18px;
        }
        .summary td {
            width: 25%;
        }
    </style>
</head>
<body>
    <h1>{{ $period_label }}</h1>
    <p class="meta">{{ $date_from->format('d M Y') }} - {{ $date_to->format('d M Y') }}</p>

    <table class="summary">
        <tr>
            <td><strong>Transaksi</strong><br>{{ $transaction_count }}</td>
            <td><strong>Penjualan</strong><br>Rp {{ number_format($total_sales, 0, ',', '.') }}</td>
            <td><strong>Modal</strong><br>Rp {{ number_format($total_cost, 0, ',', '.') }}</td>
            <td><strong>Laba / Rugi</strong><br>Rp {{ number_format(abs($profit_loss), 0, ',', '.') }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Cabang</th>
                <th>Tanggal</th>
                <th>Total Penjualan</th>
                <th>Total Modal</th>
                <th>Laba / Rugi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $row)
                <tr>
                    <td>{{ $row['code'] }}</td>
                    <td>{{ $row['branch_name'] }}</td>
                    <td>{{ $row['sold_at']->format('d M Y H:i') }}</td>
                    <td class="amount">Rp {{ number_format($row['total_amount'], 0, ',', '.') }}</td>
                    <td class="amount">Rp {{ number_format($row['total_cost'], 0, ',', '.') }}</td>
                    <td class="{{ $row['profit_loss'] >= 0 ? 'profit' : 'loss' }} amount">
                        Rp {{ number_format(abs($row['profit_loss']), 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Belum ada transaksi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

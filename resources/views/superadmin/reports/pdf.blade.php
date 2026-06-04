<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
        }
        h1, h2, p {
            margin: 0 0 5px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #795548;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .meta {
            margin-bottom: 20px;
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
            background: #fdfaf8;
            font-size: 10px;
            text-transform: uppercase;
            color: #795548;
        }
        .amount {
            white-space: nowrap;
            text-align: right;
        }
        .profit {
            color: #15803d;
            font-weight: bold;
        }
        .loss {
            color: #b91c1c;
            font-weight: bold;
        }
        .summary {
            margin-bottom: 25px;
        }
        .summary td {
            background: #fffaf5;
            padding: 12px;
        }
        .summary label {
            display: block;
            font-size: 9px;
            text-transform: uppercase;
            color: #a1887f;
            margin-bottom: 4px;
        }
        .summary .val {
            font-size: 16px;
            font-weight: bold;
            color: #795548;
        }
        .status-paid { color: #15803d; }
        .status-cancel { color: #b91c1c; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN KEUANGAN CAFE</h1>
        <p>{{ $period_label }}</p>
        <p style="font-size: 12px; color: #a1887f;">{{ $date_from->format('d M Y') }} - {{ $date_to->format('d M Y') }}</p>
    </div>

    <table class="summary">
        <tr>
            <td>
                <label>Total Transaksi</label>
                <div class="val">{{ $transaction_count }}</div>
            </td>
            <td>
                <label>Total Penjualan</label>
                <div class="val">Rp {{ number_format($total_sales, 0, ',', '.') }}</div>
            </td>
            <td>
                <label>Total Modal</label>
                <div class="val">Rp {{ number_format($total_cost, 0, ',', '.') }}</div>
            </td>
            <td>
                <label>Laba / Rugi Bersih</label>
                <div class="val {{ $profit_loss >= 0 ? 'profit' : 'loss' }}">
                    Rp {{ number_format(abs($profit_loss), 0, ',', '.') }}
                </div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Kode TRX</th>
                <th>Cabang</th>
                <th>Waktu Transaksi</th>
                <th>Penjualan</th>
                <th>Modal</th>
                <th>Laba / Rugi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $row)
                <tr>
                    <td style="font-family: monospace; font-weight: bold;">{{ $row['code'] }}</td>
                    <td>{{ $row['branch_name'] }}</td>
                    <td>{{ $row['sold_at']?->format('d M Y H:i') }}</td>
                    <td class="amount">Rp {{ number_format($row['total_amount'], 0, ',', '.') }}</td>
                    <td class="amount">Rp {{ number_format($row['total_cost'], 0, ',', '.') }}</td>
                    <td class="amount {{ $row['profit_loss'] >= 0 ? 'profit' : 'loss' }}">
                        Rp {{ number_format(abs($row['profit_loss']), 0, ',', '.') }}
                    </td>
                    <td style="text-align: center;">
                        @if($row['status'] === \App\Models\SaleTransaction::STATUS_PAID)
                            <span class="status-paid">Lunas</span>
                        @elseif($row['status'] === \App\Models\SaleTransaction::STATUS_CANCELLED)
                            <span class="status-cancel">Batal</span>
                        @else
                            <span>Pending</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px; color: #a1887f;">Belum ada transaksi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right; color: #a1887f; font-size: 9px;">
        Dicetak pada: {{ now()->format('d M Y H:i:s') }}
    </div>
</body>
</html>

@extends('superadmin.layout')

@section('title', 'Laporan Superadmin')
@section('kicker', 'Laporan Operasional')
@section('page_title', 'Laporan Harian, Mingguan, dan Bulanan')
@section('page_description', 'Ringkasan transaksi, laba rugi, dan ekspor PDF/Excel untuk akun superadmin.')

@push('head')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap');

        :root {
            --bg-main: #f9f5f0;
            --bg-card: #ffffff;
            --primary: #795548;
            --secondary: #bcaaa4;
            --accent: #d7ccc8;
            --highlight: #d4a373;
            --text-main: #6d4c41;
            --text-muted: #a1887f;
            --profit: #558b2f;
            --loss: #e57373;
            --shadow: rgba(121, 85, 72, 0.08);
        }

        .app-shell {
            background-color: var(--bg-main);
        }

        .sidebar {
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(121, 85, 72, 0.08);
        }

        .sidebar-brand h2,
        .page-header h1,
        .report-toolbar h2,
        .report-table-card h2,
        .summary-card strong {
            font-family: 'Playfair Display', Georgia, serif;
        }

        .sidebar-brand h2 {
            color: var(--primary);
        }

        .sidebar-brand p,
        .page-header p,
        .report-toolbar > div p,
        .report-table-card p,
        .summary-card small {
            color: var(--text-muted);
        }

        .nav-item {
            box-shadow: 0 4px 15px var(--shadow);
        }

        .nav-item.active,
        .nav-item:hover {
            border-color: rgba(212, 163, 115, 0.35);
            background: #fffaf5;
        }

        .logout {
            background-color: var(--highlight);
            box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3);
        }

        .logout:hover {
            background-color: #c68b59;
        }

        .main-panel {
            padding: 2rem 2.5rem;
            background-color: var(--bg-main);
            color: var(--text-main);
            line-height: 1.6;
            background-image: radial-gradient(var(--accent) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-kicker {
            display: inline-flex;
            align-items: center;
            background-color: var(--highlight);
            color: #fff;
            font-size: 0.75rem;
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .page-header h1 {
            color: var(--primary);
            font-size: 1.8rem;
            margin: 0.5rem 0 0.25rem;
        }

        .report-wrap {
            display: grid;
            gap: 1.5rem;
        }

        .report-toolbar,
        .summary-grid,
        .report-table-card {
            background: var(--bg-card);
            border: 1px solid var(--accent);
            border-radius: 20px;
            box-shadow: 0 4px 15px var(--shadow);
        }

        .report-toolbar {
            padding: 1.5rem 2rem;
            display: grid;
            gap: 1.25rem;
        }

        .report-toolbar h2 {
            color: var(--primary);
            font-size: 1.2rem;
            margin: 0;
        }

        .period-switch {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .period-switch a,
        .actions a,
        .actions button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            border: 1px solid transparent;
            padding: 0.6rem 1.2rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            font-size: 0.9rem;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        .period-switch a {
            background: #efebe9;
            color: var(--primary);
        }

        .period-switch a:hover {
            background: var(--accent);
        }

        .period-switch a.active {
            background: var(--highlight);
            color: #fff;
            box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3);
        }

        .filter-form {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
            align-items: end;
        }

        .field label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.4rem;
        }

        .field input[type="date"] {
            width: 100%;
            border: 1px solid var(--accent);
            border-radius: 12px;
            padding: 0.6rem 1rem;
            font: inherit;
            color: var(--text-main);
            background: var(--bg-card);
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .field input:focus {
            border-color: var(--highlight);
            box-shadow: 0 0 0 3px rgba(212, 163, 115, 0.15);
        }

        .actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .actions .primary {
            background: var(--highlight);
            color: #fff;
            border: none;
            box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3);
        }

        .actions .primary:hover {
            background-color: #c68b59;
            transform: translateY(-2px);
        }

        .actions .secondary {
            background: transparent;
            color: var(--primary);
            border: 1px solid var(--accent);
        }

        .actions .secondary:hover {
            border-color: var(--highlight);
            color: var(--highlight);
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            padding: 1.5rem 2rem;
        }

        .summary-card h3 {
            margin: 0 0 0.5rem;
            font-size: 0.85rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-card strong {
            display: block;
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 0.25rem;
        }

        .report-table-card {
            padding: 1.5rem 2rem;
            overflow: hidden;
        }

        .report-table-card > div:first-child {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1.25rem;
        }

        .report-table-card h2 {
            color: var(--primary);
            font-size: 1.2rem;
            margin: 0;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
        }

        .report-table th,
        .report-table td {
            padding: 0.85rem 0.75rem;
            border-bottom: 1px solid var(--accent);
            vertical-align: middle;
            text-align: left;
        }

        .report-table th {
            background: var(--bg-main);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .report-table tbody tr:hover {
            background-color: #fffaf5;
        }

        .amount {
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }

        .profit {
            color: var(--profit) !important;
            font-weight: 700;
        }

        .loss {
            color: var(--loss) !important;
            font-weight: 700;
        }

        .empty-state {
            padding: 2rem;
            text-align: center;
            color: var(--text-muted);
            font-style: italic;
        }

        @media (max-width: 1024px) {
            .summary-grid,
            .filter-form {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 900px) {
            .sidebar {
                border-right: 0;
                border-bottom: 1px solid rgba(121, 85, 72, 0.08);
            }
        }

        @media (max-width: 768px) {
            .main-panel {
                padding: 1.5rem 1rem;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }

            .report-toolbar,
            .report-table-card {
                padding: 1.25rem;
            }
        }

        @media (max-width: 640px) {
            .summary-grid,
            .filter-form {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="report-wrap">
        <section class="report-toolbar">
            <div>
                <h2>Pilih Periode</h2>
                <p>Gunakan preset harian, mingguan, bulanan, atau tentukan rentang khusus.</p>
            </div>

            <div class="period-switch">
                <a class="{{ $period === 'daily' ? 'active' : '' }}" href="{{ route('superadmin.reports.index', ['period' => 'daily']) }}">Harian</a>
                <a class="{{ $period === 'weekly' ? 'active' : '' }}" href="{{ route('superadmin.reports.index', ['period' => 'weekly']) }}">Mingguan</a>
                <a class="{{ $period === 'monthly' ? 'active' : '' }}" href="{{ route('superadmin.reports.index', ['period' => 'monthly']) }}">Bulanan</a>
            </div>

            <form method="GET" action="{{ route('superadmin.reports.index') }}" class="filter-form">
                <input type="hidden" name="period" value="custom">

                <div class="field">
                    <label for="date_from">Tanggal Mulai</label>
                    <input id="date_from" type="date" name="date_from" value="{{ optional($date_from)->format('Y-m-d') }}">
                </div>

                <div class="field">
                    <label for="date_to">Tanggal Akhir</label>
                    <input id="date_to" type="date" name="date_to" value="{{ optional($date_to)->format('Y-m-d') }}">
                </div>

                <div class="actions">
                    <button type="submit" class="primary">Terapkan Filter</button>
                    <a class="secondary" href="{{ route('superadmin.reports.pdf', request()->query()) }}">Export PDF</a>
                    <a class="secondary" href="{{ route('superadmin.reports.excel', request()->query()) }}">Export Excel</a>
                </div>
            </form>
        </section>

        <section class="summary-grid">
            <article class="summary-card">
                <h3>Periode</h3>
                <strong>{{ $period_label }}</strong>
                <small>{{ $date_from->format('d M Y') }} - {{ $date_to->format('d M Y') }}</small>
            </article>
            <article class="summary-card">
                <h3>Total Transaksi</h3>
                <strong>{{ number_format($transaction_count, 0, ',', '.') }}</strong>
                <small>Transaksi penjualan</small>
            </article>
            <article class="summary-card">
                <h3>Total Penjualan</h3>
                <strong class="amount">Rp {{ number_format($total_sales, 0, ',', '.') }}</strong>
                <small>Nilai pendapatan</small>
            </article>
            <article class="summary-card">
                <h3>Laba / Rugi</h3>
                <strong class="{{ $profit_loss >= 0 ? 'profit' : 'loss' }}">Rp {{ number_format(abs($profit_loss), 0, ',', '.') }}</strong>
                <small>{{ $profit_loss >= 0 ? 'Laba bersih' : 'Rugi bersih' }}</small>
            </article>
        </section>

        <section class="report-table-card">
            <div>
                <div>
                    <h2>Detail Transaksi</h2>
                    <p>Daftar transaksi yang masuk pada rentang laporan ini.</p>
                </div>
                <div class="actions">
                    <a class="secondary" href="{{ route('superadmin.dashboard') }}">Kembali ke Dashboard</a>
                </div>
            </div>

            @if ($transactions->isEmpty())
                <div class="empty-state">Belum ada transaksi pada periode ini.</div>
            @else
                <table class="report-table">
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
                        @foreach ($transactions as $row)
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
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    </div>
@endsection

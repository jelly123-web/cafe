@extends('superadmin.layout')

@section('title', 'Laporan Superadmin')
@section('kicker', 'Laporan Operasional')
@section('page_title', 'Laporan Harian, Mingguan, dan Bulanan')
@section('page_description', 'Ringkasan transaksi, laba rugi, dan ekspor PDF/Excel untuk akun superadmin.')

@push('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: none;
            border-right: 1px solid rgba(121, 85, 72, 0.08);
            box-shadow: 4px 0 18px rgba(121, 85, 72, 0.04);
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
            background-color: #fbf8f4;
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
        .report-table-card {
            background: var(--bg-card);
            border: 1px solid var(--accent);
            border-radius: 20px;
            box-shadow: 0 4px 15px var(--shadow);
        }

        body.sidebar-open .main-panel {
            filter: none;
        }

        .report-toolbar {
            padding: 2rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .report-toolbar-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .report-toolbar h2 {
            color: var(--primary);
            font-size: 1.4rem;
            margin: 0 0 0.5rem;
            font-family: 'Playfair Display', serif;
        }

        .period-switch {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 0.5rem;
            background: #f3eee9;
            padding: 0.4rem;
            border-radius: 14px;
            width: min(100%, 620px);
        }

        .period-switch a {
            min-height: 46px;
            padding: 0.5rem 0.75rem;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            box-sizing: border-box;
            white-space: nowrap;
        }

        .period-switch a:hover {
            color: var(--primary);
            background: rgba(255, 255, 255, 0.5);
        }

        .period-switch a.active {
            background: #fff;
            color: var(--primary);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .filter-form {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 1.25rem;
            align-items: flex-end;
            border-top: 1px solid var(--accent);
            padding-top: 1.5rem;
        }

        .field label {
            display: block;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .field input[type="date"] {
            width: 100%;
            border: 1px solid var(--accent);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-family: inherit;
            font-size: 0.95rem;
            color: var(--text-main);
            background: #fff;
            outline: none;
            transition: all 0.2s ease;
        }

        .field input:focus {
            border-color: var(--highlight);
            box-shadow: 0 0 0 4px rgba(212, 163, 115, 0.1);
        }

        .actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .actions button, .actions a {
            height: 46px;
            padding: 0 1.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.9rem;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .actions .primary {
            background: var(--highlight);
            color: #fff;
            border: none;
            box-shadow: 0 4px 12px rgba(212, 163, 115, 0.2);
        }

        .actions .primary:hover {
            background: #c68b59;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(212, 163, 115, 0.3);
        }

        .actions .secondary {
            background: #fff;
            color: var(--text-main);
            border: 1px solid var(--accent);
        }

        .actions .secondary:hover {
            border-color: var(--highlight);
            color: var(--highlight);
            background: #fdfbf9;
        }

        .btn-delete {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-delete:hover {
            background: #c62828;
            color: #fff;
            border-color: #c62828;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .summary-card {
            background: var(--bg-card);
            border: 1px solid var(--accent);
            border-radius: 18px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px var(--shadow);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 140px;
            transition: transform 0.2s ease;
        }

        .summary-card:hover {
            transform: translateY(-3px);
            border-color: var(--highlight);
        }

        .summary-card h3 {
            margin: 0 0 0.75rem;
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }

        .summary-card strong {
            display: block;
            font-size: 1.4rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
            font-family: 'Playfair Display', serif;
        }

        .summary-card small {
            color: var(--text-muted);
            font-size: 0.8rem;
            margin-top: auto;
        }

        .summary-card .amount {
            font-size: 1.5rem;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .chart-card {
            background: var(--bg-card);
            border: 1px solid var(--accent);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px var(--shadow);
        }

        .chart-card h3 {
            font-family: 'Playfair Display', serif;
            color: var(--primary);
            font-size: 1.2rem;
            margin-bottom: 1.25rem;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        @media (max-width: 1024px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        .report-table-card {
            padding: 1.5rem 2rem 1.25rem;
            overflow: hidden;
        }

        .report-table-card > div:first-child {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .report-table-card h2 {
            color: var(--primary);
            font-size: 1.2rem;
            margin: 0;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
            table-layout: fixed;
        }

        .report-table th,
        .report-table td {
            padding: 1.25rem 1rem;
            border-bottom: 1px solid var(--accent);
            vertical-align: middle;
            text-align: left;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .report-table th {
            background: #fdfaf7;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            font-weight: 800;
            border-top: 1px solid var(--accent);
        }

        .report-table th:first-child { border-top-left-radius: 12px; }
        .report-table th:last-child { border-top-right-radius: 12px; }

        .report-table tbody tr:hover {
            background-color: #fdfbf9;
        }

        /* Column widths */
        .col-code { width: 120px; }
        .col-branch { width: 150px; }
        .col-date { width: 180px; }
        .col-amount { width: 160px; }
        .col-cost { width: 160px; }
        .col-profit { width: 160px; }

        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }

        .amount {
            font-variant-numeric: tabular-nums;
            font-weight: 600;
        }

        .profit-text { color: #43a047 !important; font-weight: 700; }
        .loss-text { color: #e53935 !important; font-weight: 700; }

        .badge-profit, .badge-loss {
            display: inline-flex;
            align-items: center;
            padding: 0.4rem 0.9rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge-profit {
            background-color: #ecf3e6;
            color: #43a047 !important;
        }

        .badge-loss.loss {
            background-color: #fff0f0;
            color: #e53935 !important;
        }

        .tag { display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; }
        .tag-paid { background: #E8F5E9; color: #558B2F; }
        .tag-pending { background: #FFF3E0; color: #E65100; }
        .tag-cancelled { background: #FFEBEE; color: #C62828; }

        .empty-state {
            padding: 2rem;
            text-align: center;
            color: var(--text-muted);
            font-style: italic;
        }

        .report-table-wrap {
            overflow-x: auto;
            border-radius: 14px;
        }

        @media (max-width: 1200px) {
            .summary-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 1024px) {
            .summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .filter-form {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .filter-form .actions {
                grid-column: 1 / -1;
                justify-content: flex-end;
            }

            .actions a,
            .actions button {
                min-width: 160px;
            }
        }

        @media (max-width: 900px) {
            .sidebar {
                border-right: 0;
                border-bottom: 1px solid rgba(121, 85, 72, 0.08);
                box-shadow: none;
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

            .period-switch {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                width: 100%;
            }

            .summary-card {
                min-height: 112px;
            }

            .report-table-card > div:first-child {
                align-items: flex-start;
            }
        }

        @media (max-width: 640px) {
            .period-switch {
                gap: 0.5rem;
            }

            .summary-grid,
            .filter-form {
                grid-template-columns: 1fr;
            }

            .filter-form .actions {
                justify-content: stretch;
            }

            .actions a,
            .actions button {
                width: 100%;
                min-width: 0;
            }

            .summary-grid {
                padding: 1.25rem;
            }

            .report-table-card {
                padding: 1.25rem 1rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="report-wrap">
        <section class="report-toolbar">
            <div class="report-toolbar-header">
                <div>
                    <h2>Pilih Periode</h2>
                    <p>Gunakan preset harian, mingguan, bulanan, atau tentukan rentang khusus.</p>
                </div>

                <div class="period-switch">
                    <a class="{{ $period === 'daily' ? 'active' : '' }}" href="{{ route('superadmin.reports.index', ['period' => 'daily']) }}">Harian</a>
                    <a class="{{ $period === 'weekly' ? 'active' : '' }}" href="{{ route('superadmin.reports.index', ['period' => 'weekly']) }}">Mingguan</a>
                    <a class="{{ $period === 'monthly' ? 'active' : '' }}" href="{{ route('superadmin.reports.index', ['period' => 'monthly']) }}">Bulanan</a>
                    <a class="{{ $period === 'yearly' ? 'active' : '' }}" href="{{ route('superadmin.reports.index', ['period' => 'yearly']) }}">Tahunan</a>
                    <a class="{{ $period === 'custom' ? 'active' : '' }}" href="{{ route('superadmin.reports.index', ['period' => 'custom']) }}">Kustom</a>
                </div>
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
                <strong id="sumPeriodLabel">{{ $period_label }}</strong>
                <small id="sumPeriodRange">{{ $date_from->format('d M Y') }} - {{ $date_to->format('d M Y') }}</small>
            </article>
            <article class="summary-card">
                <h3>Total Transaksi</h3>
                <strong id="sumTxnCount">{{ number_format($transaction_count, 0, ',', '.') }}</strong>
                <small>Transaksi penjualan</small>
            </article>
            <article class="summary-card">
                <h3>Total Penjualan</h3>
                <strong class="amount" id="sumSales">Rp {{ number_format($total_sales, 0, ',', '.') }}</strong>
                <small>Nilai pendapatan</small>
            </article>
            <article class="summary-card">
                <h3>Biaya Bahan (HPP)</h3>
                <strong class="amount" id="sumCost">Rp {{ number_format($total_cost, 0, ',', '.') }}</strong>
                <small>Total modal bahan terjual</small>
            </article>
            <article class="summary-card">
                <h3>Pengeluaran Gaji</h3>
                <strong class="amount" id="sumPayroll">Rp {{ number_format($total_payroll, 0, ',', '.') }}</strong>
                <small>Total payroll dibayar</small>
            </article>
            <article class="summary-card">
                <h3>Kas Masuk / Keluar</h3>
                <strong class="amount" id="sumCashFlow">Rp {{ number_format($total_cash_in - $total_cash_out, 0, ',', '.') }}</strong>
                <small>In: Rp {{ number_format($total_cash_in, 0, ',', '.') }} | Out: Rp {{ number_format($total_cash_out, 0, ',', '.') }}</small>
            </article>
            <article class="summary-card">
                <h3>Laba Bersih</h3>
                <strong id="sumProfit" class="{{ $profit_loss >= 0 ? 'profit-text' : 'loss-text' }}">Rp {{ number_format(abs($profit_loss), 0, ',', '.') }}</strong>
                <small>{{ $profit_loss >= 0 ? 'Setelah biaya bahan + gaji' : 'Setelah biaya bahan + gaji' }}</small>
            </article>
        </section>

        @if(!empty($chart_data))
        <section class="charts-grid">
            <article class="chart-card">
                <h3>Trend Penjualan</h3>
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
            </article>
            <article class="chart-card">
                <h3>Proporsi Keuangan</h3>
                <div class="chart-container">
                    <canvas id="profitChart"></canvas>
                </div>
            </article>
        </section>
        @endif

        <section class="report-table-card">
            <div>
                <div>
                    <h2>Detail Transaksi</h2>
                    <p>Daftar transaksi yang masuk pada rentang laporan ini.</p>
                </div>
                <div class="actions">
                    <form action="{{ route('superadmin.reports.destroy-all') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus SEMUA transaksi pada periode ini?')">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="date_from" value="{{ $date_from->format('Y-m-d') }}">
                        <input type="hidden" name="date_to" value="{{ $date_to->format('Y-m-d') }}">
                        <button type="submit" class="btn-delete" style="padding: 0.6rem 1.5rem; font-size: 0.9rem; min-width: auto;">Hapus</button>
                    </form>
                </div>
            </div>

            <div id="reportTableContainer">
                @if ($transactions->isEmpty())
                    <div class="empty-state">Belum ada transaksi pada periode ini.</div>
                @else
                    <div class="report-table-wrap">
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th class="col-code">Kode</th>
                                    <th class="col-branch">Cabang</th>
                                    <th class="col-date">Tanggal</th>
                                    <th>Status</th>
                                    <th class="col-amount text-right">Total Penjualan</th>
                                    <th class="col-cost text-right">Total Modal</th>
                                    <th class="col-profit text-center">Laba / Rugi</th>
                                </tr>
                            </thead>
                            <tbody id="reportTableBody">
                                @foreach ($transactions as $row)
                                    <tr>
                                        <td>{{ $row['code'] }}</td>
                                        <td>{{ $row['branch_name'] }}</td>
                                        <td>{{ $row['sold_at']->format('d M Y H:i') }}</td>
                                        <td>
                                            <span class="tag {{ $row['status'] === \App\Models\SaleTransaction::STATUS_PAID ? 'tag-paid' : ($row['status'] === \App\Models\SaleTransaction::STATUS_CANCELLED ? 'tag-cancelled' : 'tag-pending') }}">
                                                {{ $row['status'] === \App\Models\SaleTransaction::STATUS_PAID ? 'Lunas' : ($row['status'] === \App\Models\SaleTransaction::STATUS_CANCELLED ? 'Batal' : 'Pending') }}
                                            </span>
                                        </td>
                                        <td class="amount text-right">Rp {{ number_format($row['total_amount'], 0, ',', '.') }}</td>
                                        <td class="amount text-right">Rp {{ number_format($row['total_cost'], 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <span class="{{ $row['profit_loss'] >= 0 ? 'badge-profit' : 'badge-loss' }} amount">
                                                Rp {{ number_format(abs($row['profit_loss']), 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="pagination-area" style="margin-top: 1.5rem;">
                        {{ $paginator->links('components.pagination') }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    @if(!empty($chart_data))
    <script>
        (function() {
            const chartData = @json($chart_data);
            const ctxSales = document.getElementById('salesChart').getContext('2d');
            
            new Chart(ctxSales, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Penjualan (Rp)',
                        data: chartData.values,
                        backgroundColor: 'rgba(212, 163, 115, 0.6)',
                        borderColor: 'rgba(212, 163, 115, 1)',
                        borderWidth: 2,
                        borderRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });

            const ctxProfit = document.getElementById('profitChart').getContext('2d');
            const totalSales = {{ $total_sales }};
            const totalCost = {{ $total_cost }};
            const totalPayroll = {{ $total_payroll }};
            const cashIn = {{ $total_cash_in }};
            const cashOut = {{ $total_cash_out }};
            const netProfit = {{ $profit_loss }};

            new Chart(ctxProfit, {
                type: 'doughnut',
                data: {
                    labels: ['HPP (Modal)', 'Gaji Karyawan', 'Biaya Lainnya', 'Laba Bersih'],
                    datasets: [{
                        data: [
                            totalCost, 
                            totalPayroll, 
                            Math.max(0, cashOut - cashIn), 
                            Math.max(0, netProfit)
                        ],
                        backgroundColor: [
                            '#d7ccc8',
                            '#bcaaa4',
                            '#e57373',
                            '#558b2f'
                        ],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: { size: 11 }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        })();
    </script>
    @endif

    <script>
        (function () {
            const liveUrl = @json(route('superadmin.reports.live'));
            const indexUrl = @json(route('superadmin.reports.index'));
            const destroyUrlBase = @json(route('superadmin.reports.destroy', ['transaction' => 'ID_PLACEHOLDER']));
            const csrfToken = @json(csrf_token());
            const params = new URLSearchParams(window.location.search);
            const esc = (v) => String(v ?? '').replace(/[&<>"']/g, (m) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[m]));

            const sumPeriodLabel = document.getElementById('sumPeriodLabel');
            const sumPeriodRange = document.getElementById('sumPeriodRange');
            const sumTxnCount = document.getElementById('sumTxnCount');
            const sumSales = document.getElementById('sumSales');
            const sumCost = document.getElementById('sumCost');
            const sumPayroll = document.getElementById('sumPayroll');
            const sumCashFlow = document.getElementById('sumCashFlow');
            const sumProfit = document.getElementById('sumProfit');
            const tableContainer = document.getElementById('reportTableContainer');

            // Handle pagination clicks
            tableContainer.addEventListener('click', (e) => {
                const link = e.target.closest('.pagination-link');
                if (link && link.href) {
                    e.preventDefault();
                    const url = new URL(link.href);
                    const newPage = url.searchParams.get('page');
                    if (newPage) {
                        const params = new URLSearchParams(window.location.search);
                        params.set('page', newPage);
                        // Update URL without reloading
                        const newUrl = window.location.pathname + '?' + params.toString();
                        window.history.pushState({ path: newUrl }, '', newUrl);
                        refreshLive().catch(() => {});
                    }
                }
            });

            const renderRows = (rows, paginationHtml = '') => {
                if (!rows.length) {
                    tableContainer.innerHTML = '<div class="empty-state">Belum ada transaksi pada periode ini.</div>';
                    return;
                }
                const bodyHtml = rows.map((row) => {
                    const statusClass = row.status === 'paid' ? 'tag-paid' : (row.status === 'cancelled' ? 'tag-cancelled' : 'tag-pending');
                    return `
                    <tr>
                        <td>${esc(row.code)}</td>
                        <td>${esc(row.branch_name)}</td>
                        <td>${esc(row.sold_at)}</td>
                        <td><span class="tag ${statusClass}">${esc(row.status_label)}</span></td>
                        <td class="amount text-right">${esc(row.total_amount)}</td>
                        <td class="amount text-right">${esc(row.total_cost)}</td>
                        <td class="text-center">
                            <span class="${row.profit_class === 'profit' ? 'badge-profit' : 'badge-loss'} amount">
                                ${esc(row.profit_loss)}
                            </span>
                        </td>
                    </tr>
                `; }).join('');
                tableContainer.innerHTML = `
                    <div class="report-table-wrap">
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th class="col-code">Kode</th>
                                    <th class="col-branch">Cabang</th>
                                    <th class="col-date">Tanggal</th>
                                    <th>Status</th>
                                    <th class="col-amount text-right">Total Penjualan</th>
                                    <th class="col-cost text-right">Total Modal</th>
                                    <th class="col-profit text-center">Laba / Rugi</th>
                                </tr>
                            </thead>
                            <tbody>${bodyHtml}</tbody>
                        </table>
                    </div>
                    ${paginationHtml ? `<div class="pagination-area" style="margin-top: 1.5rem;">${paginationHtml}</div>` : ''}
                `;
            };

            const renderLivePage = async (pageHref) => {
                const res = await fetch(pageHref, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                if (!res.ok) return;
                const data = await res.json();
                if (!data || !data.summary) return;

                const nextUrl = new URL(pageHref);
                nextUrl.pathname = new URL(indexUrl).pathname;
                window.history.replaceState({}, '', nextUrl.toString());

                sumPeriodLabel.textContent = data.summary.period_label ?? '-';
                sumPeriodRange.textContent = data.summary.period_range ?? '-';
                sumTxnCount.textContent = data.summary.transaction_count ?? '0';
                sumSales.textContent = data.summary.total_sales ?? 'Rp 0';
                sumCost.textContent = data.summary.total_cost ?? 'Rp 0';
                sumPayroll.textContent = data.summary.total_payroll ?? 'Rp 0';

                if (sumCashFlow) {
                    const cashIn = parseFloat((data.summary.total_cash_in || '0').replace(/[^0-9.-]+/g, ''));
                    const cashOut = parseFloat((data.summary.total_cash_out || '0').replace(/[^0-9.-]+/g, ''));
                    const net = cashIn - cashOut;
                    sumCashFlow.textContent = 'Rp ' + net.toLocaleString('id-ID');
                    const small = sumCashFlow.nextElementSibling;
                    if (small) {
                        small.textContent = `In: ${data.summary.total_cash_in} | Out: ${data.summary.total_cash_out}`;
                    }
                }

                sumProfit.textContent = data.summary.profit_loss ?? 'Rp 0';
                sumProfit.classList.remove('profit-text', 'loss-text');
                sumProfit.classList.add(data.summary.profit_class === 'profit' ? 'profit-text' : 'loss-text');

                renderRows(Array.isArray(data.rows) ? data.rows : [], data.pagination ?? '');
            };

            const refreshLive = async () => {
                const params = new URLSearchParams(window.location.search);
                const url = params.toString() ? `${liveUrl}?${params.toString()}` : liveUrl;
                const res = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                if (!res.ok) return;
                const data = await res.json();
                if (!data || !data.summary) return;

                sumPeriodLabel.textContent = data.summary.period_label ?? '-';
                sumPeriodRange.textContent = data.summary.period_range ?? '-';
                sumTxnCount.textContent = data.summary.transaction_count ?? '0';
                sumSales.textContent = data.summary.total_sales ?? 'Rp 0';
                sumCost.textContent = data.summary.total_cost ?? 'Rp 0';
                sumPayroll.textContent = data.summary.total_payroll ?? 'Rp 0';
                
                // Update Cash Flow
                if (sumCashFlow) {
                    const cashIn = parseFloat((data.summary.total_cash_in || '0').replace(/[^0-9.-]+/g, ""));
                    const cashOut = parseFloat((data.summary.total_cash_out || '0').replace(/[^0-9.-]+/g, ""));
                    const net = cashIn - cashOut;
                    sumCashFlow.textContent = 'Rp ' + net.toLocaleString('id-ID');
                    const small = sumCashFlow.nextElementSibling;
                    if (small) {
                        small.textContent = `In: ${data.summary.total_cash_in} | Out: ${data.summary.total_cash_out}`;
                    }
                }

                sumProfit.textContent = data.summary.profit_loss ?? 'Rp 0';
                sumProfit.classList.remove('profit-text', 'loss-text');
                sumProfit.classList.add(data.summary.profit_class === 'profit' ? 'profit-text' : 'loss-text');

                renderRows(Array.isArray(data.rows) ? data.rows : [], data.pagination ?? '');
            };

            tableContainer?.addEventListener('click', (event) => {
                const link = event.target.closest('.pagination-area a');
                if (!link) return;
                event.preventDefault();
                renderLivePage(link.href).catch(() => {});
            });

            refreshLive().catch(() => {});
            setInterval(() => {
                refreshLive().catch(() => {});
            }, 10000);
        })();
    </script>
@endpush

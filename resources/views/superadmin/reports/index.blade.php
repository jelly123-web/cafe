@extends('superadmin.layout')

@section('title', ($cafeBrand['name'] ?? config('app.name')) . ' - Laporan Penjualan')

@push('head')
    <style>
        :root {
            --bg-main: #f9f5f0;
            --bg-card: #ffffff;
            --primary: #795548;
            --secondary: #bcaaa4;
            --accent: #d7ccc8;
            --highlight: #d4a373;
            --text-main: #6d4c41;
            --text-muted: #a1887f;
            --profit: #81c784;
            --loss: #e57373;
            --shadow: rgba(121, 85, 72, 0.08);
        }

        .reports-shell { max-width: 100%; }
        .panel { background: var(--bg-card); border: 1px solid var(--accent); border-radius: 20px; padding: 2rem; margin-bottom: 1.5rem; box-shadow: 0 4px 15px var(--shadow); }
        .page-title { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 2rem; margin: 0 0 0.5rem; }
        .page-desc { color: var(--text-muted); font-size: 0.95rem; margin: 0; }

        /* Summary Grid */
        .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 2rem; }
        .summary-card { background: #fffaf5; border: 1px solid var(--accent); padding: 1.5rem; border-radius: 18px; display: flex; flex-direction: column; gap: 0.5rem; box-shadow: 0 2px 8px var(--shadow); }
        .summary-card label { font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .summary-card .value { font-family: 'Playfair Display', Georgia, serif; font-size: 1.5rem; font-weight: 700; color: var(--primary); }
        .summary-card .value.profit { color: #558b2f; }
        .summary-card .value.loss { color: #c62828; }

        /* Filter Toolbar */
        .report-filters { display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap; margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--accent); }
        .filter-field { display: flex; flex-direction: column; gap: 0.4rem; }
        .filter-field label { font-size: 0.85rem; font-weight: 600; color: var(--text-muted); }
        .report-filters input, .report-filters select { padding: 0.65rem 1rem; border: 1px solid var(--accent); border-radius: 12px; background: #fff; font-size: 0.95rem; color: var(--text-main); outline: none; }
        .report-filters input:focus, .report-filters select:focus { border-color: var(--highlight); box-shadow: 0 0 0 3px rgba(212, 163, 115, 0.15); }

        .btn { border: 0; border-radius: 12px; padding: 0.65rem 1.25rem; font-weight: 700; cursor: pointer; font-family: inherit; font-size: 0.9rem; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; gap: 0.5rem; }
        .btn-primary { background: var(--highlight); color: #fff; }
        .btn-primary:hover { background: #c68b59; transform: translateY(-1px); }
        .btn-outline { background: #fff; color: var(--primary); border: 1px solid var(--accent); }
        .btn-outline:hover { background: var(--bg-main); border-color: var(--highlight); }
        .btn-danger { background: transparent; color: var(--loss); border: 1px solid #ffcdd2; }
        .btn-danger:hover { background: #fff0f0; border-color: var(--loss); }

        /* Table Design */
        .table-wrap { overflow-x: auto; border-radius: 16px; border: 1px solid var(--accent); }
        .report-table { width: 100%; border-collapse: collapse; background: #fff; }
        .report-table th, .report-table td { padding: 1.1rem 1rem; text-align: left; border-bottom: 1px dashed var(--accent); font-size: 0.95rem; }
        .report-table th { background: #fdfaf8; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.5px; border-bottom: 2px solid var(--accent); }
        .report-table tbody tr:hover { background: #fffcf9; }
        .report-table .profit { color: #2e7d32; font-weight: 700; }
        .report-table .loss { color: #c62828; font-weight: 700; }
        .trx-code { font-family: 'JetBrains Mono', monospace; font-weight: 700; color: var(--highlight); }

        .pagination-area { margin-top: 1.5rem; }
        
        .section-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; gap: 1rem; flex-wrap: wrap; }
        .section-head h2 { font-family: 'Playfair Display', Georgia, serif; font-size: 1.4rem; color: var(--primary); margin: 0; }

        @media (max-width: 768px) {
            .summary-grid { grid-template-columns: 1fr 1fr; }
            .report-filters { flex-direction: column; align-items: stretch; }
            .btn { width: 100%; }
        }
    </style>
@endpush

@section('content')
    <div class="reports-shell">
        <section class="panel">
            <h1 class="page-title">Laporan Keuangan</h1>
            <p class="page-desc">Monitoring data penjualan, modal, pengeluaran gaji, dan laba bersih cafe.</p>
        </section>

        @if (session('status'))
            <div class="panel" style="color: #2e7d32; padding: 1rem; border-left: 4px solid #2e7d32;">{{ session('status') }}</div>
        @endif

        <div class="summary-grid" id="reportSummary">
            <div class="summary-card">
                <label>Total Transaksi</label>
                <div class="value" id="valTrxCount">{{ number_format($transaction_count, 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <label>Total Penjualan</label>
                <div class="value" id="valTotalSales">Rp {{ number_format($total_sales, 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <label>Total Modal (HPP)</label>
                <div class="value" id="valTotalCost">Rp {{ number_format($total_cost, 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <label>Gaji Karyawan</label>
                <div class="value" id="valTotalPayroll" style="color: #e57373;">Rp {{ number_format($total_payroll, 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <label>Laba / Rugi Bersih</label>
                <div class="value {{ $profit_loss >= 0 ? 'profit' : 'loss' }}" id="valProfitLoss">
                    Rp {{ number_format(abs($profit_loss), 0, ',', '.') }}
                </div>
            </div>
        </div>

        <section class="panel">
            <form method="GET" action="{{ route('superadmin.reports.index') }}" class="report-filters" id="reportFilterForm">
                <div class="filter-field">
                    <label>Periode</label>
                    <select name="period" id="filterPeriod">
                        <option value="daily" @selected($period === 'daily')>Hari Ini</option>
                        <option value="weekly" @selected($period === 'weekly')>Minggu Ini</option>
                        <option value="monthly" @selected($period === 'monthly')>Bulan Ini</option>
                        <option value="yearly" @selected($period === 'yearly')>Tahun Ini</option>
                        <option value="custom" @selected($period === 'custom')>Kustom Tanggal</option>
                    </select>
                </div>
                <div class="filter-field" id="customDateRange" style="{{ $period === 'custom' ? '' : 'display:none;' }}">
                    <label>Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ $date_from->toDateString() }}">
                </div>
                <div class="filter-field" id="customDateRangeEnd" style="{{ $period === 'custom' ? '' : 'display:none;' }}">
                    <label>Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ $date_to->toDateString() }}">
                </div>
                <button type="submit" class="btn btn-primary" id="btnApplyFilter">Tampilkan Data</button>
                <div style="flex:1;"></div>
                <a href="{{ route('superadmin.reports.pdf', request()->query()) }}" class="btn btn-outline" target="_blank">Export PDF</a>
                <a href="{{ route('superadmin.reports.excel', request()->query()) }}" class="btn btn-outline" target="_blank">Export Excel</a>
            </form>

            <div class="section-head">
                <div>
                    <h2 id="reportTitle">{{ $period_label }}</h2>
                    <p style="margin: 0.3rem 0 0; color: var(--text-muted); font-size: 0.9rem;" id="reportSubtitle">
                        {{ $date_from->format('d M Y') }} - {{ $date_to->format('d M Y') }}
                    </p>
                </div>
                <form method="POST" action="{{ route('superadmin.reports.destroy-all') }}" onsubmit="return confirm('Hapus semua transaksi pada periode yang dipilih?')">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="date_from" value="{{ $date_from->toDateString() }}">
                    <input type="hidden" name="date_to" value="{{ $date_to->toDateString() }}">
                    <button type="submit" class="btn btn-danger">Hapus Transaksi Periode Ini</button>
                </form>
            </div>

            <div class="table-wrap">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Kode TRX</th>
                            <th>Cabang</th>
                            <th>Waktu</th>
                            <th>Penjualan</th>
                            <th>Modal</th>
                            <th>Laba / Rugi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="reportRows">
                        @forelse ($transactions as $row)
                            <tr data-id="{{ $row['id'] }}">
                                <td><span class="trx-code">{{ $row['code'] }}</span></td>
                                <td>{{ $row['branch_name'] }}</td>
                                <td>{{ $row['sold_at']?->format('d M Y H:i') }}</td>
                                <td>Rp {{ number_format($row['total_amount'], 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($row['total_cost'], 0, ',', '.') }}</td>
                                <td class="{{ $row['profit_loss'] >= 0 ? 'profit' : 'loss' }}">
                                    Rp {{ number_format(abs($row['profit_loss']), 0, ',', '.') }}
                                </td>
                                <td>
                                    @if($row['status'] === \App\Models\SaleTransaction::STATUS_PAID)
                                        <span style="color:#558b2f; font-weight:700;">Lunas</span>
                                    @elseif($row['status'] === \App\Models\SaleTransaction::STATUS_CANCELLED)
                                        <span style="color:#c62828; font-weight:700;">Batal</span>
                                    @else
                                        <span style="color:#d4a373; font-weight:700;">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('superadmin.reports.destroy', $row['id']) }}" onsubmit="return confirm('Hapus transaksi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="border:none; background:transparent; color:var(--loss); cursor:pointer; font-weight:700;">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center; padding: 3rem 1rem; color: var(--text-muted); font-style: italic;">
                                    Belum ada transaksi pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-area" id="reportPagination">
                {{ $paginator->links('components.pagination') }}
            </div>
        </section>
    </div>

    <script>
        (function () {
            const filterPeriod = document.getElementById('filterPeriod');
            const customDateRange = document.getElementById('customDateRange');
            const customDateRangeEnd = document.getElementById('customDateRangeEnd');
            const filterForm = document.getElementById('reportFilterForm');
            const btnApply = document.getElementById('btnApplyFilter');

            filterPeriod.addEventListener('change', function () {
                const isCustom = this.value === 'custom';
                customDateRange.style.display = isCustom ? 'block' : 'none';
                customDateRangeEnd.style.display = isCustom ? 'block' : 'none';
                if (!isCustom) {
                    btnApply.click();
                }
            });

            // Live Update Logic (Auto-refresh every 10 seconds if on 'daily')
            if (filterPeriod.value === 'daily') {
                setInterval(async () => {
                    if (document.visibilityState !== 'visible') return;
                    
                    try {
                        const url = new URL(@json(route('superadmin.reports.live')), window.location.origin);
                        url.search = new URLSearchParams(new FormData(filterForm)).toString();

                        const res = await fetch(url, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                            credentials: 'same-origin'
                        });
                        
                        if (!res.ok) return;
                        const data = await res.json();
                        
                        // Update Summary
                        document.getElementById('valTrxCount').textContent = data.summary.transaction_count;
                        document.getElementById('valTotalSales').textContent = data.summary.total_sales;
                        document.getElementById('valTotalCost').textContent = data.summary.total_cost;
                        document.getElementById('valTotalPayroll').textContent = data.summary.total_payroll;
                        
                        const plVal = document.getElementById('valProfitLoss');
                        plVal.textContent = data.summary.profit_loss;
                        plVal.className = 'value ' + data.summary.profit_class;

                    } catch (e) { console.error(e); }
                }, 10000);
            }
        })();
    </script>
@endsection

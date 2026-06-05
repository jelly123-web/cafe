@extends('superadmin.layout')

@section('title', 'Laporan Penjualan — cafecaf')
@section('kicker', 'Keuangan')
@section('page_title', 'Laporan Penjualan')
@section('page_description', 'Monitoring data penjualan, modal, pengeluaran gaji, dan laba bersih cafe.')

@push('head')
    <style>
    /* ===== SUMMARY GRID ===== */
    .summary-grid {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 14px;
      margin-bottom: 24px;
    }

    .summary-card {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
      padding: 18px 20px;
      display: flex;
      flex-direction: column;
      gap: 8px;
      transition: all 0.25s ease;
      position: relative;
      overflow: hidden;
    }

    .summary-card::after {
      content: '';
      position: absolute;
      bottom: 0; left: 0; right: 0;
      height: 3px;
      background: var(--card-accent, var(--accent));
      opacity: 0;
      transition: opacity var(--transition);
    }

    .summary-card:hover {
      box-shadow: var(--shadow-md);
      transform: translateY(-2px);
    }

    .summary-card:hover::after { opacity: 1; }

    .summary-card .card-icon {
      width: 38px; height: 38px;
      border-radius: var(--radius-sm);
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; margin-bottom: 2px;
    }

    .summary-card label {
      font-size: 11px; font-weight: 700; color: var(--muted);
      text-transform: uppercase; letter-spacing: 0.5px;
    }

    .summary-card .value {
      font-size: 22px; font-weight: 900; color: var(--fg);
      letter-spacing: -0.5px; line-height: 1.1;
      font-variant-numeric: tabular-nums;
    }

    .summary-card .value.profit { color: var(--green); }
    .summary-card .value.loss { color: var(--red); }
    .summary-card .value.expense { color: var(--fg-secondary); }

    /* ===== PANEL ===== */
    .panel {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      margin-bottom: 20px;
      overflow: hidden;
    }

    .section-head {
      display: flex; justify-content: space-between; align-items: center;
      gap: 12px; padding: 18px 24px;
      border-bottom: 1px solid var(--border-light);
      flex-wrap: wrap;
    }

    .section-head h2 {
      font-size: 15px; font-weight: 800; color: var(--fg);
      letter-spacing: -0.2px; display: flex; align-items: center; gap: 8px;
      margin: 0;
    }

    .section-head h2 i { color: var(--accent); font-size: 16px; }

    .section-meta {
      font-size: 12px; color: var(--muted); margin-top: 2px;
    }

    /* ===== FILTER TOOLBAR ===== */
    .report-filters {
      display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap;
      padding: 20px 24px;
      border-bottom: 1px solid var(--border-light);
    }

    .filter-field { display: flex; flex-direction: column; gap: 5px; }

    .filter-field label {
      font-size: 12px; font-weight: 700; color: var(--fg-secondary);
      text-transform: uppercase; letter-spacing: 0.5px;
    }

    .report-filters select {
      padding: 9px 14px;
      border: 1.5px solid var(--border); border-radius: var(--radius-sm);
      background: var(--white); font-size: 13px; font-weight: 500;
      color: var(--fg); outline: none; transition: all var(--transition);
      font-family: var(--font); -webkit-appearance: none; min-height: 40px;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%239CA3B4' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
      background-repeat: no-repeat; background-position: right 12px center;
      padding-right: 32px;
    }

    .report-filters select:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(217,119,6,0.1);
    }

    /* ===== BUTTONS ===== */
    .btn {
      border: 1px solid transparent; border-radius: var(--radius-sm);
      padding: 9px 18px; cursor: pointer; font-weight: 700;
      font-family: var(--font); font-size: 13px;
      transition: all var(--transition); text-decoration: none;
      display: inline-flex; align-items: center; justify-content: center; gap: 6px;
      white-space: nowrap;
    }

    .btn-primary { background: var(--accent); color: white; border: none; }
    .btn-primary:hover { background: var(--accent-dark); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(217,119,6,0.25); }

    .btn-outline {
      background: var(--white); color: var(--fg-secondary);
      border: 1.5px solid var(--border);
    }
    .btn-outline:hover {
      border-color: var(--accent); color: var(--accent);
      background: var(--accent-light);
    }

    /* ===== TABLE ===== */
    .table-wrap { overflow-x: auto; }

    .report-table { width: 100%; border-collapse: collapse; min-width: 800px; }

    .report-table th, .report-table td {
      padding: 13px 20px; text-align: left;
      border-bottom: 1px solid var(--border-light);
      font-size: 13px; vertical-align: middle;
    }

    .report-table th {
      background: var(--bg); font-size: 11px; font-weight: 700;
      text-transform: uppercase; color: var(--muted);
      letter-spacing: 0.7px; border-bottom: 1px solid var(--border);
    }

    /* ===== TABLE CELL STYLES ===== */
    .trx-code {
      font-family: 'SF Mono', 'Fira Code', 'Courier New', monospace;
      font-weight: 700; color: var(--accent); font-size: 12px;
      background: var(--accent-light); padding: 3px 8px;
      border-radius: 4px; letter-spacing: 0.3px;
    }

    .branch-cell { display: flex; align-items: center; gap: 8px; }

    .branch-dot {
      width: 28px; height: 28px; border-radius: var(--radius-sm);
      display: flex; align-items: center; justify-content: center;
      font-size: 10px; font-weight: 800; color: white; flex-shrink: 0;
    }

    .branch-name { font-weight: 600; font-size: 13px; }

    .amount-cell { font-variant-numeric: tabular-nums; white-space: nowrap; font-weight: 500; }

    .profit { color: var(--green) !important; font-weight: 700 !important; }
    .loss { color: var(--red) !important; font-weight: 700 !important; }

    /* ===== STATUS BADGE ===== */
    .status-badge {
      display: inline-flex; align-items: center; gap: 4px;
      padding: 4px 10px; border-radius: var(--radius-full);
      font-size: 11px; font-weight: 700; letter-spacing: 0.2px;
    }

    .status-badge.paid { background: var(--green-light); color: var(--green); }
    .status-badge.cancelled { background: var(--red-light); color: var(--red); }
    .status-badge.pending { background: var(--accent-light); color: var(--accent-dark); }
    </style>
@endpush

@push('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    /* ... (existing styles) ... */
    .chart-container {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      padding: 24px;
      margin-bottom: 20px;
    }
    </style>
@endpush

@section('content')
    <!-- CHART -->
    <div class="chart-container fade-in">
        <canvas id="salesChart" height="80"></canvas>
    </div>

    <!-- SUMMARY CARDS -->
    <div class="summary-grid fade-in" id="reportSummary">
        <!-- ... (cards remain the same, but with updated IDs for AJAX) ... -->
        <div class="summary-card" style="--card-accent: var(--blue);">
            <div class="card-icon" style="background:var(--blue-light);color:var(--blue);"><i class="fas fa-receipt"></i></div>
            <label>Total Transaksi</label>
            <div class="value" id="valTrxCount">{{ number_format($transaction_count, 0, ',', '.') }}</div>
        </div>
        <!-- ... -->
    </div>

    <!-- MAIN PANEL -->
    <section class="panel fade-in">
        <!-- FILTER TOOLBAR -->
        <form class="report-filters" id="reportFilterForm">
            <div class="filter-field">
                <label>Periode</label>
                <select name="period" id="filterPeriod">
                    <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="weekly" {{ request('period') == 'weekly' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="yearly" {{ request('period') == 'yearly' ? 'selected' : '' }}>Tahun Ini</option>
                </select>
            </div>
            <!-- ... -->
        </form>
        <!-- ... -->
    </section>
@endsection

@push('scripts')
<script>
    let salesChart;
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    function initChart(labels, data) {
        if (salesChart) salesChart.destroy();
        salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Penjualan',
                    data: data,
                    borderColor: '#D97706',
                    backgroundColor: 'rgba(217, 119, 6, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { responsive: true }
        });
    }

    // Initialize with data
    @if(!empty($chart_data['labels']))
        initChart(@json($chart_data['labels']), @json($chart_data['values']));
    @else
        initChart([], []);
    @endif

    // AJAX Filter
    document.getElementById('filterPeriod').addEventListener('change', async (e) => {
        const period = e.target.value;
        const res = await fetch(`{{ route('superadmin.reports.index') }}?period=${period}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        
        // Update Summary Cards
        document.getElementById('valTrxCount').textContent = data.summary.transaction_count;
        document.getElementById('valTotalSales').textContent = data.summary.total_sales;
        document.getElementById('valTotalCost').textContent = data.summary.total_cost;
        document.getElementById('valTotalPayroll').textContent = data.summary.total_payroll;
        
        const profitLossEl = document.getElementById('valProfitLoss');
        profitLossEl.textContent = data.summary.profit_loss;
        profitLossEl.className = 'value ' + data.summary.profit_class;

        // Update Subtitle
        document.getElementById('reportSubtitle').textContent = data.summary.period_range;

        // Update Table
        const tbody = document.getElementById('reportRows');
        tbody.innerHTML = data.rows.map(row => `
            <tr>
                <td><span class="trx-code">${row.code}</span></td>
                <td>
                    <div class="branch-cell">
                        <div class="branch-dot" style="background:linear-gradient(135deg, #D97706, #F59E0B);">...</div>
                        <span class="branch-name">${row.branch_name}</span>
                    </div>
                </td>
                <td>${row.sold_at}</td>
                <td class="amount-cell">${row.total_amount}</td>
                <td class="amount-cell">${row.total_cost}</td>
                <td class="${row.profit_class}">${row.profit_loss}</td>
                <td>
                    <span class="status-badge ${row.status}">
                        <span class="status-dot"></span> ${row.status_label}
                    </span>
                </td>
            </tr>
        `).join('');
        
        // Update Pagination
        document.querySelector('.pagination-area').innerHTML = data.pagination;
        
        // Update Chart
        initChart(data.chart_data.labels, data.chart_data.values);
    });
</script>
@endpush

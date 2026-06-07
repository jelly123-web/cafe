@extends('cashier.layout')

@section('title', 'Laporan Kasir')
@section('page_title', 'Laporan Kasir')
@section('page_icon', 'fas fa-file-invoice-dollar')
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

    .finance-visuals {
      display: grid;
      grid-template-columns: minmax(0, 1.7fr) minmax(320px, 0.95fr);
      gap: 18px;
      margin-bottom: 22px;
    }

    .chart-card {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      padding: 22px 22px 18px;
      box-shadow: var(--shadow-sm);
    }

    .chart-card-head {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 14px;
      margin-bottom: 18px;
    }

    .chart-card-head h3 {
      font-size: 17px;
      font-weight: 800;
      color: var(--fg);
      display: flex;
      align-items: center;
      gap: 8px;
      letter-spacing: -0.2px;
    }

    .chart-card-head p {
      margin-top: 4px;
      font-size: 12px;
      color: var(--muted);
    }

    .chart-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 7px 12px;
      border-radius: var(--radius-full);
      background: var(--accent-light);
      color: var(--accent-dark);
      font-size: 11px;
      font-weight: 800;
      white-space: nowrap;
    }

    .bar-chart {
      display: flex;
      align-items: end;
      gap: 12px;
      height: 280px;
      padding: 18px 10px 8px;
      border-radius: var(--radius-md);
      background: linear-gradient(180deg, rgba(217,119,6,0.05), rgba(255,255,255,0.9));
      border: 1px solid var(--border-light);
      overflow-x: auto;
    }

    .bar-chart-empty {
      min-height: 280px;
      display: grid;
      place-items: center;
      border-radius: var(--radius-md);
      border: 1px dashed var(--border);
      background: #FAFBFC;
      color: var(--muted);
      font-size: 13px;
      text-align: center;
      padding: 24px;
    }

    .bar-item {
      min-width: 54px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      flex: 1 0 54px;
    }

    .bar-value {
      font-size: 11px;
      font-weight: 700;
      color: var(--fg-secondary);
      line-height: 1;
    }

    .bar-track {
      width: 100%;
      height: 200px;
      display: flex;
      align-items: end;
      justify-content: center;
    }

    .bar-fill {
      width: 100%;
      max-width: 42px;
      min-height: 8px;
      border-radius: 14px 14px 8px 8px;
      background: linear-gradient(180deg, #F59E0B 0%, #D97706 100%);
      box-shadow: 0 10px 18px rgba(217, 119, 6, 0.2);
      transition: height 0.5s ease;
    }

    .bar-label {
      font-size: 11px;
      font-weight: 700;
      color: var(--fg-secondary);
      text-align: center;
      line-height: 1.3;
    }

    .donut-layout {
      display: grid;
      grid-template-columns: 140px minmax(0, 1fr);
      gap: 16px;
      align-items: center;
      min-height: 280px;
    }

    .donut-wrap {
      width: 140px;
      height: 140px;
      border-radius: 50%;
      position: relative;
      display: grid;
      place-items: center;
      background: #F3F4F6;
      margin: 0 auto;
    }

    .donut-wrap::after {
      content: '';
      width: 82px;
      height: 82px;
      border-radius: 50%;
      background: var(--white);
      box-shadow: inset 0 0 0 1px var(--border-light);
    }

    .donut-center {
      position: absolute;
      inset: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      z-index: 2;
      text-align: center;
      padding: 0 18px;
    }

    .donut-center strong {
      font-size: 16px;
      font-weight: 900;
      color: var(--fg);
      line-height: 1.1;
    }

    .donut-center span {
      font-size: 11px;
      color: var(--muted);
      font-weight: 700;
      margin-top: 4px;
    }

    .donut-legend {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .donut-legend-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 10px 12px;
      border: 1px solid var(--border-light);
      border-radius: var(--radius-sm);
      background: #fff;
    }

    .donut-legend-label {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      min-width: 0;
      color: var(--fg-secondary);
      font-size: 12px;
      font-weight: 700;
    }

    .donut-legend-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      flex-shrink: 0;
    }

    .donut-legend-value {
      font-size: 12px;
      font-weight: 800;
      color: var(--fg);
      white-space: nowrap;
    }
    .summary-card .value.expense { color: var(--fg-secondary); }

    /* ===== SECTION HEAD ===== */
    .section-head {
      display: flex; justify-content: space-between; align-items: center;
      gap: 12px; padding: 18px 24px;
      border-bottom: 1px solid var(--border-light);
      flex-wrap: wrap;
    }

    .section-head h2 {
      font-size: 15px; font-weight: 800; color: var(--fg);
      letter-spacing: -0.2px; display: flex; align-items: center; gap: 8px;
    }

    .section-head h2 i { color: var(--accent); font-size: 16px; }

    .section-head .section-meta {
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

    .report-filters input,
    .report-filters select {
      padding: 9px 14px;
      border: 1.5px solid var(--border); border-radius: var(--radius-sm);
      background: var(--white); font-size: 13px; font-weight: 500;
      color: var(--fg); outline: none; transition: all var(--transition);
      font-family: var(--font); -webkit-appearance: none; min-height: 40px;
    }

    .report-filters input:focus,
    .report-filters select:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(217,119,6,0.1);
    }

    .report-filters select {
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%239CA3B4' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
      background-repeat: no-repeat; background-position: right 12px center;
      padding-right: 32px;
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

    .btn-primary {
      background: var(--accent); color: white; border: none;
    }
    .btn-primary:hover {
      background: var(--accent-dark); transform: translateY(-1px);
      box-shadow: 0 4px 14px rgba(217,119,6,0.25);
    }

    .btn-outline {
      background: var(--white); color: var(--fg-secondary);
      border: 1.5px solid var(--border);
    }
    .btn-outline:hover {
      border-color: var(--accent); color: var(--accent);
      background: var(--accent-light);
    }

    .btn-danger {
      background: transparent; color: var(--red);
      border: 1.5px solid #FECACA;
    }
    .btn-danger:hover { background: var(--red-light); border-color: var(--red); }

    .btn-delete-row {
      border: none; background: transparent; color: var(--muted);
      cursor: pointer; font-weight: 700; font-size: 12px;
      font-family: var(--font); padding: 6px 10px;
      border-radius: var(--radius-sm); transition: all var(--transition);
      display: inline-flex; align-items: center; gap: 4px;
    }
    .btn-delete-row:hover { background: var(--red-light); color: var(--red); }

    /* ===== PANEL ===== */
    .panel {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      margin-bottom: 20px;
      overflow: hidden;
    }

    /* ===== TABLE ===== */
    .table-wrap { overflow-x: auto; }

    .report-table {
      width: 100%; border-collapse: collapse;
    }

    .report-table th,
    .report-table td {
      padding: 13px 20px; text-align: left;
      border-bottom: 1px solid var(--border-light);
      font-size: 13px; vertical-align: middle;
    }

    .report-table th {
      background: var(--bg); font-size: 11px; font-weight: 700;
      text-transform: uppercase; color: var(--muted);
      letter-spacing: 0.7px; border-bottom: 1px solid var(--border);
    }

    .report-table tbody tr { transition: background var(--transition); }
    .report-table tbody tr:hover { background: #FAFBFC; }
    .report-table tbody tr:last-child td { border-bottom: none; }

    /* ===== TABLE CELL STYLES ===== */
    .trx-code {
      font-family: 'SF Mono', 'Fira Code', 'Courier New', monospace;
      font-weight: 700; color: var(--accent); font-size: 12px;
      background: var(--accent-light); padding: 3px 8px;
      border-radius: 4px; letter-spacing: 0.3px;
    }

    .branch-cell {
      display: flex; align-items: center; gap: 8px;
    }

    .branch-dot {
      width: 28px; height: 28px; border-radius: var(--radius-sm);
      display: flex; align-items: center; justify-content: center;
      font-size: 11px; font-weight: 800; color: white; flex-shrink: 0;
    }

    .branch-dot i { font-size: 12px; }

    .branch-name { font-weight: 600; font-size: 13px; }

    .amount-cell {
      font-variant-numeric: tabular-nums; white-space: nowrap;
      font-weight: 500;
    }

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

    .status-badge .status-dot {
      width: 5px; height: 5px; border-radius: 50%;
      background: currentColor;
    }

    .status-badge.paid .status-dot { animation: dotPulse 2s infinite; }

    @keyframes dotPulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.3; }
    }

    /* ===== EMPTY STATE ===== */
    .empty-state {
      text-align: center; padding: 48px 20px; color: var(--muted); font-size: 14px;
    }
    .empty-state::before {
      content: '\f201'; font-family: 'Font Awesome 6 Free'; font-weight: 900;
      display: block; font-size: 36px; margin-bottom: 10px; color: var(--border);
    }
    .empty-state em { font-style: normal; font-weight: 700; color: var(--fg-secondary); }

    /* ===== PAGINATION ===== */
    .pagination-area { padding: 14px 24px; border-top: 1px solid var(--border-light); }
    .pagination-wrap { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
    .pagination-meta { font-size: 12px; color: var(--muted); font-weight: 500; }
    .pagination-links { display: flex; gap: 4px; flex-wrap: wrap; }
    .pagination-link, .pagination-dots {
      display: inline-flex; align-items: center; justify-content: center;
      min-width: 34px; height: 34px; border-radius: var(--radius-sm);
      font-size: 12px; font-weight: 600; text-decoration: none;
      border: 1px solid var(--border); color: var(--fg-secondary);
      padding: 0 8px; background: var(--white); transition: all var(--transition);
      font-family: var(--font); cursor: pointer;
    }
    .pagination-link:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-light); }
    .pagination-link.active { background: var(--accent); border-color: var(--accent); color: white; }

    /* ===== LIVE INDICATOR ===== */
    .live-indicator {
      display: inline-flex; align-items: center; gap: 6px;
      font-size: 11px; font-weight: 700; color: var(--green);
      text-transform: uppercase; letter-spacing: 0.5px;
    }

    .live-dot {
      width: 7px; height: 7px; background: var(--green);
      border-radius: 50%; animation: livePulse 1.5s infinite;
    }

    @keyframes livePulse {
      0% { box-shadow: 0 0 0 0 rgba(5,150,105,0.4); }
      70% { box-shadow: 0 0 0 6px rgba(5,150,105,0); }
      100% { box-shadow: 0 0 0 0 rgba(5,150,105,0); }
    }
</style>
@endpush

@section('content')
    <!-- SUMMARY CARDS -->
    <div class="summary-grid fade-in" id="reportSummary">
        <div class="summary-card" style="--card-accent: var(--blue);">
            <div class="card-icon" style="background:var(--blue-light);color:var(--blue);"><i class="fas fa-receipt"></i></div>
            <label>Total Transaksi</label>
            <div class="value" id="valTrxCount">{{ $summary['transaction_count'] }}</div>
        </div>
        <div class="summary-card" style="--card-accent: var(--green);">
            <div class="card-icon" style="background:var(--green-light);color:var(--green);"><i class="fas fa-arrow-trend-up"></i></div>
            <label>Total Penjualan</label>
            <div class="value" id="valTotalSales">{{ $summary['total_sales'] }}</div>
        </div>
        <div class="summary-card" style="--card-accent: var(--accent);">
            <div class="card-icon" style="background:var(--accent-light);color:var(--accent);"><i class="fas fa-boxes-stacked"></i></div>
            <label>Total Modal (HPP)</label>
            <div class="value expense" id="valTotalCost">{{ $summary['total_cost'] }}</div>
        </div>
        <div class="summary-card" style="--card-accent: var(--red);">
            <div class="card-icon" style="background:var(--red-light);color:var(--red);"><i class="fas fa-wallet"></i></div>
            <label>Gaji Karyawan</label>
            <div class="value expense" id="valTotalPayroll">{{ $summary['total_payroll'] }}</div>
        </div>
        <div class="summary-card" style="--card-accent: var(--green);">
            <div class="card-icon" style="background:var(--green-light);color:var(--green);"><i class="fas fa-chart-line"></i></div>
            <label>Laba / Rugi Bersih</label>
            <div class="value {{ $summary['profit_class'] }}" id="valProfitLoss">{{ $summary['profit_loss'] }}</div>
        </div>
    </div>

    <!-- MAIN PANEL -->
    <section class="panel fade-in">
        <!-- SECTION HEAD -->
        <div class="section-head">
            <div>
                <h2><i class="fas fa-table-list"></i> Daftar Transaksi</h2>
                <div class="section-meta" id="reportSubtitle">{{ $summary['period_range'] }}</div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="table-wrap">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Kode TRX</th>
                        <th>Meja / Pesanan</th>
                        <th>Waktu</th>
                        <th>Penjualan</th>
                        <th>Modal</th>
                        <th>Laba / Rugi</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="reportRows">
                    @forelse ($rows as $row)
                        <tr data-id="{{ $row->id }}">
                            <td><span class="trx-code">{{ $row->code }}</span></td>
                            <td>
                                <div class="branch-cell">
                                    @if ($row->table)
                                        <div class="branch-dot" style="background:linear-gradient(135deg, #D97706, #F59E0B);"><i class="fas fa-chair"></i></div>
                                        <span class="branch-name">Meja {{ $row->table->name }}</span>
                                    @else
                                        <div class="branch-dot" style="background:linear-gradient(135deg, #2563EB, #60A5FA);"><i class="fas fa-bag-shopping"></i></div>
                                        <span class="branch-name">Pesanan Langsung</span>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $row->sold_at_label }}</td>
                            <td class="amount-cell">{{ $row->total_amount_label }}</td>
                            <td class="amount-cell">{{ $row->total_cost_label }}</td>
                            <td class="{{ $row->profit_class }}">{{ $row->profit_loss }}</td>
                            <td>
                                <span class="status-badge {{ $row->status }}">
                                    <span class="status-dot"></span> {{ $row->status_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <em>Belum ada transaksi</em> pada periode ini.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="pagination-area" id="reportPagination">
            {{ $rows->links('components.pagination') }}
        </div>
    </section>
@endsection

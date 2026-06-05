@extends('superadmin.layout')

@section('title', 'Gaji Karyawan — cafecaf')
@section('page_title', 'Gaji Karyawan')
@section('page_description', '')

@push('head')
    <style>
    /* ===== VARIABEL DESAIN ===== */
    :root {
      --bg: #F4F5F7;
      --bg-card: #FFFFFF;
      --white: #FFFFFF;
      --border: #E8EAED;
      --border-light: #F0F1F3;
      --fg: #1A1D23;
      --fg-secondary: #5F6577;
      --muted: #9CA3B4;
      --accent: #D97706;
      --accent-light: #FEF3C7;
      --accent-dark: #B45309;
      --green: #059669;
      --green-light: #D1FAE5;
      --red: #DC2626;
      --red-light: #FEE2E2;
      --blue: #2563EB;
      --blue-light: #DBEAFE;
      --purple: #7C3AED;
      --purple-light: #EDE9FE;
      --teal: #0D9488;
      --teal-light: #CCFBF1;
      --shadow-xs: 0 1px 2px rgba(0,0,0,0.03);
      --shadow-sm: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.02);
      --shadow-md: 0 4px 12px rgba(0,0,0,0.05);
      --shadow-lg: 0 8px 30px rgba(0,0,0,0.07);
      --shadow-xl: 0 20px 60px rgba(0,0,0,0.1);
      --radius-sm: 8px;
      --radius-md: 12px;
      --radius-lg: 16px;
      --radius-xl: 20px;
      --radius-full: 999px;
      --font: 'Plus Jakarta Sans', -apple-system, sans-serif;
      --transition: 0.2s ease;
    }

    /* ===== PANEL ===== */
    .panel {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      margin-bottom: 20px;
      overflow: hidden;
    }

    .panel-head {
      display: flex; align-items: center; justify-content: space-between;
      gap: 12px; padding: 18px 24px;
      border-bottom: 1px solid var(--border-light);
    }

    .panel-head h2 {
      font-size: 15px; font-weight: 800; color: var(--fg);
      letter-spacing: -0.2px; display: flex; align-items: center; gap: 8px;
    }

    .panel-head h2 i { color: var(--accent); font-size: 16px; }

    .panel-head .panel-head-meta {
      font-size: 12px; color: var(--muted); font-weight: 500; margin-top: 2px;
    }

    /* ===== PAYROLL FORM ===== */
    .payroll-form {
      display: grid;
      grid-template-columns: repeat(6, minmax(0, 1fr));
      gap: 14px;
      align-items: end;
      padding: 24px;
    }

    .field { display: flex; flex-direction: column; gap: 6px; }

    .field label {
      font-size: 12px; font-weight: 700; color: var(--fg-secondary);
      text-transform: uppercase; letter-spacing: 0.5px;
    }

    .payroll-form input,
    .payroll-form select {
      width: 100%; border: 1.5px solid var(--border); border-radius: var(--radius-sm);
      padding: 10px 14px; background: var(--white); color: var(--fg);
      font-size: 14px; font-weight: 500; outline: none;
      transition: all var(--transition); font-family: var(--font);
      -webkit-appearance: none; min-height: 42px;
    }

    .payroll-form input::placeholder { color: var(--muted); font-weight: 400; }

    .payroll-form input:focus,
    .payroll-form select:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(217,119,6,0.1);
    }

    .payroll-form select {
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%239CA3B4' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
      background-repeat: no-repeat; background-position: right 12px center;
      padding-right: 32px;
    }

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    input[type="number"] { -moz-appearance: textfield; }

    /* ===== BUTTONS ===== */
    .btn {
      border: 1px solid transparent; border-radius: var(--radius-sm);
      padding: 10px 20px; cursor: pointer; font-weight: 700;
      font-family: var(--font); font-size: 13px;
      transition: all var(--transition); text-decoration: none;
      display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    }

    .btn-primary {
      background: var(--accent); color: white; border: none;
      min-height: 42px; white-space: nowrap;
    }
    .btn-primary:hover {
      background: var(--accent-dark); transform: translateY(-1px);
      box-shadow: 0 4px 14px rgba(217,119,6,0.25);
    }

    .btn-danger {
      background: transparent; color: var(--red);
      border: 1.5px solid #FECACA; padding: 8px 14px; font-size: 12px;
      border-radius: var(--radius-sm);
    }
    .btn-danger:hover { background: var(--red-light); border-color: var(--red); }

    .btn-danger-sm {
      background: transparent; color: var(--red);
      border: 1.5px solid #FECACA; padding: 6px 12px; font-size: 11px;
      border-radius: var(--radius-sm);
    }
    .btn-danger-sm:hover { background: var(--red-light); border-color: var(--red); }

    /* ===== TABLE ===== */
    .table-wrap { overflow-x: auto; }

    .payroll-table {
      width: 100%; min-width: 900px; border-collapse: collapse; table-layout: fixed;
    }

    .payroll-table th,
    .payroll-table td {
      padding: 14px 20px; border-bottom: 1px solid var(--border-light);
      vertical-align: middle; text-align: left; font-size: 13px;
    }

    .payroll-table th {
      background: var(--bg); font-size: 11px; text-transform: uppercase;
      letter-spacing: 0.7px; color: var(--muted); font-weight: 700;
      border-bottom: 1px solid var(--border);
      position: sticky; top: 0; z-index: 2;
    }

    .payroll-table tbody tr { transition: background var(--transition); }
    .payroll-table tbody tr:hover { background: #FAFBFC; }
    .payroll-table tbody tr:last-child td { border-bottom: none; }

    /* ===== TABLE CELL STYLES ===== */
    .employee-cell {
      display: flex; align-items: center; gap: 10px;
    }

    .employee-avatar {
      width: 32px; height: 32px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 12px; font-weight: 800; color: white; flex-shrink: 0;
    }

    .employee-name { font-weight: 700; color: var(--fg); font-size: 13px; }
    .employee-code { font-size: 11px; color: var(--muted); font-weight: 500; }

    .amount {
      font-variant-numeric: tabular-nums; white-space: nowrap;
      color: var(--fg-secondary); font-weight: 500; font-size: 13px;
    }

    .net-salary {
      font-weight: 800; color: var(--accent); font-size: 14px; white-space: nowrap;
      font-variant-numeric: tabular-nums;
    }

    .deduction-amount {
      font-variant-numeric: tabular-nums; white-space: nowrap;
      color: var(--red); font-weight: 600; font-size: 13px;
    }

    .allowance-amount {
      font-variant-numeric: tabular-nums; white-space: nowrap;
      color: var(--green); font-weight: 600; font-size: 13px;
    }

    /* ===== PERIOD BADGE ===== */
    .period-badge {
      display: inline-flex; align-items: center; gap: 4px;
      padding: 4px 10px; border-radius: var(--radius-full);
      font-size: 11px; font-weight: 700; letter-spacing: 0.2px;
      background: var(--accent-light); color: var(--accent-dark);
    }

    .period-badge i { font-size: 10px; }

    /* ===== COLUMN WIDTHS ===== */
    .col-period { width: 120px; }
    .col-employee { width: 200px; }
    .col-money { width: 155px; }
    .col-action { width: 90px; text-align: center !important; white-space: nowrap; }
    .payroll-table td.col-action form { display: inline-flex; margin: 0; }

    /* ===== EMPTY STATE ===== */
    .empty-state {
      color: var(--muted); text-align: center; padding: 48px 20px; font-size: 14px;
    }
    .empty-state::before {
      content: '\f09d'; font-family: 'Font Awesome 6 Free'; font-weight: 900;
      display: block; font-size: 36px; margin-bottom: 10px; color: var(--border);
    }
    .empty-state em { font-style: normal; font-weight: 700; color: var(--fg-secondary); }

    /* ===== SUMMARY STRIP ===== */
    .summary-strip {
      display: flex; gap: 0; padding: 0 24px;
      border-top: 1px solid var(--border-light);
    }

    .summary-item {
      flex: 1; padding: 16px 20px;
      display: flex; flex-direction: column; gap: 2px;
      border-right: 1px solid var(--border-light);
    }

    .summary-item:last-child { border-right: none; }

    .summary-label {
      font-size: 11px; font-weight: 700; color: var(--muted);
      text-transform: uppercase; letter-spacing: 0.5px;
    }

    .summary-value {
      font-size: 18px; font-weight: 900; color: var(--fg);
      font-variant-numeric: tabular-nums; letter-spacing: -0.3px;
    }

    .summary-value.accent { color: var(--accent); }
    .summary-value.green { color: var(--green); }
    .summary-value.red { color: var(--red); }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1100px) {
      .payroll-form { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    @media (max-width: 768px) {
      .payroll-form { grid-template-columns: 1fr; padding: 16px; }
      .panel-head { padding: 14px 16px; flex-direction: column; align-items: flex-start; }
      .payroll-table th, .payroll-table td { padding: 10px 14px; font-size: 12px; }
      .summary-strip { flex-direction: column; }
      .summary-item { border-right: none; border-bottom: 1px solid var(--border-light); }
      .summary-item:last-child { border-bottom: none; }
    }
    </style>
@endpush

@section('content')
    <!-- FORM PANEL -->
    <section class="panel fade-in">
        <form class="payroll-form" id="payrollForm" method="POST" action="{{ route('superadmin.payrolls.store') }}">
            @csrf
            <div class="field">
                <label>Karyawan</label>
                <select name="employee_id" required>
                    <option value="">Pilih karyawan</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->employee_code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>Periode</label>
                <input type="month" name="period_month" required value="{{ date('Y-m') }}">
            </div>
            <div class="field">
                <label>Gaji Pokok</label>
                <input type="number" step="0.01" min="0" name="base_salary" placeholder="3500000" required>
            </div>
            <div class="field">
                <label>Tunjangan</label>
                <input type="number" step="0.01" min="0" name="allowances" placeholder="Opsional">
            </div>
            <div class="field">
                <label>Potongan</label>
                <input type="number" step="0.01" min="0" name="deductions" placeholder="Opsional">
            </div>
            <button type="submit" class="btn btn-primary" id="payrollSubmitBtn"><i class="fas fa-plus"></i> Simpan Gaji</button>
        </form>
    </section>

    <!-- TABLE PANEL -->
    <section class="panel fade-in">
        <div class="panel-head">
            <div>
                <h2><i class="fas fa-table-list"></i> Daftar Gaji</h2>
                <div class="panel-head-meta">Data payroll seluruh karyawan per periode.</div>
            </div>
            @if ($payrolls->total() > 0)
                <form method="POST" action="{{ route('superadmin.payrolls.destroy-all') }}" onsubmit="return confirm('Hapus semua data gaji?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash-can"></i> Hapus Semua</button>
                </form>
            @endif
        </div>

        @php
            $totalBase = $payrolls_summary->sum('base_salary');
            $totalAllowances = $payrolls_summary->sum('allowances');
            $totalDeductions = $payrolls_summary->sum('deductions');
            $totalNet = $payrolls_summary->sum('net_salary');
        @endphp

        <!-- SUMMARY STRIP -->
        <div class="summary-strip">
            <div class="summary-item">
                <span class="summary-label">Total Gaji Pokok</span>
                <span class="summary-value" id="sumBase">Rp {{ number_format($totalBase, 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Tunjangan</span>
                <span class="summary-value green" id="sumAllow">Rp {{ number_format($totalAllowances, 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Potongan</span>
                <span class="summary-value red" id="sumDeduct">Rp {{ number_format($totalDeductions, 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Gaji Bersih</span>
                <span class="summary-value accent" id="sumNet">Rp {{ number_format($totalNet, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="table-wrap">
            <table class="payroll-table">
                <thead>
                    <tr>
                        <th class="col-period">Periode</th>
                        <th class="col-employee">Karyawan</th>
                        <th class="col-money">Pokok</th>
                        <th class="col-money">Tunjangan</th>
                        <th class="col-money">Potongan</th>
                        <th class="col-money">Gaji Bersih</th>
                        <th class="col-action">Aksi</th>
                    </tr>
                </thead>
                <tbody id="payrollTableBody">
                    @forelse ($payrolls as $payroll)
                        <tr data-payroll-id="{{ $payroll->id }}">
                            <td class="col-period"><span class="period-badge"><i class="far fa-calendar"></i> {{ $payroll->period_month?->translatedFormat('M Y') }}</span></td>
                            <td class="col-employee">
                                <div class="employee-cell">
                                    @php
                                        $initials = collect(explode(' ', $payroll->employee?->name))->map(fn($n) => strtoupper(substr($n, 0, 1)))->take(2)->join('');
                                        $colors = ['#D97706', '#7C3AED', '#059669', '#DC2626', '#2563EB', '#0D9488'];
                                        $bgColor = $colors[$payroll->employee_id % count($colors)];
                                    @endphp
                                    <div class="employee-avatar" style="background: linear-gradient(135deg, {{ $bgColor }}, #F59E0B);">{{ $initials }}</div>
                                    <div>
                                        <span class="employee-name">{{ $payroll->employee?->name }}</span><br>
                                        <span class="employee-code">{{ $payroll->employee?->employee_code }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="col-money"><span class="amount">Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</span></td>
                            <td class="col-money"><span class="allowance-amount">+ Rp {{ number_format($payroll->allowances, 0, ',', '.') }}</span></td>
                            <td class="col-money"><span class="deduction-amount">- Rp {{ number_format($payroll->deductions, 0, ',', '.') }}</span></td>
                            <td class="col-money"><span class="net-salary">Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}</span></td>
                            <td class="col-action">
                                <form method="POST" action="{{ route('superadmin.payrolls.destroy', $payroll) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger-sm" onclick="return confirm('Hapus data ini?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state"><em>Belum ada data gaji.</em></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-area">
            {{ $payrolls->links('components.pagination') }}
        </div>
    </section>
@endsection

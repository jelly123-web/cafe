@extends('superadmin.layout')

@section('title', 'Gaji Karyawan')
@section('kicker', 'ERP')
@section('page_title', 'Gaji Karyawan')
@section('page_description', 'Mencatat payroll karyawan dan otomatis dihitung sebagai pengeluaran di laporan akhir.')

@push('head')
    <style>
        .panel { background: var(--bg-card); border: 1px solid var(--accent); border-radius: 20px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; box-shadow: 0 4px 15px var(--shadow); }
        .payroll-form { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 1rem; align-items: end; }
        .field { display: flex; flex-direction: column; gap: 0.4rem; }
        .field label { font-size: 0.8rem; font-weight: 600; color: var(--text-muted); }
        .payroll-form input, .payroll-form select {
            width: 100%; border: 1px solid var(--accent); border-radius: 12px; padding: 0.65rem 1rem;
            background-color: var(--bg-main); color: var(--text-main); font-family: inherit; font-size: 0.9rem; outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .payroll-form input:focus, .payroll-form select:focus { border-color: var(--highlight); box-shadow: 0 0 0 3px rgba(212, 163, 115, 0.15); background-color: #fff; }
        input[type="number"]::-webkit-inner-spin-button, input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        input[type="number"] { -moz-appearance: textfield; }
        .table-wrap { overflow-x: auto; margin: 0; }
        .payroll-table { width: 100%; min-width: 980px; border-collapse: collapse; table-layout: fixed; }
        .payroll-table th, .payroll-table td { padding: 1rem 0.75rem; border-bottom: 1px dashed var(--accent); vertical-align: middle; text-align: left; font-size: 0.95rem; }
        .payroll-table th { background: var(--bg-main); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); font-weight: 600; border-bottom: 2px solid var(--highlight); }
        .payroll-table tbody tr:hover { background-color: #FFFAF5; }
        .payroll-table tbody tr:last-child td { border-bottom: none; }
        .employee-name { font-weight: 600; color: var(--primary); }
        .amount { font-variant-numeric: tabular-nums; white-space: nowrap; }
        .net-salary { font-weight: 700; color: var(--highlight); font-size: 1.05rem; white-space: nowrap; }
        .col-period { width: 110px; }
        .col-employee { width: 220px; }
        .col-money { width: 190px; }
        .col-action { width: 120px; text-align: center !important; white-space: nowrap; }
        .payroll-table td.col-action form { display: inline-flex; margin: 0; }
        .btn { border: 1px solid transparent; border-radius: 12px; padding: 0.65rem 1.2rem; cursor: pointer; font-weight: 600; font-family: inherit; font-size: 0.9rem; transition: all 0.2s ease; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .btn-primary { background: var(--highlight); color: #fff; border: none; box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3); }
        .btn-primary:hover { background: #c68b59; transform: translateY(-2px); }
        .btn-danger { background: transparent; color: var(--loss); border-color: #FFCDD2; padding: 0.45rem 0.85rem; font-size: 0.85rem; border-radius: 8px; }
        .btn-danger:hover { background: #FFEBEE; border-color: var(--loss); }
        .panel-head { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem; padding-bottom: 0.9rem; border-bottom: 1px solid var(--accent); }
        .panel-head h2 { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.45rem; margin: 0; }
        .empty-state { color: var(--text-muted); font-style: italic; text-align: center; padding: 2.5rem 1rem; }
        .pagination-area { margin-top: 1.5rem; }
        @media (max-width: 1100px) { .payroll-form { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
        @media (max-width: 768px) {
            .panel { padding: 1.25rem; }
            .payroll-form { grid-template-columns: 1fr; }
            .payroll-table th, .payroll-table td { padding: 0.75rem 0.5rem; font-size: 0.85rem; }
        }
    </style>
@endpush

@section('content')
    <section class="panel">
        <form method="POST" action="{{ route('superadmin.payrolls.store') }}" class="payroll-form">
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
                <input type="month" name="period_month" required>
            </div>
            <div class="field">
                <label>Gaji Pokok</label>
                <input type="number" step="0.01" min="0" name="base_salary" placeholder="Contoh: 3500000" required>
            </div>
            <div class="field">
                <label>Tunjangan</label>
                <input type="number" step="0.01" min="0" name="allowances" placeholder="Opsional">
            </div>
            <div class="field">
                <label>Potongan</label>
                <input type="number" step="0.01" min="0" name="deductions" placeholder="Opsional">
            </div>
            <button type="submit" class="btn btn-primary">Simpan Gaji</button>
        </form>
    </section>

    <section class="panel">
        <div class="panel-head">
            <h2>Daftar Gaji</h2>
            @if ($payrolls->total() > 0)
                <form method="POST" action="{{ route('superadmin.payrolls.destroy-all') }}" onsubmit="return confirm('Hapus semua data gaji karyawan?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus Semua Gaji</button>
                </form>
            @endif
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
                <tbody>
                    @forelse ($payrolls as $payroll)
                        <tr>
                            <td class="col-period">{{ $payroll->period_month?->format('M Y') }}</td>
                            <td class="col-employee"><span class="employee-name">{{ $payroll->employee?->name }}</span></td>
                            <td class="col-money"><span class="amount">Rp {{ number_format((float) $payroll->base_salary, 0, ',', '.') }}</span></td>
                            <td class="col-money"><span class="amount">Rp {{ number_format((float) $payroll->allowances, 0, ',', '.') }}</span></td>
                            <td class="col-money"><span class="amount">Rp {{ number_format((float) $payroll->deductions, 0, ',', '.') }}</span></td>
                            <td class="col-money"><span class="net-salary">Rp {{ number_format((float) $payroll->net_salary, 0, ',', '.') }}</span></td>
                            <td class="col-action">
                                <form method="POST" action="{{ route('superadmin.payrolls.destroy', $payroll) }}" onsubmit="return confirm('Hapus data gaji ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty-state">Belum ada data gaji.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-area">{{ $payrolls->links('components.pagination') }}</div>
    </section>
@endsection

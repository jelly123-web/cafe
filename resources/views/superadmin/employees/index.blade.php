@extends('superadmin.layout')

@push('head')
    <style>
        .panel { background: var(--bg-card); border: 1px solid var(--accent); border-radius: 20px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; box-shadow: 0 4px 15px var(--shadow); }
        .employee-form { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 1rem; align-items: end; }
        .field { display: flex; flex-direction: column; gap: 0.4rem; }
        .field label { font-size: 0.8rem; font-weight: 600; color: var(--text-muted); }
        .employee-form input { width: 100%; border: 1px solid var(--accent); border-radius: 12px; padding: 0.65rem 1rem; background-color: var(--bg-main); color: var(--text-main); font-family: inherit; font-size: 0.9rem; outline: none; transition: border-color 0.2s ease, box-shadow 0.2s ease; }
        .employee-form input:focus { border-color: var(--highlight); box-shadow: 0 0 0 3px rgba(212, 163, 115, 0.15); background-color: #fff; }
        input[type="date"]::-webkit-calendar-picker-indicator { cursor: pointer; opacity: 0.6; }
        .btn { border: 1px solid transparent; border-radius: 12px; padding: 0.65rem 1.2rem; cursor: pointer; font-weight: 600; font-family: inherit; font-size: 0.9rem; transition: all 0.2s ease; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .btn-primary { background: var(--highlight); color: #fff; border: none; box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3); width: 100%; }
        .btn-primary:hover { background: #c68b59; transform: translateY(-2px); }
        .btn-danger { background: transparent; color: var(--loss); border-color: #FFCDD2; }
        .btn-danger:hover { background: #FFEBEE; border-color: var(--loss); }
        .panel-head { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem; padding-bottom: 0.9rem; border-bottom: 1px solid var(--accent); }
        .panel-head h2 { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.45rem; margin: 0; }
        .row-actions { display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap; }
        .row-actions form { margin: 0; }
        .table-wrap { overflow-x: visible; margin: 0; }
        .employee-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .employee-table th, .employee-table td { padding: 1rem 0.75rem; border-bottom: 1px dashed var(--accent); vertical-align: middle; text-align: left; font-size: 0.95rem; }
        .employee-table th { background: var(--bg-main); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); font-weight: 600; border-bottom: 2px solid var(--highlight); }
        .employee-table tbody tr:hover { background-color: #FFFAF5; }
        .employee-table tbody tr:last-child td { border-bottom: none; }
        .employee-table th:nth-child(1), .employee-table td:nth-child(1) { width: 140px; }
        .employee-table th:nth-child(2), .employee-table td:nth-child(2) { width: 26%; }
        .employee-table th:nth-child(3), .employee-table td:nth-child(3) { width: 20%; }
        .employee-table th:nth-child(4), .employee-table td:nth-child(4) { width: 18%; }
        .employee-table th:nth-child(5), .employee-table td:nth-child(5) { width: 150px; }
        .employee-table th:nth-child(6), .employee-table td:nth-child(6) { width: 120px; text-align: right; }
        .employee-table td { overflow-wrap: anywhere; }
        .emp-name { font-weight: 600; color: var(--primary); }
        .emp-code { display: inline-flex; align-items: center; padding: 0.25rem 0.65rem; border-radius: 6px; background: rgba(212, 163, 115, 0.12); color: var(--highlight); font-size: 0.85rem; font-weight: 700; letter-spacing: 0.5px; }
        .text-muted { color: var(--text-muted); }
        .empty-state { color: var(--text-muted); font-style: italic; text-align: center; padding: 2.5rem 1rem; }
        .pagination-area { margin-top: 1.5rem; }
        @media (max-width: 1100px) { .employee-form { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
        @media (max-width: 768px) {
            .panel { padding: 1.25rem; }
            .employee-form { grid-template-columns: 1fr; gap: 0.85rem; }
            .employee-form .field { width: 100%; }
            .btn-primary { padding: 0.75rem; }
            
            .panel-head { align-items: stretch; flex-direction: column; gap: 0.75rem; }
            .panel-head h2 { font-size: 1.3rem; }
            .btn-danger { width: 100%; justify-content: center; }
            
            .table-wrap { overflow-x: auto; margin: 0 -0.5rem; }
            .employee-table { min-width: 760px; }
            .employee-table th, .employee-table td { padding: 0.75rem 0.5rem; font-size: 0.85rem; }
        }
    </style>
@endpush

@section('title', 'Data Karyawan')
@section('kicker', 'ERP')
@section('page_title', 'Data Karyawan')
@section('page_description', 'Mencatat data pekerja untuk kebutuhan payroll dan laporan pengeluaran.')

@section('content')
    <section class="panel">
        <form method="POST" action="{{ route('superadmin.employees.store') }}" class="employee-form">
            @csrf
            <div class="field">
                <label>Nama karyawan</label>
                <input type="text" name="name" placeholder="Nama lengkap" required maxlength="100">
            </div>
            <div class="field">
                <label>Posisi/Jabatan</label>
                <input type="text" name="position" placeholder="Contoh: Barista" maxlength="100">
            </div>
            <div class="field">
                <label>No HP</label>
                <input type="text" name="phone" placeholder="08xxxxxxxxxx" maxlength="12">
            </div>
            <div class="field">
                <label>Tanggal Masuk</label>
                <input type="date" name="hire_date">
            </div>
            <button type="submit" class="btn btn-primary">Tambah Karyawan</button>
        </form>
    </section>

    <section class="panel">
        <div class="panel-head">
            <h2>Daftar Karyawan</h2>
            @if ($employees->total() > 0)
                <form method="POST" action="{{ route('superadmin.employees.destroy-all') }}" onsubmit="return confirm('Hapus semua data karyawan? Data gaji karyawan terkait juga ikut terhapus.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus Semua Karyawan</button>
                </form>
            @endif
        </div>
        <div class="table-wrap">
            <table class="employee-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Posisi</th>
                        <th>No HP</th>
                        <th>Tgl Masuk</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td><span class="emp-code">{{ $employee->employee_code }}</span></td>
                            <td><span class="emp-name">{{ $employee->name }}</span></td>
                            <td>{{ $employee->position ?: '-' }}</td>
                            <td class="{{ $employee->phone ? '' : 'text-muted' }}">{{ $employee->phone ?: '-' }}</td>
                            <td>{{ $employee->hire_date?->format('d M Y') ?: '-' }}</td>
                            <td>
                                <div class="row-actions">
                                    <form method="POST" action="{{ route('superadmin.employees.destroy', $employee) }}" onsubmit="return confirm('Hapus karyawan ini? Data gaji terkait juga ikut terhapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-state">Belum ada data karyawan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-area">{{ $employees->links('components.pagination') }}</div>
    </section>
@endsection

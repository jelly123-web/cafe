@extends('superadmin.layout')

@section('title', 'Data Karyawan')
@section('page_title', 'Data Karyawan')
@section('page_description', 'Mencatat data pekerja untuk kebutuhan payroll dan laporan pengeluaran.')

@push('head')
    <link rel="stylesheet" href="{{ asset('css/superadmin/employees.css') }}">
@endpush

@section('content')
    <!-- STATS GRID -->
    <section class="stats-grid">
        <article class="stat-card">
            <div class="stat-icon" style="background:var(--accent-light);color:var(--accent-dark);"><i class="fas fa-users"></i></div>
            <strong>{{ $employees->total() }}</strong>
            <span>Total Karyawan</span>
        </article>
    </section>

    <!-- ADD EMPLOYEE FORM -->
    <section class="panel">
        <div class="panel-head">
            <div>
                <h2><i class="fas fa-user-plus"></i> Tambah Karyawan</h2>
                <div class="panel-desc">Mencatat data pekerja baru untuk kebutuhan operasional.</div>
            </div>
        </div>
        <div class="employee-form-container">
            <form method="POST" action="{{ route('superadmin.employees.store') }}" class="employee-form">
                @csrf
                <div class="form-group">
                    <label>Nama Karyawan</label>
                    <input type="text" name="name" placeholder="Nama lengkap" required>
                </div>
                <div class="form-group">
                    <label>Posisi / Jabatan</label>
                    <input type="text" name="position" placeholder="Contoh: Barista">
                </div>
                <div class="form-group">
                    <label>No HP</label>
                    <input type="text" name="phone" placeholder="08xxxxxxxxxx">
                </div>
                <div class="form-group">
                    <label>Tanggal Masuk</label>
                    <input type="date" name="hire_date">
                </div>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-plus"></i> Simpan Karyawan
                </button>
            </form>
        </div>
    </section>

    <!-- EMPLOYEE TABLE -->
    <section class="panel">
        <div class="panel-head">
            <div>
                <h2><i class="fas fa-address-book"></i> Daftar Karyawan</h2>
                <div class="panel-desc">{{ $employees->total() }} karyawan aktif dalam sistem</div>
            </div>
            @if($employees->total() > 0)
                <form method="POST" action="{{ route('superadmin.employees.destroy-all') }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus SEMUA data karyawan?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-delete-all"><i class="fas fa-trash-can"></i> Hapus Semua</button>
                </form>
            @endif
        </div>
        <div class="table-container">
            <table class="employee-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Posisi</th>
                        <th>No HP</th>
                        <th>Tgl Masuk</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                        <tr>
                            <td><span class="badge-code">{{ $emp->employee_code }}</span></td>
                            <td><span class="emp-name">{{ $emp->name }}</span></td>
                            <td>
                                <span class="emp-pos">
                                    <span class="pos-dot" style="background: var(--accent);"></span>
                                    {{ $emp->position ?? '-' }}
                                </span>
                            </td>
                            <td>{{ $emp->phone ?? '-' }}</td>
                            <td>{{ $emp->hire_date ? $emp->hire_date->format('d M Y') : '-' }}</td>
                            <td style="text-align: right;">
                                <form method="POST" action="{{ route('superadmin.employees.destroy', $emp) }}" onsubmit="return confirm('Hapus data karyawan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-delete" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 48px; color: var(--muted);">
                                <i class="fas fa-user-slash" style="font-size: 24px; display: block; margin-bottom: 12px; opacity: 0.5;"></i>
                                Belum ada data karyawan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($employees->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid var(--border-light);">
                {{ $employees->links('components.pagination') }}
            </div>
        @endif
    </section>
@endsection

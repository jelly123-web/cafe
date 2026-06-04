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
        <form method="POST" action="{{ route('superadmin.payrolls.store') }}" class="payroll-form" id="payrollForm">
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
            <button type="submit" class="btn btn-primary" id="payrollSubmitBtn">Simpan Gaji</button>
        </form>
    </section>

    <section class="panel">
        <div class="panel-head">
            <h2>Daftar Gaji</h2>
            @if ($payrolls->total() > 0)
                <form method="POST" action="{{ route('superadmin.payrolls.destroy-all') }}" id="destroyAllPayrollsForm">
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
                <tbody id="payrollTableBody">
                    @forelse ($payrolls as $payroll)
                        <tr data-payroll-id="{{ $payroll->id }}">
                            <td class="col-period">{{ $payroll->period_month?->format('M Y') }}</td>
                            <td class="col-employee"><span class="employee-name">{{ $payroll->employee?->name }}</span></td>
                            <td class="col-money"><span class="amount">Rp {{ number_format((float) $payroll->base_salary, 0, ',', '.') }}</span></td>
                            <td class="col-money"><span class="amount">Rp {{ number_format((float) $payroll->allowances, 0, ',', '.') }}</span></td>
                            <td class="col-money"><span class="amount">Rp {{ number_format((float) $payroll->deductions, 0, ',', '.') }}</span></td>
                            <td class="col-money"><span class="net-salary">Rp {{ number_format((float) $payroll->net_salary, 0, ',', '.') }}</span></td>
                            <td class="col-action">
                                <form method="POST" action="{{ route('superadmin.payrolls.destroy', $payroll) }}" class="payroll-delete-form">
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

@push('scripts')
    <script>
        (function () {
            const form = document.getElementById('payrollForm');
            const submitBtn = document.getElementById('payrollSubmitBtn');
            const tableBody = document.getElementById('payrollTableBody');
            const destroyAllForm = document.getElementById('destroyAllPayrollsForm');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            if (!form || !tableBody) {
                return;
            }

            const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
            }[char]));

            const setButtonLoading = (button, loadingText, isLoading) => {
                if (!button) {
                    return;
                }

                if (!button.dataset.defaultText) {
                    button.dataset.defaultText = button.textContent.trim();
                }

                button.disabled = isLoading;
                button.textContent = isLoading ? loadingText : button.dataset.defaultText;
            };

            const payrollRow = (payroll) => `
                <tr data-payroll-id="${payroll.id}">
                    <td class="col-period">${escapeHtml(payroll.period_label)}</td>
                    <td class="col-employee"><span class="employee-name">${escapeHtml(payroll.employee_name)}</span></td>
                    <td class="col-money"><span class="amount">${escapeHtml(payroll.base_salary)}</span></td>
                    <td class="col-money"><span class="amount">${escapeHtml(payroll.allowances)}</span></td>
                    <td class="col-money"><span class="amount">${escapeHtml(payroll.deductions)}</span></td>
                    <td class="col-money"><span class="net-salary">${escapeHtml(payroll.net_salary)}</span></td>
                    <td class="col-action">
                        <form method="POST" action="${escapeHtml(payroll.delete_url)}" class="payroll-delete-form">
                            <input type="hidden" name="_token" value="${escapeHtml(csrfToken)}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            `;

            const renderEmptyState = () => {
                tableBody.innerHTML = '<tr><td colspan="7" class="empty-state">Belum ada data gaji.</td></tr>';
            };

            const removeEmptyState = () => {
                const emptyRow = tableBody.querySelector('.empty-state')?.closest('tr');
                emptyRow?.remove();
            };

            const attachDeleteHandler = (deleteForm) => {
                deleteForm.addEventListener('submit', async (event) => {
                    if (!confirm('Hapus data gaji ini?')) {
                        event.preventDefault();
                        return;
                    }

                    event.preventDefault();
                    const button = deleteForm.querySelector('button[type="submit"]');
                    setButtonLoading(button, 'Menghapus...', true);

                    try {
                        const response = await fetch(deleteForm.action, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: new FormData(deleteForm),
                        });

                        const payload = await response.json();
                        if (!response.ok) {
                            throw new Error(payload.message || 'Gagal menghapus data gaji.');
                        }

                        tableBody.querySelector(`[data-payroll-id="${payload.payroll_id}"]`)?.remove();
                        if (!tableBody.querySelector('tr')) {
                            renderEmptyState();
                        }
                        window.showToast?.(payload.message, 'success');
                    } catch (error) {
                        window.showToast?.(error.message || 'Terjadi kesalahan.', 'error');
                    } finally {
                        setButtonLoading(button, 'Menghapus...', false);
                    }
                });
            };

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                setButtonLoading(submitBtn, 'Menyimpan...', true);

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: new FormData(form),
                    });

                    const payload = await response.json();
                    if (!response.ok) {
                        const firstError = payload.errors ? Object.values(payload.errors)[0]?.[0] : null;
                        throw new Error(firstError || payload.message || 'Gagal menyimpan gaji.');
                    }

                    removeEmptyState();
                    const existingRow = tableBody.querySelector(`[data-payroll-id="${payload.payroll.id}"]`);
                    if (existingRow) {
                        existingRow.outerHTML = payrollRow(payload.payroll);
                    } else {
                        tableBody.insertAdjacentHTML('afterbegin', payrollRow(payload.payroll));
                    }
                    attachDeleteHandler(tableBody.querySelector(`[data-payroll-id="${payload.payroll.id}"] .payroll-delete-form`));
                    form.reset();
                    window.showToast?.(payload.message, 'success');
                } catch (error) {
                    window.showToast?.(error.message || 'Terjadi kesalahan.', 'error');
                } finally {
                    setButtonLoading(submitBtn, 'Menyimpan...', false);
                }
            });

            destroyAllForm?.addEventListener('submit', async (event) => {
                if (!confirm('Hapus semua data gaji karyawan?')) {
                    event.preventDefault();
                    return;
                }

                event.preventDefault();
                const button = destroyAllForm.querySelector('button[type="submit"]');
                setButtonLoading(button, 'Menghapus...', true);

                try {
                    const response = await fetch(destroyAllForm.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: new FormData(destroyAllForm),
                    });

                    const payload = await response.json();
                    if (!response.ok) {
                        throw new Error(payload.message || 'Gagal menghapus semua data gaji.');
                    }

                    renderEmptyState();
                    destroyAllForm.remove();
                    window.showToast?.(payload.message, 'success');
                } catch (error) {
                    window.showToast?.(error.message || 'Terjadi kesalahan.', 'error');
                    setButtonLoading(button, 'Menghapus...', false);
                }
            });

            tableBody.querySelectorAll('.payroll-delete-form').forEach(attachDeleteHandler);
        })();
    </script>
@endpush

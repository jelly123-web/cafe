<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuperadminPayrollController extends Controller
{
    public function index(): View
    {
        return view('superadmin.payrolls.index', [
            'employees' => Employee::query()->where('is_active', true)->orderBy('name')->get(),
            'payrolls' => Payroll::query()->with('employee')->orderByDesc('period_month')->paginate(12),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'period_month' => ['required', 'date'],
            'base_salary' => ['required', 'numeric', 'min:0'],
            'allowances' => ['nullable', 'numeric', 'min:0'],
            'deductions' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $base = (float) $data['base_salary'];
        $allow = (float) ($data['allowances'] ?? 0);
        $deduct = (float) ($data['deductions'] ?? 0);
        $net = max(0, $base + $allow - $deduct);

        Payroll::query()->updateOrCreate(
            [
                'employee_id' => $data['employee_id'],
                'period_month' => date('Y-m-01', strtotime((string) $data['period_month'])),
            ],
            [
                'base_salary' => $base,
                'allowances' => $allow,
                'deductions' => $deduct,
                'net_salary' => $net,
                'paid_at' => now(),
                'notes' => $data['notes'] ?? null,
            ]
        );

        return back()->with('status', 'Data gaji berhasil disimpan.');
    }

    public function destroy(Payroll $payroll): RedirectResponse
    {
        $payroll->delete();
        return back()->with('status', 'Data gaji berhasil dihapus.');
    }

    public function destroyAll(): RedirectResponse
    {
        Payroll::query()->delete();

        return back()->with('status', 'Semua data gaji berhasil dihapus.');
    }
}

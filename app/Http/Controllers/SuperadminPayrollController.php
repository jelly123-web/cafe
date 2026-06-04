<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Http\JsonResponse;
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

    public function store(Request $request): RedirectResponse|JsonResponse
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

        $payroll = Payroll::query()->updateOrCreate(
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

        $payroll->load('employee');

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Data gaji berhasil disimpan.',
                'payroll' => $this->payrollPayload($payroll),
            ]);
        }

        return back()->with('status', 'Data gaji berhasil disimpan.');
    }

    public function destroy(Request $request, Payroll $payroll): RedirectResponse|JsonResponse
    {
        $payroll->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Data gaji berhasil dihapus.',
                'payroll_id' => $payroll->id,
            ]);
        }

        return back()->with('status', 'Data gaji berhasil dihapus.');
    }

    public function destroyAll(Request $request): RedirectResponse|JsonResponse
    {
        Payroll::query()->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Semua data gaji berhasil dihapus.',
            ]);
        }

        return back()->with('status', 'Semua data gaji berhasil dihapus.');
    }

    private function payrollPayload(Payroll $payroll): array
    {
        return [
            'id' => $payroll->id,
            'period_label' => $payroll->period_month?->format('M Y'),
            'employee_name' => $payroll->employee?->name ?: '-',
            'base_salary' => 'Rp ' . number_format((float) $payroll->base_salary, 0, ',', '.'),
            'allowances' => 'Rp ' . number_format((float) $payroll->allowances, 0, ',', '.'),
            'deductions' => 'Rp ' . number_format((float) $payroll->deductions, 0, ',', '.'),
            'net_salary' => 'Rp ' . number_format((float) $payroll->net_salary, 0, ',', '.'),
            'delete_url' => route('superadmin.payrolls.destroy', $payroll),
        ];
    }
}

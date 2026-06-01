<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuperadminEmployeeController extends Controller
{
    public function index(): View
    {
        return view('superadmin.employees.index', [
            'employees' => Employee::query()->orderBy('name')->paginate(10),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'position' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:12'],
            'hire_date' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $next = (int) Employee::query()->count() + 1;
        $code = 'EMP-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
        while (Employee::query()->where('employee_code', $code)->exists()) {
            $code = 'EMP-' . str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
        }

        Employee::query()->create([
            'employee_code' => $code,
            'name' => $data['name'],
            'position' => $data['position'] ?? null,
            'phone' => $data['phone'] ?? null,
            'hire_date' => $data['hire_date'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        return back()->with('status', 'Karyawan berhasil ditambahkan.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();

        return back()->with('status', 'Karyawan berhasil dihapus.');
    }

    public function destroyAll(): RedirectResponse
    {
        Employee::query()->delete();

        return back()->with('status', 'Semua data karyawan berhasil dihapus.');
    }
}

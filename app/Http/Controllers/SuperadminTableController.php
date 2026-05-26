<?php

namespace App\Http\Controllers;

use App\Models\DiningTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SuperadminTableController extends Controller
{
    public function index(): View
    {
        $tables = DiningTable::query()
            ->withCount('sales')
            ->orderByRaw('CAST(number AS UNSIGNED) ASC')
            ->paginate(4)
            ->withQueryString();

        return view('superadmin.tables.index', [
            'tables' => $tables,
        ]);
    }

    public function create(): View
    {
        return view('superadmin.tables.create', [
            'table' => new DiningTable(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateTable($request);

        DiningTable::create([
            'number' => $data['number'],
            'name' => $data['name'],
            'qr_token' => Str::uuid()->toString(),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('superadmin.tables.index')->with('status', 'Meja berhasil ditambahkan.');
    }

    public function edit(DiningTable $table): View
    {
        return view('superadmin.tables.edit', [
            'table' => $table,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, DiningTable $table): RedirectResponse
    {
        $data = $this->validateTable($request, $table->id);

        $table->update([
            'number' => $data['number'],
            'name' => $data['name'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('superadmin.tables.index')->with('status', 'Meja berhasil diperbarui.');
    }

    public function destroy(DiningTable $table): RedirectResponse
    {
        if ($table->sales()->exists()) {
            return back()->with('error', 'Meja yang sudah dipakai transaksi tidak bisa dihapus.');
        }

        $table->delete();

        return redirect()->route('superadmin.tables.index')->with('status', 'Meja berhasil dihapus.');
    }

    public function destroyAll(): RedirectResponse
    {
        $tableCount = DiningTable::count();

        if ($tableCount === 0) {
            return redirect()->route('superadmin.tables.index')->with('error', 'Belum ada meja untuk dihapus.');
        }

        DB::transaction(function () {
            DiningTable::query()->delete();
        });

        return redirect()
            ->route('superadmin.tables.index')
            ->with('status', "Semua meja berhasil dihapus ({$tableCount} meja).");
    }

    private function validateTable(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tables', 'number')->ignore($ignoreId),
            ],
            'name' => ['required', 'string', 'max:255'],
        ]);
    }
}

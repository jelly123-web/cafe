<?php

namespace App\Http\Controllers;

use App\Models\DiningTable;
use App\Models\SaleTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

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

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $this->validateTable($request);

        $table = DiningTable::create([
            'number' => $data['number'],
            'name' => $data['name'],
            'qr_token' => Str::uuid()->toString(),
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->expectsJson()) {
            $table->loadCount('sales');
            return response()->json([
                'message' => 'Meja berhasil ditambahkan.',
                'table' => $this->tablePayload($table),
            ]);
        }

        return redirect()->route('superadmin.tables.index')->with('status', 'Meja berhasil ditambahkan.');
    }

    public function edit(DiningTable $table): View
    {
        return view('superadmin.tables.edit', [
            'table' => $table,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, DiningTable $table): RedirectResponse|JsonResponse
    {
        $data = $this->validateTable($request, $table->id);

        $table->update([
            'number' => $data['number'],
            'name' => $data['name'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->expectsJson()) {
            $table->loadCount('sales');
            return response()->json([
                'message' => 'Meja berhasil diperbarui.',
                'table' => $this->tablePayload($table),
            ]);
        }

        return redirect()->route('superadmin.tables.index')->with('status', 'Meja berhasil diperbarui.');
    }

    public function destroy(DiningTable $table): RedirectResponse
    {
        SaleTransaction::query()->where('table_id', $table->id)->update(['table_id' => null]);

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
            SaleTransaction::query()->whereNotNull('table_id')->update(['table_id' => null]);
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

    private function tablePayload(DiningTable $table): array
    {
        return [
            'id' => $table->id,
            'number' => $table->number,
            'name' => $table->name,
            'qr_token' => $table->qr_token,
            'is_active' => (bool) $table->is_active,
            'sales_count' => (int) ($table->sales_count ?? 0),
            'show_url' => route('tables.show', $table),
            'delete_url' => route('superadmin.tables.destroy', $table),
            'qr_url' => route('superadmin.tables.qr', $table),
        ];
    }

    public function qr(DiningTable $table): Response
    {
        $svg = app('qrcode')
            ->format('svg')
            ->size(360)
            ->margin(1)
            ->generate(route('tables.show', $table));

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }
}

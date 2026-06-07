<?php

namespace App\Http\Controllers;

use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InventoryController extends Controller
{
    private const CONDITION_GOOD = 'good';
    private const CONDITION_LESS_GOOD = 'less_good';
    private const CONDITION_DAMAGED = 'damaged';

    public function live(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'items' => \App\Models\InventoryItem::query()->with('category')->get(),
        ]);
    }

    public function index(): View
    {
        return $this->page('inventory');
    }

    public function stockInPage(): View
    {
        return $this->page('movement');
    }

    public function stockOutPage(): View
    {
        return $this->page('movement');
    }

    public function storeCategory(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'type' => ['required', 'in:bahan,barang'],
            'unit' => ['required', 'string', 'max:40'],
        ]);

        $category = InventoryCategory::query()->create($data);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Kategori inventory berhasil ditambahkan.',
                'category' => $category,
            ]);
        }

        return back()->with('success', 'Kategori inventory berhasil ditambahkan.');
    }

    public function storeItem(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'inventory_category_id' => ['required', 'exists:inventory_categories,id'],
            'name' => ['required', 'string', 'max:120'],
            'min_stock' => ['nullable', 'numeric', 'min:0'],
        ]);

        $category = InventoryCategory::query()->findOrFail($data['inventory_category_id']);

        $item = InventoryItem::query()->create([
            'inventory_category_id' => $category->id,
            'name' => $data['name'],
            'type' => $category->type,
            'unit' => $category->unit,
            'min_stock' => (float) ($data['min_stock'] ?? 0),
            'stock' => 0,
            'stock_good' => 0,
            'stock_less_good' => 0,
            'stock_damaged' => 0,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            $item->load('category');
            return response()->json([
                'message' => ($category->type === 'barang' ? 'Barang' : 'Bahan') . ' baru berhasil ditambahkan.',
                'item' => $item,
            ]);
        }

        return back()->with('success', ($category->type === 'barang' ? 'Barang' : 'Bahan') . ' baru berhasil ditambahkan.');
    }

    public function destroyItem(InventoryItem $item): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $item->delete();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['message' => 'Item berhasil dihapus.']);
        }

        return back()->with('success', 'Item berhasil dihapus.');
    }

    public function destroyItemsByType(Request $request, string $type): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        abort_unless(in_array($type, ['bahan', 'barang'], true), 404);

        $deleted = InventoryItem::query()
            ->where('type', $type)
            ->delete();

        $label = $type === 'barang' ? 'barang/perlengkapan' : 'bahan baku';
        $message = $deleted > 0
            ? 'Semua data ' . $label . ' berhasil dihapus.'
            : 'Tidak ada data ' . $label . ' untuk dihapus.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $message,
                'deleted' => $deleted,
            ]);
        }

        return back()->with('success', $message);
    }

    public function destroyMovement(Request $request, InventoryMovement $movement): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $movement->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => 'Riwayat berhasil dihapus.']);
        }

        return back()->with('success', 'Riwayat berhasil dihapus.');
    }

    public function destroyAllMovements(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $deleted = InventoryMovement::query()->delete();
        $message = $deleted > 0
            ? 'Semua riwayat barang masuk/keluar berhasil dihapus.'
            : 'Tidak ada riwayat barang masuk/keluar untuk dihapus.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $message,
                'deleted' => $deleted,
            ]);
        }

        return back()->with('success', $message);
    }

    public function stockIn(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'stock_condition' => ['required', 'in:good,less_good,damaged'],
            'qty' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:300'],
        ]);

        DB::transaction(function () use ($request, $data): void {
            $item = InventoryItem::query()->lockForUpdate()->findOrFail($data['inventory_item_id']);
            $qty = (float) $data['qty'];
            $conditionField = $this->conditionField($data['stock_condition']);

            $item->update([
                $conditionField => (float) $item->{$conditionField} + $qty,
                'stock' => (float) $item->total_stock + $qty,
            ]);

            InventoryMovement::query()->create([
                'inventory_item_id' => $item->id,
                'user_id' => $request->user()?->id,
                'type' => 'in',
                'stock_condition' => $data['stock_condition'],
                'qty' => $qty,
                'notes' => $data['notes'] ?? null,
                'moved_at' => now(),
            ]);
        });

        return back()->with('success', 'Barang masuk berhasil dicatat.');
    }

    public function stockOut(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'stock_condition' => ['required', 'in:good,less_good,damaged'],
            'qty' => ['required', 'numeric', 'min:0.01'],
            'used_for' => ['required', 'string', 'max:150'],
            'used_items' => ['required', 'string', 'max:600'],
            'notes' => ['nullable', 'string', 'max:300'],
        ]);

        DB::transaction(function () use ($request, $data): void {
            $item = InventoryItem::query()->lockForUpdate()->findOrFail($data['inventory_item_id']);
            $qty = (float) $data['qty'];
            $conditionField = $this->conditionField($data['stock_condition']);
            if ((float) $item->{$conditionField} < $qty) {
                throw ValidationException::withMessages([
                    'qty' => 'Stok tidak cukup untuk barang keluar.',
                ]);
            }

            $item->update([
                $conditionField => (float) $item->{$conditionField} - $qty,
                'stock' => max(0, (float) $item->total_stock - $qty),
            ]);

            InventoryMovement::query()->create([
                'inventory_item_id' => $item->id,
                'user_id' => $request->user()?->id,
                'type' => 'out',
                'stock_condition' => $data['stock_condition'],
                'qty' => $qty,
                'usage_title' => $data['used_for'],
                'notes' => trim(
                    "Dipakai untuk: {$data['used_for']}\nBahan dipakai: {$data['used_items']}\n"
                    . ($data['notes'] ?? '')
                ),
                'moved_at' => now(),
            ]);
        });

        return back()->with('success', 'Barang keluar berhasil dicatat.');
    }

    public function stockOpname(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'from_condition' => ['required', 'in:good,less_good,damaged'],
            'to_condition' => ['required', 'in:good,less_good,damaged', 'different:from_condition'],
            'qty' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:300'],
        ]);

        DB::transaction(function () use ($request, $data): void {
            $item = InventoryItem::query()->lockForUpdate()->findOrFail($data['inventory_item_id']);
            $qty = (float) $data['qty'];
            $fromField = $this->conditionField($data['from_condition']);
            $toField = $this->conditionField($data['to_condition']);

            if ((float) $item->{$fromField} < $qty) {
                throw ValidationException::withMessages([
                    'qty' => 'Stok kondisi asal tidak cukup untuk opname.',
                ]);
            }

            $item->update([
                $fromField => (float) $item->{$fromField} - $qty,
                $toField => (float) $item->{$toField} + $qty,
                'stock' => (float) $item->total_stock,
            ]);

            InventoryMovement::query()->create([
                'inventory_item_id' => $item->id,
                'user_id' => $request->user()?->id,
                'type' => 'opname',
                'stock_condition' => $data['from_condition'],
                'to_stock_condition' => $data['to_condition'],
                'qty' => $qty,
                'notes' => $data['notes'] ?? 'Stok opname',
                'moved_at' => now(),
            ]);
        });

        return back()->with('success', 'Stok opname berhasil dicatat.');
    }

    private function conditionField(string $condition): string
    {
        return match ($condition) {
            self::CONDITION_GOOD => 'stock_good',
            self::CONDITION_LESS_GOOD => 'stock_less_good',
            self::CONDITION_DAMAGED => 'stock_damaged',
        };
    }

    private function page(string $tab): View
    {
        return view('inventory.index', [
            'activeTab' => $tab,
            'categories' => InventoryCategory::query()->orderBy('name')->get(),
            'items' => InventoryItem::query()->with('category')->orderBy('name')->paginate(10, ['*'], 'items_page'),
            'allItems' => InventoryItem::query()->with('category')->orderBy('name')->get(),
            'movements' => InventoryMovement::query()
                ->with(['item', 'user'])
                ->orderByDesc('moved_at')
                ->paginate(12, ['*'], 'movements_page'),
        ]);
    }
}

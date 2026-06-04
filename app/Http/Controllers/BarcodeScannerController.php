<?php

namespace App\Http\Controllers;

use App\Models\CashierCart;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Menu;
use App\Models\MenuCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class BarcodeScannerController extends Controller
{
    public function scan(Request $request): JsonResponse
    {
        $data = $request->validate([
            'barcode' => ['required', 'string', 'max:120'],
            'target' => ['required', Rule::in(['all', 'menu', 'inventory'])],
        ]);

        $barcode = $this->cleanBarcode($data['barcode']);
        $target = $data['target'];

        if (in_array($target, ['all', 'menu'], true)) {
            $menu = $this->findMenuByBarcode($barcode);
            if ($menu) {
                return response()->json([
                    'found' => true,
                    'type' => 'menu',
                    'item' => $this->menuPayload($menu),
                ]);
            }
        }

        if (in_array($target, ['all', 'inventory'], true)) {
            $item = $this->findInventoryByBarcode($barcode);
            if ($item) {
                return response()->json([
                    'found' => true,
                    'type' => 'inventory',
                    'item' => $this->inventoryPayload($item),
                ]);
            }
        }

        return response()->json([
            'found' => false,
            'barcode' => $barcode,
            'message' => 'Barcode belum terdaftar. Lengkapi data lalu simpan.',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'record_type' => ['required', Rule::in(['menu', 'inventory'])],
            'barcode' => ['required', 'string', 'max:120'],
            'name' => ['required', 'string', 'max:120'],
            'menu_category_id' => ['required_if:record_type,menu', 'nullable', 'exists:menu_categories,id'],
            'selling_price' => ['required_if:record_type,menu', 'nullable', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'inventory_category_id' => ['required_if:record_type,inventory', 'nullable', 'exists:inventory_categories,id'],
            'stock_good' => ['nullable', 'numeric', 'min:0'],
            'min_stock' => ['nullable', 'numeric', 'min:0'],
        ]);

        $barcode = $this->cleanBarcode($data['barcode']);

        if ($data['record_type'] === 'menu') {
            $menu = DB::transaction(function () use ($data, $barcode) {
                $menu = $this->findMenuByBarcode($barcode) ?: new Menu();
                $menu->fill([
                    'menu_category_id' => $data['menu_category_id'],
                    'barcode' => Schema::hasColumn('menus', 'barcode') ? $barcode : null,
                    'code' => $menu->exists ? $menu->code : $this->uniqueMenuCode($barcode),
                    'name' => $data['name'],
                    'selling_price' => (float) ($data['selling_price'] ?? 0),
                    'cost_price' => (float) ($data['cost_price'] ?? 0),
                    'is_sold_out' => false,
                ]);
                $menu->save();

                return $menu->fresh('category');
            });

            Cache::forget('cashier.transaction.catalog');

            return response()->json([
                'message' => 'Menu berhasil disimpan dari barcode.',
                'type' => 'menu',
                'item' => $this->menuPayload($menu),
            ]);
        }

        $item = DB::transaction(function () use ($request, $data, $barcode) {
            $category = InventoryCategory::query()->findOrFail($data['inventory_category_id']);
            $item = $this->findInventoryByBarcode($barcode) ?: new InventoryItem();
            $oldStock = (float) ($item->stock_good ?? 0);
            $newStock = (float) ($data['stock_good'] ?? 0);

            $item->fill([
                'inventory_category_id' => $category->id,
                'barcode' => Schema::hasColumn('inventory_items', 'barcode') ? $barcode : null,
                'name' => $data['name'],
                'type' => $category->type,
                'unit' => $category->unit,
                'min_stock' => (float) ($data['min_stock'] ?? 0),
                'stock_good' => $newStock,
                'stock_less_good' => (float) ($item->stock_less_good ?? 0),
                'stock_damaged' => (float) ($item->stock_damaged ?? 0),
                'stock' => $newStock + (float) ($item->stock_less_good ?? 0) + (float) ($item->stock_damaged ?? 0),
            ]);
            $item->save();

            $diff = $newStock - $oldStock;
            if ($diff > 0) {
                InventoryMovement::query()->create([
                    'inventory_item_id' => $item->id,
                    'user_id' => $request->user()?->id,
                    'type' => 'in',
                    'stock_condition' => 'good',
                    'qty' => $diff,
                    'notes' => 'Scan barcode',
                    'moved_at' => now(),
                ]);
            }

            return $item->fresh('category');
        });

        return response()->json([
            'message' => 'Barang berhasil disimpan dari barcode.',
            'type' => 'inventory',
            'item' => $this->inventoryPayload($item),
        ]);
    }

    public function stockIn(Request $request): JsonResponse
    {
        $data = $request->validate([
            'barcode' => ['required', 'string', 'max:120'],
            'qty' => ['required', 'numeric', 'min:0.01'],
        ]);

        $barcode = $this->cleanBarcode($data['barcode']);
        $qty = (float) $data['qty'];

        $item = DB::transaction(function () use ($request, $barcode, $qty) {
            $item = $this->findInventoryByBarcode($barcode, true);
            abort_unless($item, 404, 'Barang dengan barcode ini belum terdaftar.');

            $item->update([
                'stock_good' => (float) $item->stock_good + $qty,
                'stock' => (float) $item->total_stock + $qty,
            ]);

            InventoryMovement::query()->create([
                'inventory_item_id' => $item->id,
                'user_id' => $request->user()?->id,
                'type' => 'in',
                'stock_condition' => 'good',
                'qty' => $qty,
                'notes' => 'Scan barcode',
                'moved_at' => now(),
            ]);

            return $item->fresh('category');
        });

        return response()->json([
            'message' => 'Stok barang berhasil ditambahkan.',
            'type' => 'inventory',
            'item' => $this->inventoryPayload($item),
        ]);
    }

    public function addMenuToCart(Request $request): JsonResponse
    {
        $data = $request->validate([
            'barcode' => ['required', 'string', 'max:120'],
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        $barcode = $this->cleanBarcode($data['barcode']);
        $menu = $this->findMenuByBarcode($barcode);

        if (! $menu) {
            $inventory = $this->findInventoryByBarcode($barcode);
            if ($inventory) {
                return response()->json([
                    'message' => 'Menu belum terdaftar, tapi ditemukan di inventory.',
                    'found_in_inventory' => true,
                    'inventory' => $this->inventoryPayload($inventory),
                ], 404);
            }

            return response()->json([
                'message' => 'Menu dengan barcode ini belum terdaftar.',
                'found_in_inventory' => false,
                'barcode' => $barcode,
            ], 404);
        }

        $cart = CashierCart::query()->firstOrCreate(['user_id' => $request->user()->id]);
        $item = $cart->items()->where('menu_id', $menu->id)->first();
        if ($item) {
            $item->update([
                'qty' => (int) $item->qty + (int) $data['qty'],
                'unit_price' => (float) $menu->selling_price,
                'unit_cost' => (float) $menu->cost_price,
            ]);
        } else {
            $cart->items()->create([
                'menu_id' => $menu->id,
                'qty' => (int) $data['qty'],
                'unit_price' => (float) $menu->selling_price,
                'unit_cost' => (float) $menu->cost_price,
            ]);
        }

        return response()->json([
            'message' => $menu->name . ' masuk ke keranjang kasir.',
            'item' => $this->menuPayload($menu),
        ]);
    }

    private function cleanBarcode(string $barcode): string
    {
        return trim($barcode);
    }

    private function findMenuByBarcode(string $barcode): ?Menu
    {
        return Menu::query()
            ->with('category')
            ->where('code', $barcode)
            ->when(Schema::hasColumn('menus', 'barcode'), fn ($query) => $query->orWhere('barcode', $barcode))
            ->first();
    }

    private function findInventoryByBarcode(string $barcode, bool $lock = false): ?InventoryItem
    {
        if (! Schema::hasColumn('inventory_items', 'barcode')) {
            return null;
        }

        $query = InventoryItem::query()->with('category');
        if ($lock) {
            $query->lockForUpdate();
        }

        return $query
            ->where('barcode', $barcode)
            ->first();
    }

    private function uniqueMenuCode(string $barcode): string
    {
        $base = $barcode !== '' ? $barcode : 'MENU';
        $code = $base;
        $i = 1;
        while (Menu::query()->where('code', $code)->exists()) {
            $code = $base . '-' . $i;
            $i++;
        }

        return $code;
    }

    private function menuPayload(Menu $menu): array
    {
        return [
            'id' => $menu->id,
            'barcode' => Schema::hasColumn('menus', 'barcode') ? ($menu->barcode ?: $menu->code) : $menu->code,
            'code' => $menu->code,
            'name' => $menu->name,
            'category' => $menu->category?->name ?? '-',
            'selling_price' => (float) $menu->selling_price,
            'cost_price' => (float) $menu->cost_price,
            'selling_price_label' => 'Rp ' . number_format((float) $menu->selling_price, 0, ',', '.'),
        ];
    }

    private function inventoryPayload(InventoryItem $item): array
    {
        return [
            'id' => $item->id,
            'barcode' => Schema::hasColumn('inventory_items', 'barcode') ? ($item->barcode ?: '') : '',
            'name' => $item->name,
            'category' => $item->category?->name ?? '-',
            'unit' => $item->unit,
            'type' => $item->type,
            'stock_good' => (float) $item->stock_good,
            'stock_total' => (float) $item->total_stock,
        ];
    }
}

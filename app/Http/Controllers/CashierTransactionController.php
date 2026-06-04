<?php

namespace App\Http\Controllers;

use App\Models\CashierCart;
use App\Models\CashierCartItem;
use App\Models\DiningTable;
use App\Models\Menu;
use App\Models\SaleTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CashierTransactionController extends Controller
{
    private function normalizeRole(?string $role): string
    {
        return match (strtolower(trim((string) $role))) {
            'dapur' => 'kitchen',
            default => strtolower(trim((string) $role)),
        };
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $role = $this->normalizeRole($user?->role);
        abort_unless($user && in_array($role, ['kasir', 'staff', 'admin', 'superadmin', 'leader_cashier', 'kitchen', 'inventory'], true), 403);

        $catalog = Cache::remember('cashier.transaction.catalog', now()->addMinutes(1), function () {
            return [
                'menus' => Menu::query()->where('code', '!=', 'A01')->orderBy('name')->get(),
                'addonMenus' => Menu::query()
                    ->whereIn('code', ['A01'])
                    ->orderBy('name')
                    ->get(),
                'tables' => DiningTable::query()->where('is_active', true)->orderByRaw('CAST(number AS UNSIGNED) ASC')->get(),
            ];
        });

        return view('cashier.transactions.index', [
            'menus' => $catalog['menus'],
            'addonMenus' => $catalog['addonMenus'],
            'tables' => $catalog['tables'],
            'cart' => $this->cartRows($user->id),
        ]);
    }

    public function addItem(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'menu_id' => ['required', 'exists:menus,id'],
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        $menu = Menu::query()->findOrFail($data['menu_id']);
        $cart = $this->cartModel($request->user()->id);
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

        return back()->with('success', "{$menu->name} ditambahkan ke pesanan.");
    }

    public function updateItem(Request $request, Menu $menu): RedirectResponse
    {
        $data = $request->validate([
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $this->cartModel($request->user()->id);
        $item = $cart->items()->where('menu_id', $menu->id)->first();
        abort_unless($item, 404);
        $item->update(['qty' => (int) $data['qty']]);

        return back()->with('success', "Jumlah {$menu->name} diperbarui.");
    }

    public function removeItem(Request $request, Menu $menu): RedirectResponse
    {
        $cart = $this->cartModel($request->user()->id);
        $cart->items()->where('menu_id', $menu->id)->delete();

        return back()->with('success', "{$menu->name} dihapus dari pesanan.");
    }

    public function checkout(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'table_id' => ['nullable', 'exists:tables,id'],
            'notes' => ['nullable', 'string', 'max:500'],
            'menu_id' => ['nullable', 'exists:menus,id'],
            'qty' => ['nullable', 'integer', 'min:1'],
            'addon_menu_id' => ['nullable', 'exists:menus,id'],
            'addon_qty' => ['nullable', 'integer', 'min:1'],
            'addon_custom_name' => ['nullable', 'string', 'max:100'],
            'addon_custom_price' => ['nullable', 'numeric', 'min:0'],
            'addon_custom_cost' => ['nullable', 'numeric', 'min:0'],
            'addon_custom_qty' => ['nullable', 'integer', 'min:1'],
        ]);

        $userId = (int) $request->user()->id;

        if (!empty($data['menu_id'])) {
            $this->appendMenuToCart($userId, (int) $data['menu_id'], (int) ($data['qty'] ?? 1));
        }

        $addonName = trim((string) ($data['addon_custom_name'] ?? ''));
        $addonQty = (int) ($data['addon_custom_qty'] ?? 0);
        $addonPrice = (float) ($data['addon_custom_price'] ?? 0);
        $addonCost = (float) ($data['addon_custom_cost'] ?? 0);
        $manualAddonActive = $addonName !== '' && $addonQty > 0 && $addonPrice >= 0;

        if ($manualAddonActive) {
            $addonMenu = Menu::query()->where('code', 'A01')->first();
            abort_unless($addonMenu, 422, 'Menu topping manual belum tersedia.');
            $cart = $this->cartModel($userId);
            $item = $cart->items()->where('menu_id', $addonMenu->id)->first();
            $lineTotal = $addonPrice * $addonQty;
            $lineCost = $addonCost * $addonQty;

            if ($item) {
                $item->update([
                    'qty' => (int) $item->qty + $addonQty,
                    'unit_price' => $addonPrice,
                    'unit_cost' => $addonCost,
                ]);
            } else {
                $cart->items()->create([
                    'menu_id' => $addonMenu->id,
                    'qty' => $addonQty,
                    'unit_price' => $addonPrice,
                    'unit_cost' => $addonCost,
                ]);
            }

            $data['notes'] = trim(implode(' | ', array_filter([
                (string) ($data['notes'] ?? ''),
                sprintf('%s x%d', $addonName, $addonQty),
            ])));
        } elseif (!empty($data['addon_menu_id'])) {
            $this->appendMenuToCart($userId, (int) $data['addon_menu_id'], (int) ($data['addon_qty'] ?? 1));
        }

        $cartModel = $this->cartModel($userId);
        $cartItems = $cartModel->items()->with('menu')->get();
        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Pesanan masih kosong. Pilih menu terlebih dahulu.');
        }

        $code = 'TRX-' . str_pad((string) ((int) SaleTransaction::query()->count() + 1), 4, '0', STR_PAD_LEFT);
        while (SaleTransaction::query()->where('code', $code)->exists()) {
            $code = 'TRX-' . str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
        }

        DB::transaction(function () use ($data, $cartModel, $cartItems, $code): void {
            $totalAmount = $cartItems->sum(fn ($item) => (float) $item->unit_price * (int) $item->qty);
            $totalCost = $cartItems->sum(fn ($item) => (float) $item->unit_cost * (int) $item->qty);

            $sale = SaleTransaction::query()->create([
                'code' => $code,
                'branch_id' => 1,
                'table_id' => $data['table_id'] ?? null,
                'sold_at' => now(),
                'total_amount' => $totalAmount,
                'total_cost' => $totalCost,
                'notes' => $data['notes'] ?? null,
                'status' => SaleTransaction::STATUS_PENDING,
            ]);

            foreach ($cartItems as $item) {
                $sale->items()->create([
                    'menu_id' => $item->menu_id,
                    'qty' => $item->qty,
                    'unit_price' => $item->unit_price,
                    'unit_cost' => $item->unit_cost,
                    'line_total' => (float) $item->unit_price * (int) $item->qty,
                    'line_cost' => (float) $item->unit_cost * (int) $item->qty,
                ]);
            }

            $cartModel->items()->delete();
        });

        return redirect()->route('cashier.orders.index')->with('success', "Pesanan {$code} berhasil dibuat.");
    }

    private function cartModel(int $userId): CashierCart
    {
        return CashierCart::query()->firstOrCreate(['user_id' => $userId]);
    }

    private function appendMenuToCart(int $userId, int $menuId, int $qty): void
    {
        $menu = Menu::query()->findOrFail($menuId);
        $cart = $this->cartModel($userId);
        $item = $cart->items()->where('menu_id', $menu->id)->first();

        if ($item) {
            $item->update([
                'qty' => (int) $item->qty + $qty,
                'unit_price' => (float) $menu->selling_price,
                'unit_cost' => (float) $menu->cost_price,
            ]);

            return;
        }

        $cart->items()->create([
            'menu_id' => $menu->id,
            'qty' => $qty,
            'unit_price' => (float) $menu->selling_price,
            'unit_cost' => (float) $menu->cost_price,
        ]);
    }

    private function cartRows(int $userId): array
    {
        $cart = $this->cartModel($userId);
        $items = $cart->items()->with('menu')->get();

        $rows = [];
        /** @var CashierCartItem $item */
        foreach ($items as $item) {
            $lineTotal = (float) $item->unit_price * (int) $item->qty;
            $rows[(string) $item->menu_id] = [
                'menu_id' => $item->menu_id,
                'code' => $item->menu?->code,
                'name' => $item->menu?->name ?? 'Menu',
                'unit_price' => (float) $item->unit_price,
                'unit_cost' => (float) $item->unit_cost,
                'qty' => (int) $item->qty,
                'line_total' => $lineTotal,
                'is_addon' => strtolower((string) $item->menu?->code) === 'a01',
            ];
        }

        return $rows;
    }
}

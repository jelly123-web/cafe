<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\DiningTable;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\SaleTransaction;
use App\Models\SaleTransactionItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PublicTableMenuController extends Controller
{
    private function toppingOptions(): array
    {
        return [
            [
                'key' => 'extra_telur',
                'name' => 'Extra Telur',
                'price' => 3000,
                'cost' => 1000,
            ],
        ];
    }

    private function liveOrderPayload(DiningTable $table): array
    {
        $orders = SaleTransaction::query()
            ->with('items.menu')
            ->where('table_id', $table->id)
            ->whereDate('sold_at', now()->toDateString())
            ->whereIn('status', [
                SaleTransaction::STATUS_PENDING,
                SaleTransaction::STATUS_PROCESSING,
                SaleTransaction::STATUS_READY,
            ])
            ->orderByDesc('sold_at')
            ->limit(8)
            ->get();

        $latestTs = optional($orders->first()?->updated_at ?? $orders->first()?->sold_at)?->timestamp ?? 0;

        return [
            'count' => $orders->count(),
            'latest_ts' => $latestTs,
            'html' => view('public.partials.table-order-status', [
                'orders' => $orders,
            ])->render(),
        ];
    }

    public function show(DiningTable $table): View
    {
        abort_unless($table->is_active, 404);

        $categories = MenuCategory::query()
            ->with(['menus' => fn ($query) => $query->where('is_sold_out', false)->orderBy('name')])
            ->orderBy('name')
            ->get();

        $menus = Menu::query()
            ->with('category')
            ->where('is_sold_out', false)
            ->where('code', '!=', 'A01')
            ->orderBy('name')
            ->get();

        return view('public.table-menu', [
            'table' => $table,
            'categories' => $categories,
            'menus' => $menus,
            'toppings' => $this->toppingOptions(),
            'orders' => SaleTransaction::query()
                ->with('items.menu')
                ->where('table_id', $table->id)
                ->whereDate('sold_at', now()->toDateString())
                ->whereIn('status', [
                    SaleTransaction::STATUS_PENDING,
                    SaleTransaction::STATUS_PROCESSING,
                    SaleTransaction::STATUS_READY,
                ])
                ->orderByDesc('sold_at')
                ->limit(8)
                ->get(),
        ]);
    }

    public function liveOrders(DiningTable $table): JsonResponse
    {
        abort_unless($table->is_active, 404);

        return response()->json($this->liveOrderPayload($table));
    }

    public function order(Request $request, DiningTable $table): RedirectResponse|JsonResponse
    {
        abort_unless($table->is_active, 404);

        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array'],
            'items.*.menu_id' => ['required', 'exists:menus,id'],
            'items.*.qty' => ['nullable', 'integer', 'min:0'],
            'items.*.addon_name' => ['nullable', 'string', 'max:100'],
            'items.*.addon_qty' => ['nullable', 'integer', 'min:0'],
            'items.*.addon_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.addon_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        $rawItems = collect($data['items'] ?? [])
            ->map(fn (array $row) => [
                'menu_id' => (int) ($row['menu_id'] ?? 0),
                'qty' => (int) ($row['qty'] ?? 0),
                'addon_name' => trim((string) ($row['addon_name'] ?? '')),
                'addon_qty' => (int) ($row['addon_qty'] ?? 0),
                'addon_price' => (float) ($row['addon_price'] ?? 0),
                'addon_cost' => (float) ($row['addon_cost'] ?? 0),
            ])
            ->filter(fn (array $row) => $row['qty'] > 0)
            ->values();

        if ($rawItems->isEmpty()) {
            return back()->withErrors(['items' => 'Pilih minimal 1 menu untuk dipesan.'])->withInput();
        }

        $menuMap = Menu::query()
            ->whereIn('id', $rawItems->pluck('menu_id')->all())
            ->where('is_sold_out', false)
            ->get()
            ->keyBy('id');

        if ($menuMap->count() !== $rawItems->count()) {
            return back()->withErrors(['items' => 'Sebagian menu tidak tersedia. Silakan refresh halaman.'])->withInput();
        }

        $branchId = (int) (Branch::query()->orderBy('id')->value('id') ?? 1);

        DB::transaction(function () use ($table, $rawItems, $menuMap, $data, $branchId): void {
            $totalAmount = 0.0;
            $totalCost = 0.0;
            $noteParts = [];

            foreach ($rawItems as $row) {
                $menu = $menuMap->get($row['menu_id']);
                $lineAddonAmount = max(0, $row['addon_qty']) * max(0, $row['addon_price']);
                $lineAddonCost = max(0, $row['addon_qty']) * max(0, $row['addon_cost']);
                $totalAmount += ((float) $menu->selling_price * $row['qty']) + $lineAddonAmount;
                $totalCost += ((float) $menu->cost_price * $row['qty']) + $lineAddonCost;

                if ($row['addon_name'] !== '' && $row['addon_qty'] > 0) {
                    $noteParts[] = sprintf(
                        '%s x%d + %s x%d',
                        $menu->name,
                        $row['qty'],
                        $row['addon_name'],
                        $row['addon_qty']
                    );
                } else {
                    $noteParts[] = sprintf('%s x%d', $menu->name, $row['qty']);
                }
            }

            $code = $this->nextCode();
            $notes = trim((string) ($data['notes'] ?? ''));
            $addonSummary = trim(implode(' | ', $noteParts));
            $combinedNotes = trim(implode(' || ', array_filter([$notes, $addonSummary])));

            $trxData = [
                'code' => $code,
                'branch_id' => $branchId,
                'table_id' => $table->id,
                'sold_at' => now(),
                'total_amount' => $totalAmount,
                'total_cost' => $totalCost,
                'notes' => $combinedNotes !== '' ? $combinedNotes : null,
            ];

            if (Schema::hasColumn('sale_transactions', 'status')) {
                $trxData['status'] = SaleTransaction::STATUS_PENDING;
            }

            $sale = SaleTransaction::query()->create($trxData);

            foreach ($rawItems as $row) {
                $menu = $menuMap->get($row['menu_id']);
                $lineAddonAmount = max(0, $row['addon_qty']) * max(0, $row['addon_price']);
                $lineAddonCost = max(0, $row['addon_qty']) * max(0, $row['addon_cost']);
                SaleTransactionItem::query()->create([
                    'sale_transaction_id' => $sale->id,
                    'menu_id' => $menu->id,
                    'qty' => $row['qty'],
                    'unit_price' => $menu->selling_price,
                    'unit_cost' => $menu->cost_price,
                    'line_total' => ((float) $menu->selling_price * $row['qty']) + $lineAddonAmount,
                    'line_cost' => ((float) $menu->cost_price * $row['qty']) + $lineAddonCost,
                ]);
            }
        });

        $payload = $this->liveOrderPayload($table);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => 'Pesanan berhasil dikirim ke kasir/dapur.',
                'count' => $payload['count'],
                'latest_ts' => $payload['latest_ts'],
                'html' => $payload['html'],
            ]);
        }

        return redirect()
            ->route('tables.show', $table->qr_token)
            ->with('success', 'Pesanan berhasil dikirim ke kasir/dapur.');
    }

    private function nextCode(): string
    {
        $last = SaleTransaction::query()->orderByDesc('id')->value('code');
        if (! is_string($last) || ! preg_match('/^TRX-(\d+)$/', $last, $m)) {
            return 'TRX-0001';
        }

        return 'TRX-' . str_pad((string) ((int) $m[1] + 1), 4, '0', STR_PAD_LEFT);
    }
}

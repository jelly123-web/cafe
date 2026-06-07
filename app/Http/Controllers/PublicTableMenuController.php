<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\DiningTable;
use App\Models\FoodPackage;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Promo;
use App\Models\SaleTransaction;
use App\Models\SaleTransactionItem;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PublicTableMenuController extends Controller
{
    private function brandPayload(): array
    {
        $brandName = SystemSetting::getValue('cafe_name', config('app.name', 'Cafe'));
        $brandLogo = SystemSetting::getValue('cafe_logo');
        $heroTag = SystemSetting::getValue('hero_banner_tag', 'PROMO SPESIAL HARI INI');
        $heroTitle = SystemSetting::getValue('hero_banner_title', 'Diskon 50% Untuk Semua Paket Nasi Goreng');
        $heroDesc = SystemSetting::getValue('hero_banner_desc', 'Nikmati paket lengkap dengan harga setengah. Berlaku sampai pukul 23:59 malam ini.');
        $heroButtonText = SystemSetting::getValue('hero_banner_button_text', 'Lihat Promo');
        $heroImage = SystemSetting::getValue('hero_banner_image');

        return [
            'name' => $brandName,
            'logo_url' => $brandLogo ? '/brand-logo?v=' . rawurlencode($brandLogo) : null,
            'hero' => [
                'tag' => $heroTag,
                'title' => $heroTitle,
                'desc' => $heroDesc,
                'button_text' => $heroButtonText,
                'image_url' => $heroImage ? Storage::disk('public')->url($heroImage) : null,
            ],
        ];
    }

    private function activePromos()
    {
        return Promo::query()
            ->with([
                'menus:id,name,selling_price,image_path,code',
                'foodPackages:id,name,selling_price,image_path',
            ])
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('start_at')
                    ->orWhereDate('start_at', '<=', today());
            })
            ->where(function ($query) {
                $query->whereNull('end_at')
                    ->orWhereDate('end_at', '>=', today());
            })
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();
    }

    private function resolvePromoPrice(float $basePrice, Promo $promo): ?float
    {
        if ($promo->min_spend > 0) {
            return null;
        }

        if ($promo->type === 'percentage') {
            return max(0, $basePrice - ($basePrice * ((float) $promo->value / 100)));
        }

        if ($promo->type === 'fixed_discount') {
            return max(0, $basePrice - (float) $promo->value);
        }

        return null;
    }

    private function promoPeriodLabel(Promo $promo): string
    {
        if ($promo->start_at && $promo->end_at) {
            return $promo->start_at->format('d/m/Y') . ' - ' . $promo->end_at->format('d/m/Y');
        }

        if ($promo->start_at) {
            return 'Mulai ' . $promo->start_at->format('d/m/Y');
        }

        if ($promo->end_at) {
            return 'Sampai ' . $promo->end_at->format('d/m/Y');
        }

        return 'Tanpa batas tanggal';
    }

    private function buildSpecificPromoMeta(float $basePrice, Promo $promo): ?array
    {
        if ($promo->applies_to !== 'specific') {
            return null;
        }

        if (in_array($promo->type, ['percentage', 'fixed_discount'], true)) {
            $promoPrice = $this->resolvePromoPrice($basePrice, $promo);
            if ($promoPrice === null) {
                return null;
            }

            return [
                'id' => $promo->id,
                'name' => $promo->name,
                'type' => $promo->type,
                'value' => (float) $promo->value,
                'buy_qty' => (int) $promo->buy_qty,
                'get_qty' => (int) $promo->get_qty,
                'unit_price' => $promoPrice,
                'period_label' => $this->promoPeriodLabel($promo),
            ];
        }

        if ($promo->type === 'buy_x_get_y') {
            return [
                'id' => $promo->id,
                'name' => $promo->name,
                'type' => $promo->type,
                'value' => (float) $promo->value,
                'buy_qty' => (int) $promo->buy_qty,
                'get_qty' => (int) $promo->get_qty,
                'unit_price' => $basePrice,
                'period_label' => $this->promoPeriodLabel($promo),
            ];
        }

        return null;
    }

    private function calculateSpecificLinePricing(int $qty, float $basePrice, ?array $promoMeta): array
    {
        $baseTotal = $basePrice * $qty;
        $discount = 0.0;

        if ($promoMeta) {
            if (in_array($promoMeta['type'] ?? null, ['percentage', 'fixed_discount'], true)) {
                $discount = max(0, $basePrice - (float) ($promoMeta['unit_price'] ?? $basePrice)) * $qty;
            } elseif (($promoMeta['type'] ?? null) === 'buy_x_get_y') {
                $buyQty = max(0, (int) ($promoMeta['buy_qty'] ?? 0));
                $getQty = max(0, (int) ($promoMeta['get_qty'] ?? 0));
                if ($buyQty > 0 && $getQty > 0) {
                    $bundleSize = $buyQty + $getQty;
                    $freeUnits = intdiv($qty, $bundleSize) * $getQty;
                    $discount = $freeUnits * $basePrice;
                }
            }
        }

        $discount = min($discount, $baseTotal);

        return [
            'base_total' => $baseTotal,
            'discount' => $discount,
            'line_total' => max(0, $baseTotal - $discount),
        ];
    }

    private function resolveGlobalOrderPromo(float $subtotal, Collection $promos): ?array
    {
        $bestPromo = null;
        $bestDiscount = 0.0;

        foreach ($promos as $promo) {
            if ($promo->applies_to !== 'all' || !in_array($promo->type, ['percentage', 'fixed_discount'], true)) {
                continue;
            }

            $minSpend = (float) $promo->min_spend;
            if ($subtotal < $minSpend) {
                continue;
            }

            $discount = $promo->type === 'percentage'
                ? $subtotal * ((float) $promo->value / 100)
                : (float) $promo->value;

            $discount = min($discount, $subtotal);

            if ($discount > $bestDiscount) {
                $bestDiscount = $discount;
                $bestPromo = [
                    'id' => $promo->id,
                    'name' => $promo->name,
                    'type' => $promo->type,
                    'value' => (float) $promo->value,
                    'min_spend' => $minSpend,
                    'discount' => $discount,
                    'period_label' => $this->promoPeriodLabel($promo),
                ];
            }
        }

        return $bestPromo;
    }

    private function decorateItemsWithPromos(Collection $menus, Collection $packages, Collection $promos): array
    {
        $menuPromoMap = [];
        $packagePromoMap = [];

        foreach ($menus as $menu) {
            $basePrice = (float) $menu->selling_price;
            $bestPrice = $basePrice;
            $bestLabel = null;
            $bestMeta = null;

            foreach ($promos as $promo) {
                $applies = $promo->applies_to === 'specific' && $promo->menus->contains('id', $menu->id);
                if (! $applies) {
                    continue;
                }

                $promoMeta = $this->buildSpecificPromoMeta($basePrice, $promo);
                if (! $promoMeta) {
                    continue;
                }

                if (in_array($promo->type, ['percentage', 'fixed_discount'], true)) {
                    $promoPrice = (float) $promoMeta['unit_price'];
                    if ($promoPrice >= $bestPrice) {
                        continue;
                    }

                    $bestPrice = $promoPrice;
                    $bestLabel = $promo->name;
                    $bestMeta = $promoMeta;
                    continue;
                }

                if ($promo->type === 'buy_x_get_y' && $bestMeta === null) {
                    $bestLabel = $promo->name;
                    $bestMeta = $promoMeta;
                }
            }

            $menu->setAttribute('original_price', $basePrice);
            $menu->setAttribute('display_price', $bestPrice);
            $menu->setAttribute('has_promo_price', $bestPrice < $basePrice);
            $menu->setAttribute('promo_label', $bestLabel);
            $menu->setAttribute('promo_meta', $bestMeta);
            $menuPromoMap[$menu->id] = [
                'price' => $bestPrice,
                'original_price' => $basePrice,
                'promo_meta' => $bestMeta,
            ];
        }

        foreach ($packages as $package) {
            $basePrice = (float) $package->selling_price;
            $bestPrice = $basePrice;
            $bestLabel = null;
            $bestMeta = null;

            foreach ($promos as $promo) {
                $applies = $promo->applies_to === 'specific' && $promo->foodPackages->contains('id', $package->id);
                if (! $applies) {
                    continue;
                }

                $promoMeta = $this->buildSpecificPromoMeta($basePrice, $promo);
                if (! $promoMeta) {
                    continue;
                }

                if (in_array($promo->type, ['percentage', 'fixed_discount'], true)) {
                    $promoPrice = (float) $promoMeta['unit_price'];
                    if ($promoPrice >= $bestPrice) {
                        continue;
                    }

                    $bestPrice = $promoPrice;
                    $bestLabel = $promo->name;
                    $bestMeta = $promoMeta;
                    continue;
                }

                if ($promo->type === 'buy_x_get_y' && $bestMeta === null) {
                    $bestLabel = $promo->name;
                    $bestMeta = $promoMeta;
                }
            }

            $package->setAttribute('original_price', $basePrice);
            $package->setAttribute('display_price', $bestPrice);
            $package->setAttribute('has_promo_price', $bestPrice < $basePrice);
            $package->setAttribute('promo_label', $bestLabel);
            $package->setAttribute('promo_meta', $bestMeta);
            $packagePromoMap[$package->id] = [
                'price' => $bestPrice,
                'original_price' => $basePrice,
                'promo_meta' => $bestMeta,
            ];
        }

        return [$menus, $packages, $menuPromoMap, $packagePromoMap];
    }

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
            ->with(['items.menu', 'items.foodPackage'])
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

    private function liveMenuPayload(): array
    {
        $promos = $this->activePromos();
        $brand = $this->brandPayload();
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

        $packages = FoodPackage::query()
            ->with(['menus' => fn ($query) => $query->where('is_sold_out', false)->orderBy('name')])
            ->orderBy('name')
            ->get();

        [$menus, $packages] = $this->decorateItemsWithPromos($menus, $packages, $promos);

        $latestMenuTs = Menu::query()->max('updated_at');
        $latestCategoryTs = MenuCategory::query()->max('updated_at');
        $latestPackageTs = FoodPackage::query()->max('updated_at');
        $latestPromoTs = Promo::query()->max('updated_at');
        $latestSettingTs = SystemSetting::query()->max('updated_at');
        $menuCount = Menu::query()->where('is_sold_out', false)->where('code', '!=', 'A01')->count();
        $categoryCount = MenuCategory::query()->count();
        $packageCount = FoodPackage::query()->count();
        $promoCount = $promos->count();

        return [
            'latest_ts' => max(
                strtotime((string) $latestMenuTs) ?: 0,
                strtotime((string) $latestCategoryTs) ?: 0,
                strtotime((string) $latestPackageTs) ?: 0,
                strtotime((string) $latestPromoTs) ?: 0,
                strtotime((string) $latestSettingTs) ?: 0
            ),
            'settings_ts' => strtotime((string) $latestSettingTs) ?: 0,
            'menu_count' => $menuCount,
            'category_count' => $categoryCount,
            'package_count' => $packageCount,
            'promo_count' => $promoCount,
            'brand' => $brand,
            'promos_html' => view('public.partials.promo-strip', [
                'promos' => $promos,
            ])->render(),
            'categories_html' => view('public.partials.menu-categories', [
                'categories' => $categories,
                'activeFilter' => 'all',
            ])->render(),
            'packages_html' => view('public.partials.package-grid', [
                'packages' => $packages,
            ])->render(),
            'menus_html' => view('public.partials.menu-grid', [
                'menus' => $menus,
            ])->render(),
        ];
    }

    public function show(DiningTable $table): View
    {
        abort_unless($table->is_active, 404);

        $promos = $this->activePromos();
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

        $packages = FoodPackage::query()
            ->with(['menus' => fn ($query) => $query->where('is_sold_out', false)->orderBy('name')])
            ->orderBy('name')
            ->get();

        [$menus, $packages, $menuPromoMap, $packagePromoMap] = $this->decorateItemsWithPromos($menus, $packages, $promos);

        $liveMenu = $this->liveMenuPayload();

        return view('public.table-menu', [
            'table' => $table,
            'promos' => $promos,
            'categories' => $categories,
            'packages' => $packages,
            'menus' => $menus,
            'initial_menu_ts' => $liveMenu['latest_ts'],
            'initial_settings_ts' => $liveMenu['settings_ts'] ?? 0,
            'initial_menu_count' => $liveMenu['menu_count'],
            'initial_category_count' => $liveMenu['category_count'],
            'initial_package_count' => $liveMenu['package_count'],
            'initial_promo_count' => $liveMenu['promo_count'],
            'toppings' => $this->toppingOptions(),
            'menuPromoMap' => $menuPromoMap,
            'packagePromoMap' => $packagePromoMap,
            'orders' => SaleTransaction::query()
                ->with(['items.menu', 'items.foodPackage'])
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

    public function liveMenus(DiningTable $table): JsonResponse
    {
        abort_unless($table->is_active, 404);

        return response()->json($this->liveMenuPayload());
    }

    public function liveOrders(DiningTable $table): JsonResponse
    {
        abort_unless($table->is_active, 404);

        return response()->json($this->liveOrderPayload($table));
    }

    public function order(Request $request, DiningTable $table): RedirectResponse|JsonResponse
    {
        abort_unless($table->is_active, 404);

        $normalizedItems = collect($request->input('items', []))
            ->map(function ($row): array {
                return [
                    'menu_id' => (int) ($row['menu_id'] ?? 0),
                    'qty' => (int) ($row['qty'] ?? 0),
                    'addon_name' => trim((string) ($row['addon_name'] ?? '')),
                    'addon_qty' => (int) ($row['addon_qty'] ?? 0),
                    'addon_price' => (float) ($row['addon_price'] ?? 0),
                    'addon_cost' => (float) ($row['addon_cost'] ?? 0),
                ];
            })
            ->filter(fn (array $row) => $row['menu_id'] > 0 && $row['qty'] > 0)
            ->values()
            ->all();

        $normalizedPackages = collect($request->input('packages', []))
            ->map(function ($row): array {
                return [
                    'package_id' => (int) ($row['package_id'] ?? 0),
                    'qty' => (int) ($row['qty'] ?? 0),
                ];
            })
            ->filter(fn (array $row) => $row['package_id'] > 0 && $row['qty'] > 0)
            ->values()
            ->all();

        $request->merge([
            'items' => $normalizedItems,
            'packages' => $normalizedPackages,
        ]);

        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['nullable', 'array'],
            'items.*.menu_id' => ['required', 'integer', 'min:1', 'exists:menus,id'],
            'items.*.qty' => ['nullable', 'integer', 'min:0'],
            'items.*.addon_name' => ['nullable', 'string', 'max:100'],
            'items.*.addon_qty' => ['nullable', 'integer', 'min:0'],
            'items.*.addon_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.addon_cost' => ['nullable', 'numeric', 'min:0'],
            'packages' => ['nullable', 'array'],
            'packages.*.package_id' => ['required', 'integer', 'min:1', 'exists:food_packages,id'],
            'packages.*.qty' => ['required', 'integer', 'min:1'],
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

        $rawPackages = collect($data['packages'] ?? [])
            ->map(fn (array $row) => [
                'package_id' => (int) ($row['package_id'] ?? 0),
                'qty' => (int) ($row['qty'] ?? 0),
            ])
            ->filter(fn (array $row) => $row['qty'] > 0)
            ->values();

        if ($rawItems->isEmpty() && $rawPackages->isEmpty()) {
            return $this->jsonOrderError($request, 'Pilih minimal 1 menu atau paket untuk dipesan.');
        }

        $menuMap = Menu::query()
            ->whereIn('id', $rawItems->pluck('menu_id')->all())
            ->where('is_sold_out', false)
            ->get()
            ->keyBy('id');

        if ($menuMap->count() !== $rawItems->count()) {
            return $this->jsonOrderError($request, 'Sebagian menu tidak tersedia. Silakan refresh halaman.');
        }

        $packageMap = FoodPackage::query()
            ->whereIn('id', $rawPackages->pluck('package_id')->all())
            ->get()
            ->keyBy('id');

        if ($packageMap->count() !== $rawPackages->count()) {
            return $this->jsonOrderError($request, 'Sebagian paket tidak tersedia. Silakan refresh halaman.');
        }

        $activePromos = $this->activePromos();
        [, , $menuPromoMap, $packagePromoMap] = $this->decorateItemsWithPromos($menuMap->values(), $packageMap->values(), $activePromos);

        $branchId = (int) (Branch::query()->orderBy('id')->value('id') ?? 1);

        DB::transaction(function () use ($table, $rawItems, $rawPackages, $menuMap, $packageMap, $data, $branchId, $activePromos, $menuPromoMap, $packagePromoMap): void {
            $totalAmount = 0.0;
            $totalCost = 0.0;
            $noteParts = [];
            $specificDiscountTotal = 0.0;

            // Process regular menus
            foreach ($rawItems as $row) {
                $menu = $menuMap->get($row['menu_id']);
                $baseUnitPrice = (float) $menu->selling_price;
                $promoMeta = $menuPromoMap[$menu->id]['promo_meta'] ?? null;
                $linePricing = $this->calculateSpecificLinePricing($row['qty'], $baseUnitPrice, $promoMeta);
                $unitPrice = (float) ($menuPromoMap[$menu->id]['price'] ?? $baseUnitPrice);
                $lineAddonAmount = max(0, $row['addon_qty']) * max(0, $row['addon_price']);
                $lineAddonCost = max(0, $row['addon_qty']) * max(0, $row['addon_cost']);
                $totalAmount += $linePricing['line_total'] + $lineAddonAmount;
                $totalCost += ((float) $menu->cost_price * $row['qty']) + $lineAddonCost;
                $specificDiscountTotal += $linePricing['discount'];

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

            // Process packages
            foreach ($rawPackages as $row) {
                $package = $packageMap->get($row['package_id']);
                $baseUnitPrice = (float) $package->selling_price;
                $promoMeta = $packagePromoMap[$package->id]['promo_meta'] ?? null;
                $linePricing = $this->calculateSpecificLinePricing($row['qty'], $baseUnitPrice, $promoMeta);
                $unitPrice = (float) ($packagePromoMap[$package->id]['price'] ?? $baseUnitPrice);
                $totalAmount += $linePricing['line_total'];
                $totalCost += (float) $package->cost_price * $row['qty'];
                $specificDiscountTotal += $linePricing['discount'];
                $noteParts[] = sprintf('%s x%d', $package->name, $row['qty']);
            }

            $globalPromo = $this->resolveGlobalOrderPromo($totalAmount, $activePromos);
            $globalDiscount = (float) ($globalPromo['discount'] ?? 0);
            $grandTotal = max(0, $totalAmount - $globalDiscount);

            $code = $this->nextCode();
            $notes = trim((string) ($data['notes'] ?? ''));
            $addonSummary = trim(implode(' | ', $noteParts));
            $promoSummary = [];
            if ($specificDiscountTotal > 0) {
                $promoSummary[] = 'Diskon item Rp ' . number_format($specificDiscountTotal, 0, ',', '.');
            }
            if ($globalPromo) {
                $promoSummary[] = $globalPromo['name'] . ' -Rp ' . number_format($globalDiscount, 0, ',', '.');
            }
            $combinedNotes = trim(implode(' || ', array_filter([$notes, $addonSummary, implode(' | ', $promoSummary)])));

            $trxData = [
                'code' => $code,
                'branch_id' => $branchId,
                'table_id' => $table->id,
                'sold_at' => now(),
                'total_amount' => $grandTotal,
                'total_cost' => $totalCost,
                'notes' => $combinedNotes !== '' ? $combinedNotes : null,
            ];

            if (Schema::hasColumn('sale_transactions', 'status')) {
                $trxData['status'] = SaleTransaction::STATUS_PENDING;
            }

            $sale = SaleTransaction::query()->create($trxData);

            // Record menu items
            foreach ($rawItems as $row) {
                $menu = $menuMap->get($row['menu_id']);
                $baseUnitPrice = (float) $menu->selling_price;
                $promoMeta = $menuPromoMap[$menu->id]['promo_meta'] ?? null;
                $linePricing = $this->calculateSpecificLinePricing($row['qty'], $baseUnitPrice, $promoMeta);
                $unitPrice = (float) ($menuPromoMap[$menu->id]['price'] ?? $baseUnitPrice);
                $lineAddonAmount = max(0, $row['addon_qty']) * max(0, $row['addon_price']);
                $lineAddonCost = max(0, $row['addon_qty']) * max(0, $row['addon_cost']);
                SaleTransactionItem::query()->create([
                    'sale_transaction_id' => $sale->id,
                    'menu_id' => $menu->id,
                    'qty' => $row['qty'],
                    'unit_price' => $unitPrice,
                    'unit_cost' => $menu->cost_price,
                    'line_total' => $linePricing['line_total'] + $lineAddonAmount,
                    'line_cost' => ((float) $menu->cost_price * $row['qty']) + $lineAddonCost,
                ]);
            }

            // Record package items
            foreach ($rawPackages as $row) {
                $package = $packageMap->get($row['package_id']);
                $baseUnitPrice = (float) $package->selling_price;
                $promoMeta = $packagePromoMap[$package->id]['promo_meta'] ?? null;
                $linePricing = $this->calculateSpecificLinePricing($row['qty'], $baseUnitPrice, $promoMeta);
                $unitPrice = (float) ($packagePromoMap[$package->id]['price'] ?? $baseUnitPrice);
                SaleTransactionItem::query()->create([
                    'sale_transaction_id' => $sale->id,
                    'food_package_id' => $package->id,
                    'qty' => $row['qty'],
                    'unit_price' => $unitPrice,
                    'unit_cost' => $package->cost_price,
                    'line_total' => $linePricing['line_total'],
                    'line_cost' => (float) $package->cost_price * $row['qty'],
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
        $lastCode = SaleTransaction::query()
            ->where('code', 'like', 'TRX-%')
            ->orderByRaw('CAST(SUBSTRING(code, 5) AS UNSIGNED) DESC')
            ->value('code');

        $lastNumber = 0;
        if ($lastCode && preg_match('/^TRX-(\d+)$/', (string) $lastCode, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        $nextNumber = $lastNumber + 1;
        
        do {
            $code = 'TRX-' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
            $exists = SaleTransaction::query()->where('code', $code)->exists();
            if ($exists) {
                $nextNumber++;
            }
        } while ($exists);

        return $code;
    }

    private function jsonOrderError(Request $request, string $message, int $status = 422): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => false,
                'message' => $message,
            ], $status);
        }

        return back()->withErrors(['items' => $message])->withInput();
    }
}

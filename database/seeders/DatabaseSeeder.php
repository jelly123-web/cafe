<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\CashFlowEntry;
use App\Models\DiningTable;
use App\Models\Employee;
use App\Models\FoodPackage;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Payroll;
use App\Models\SaleTransaction;
use App\Models\SaleTransactionItem;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        DB::transaction(function () {
            $this->seedSystemSettings();
            $users = $this->seedUsers();
            $branches = $this->seedBranches();
            $tables = $this->seedTables();
            $categories = $this->seedMenuCategories();
            $menus = $this->seedMenusAndPackagesIfEmpty($categories);
            $this->cleanupLegacyEmployeeSeedData();
            $employees = $this->seedEmployees();
            $this->seedPayrolls($employees);
            $inventoryItems = $this->seedInventory();
            $this->seedInventoryMovements($inventoryItems, $users);
            $this->seedSales($branches, $tables, $menus);
            $this->seedCashFlow($users);
        });
    }

    private function seedSystemSettings(): void
    {
        $settings = [
            'cafe_name' => 'Cafe Serba',
            'cafe_logo' => null,
            'cafe_phone' => '+62 812-3456-7890',
        ];

        foreach ($settings as $key => $value) {
            SystemSetting::setValue($key, $value);
        }
    }

    private function seedUsers(): array
    {
        $roles = [
            'superadmin' => 'Super Admin',
            'admin' => 'Admin Cafe',
            'kasir' => 'Kasir Utama',
            'kitchen' => 'Tim Dapur',
            'inventory' => 'Admin Gudang',
            'leader_cashier' => 'Leader Kasir',
        ];

        $users = [];

        foreach ($roles as $username => $name) {
            $users[$username] = User::updateOrCreate(
                ['username' => $username],
                [
                    'name' => $name,
                    'email' => "{$username}@cafe.local",
                    'password' => $username,
                    'role' => $username,
                    'is_active' => true,
                    'permissions' => $this->getPermissionsForRole($username),
                    'email_verified_at' => now(),
                ]
            );
        }

        return $users;
    }

    private function seedBranches(): array
    {
        $branch = Branch::updateOrCreate(
            ['code' => 'PST'],
            ['name' => 'Cabang Pusat']
        );

        return [
            'PST' => $branch,
        ];
    }

    private function seedTables(): array
    {
        $existingTables = DiningTable::query()
            ->orderByRaw('CAST(number AS UNSIGNED) ASC')
            ->get()
            ->keyBy('number')
            ->all();

        if (! empty($existingTables)) {
            return $existingTables;
        }

        $table = DiningTable::create([
            'number' => '1',
            'name' => 'Meja 1',
            'qr_token' => (string) Str::uuid(),
            'is_active' => true,
        ]);

        return [
            '1' => $table,
        ];
    }

    private function seedMenuCategories(): array
    {
        $categories = [
            'makanan' => 'makanan',
            'minuman' => 'minuman',
            'paket' => 'paket',
        ];

        $out = [];

        foreach ($categories as $name => $slug) {
            $out[$name] = MenuCategory::updateOrCreate(
                ['name' => $name],
                ['slug' => $slug]
            );
        }

        return $out;
    }

    private function seedMenus(array $categories): array
    {
        $menus = [
            [
                'code' => 'K01',
                'name' => 'Es Kopi Susu Gula Aren',
                'selling_price' => 28000,
                'cost_price' => 12000,
                'category' => 'minuman',
            ],
            [
                'code' => 'K02',
                'name' => 'Cappuccino',
                'selling_price' => 30000,
                'cost_price' => 13000,
                'category' => 'minuman',
            ],
            [
                'code' => 'K03',
                'name' => 'Americano',
                'selling_price' => 24000,
                'cost_price' => 10000,
                'category' => 'minuman',
            ],
            [
                'code' => 'N01',
                'name' => 'Matcha Latte',
                'selling_price' => 32000,
                'cost_price' => 14000,
                'category' => 'minuman',
            ],
            [
                'code' => 'N02',
                'name' => 'Chocolate Latte',
                'selling_price' => 32000,
                'cost_price' => 14000,
                'category' => 'minuman',
            ],
            [
                'code' => 'T01',
                'name' => 'Teh Tarik',
                'selling_price' => 18000,
                'cost_price' => 7000,
                'category' => 'minuman',
            ],
            [
                'code' => 'T02',
                'name' => 'Es Teh Lemon',
                'selling_price' => 17000,
                'cost_price' => 6000,
                'category' => 'minuman',
            ],
            [
                'code' => 'F01',
                'name' => 'Nasi Goreng Cafe',
                'selling_price' => 35000,
                'cost_price' => 18000,
                'category' => 'makanan',
            ],
            [
                'code' => 'F02',
                'name' => 'Chicken Katsu Rice',
                'selling_price' => 38000,
                'cost_price' => 20000,
                'category' => 'makanan',
            ],
            [
                'code' => 'F03',
                'name' => 'Chicken Teriyaki Rice',
                'selling_price' => 38000,
                'cost_price' => 20000,
                'category' => 'makanan',
            ],
            [
                'code' => 'S01',
                'name' => 'Kentang Goreng',
                'selling_price' => 22000,
                'cost_price' => 9000,
                'category' => 'makanan',
            ],
            [
                'code' => 'S02',
                'name' => 'Cireng Bumbu Rujak',
                'selling_price' => 20000,
                'cost_price' => 8000,
                'category' => 'makanan',
            ],
            [
                'code' => 'A01',
                'name' => 'Extra Telur',
                'selling_price' => 3000,
                'cost_price' => 1000,
                'category' => 'makanan',
            ],
        ];

        $out = [];

        foreach ($menus as $menuData) {
            $category = $menuData['category'] ? ($categories[$menuData['category']] ?? null) : null;

            $out[$menuData['code']] = Menu::updateOrCreate(
                ['code' => $menuData['code']],
                [
                    'menu_category_id' => $category?->id,
                    'name' => $menuData['name'],
                    'selling_price' => $menuData['selling_price'],
                    'cost_price' => $menuData['cost_price'],
                    'image_path' => null,
                    'is_sold_out' => false,
                ]
            );
        }

        return $out;
    }

    private function seedMenusAndPackagesIfEmpty(array $categories): array
    {
        if (Menu::query()->where('code', '!=', 'A01')->exists() || FoodPackage::query()->exists()) {
            return Menu::query()->where('code', '!=', 'A01')->get()->keyBy('code')->all();
        }

        $menus = $this->seedMenus($categories);
        $this->seedFoodPackages($categories, $menus);

        return $menus;
    }

    private function seedFoodPackages(array $categories, array $menus): array
    {
        $packages = [
            [
                'code' => 'PKG-KELUARGA',
                'name' => 'Paket Keluarga',
                'selling_price' => 125000,
                'cost_price' => 63000,
                'notes' => 'Cocok untuk 3-4 orang.',
                'free_item' => 'Extra Telur',
                'menu_category' => 'paket',
                'menus' => [
                    'F01' => 2,
                    'S01' => 1,
                    'K01' => 2,
                ],
            ],
            [
                'code' => 'PKG-HEMAT2',
                'name' => 'Paket Hemat 2',
                'selling_price' => 79000,
                'cost_price' => 41000,
                'notes' => 'Menu hemat untuk dua orang.',
                'free_item' => 'Extra Telur',
                'menu_category' => 'paket',
                'menus' => [
                    'F02' => 1,
                    'K02' => 2,
                ],
            ],
            [
                'code' => 'PKG-COFFEE',
                'name' => 'Paket Coffee Break',
                'selling_price' => 68000,
                'cost_price' => 32000,
                'notes' => 'Paket minuman ringan untuk meeting atau istirahat.',
                'free_item' => null,
                'menu_category' => 'paket',
                'menus' => [
                    'K01' => 1,
                    'K03' => 1,
                    'S02' => 1,
                ],
            ],
        ];

        $out = [];

        foreach ($packages as $packageData) {
            $syncData = [];
            $descriptionLines = [];

            foreach ($packageData['menus'] as $menuCode => $qty) {
                $menu = $menus[$menuCode] ?? null;
                if (! $menu) {
                    continue;
                }

                $syncData[$menu->id] = ['quantity' => $qty];
                $descriptionLines[] = "({$qty}x) {$menu->name}";
            }

            $menuCategory = $categories[$packageData['menu_category']] ?? null;
            $menuCategoryId = $menuCategory?->id;

            $package = FoodPackage::updateOrCreate(
                ['code' => $packageData['code']],
                [
                    'name' => $packageData['name'],
                    'description' => implode(PHP_EOL, $descriptionLines),
                    'selling_price' => $packageData['selling_price'],
                    'cost_price' => $packageData['cost_price'],
                    'image_path' => null,
                    'notes' => $packageData['notes'],
                    'free_item' => $packageData['free_item'],
                    'menu_category_id' => $menuCategoryId,
                ]
            );

            $package->menus()->sync($syncData);
            $out[$packageData['code']] = $package;
        }

        return $out;
    }

    private function seedEmployees(): array
    {
        $existingEmployees = Employee::query()
            ->orderBy('name')
            ->get()
            ->keyBy('employee_code')
            ->all();

        return $existingEmployees;
    }

    private function seedPayrolls(array $employees): void
    {
        return;
    }

    private function cleanupLegacyEmployeeSeedData(): void
    {
        $legacyEmployees = [
            'EMP-001' => ['name' => 'Budi Santoso', 'position' => 'Kasir', 'phone' => '081234567801'],
            'EMP-002' => ['name' => 'Siti Aisyah', 'position' => 'Barista', 'phone' => '081234567802'],
            'EMP-003' => ['name' => 'Agus Pratama', 'position' => 'Kitchen Crew', 'phone' => '081234567803'],
        ];

        $employees = Employee::query()->get();
        if ($employees->isEmpty()) {
            return;
        }

        $legacyIds = [];
        foreach ($employees as $employee) {
            $expected = $legacyEmployees[$employee->employee_code] ?? null;
            if (! $expected) {
                continue;
            }

            if (
                $employee->name === $expected['name']
                && $employee->position === $expected['position']
                && $employee->phone === $expected['phone']
            ) {
                $legacyIds[] = $employee->id;
            }
        }

        if ($legacyIds === []) {
            return;
        }

        Payroll::query()->whereIn('employee_id', $legacyIds)->delete();
        Employee::query()->whereIn('id', $legacyIds)->delete();
    }

    private function seedInventory(): array
    {
        $categories = [
            ['name' => 'Bahan Kopi', 'type' => 'bahan', 'unit' => 'kg'],
            ['name' => 'Bahan Makanan', 'type' => 'bahan', 'unit' => 'kg'],
            ['name' => 'Kemasan', 'type' => 'barang', 'unit' => 'pcs'],
            ['name' => 'Peralatan', 'type' => 'barang', 'unit' => 'pcs'],
        ];

        $categoryModels = [];

        foreach ($categories as $categoryData) {
            $categoryModels[$categoryData['name']] = InventoryCategory::updateOrCreate(
                ['name' => $categoryData['name']],
                [
                    'type' => $categoryData['type'],
                    'unit' => $categoryData['unit'],
                ]
            );
        }

        $items = [
            [
                'name' => 'Kopi Arabica Beans',
                'category' => 'Bahan Kopi',
                'type' => 'bahan',
                'unit' => 'kg',
                'stock' => 12,
                'min_stock' => 5,
                'stock_good' => 10,
                'stock_less_good' => 1,
                'stock_damaged' => 1,
            ],
            [
                'name' => 'Susu UHT',
                'category' => 'Bahan Makanan',
                'type' => 'bahan',
                'unit' => 'liter',
                'stock' => 24,
                'min_stock' => 8,
                'stock_good' => 20,
                'stock_less_good' => 3,
                'stock_damaged' => 1,
            ],
            [
                'name' => 'Gula Aren',
                'category' => 'Bahan Makanan',
                'type' => 'bahan',
                'unit' => 'kg',
                'stock' => 10,
                'min_stock' => 4,
                'stock_good' => 9,
                'stock_less_good' => 1,
                'stock_damaged' => 0,
            ],
            [
                'name' => 'Cup 12oz',
                'category' => 'Kemasan',
                'type' => 'barang',
                'unit' => 'pcs',
                'stock' => 250,
                'min_stock' => 100,
                'stock_good' => 230,
                'stock_less_good' => 15,
                'stock_damaged' => 5,
            ],
            [
                'name' => 'Sendok Stainless',
                'category' => 'Peralatan',
                'type' => 'barang',
                'unit' => 'pcs',
                'stock' => 60,
                'min_stock' => 20,
                'stock_good' => 55,
                'stock_less_good' => 3,
                'stock_damaged' => 2,
            ],
        ];

        $out = [];

        foreach ($items as $itemData) {
            $categoryModel = $categoryModels[$itemData['category']] ?? null;
            $categoryId = $categoryModel?->id;

            $out[$itemData['name']] = InventoryItem::updateOrCreate(
                ['name' => $itemData['name']],
                [
                    'inventory_category_id' => $categoryId,
                    'type' => $itemData['type'],
                    'unit' => $itemData['unit'],
                    'stock' => $itemData['stock'],
                    'min_stock' => $itemData['min_stock'],
                    'stock_good' => $itemData['stock_good'],
                    'stock_less_good' => $itemData['stock_less_good'],
                    'stock_damaged' => $itemData['stock_damaged'],
                ]
            );
        }

        return $out;
    }

    private function seedInventoryMovements(array $inventoryItems, array $users): void
    {
        $leader = $users['leader_cashier'] ?? null;

        $movements = [
            [
                'item' => 'Kopi Arabica Beans',
                'type' => 'in',
                'stock_condition' => null,
                'to_stock_condition' => 'stock_good',
                'qty' => 5,
                'usage_title' => 'Stok masuk dari supplier',
                'notes' => 'Pengiriman awal bahan kopi.',
                'moved_at' => now()->subDays(7),
            ],
            [
                'item' => 'Cup 12oz',
                'type' => 'out',
                'stock_condition' => 'stock_good',
                'to_stock_condition' => 'stock_damaged',
                'qty' => 20,
                'usage_title' => 'Pemakaian operasional',
                'notes' => 'Cup dipakai untuk penjualan harian.',
                'moved_at' => now()->subDays(2),
            ],
            [
                'item' => 'Susu UHT',
                'type' => 'out',
                'stock_condition' => 'stock_good',
                'to_stock_condition' => 'stock_damaged',
                'qty' => 2,
                'usage_title' => 'Opname bahan',
                'notes' => 'Penyesuaian stok setelah opname.',
                'moved_at' => now()->subDay(),
            ],
        ];

        foreach ($movements as $movement) {
            $item = $inventoryItems[$movement['item']] ?? null;
            if (! $item) {
                continue;
            }

            InventoryMovement::updateOrCreate(
                [
                    'inventory_item_id' => $item->id,
                    'type' => $movement['type'],
                    'moved_at' => $movement['moved_at']->toDateTimeString(),
                ],
                [
                    'user_id' => $leader?->id,
                    'stock_condition' => $movement['stock_condition'],
                    'to_stock_condition' => $movement['to_stock_condition'],
                    'qty' => $movement['qty'],
                    'usage_title' => $movement['usage_title'],
                    'notes' => $movement['notes'],
                ]
            );
        }
    }

    private function seedSales(array $branches, array $tables, array $menus): void
    {
        $branch = $branches['PST'] ?? null;
        if (! $branch) {
            return;
        }

        $orders = [
            [
                'code' => 'TRX-SEED-001',
                'table' => '1',
                'sold_at' => now()->subHours(1),
                'paid_at' => now()->subHours(1),
                'payment_method' => 'cash',
                'items' => [
                    ['menu' => 'F01', 'qty' => 2],
                    ['menu' => 'K01', 'qty' => 2],
                ],
            ],
            [
                'code' => 'TRX-SEED-002',
                'table' => '3',
                'sold_at' => now()->subHours(3),
                'paid_at' => now()->subHours(3),
                'payment_method' => 'qris',
                'items' => [
                    ['menu' => 'F02', 'qty' => 1],
                    ['menu' => 'N01', 'qty' => 1],
                ],
            ],
            [
                'code' => 'TRX-SEED-003',
                'table' => '5',
                'sold_at' => now()->subHours(5),
                'paid_at' => now()->subHours(5),
                'payment_method' => 'debit',
                'items' => [
                    ['menu' => 'S01', 'qty' => 2],
                    ['menu' => 'T01', 'qty' => 2],
                ],
            ],
            [
                'code' => 'TRX-SEED-004',
                'table' => '7',
                'sold_at' => now()->subDay()->setTime(18, 15),
                'paid_at' => now()->subDay()->setTime(18, 15),
                'payment_method' => 'cash',
                'items' => [
                    ['menu' => 'F03', 'qty' => 1],
                    ['menu' => 'K02', 'qty' => 1],
                    ['menu' => 'S02', 'qty' => 1],
                ],
            ],
            [
                'code' => 'TRX-SEED-005',
                'table' => '9',
                'sold_at' => now()->subDay()->setTime(20, 5),
                'paid_at' => now()->subDay()->setTime(20, 5),
                'payment_method' => 'qris',
                'items' => [
                    ['menu' => 'N02', 'qty' => 1],
                    ['menu' => 'T02', 'qty' => 2],
                ],
            ],
        ];

        foreach ($orders as $orderData) {
            $table = $tables[$orderData['table']] ?? null;
            if (! $table) {
                continue;
            }

            $transaction = SaleTransaction::updateOrCreate(
                ['code' => $orderData['code']],
                [
                    'branch_id' => $branch->id,
                    'table_id' => $table->id,
                    'sold_at' => $orderData['sold_at'],
                    'notes' => 'Seed data transaksi cafe',
                    'status' => SaleTransaction::STATUS_PAID,
                    'cancelled_at' => null,
                    'cancelled_by' => null,
                    'paid_at' => $orderData['paid_at'],
                    'payment_method' => $orderData['payment_method'],
                    'total_amount' => 0,
                    'total_cost' => 0,
                ]
            );

            SaleTransactionItem::query()->where('sale_transaction_id', $transaction->id)->delete();

            $totalAmount = 0;
            $totalCost = 0;

            foreach ($orderData['items'] as $itemData) {
                $menu = $menus[$itemData['menu']] ?? null;
                if (! $menu) {
                    continue;
                }

                $qty = (int) $itemData['qty'];
                $lineTotal = (float) $menu->selling_price * $qty;
                $lineCost = (float) $menu->cost_price * $qty;

                SaleTransactionItem::create([
                    'sale_transaction_id' => $transaction->id,
                    'menu_id' => $menu->id,
                    'food_package_id' => null,
                    'qty' => $qty,
                    'unit_price' => $menu->selling_price,
                    'unit_cost' => $menu->cost_price,
                    'line_total' => $lineTotal,
                    'line_cost' => $lineCost,
                ]);

                $totalAmount += $lineTotal;
                $totalCost += $lineCost;
            }

            $transaction->update([
                'total_amount' => $totalAmount,
                'total_cost' => $totalCost,
            ]);
        }
    }

    private function seedCashFlow(array $users): void
    {
        $leader = $users['leader_cashier'] ?? null;

        CashFlowEntry::updateOrCreate(
            [
                'type' => 'in',
                'description' => 'Modal awal laci kasir',
            ],
            [
                'amount' => 500000,
                'happened_at' => now()->startOfDay(),
                'created_by' => $leader?->id,
            ]
        );

        CashFlowEntry::updateOrCreate(
            [
                'type' => 'out',
                'description' => 'Beli air galon & kebersihan',
            ],
            [
                'amount' => 150000,
                'happened_at' => now()->setTime(14, 0),
                'created_by' => $leader?->id,
            ]
        );
    }

    private function getPermissionsForRole(string $role): array
    {
        if ($role === 'superadmin') {
            return array_fill_keys(array_keys(User::PERMISSIONS), true);
        }

        $permissions = [];

        foreach (User::PERMISSIONS as $key => $label) {
            if (str_starts_with($key, $role)) {
                $permissions[$key] = true;
            }
        }

        return $permissions;
    }
}

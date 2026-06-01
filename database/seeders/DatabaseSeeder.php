<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\DiningTable;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\SaleTransaction;
use App\Models\SaleTransactionItem;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\CashFlowEntry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. System Settings (Only create if not exists to prevent overwriting user changes)
        if (!SystemSetting::where('key', 'cafe_name')->exists()) {
            SystemSetting::create(['key' => 'cafe_name', 'value' => 'Cafe Serba']);
        }
        
        if (!SystemSetting::where('key', 'cafe_logo')->exists()) {
            SystemSetting::create(['key' => 'cafe_logo', 'value' => null]);
        }

        // 2. Users
        $roles = [
            'superadmin' => 'Super Admin',
            'kasir' => 'Kasir Utama',
            'kitchen' => 'Tim Dapur',
            'inventory' => 'Admin Gudang',
            'leader_cashier' => 'Leader Kasir'
        ];

        foreach ($roles as $username => $name) {
            User::firstOrCreate(
                ['username' => $username],
                [
                    'name' => $name,
                    'email' => "$username@cafe.local",
                    'password' => $username,
                    'role' => $username,
                    'is_active' => true,
                    'permissions' => $this->getPermissionsForRole($username),
                    'email_verified_at' => now(),
                ]
            );
        }

        // 3. Branches
        $branchesData = [
            ['code' => 'PST', 'name' => 'Cabang Pusat'],
        ];

        foreach ($branchesData as $b) {
            Branch::firstOrCreate(['code' => $b['code']], ['name' => $b['name']]);
        }
        $branches = Branch::all();

        // 4. Tables (Only create if no tables exist)
        if (DiningTable::count() === 0) {
            $tables = collect(range(1, 15))->map(fn($n) => DiningTable::create([
                'number' => (string)$n,
                'name' => "Meja $n",
                'qr_token' => Str::uuid()->toString(),
                'is_active' => true
            ]));
        } else {
            $tables = DiningTable::all();
        }

        // 5. Hapus menu demo lama supaya database tetap bersih.
        $demoMenuCodes = ['C01', 'C02', 'C03', 'P01', 'P02', 'F01', 'F02', 'F03', 'S01', 'S02', 'A01'];
        Menu::query()->whereIn('code', $demoMenuCodes)->delete();
        MenuCategory::query()->delete();
        Menu::updateOrCreate(
            ['code' => 'A01'],
            [
                'menu_category_id' => null,
                'name' => 'Extra Telur',
                'selling_price' => 3000,
                'cost_price' => 1000,
                'image_path' => null,
                'is_sold_out' => false,
            ]
        );

        // 6. Employees & Payroll
        $employees = collect(['Budi', 'Siti', 'Agus'])->map(fn($name, $index) => Employee::updateOrCreate(
            ['name' => $name],
            [
                'employee_code' => 'EMP-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'position' => 'Staff',
                'hire_date' => now()->subMonths(6)
            ]
        ));

        $payrollPeriod = now()->startOfMonth()->toDateString();
        foreach ($employees as $emp) {
            Payroll::updateOrCreate(
                [
                    'employee_id' => $emp->id,
                    'period_month' => $payrollPeriod,
                ],
                [
                    'base_salary' => 2500000,
                    'allowances' => 0,
                    'deductions' => 0,
                    'net_salary' => 2500000,
                    'paid_at' => now()->subDays(5),
                    'notes' => 'Seed data payroll bulanan',
                ]
            );
        }

        // 7. Transactions (Only create if no transactions exist)
        if (SaleTransaction::count() === 0 && Menu::count() > 0) {
            $menu = Menu::query()->orderBy('name')->first();
            $orderSeeds = [
                [
                    'branch_code' => 'PST',
                    'table_number' => '1',
                    'sold_at' => now()->subHours(1),
                    'paid_at' => now()->subHours(1),
                    'payment_method' => 'cash',
                    'items' => [
                        ['menu' => $menu?->code, 'qty' => 2],
                    ],
                ],
                [
                    'branch_code' => 'PST',
                    'table_number' => '3',
                    'sold_at' => now()->subHours(3),
                    'paid_at' => now()->subHours(3),
                    'payment_method' => 'qris',
                    'items' => [
                        ['menu' => $menu?->code, 'qty' => 1],
                    ],
                ],
                [
                    'branch_code' => 'PST',
                    'table_number' => '5',
                    'sold_at' => now()->subHours(5),
                    'paid_at' => now()->subHours(5),
                    'payment_method' => 'debit',
                    'items' => [
                        ['menu' => $menu?->code, 'qty' => 1],
                    ],
                ],
                [
                    'branch_code' => 'PST',
                    'table_number' => '7',
                    'sold_at' => now()->subDay()->setTime(18, 15),
                    'paid_at' => now()->subDay()->setTime(18, 15),
                    'payment_method' => 'cash',
                    'items' => [
                        ['menu' => $menu?->code, 'qty' => 2],
                    ],
                ],
                [
                    'branch_code' => 'PST',
                    'table_number' => '9',
                    'sold_at' => now()->subDay()->setTime(20, 5),
                    'paid_at' => now()->subDay()->setTime(20, 5),
                    'payment_method' => 'qris',
                    'items' => [
                        ['menu' => $menu?->code, 'qty' => 1],
                    ],
                ],
            ];

            foreach ($orderSeeds as $seed) {
                $branch = $branches->firstWhere('code', $seed['branch_code']);
                $table = $tables->firstWhere('number', $seed['table_number']);

                $sale = SaleTransaction::create([
                    'code' => 'TRX-' . strtoupper(Str::random(6)),
                    'branch_id' => $branch?->id,
                    'table_id' => $table?->id,
                    'sold_at' => $seed['sold_at'],
                    'status' => SaleTransaction::STATUS_PAID,
                    'paid_at' => $seed['paid_at'],
                    'payment_method' => $seed['payment_method'],
                    'total_amount' => 0,
                    'total_cost' => 0,
                ]);

                $totalAmount = 0;
                $totalCost = 0;

                foreach ($seed['items'] as $itemSeed) {
                    $menu = Menu::query()->where('code', $itemSeed['menu'])->first();
                    if (! $menu) {
                        continue;
                    }

                    $qty = (int) $itemSeed['qty'];
                    $lineTotal = (float) $menu->selling_price * $qty;
                    $lineCost = (float) $menu->cost_price * $qty;

                    SaleTransactionItem::create([
                        'sale_transaction_id' => $sale->id,
                        'menu_id' => $menu->id,
                        'qty' => $qty,
                        'unit_price' => $menu->selling_price,
                        'unit_cost' => $menu->cost_price,
                        'line_total' => $lineTotal,
                        'line_cost' => $lineCost,
                    ]);

                    $totalAmount += $lineTotal;
                    $totalCost += $lineCost;
                }

                $sale->update([
                    'total_amount' => $totalAmount,
                    'total_cost' => $totalCost,
                ]);
            }
        }

        // 8. Cash Flow Entries (Only create if no cash flows exist)
        $leader = User::where('role', 'leader_cashier')->first();

        CashFlowEntry::updateOrCreate(
            ['type' => 'in', 'description' => 'Modal awal laci kasir'],
            [
                'amount' => 500000,
                'happened_at' => now()->startOfDay(),
                'created_by' => $leader?->id,
            ]
        );

        CashFlowEntry::updateOrCreate(
            ['type' => 'out', 'description' => 'Beli air galon & kebersihan'],
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
        
        $perms = [];
        foreach (User::PERMISSIONS as $key => $label) {
            if (str_starts_with($key, $role)) {
                $perms[$key] = true;
            }
        }
        return $perms;
    }
}

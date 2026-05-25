<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\SaleTransaction;
use App\Models\SaleTransactionItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@cafe.local',
                'password' => 'superadmin',
                'role' => 'superadmin',
                'is_active' => true,
                'permissions' => [
                    'view_dashboard' => true,
                    'view_sales' => true,
                    'manage_menus' => true,
                    'manage_branches' => true,
                    'manage_users' => true,
                    'manage_orders' => true,
                    'view_all_orders' => true,
                    'cancel_orders' => true,
                    'order_history' => true,
                    'monitor_orders_realtime' => true,
                ],
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['username' => 'kasir'],
            [
                'name' => 'Kasir Utama',
                'email' => 'kasir@cafe.local',
                'password' => 'kasir',
                'role' => 'staff',
                'is_active' => true,
                'permissions' => [
                    'view_dashboard' => true,
                    'view_sales' => true,
                    'manage_menus' => false,
                    'manage_branches' => false,
                    'manage_users' => false,
                    'manage_orders' => false,
                    'view_all_orders' => false,
                    'cancel_orders' => false,
                    'order_history' => false,
                    'monitor_orders_realtime' => false,
                ],
                'email_verified_at' => now(),
            ]
        );

        $branches = collect([
            ['code' => 'PST', 'name' => 'Cabang Pusat'],
            ['code' => 'BRT', 'name' => 'Cabang Barat'],
            ['code' => 'TMR', 'name' => 'Cabang Timur'],
        ])->mapWithKeys(function (array $branch) {
            return [
                $branch['code'] => Branch::updateOrCreate(
                    ['code' => $branch['code']],
                    ['name' => $branch['name']]
                ),
            ];
        });

        $categories = collect([
            ['name' => 'Coffee'],
            ['name' => 'Pastry'],
            ['name' => 'Tea'],
            ['name' => 'Signature Drink'],
        ])->mapWithKeys(function (array $category) {
            $model = MenuCategory::updateOrCreate(
                ['name' => $category['name']],
                ['slug' => \Illuminate\Support\Str::slug($category['name'])]
            );

            return [$category['name'] => $model];
        });

        $menus = collect([
            ['code' => 'M001', 'name' => 'Es Kopi Susu', 'category' => 'Coffee', 'selling_price' => 15000, 'cost_price' => 6000],
            ['code' => 'M002', 'name' => 'Americano', 'category' => 'Coffee', 'selling_price' => 18000, 'cost_price' => 7000],
            ['code' => 'M003', 'name' => 'Croissant', 'category' => 'Pastry', 'selling_price' => 22000, 'cost_price' => 9000],
            ['code' => 'M004', 'name' => 'Matcha Latte', 'category' => 'Signature Drink', 'selling_price' => 24000, 'cost_price' => 10000],
        ])->mapWithKeys(function (array $menu) use ($categories) {
            return [
                $menu['code'] => Menu::updateOrCreate(
                    ['code' => $menu['code']],
                    [
                        'menu_category_id' => $categories[$menu['category']]->id,
                        'name' => $menu['name'],
                        'selling_price' => $menu['selling_price'],
                        'cost_price' => $menu['cost_price'],
                    ]
                ),
            ];
        });

        $transactions = [
            [
                'code' => 'TRX-0001',
                'branch' => $branches['PST'],
                'sold_at' => Carbon::today()->setTime(9, 15),
                'items' => [
                    ['menu' => $menus['M001'], 'qty' => 2],
                    ['menu' => $menus['M003'], 'qty' => 1],
                ],
            ],
            [
                'code' => 'TRX-0002',
                'branch' => $branches['BRT'],
                'sold_at' => Carbon::today()->setTime(11, 20),
                'items' => [
                    ['menu' => $menus['M002'], 'qty' => 1],
                    ['menu' => $menus['M004'], 'qty' => 2],
                ],
            ],
            [
                'code' => 'TRX-0003',
                'branch' => $branches['TMR'],
                'sold_at' => Carbon::yesterday()->setTime(15, 45),
                'items' => [
                    ['menu' => $menus['M001'], 'qty' => 3],
                    ['menu' => $menus['M002'], 'qty' => 1],
                ],
            ],
            [
                'code' => 'TRX-0004',
                'branch' => $branches['PST'],
                'sold_at' => Carbon::yesterday()->setTime(19, 10),
                'items' => [
                    ['menu' => $menus['M003'], 'qty' => 1],
                    ['menu' => $menus['M004'], 'qty' => 1],
                ],
            ],
        ];

        foreach ($transactions as $data) {
            $sale = SaleTransaction::updateOrCreate(
                ['code' => $data['code']],
                [
                    'branch_id' => $data['branch']->id,
                    'sold_at' => $data['sold_at'],
                ]
            );

            DB::table('sale_transactions')
                ->where('id', $sale->id)
                ->update([
                    'branch_id' => $data['branch']->id,
                    'sold_at' => $data['sold_at']->format('Y-m-d H:i:s'),
                ]);

            $totalAmount = 0;
            $totalCost = 0;

            if ($sale->items()->count() === 0) {
                foreach ($data['items'] as $item) {
                    $lineTotal = $item['menu']->selling_price * $item['qty'];
                    $lineCost = $item['menu']->cost_price * $item['qty'];

                    SaleTransactionItem::create([
                        'sale_transaction_id' => $sale->id,
                        'menu_id' => $item['menu']->id,
                        'qty' => $item['qty'],
                        'unit_price' => $item['menu']->selling_price,
                        'unit_cost' => $item['menu']->cost_price,
                        'line_total' => $lineTotal,
                        'line_cost' => $lineCost,
                    ]);

                    $totalAmount += $lineTotal;
                    $totalCost += $lineCost;
                }
            } else {
                foreach ($sale->items as $item) {
                    $totalAmount += $item->line_total;
                    $totalCost += $item->line_cost;
                }
            }

            $sale->update([
                'total_amount' => $totalAmount,
                'total_cost' => $totalCost,
            ]);
        }
    }
}

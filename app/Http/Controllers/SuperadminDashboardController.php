<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Menu;
use App\Models\SaleTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SuperadminDashboardController extends Controller
{
    public function index(Request $request): View
    {
        return view('superadmin.dashboard', $this->dashboardData($request));
    }

    public function live(Request $request): RedirectResponse
    {
        return redirect()->route('superadmin.dashboard', $request->except('page'));
    }

    public function fragment(Request $request): View
    {
        return view('superadmin.live-dashboard', $this->dashboardData($request));
    }

    private function dashboardData(Request $request): array
    {
        $page = (int) $request->query('page', 1);

        return Cache::remember(
            'superadmin.dashboard.' . today()->format('Y-m-d') . '.page.' . $page,
            now()->addSeconds(30), // Increased cache time for better performance
            function () use ($request) {
                // Combined aggregate query
                $totals = SaleTransaction::query()
                    ->where('status', SaleTransaction::STATUS_PAID)
                    ->selectRaw('COALESCE(SUM(total_amount), 0) as sales, COALESCE(SUM(total_cost), 0) as cost')
                    ->first();
                
                $totalSales = (float) $totals->sales;
                $totalCost = (float) $totals->cost;
                $profitLoss = $totalSales - $totalCost;
                
                $todayTransactions = SaleTransaction::query()
                    ->whereDate('sold_at', today())
                    ->where('status', SaleTransaction::STATUS_PAID)
                    ->count();

                // Optimized Top Menu query using a subquery for better performance
                $topMenu = Menu::query()
                    ->select('menus.*')
                    ->selectSub(function ($query) {
                        $query->from('sale_transaction_items')
                            ->join('sale_transactions', 'sale_transactions.id', '=', 'sale_transaction_items.sale_transaction_id')
                            ->whereColumn('sale_transaction_items.menu_id', 'menus.id')
                            ->where('sale_transactions.status', SaleTransaction::STATUS_PAID)
                            ->selectRaw('SUM(qty)');
                    }, 'sold_qty')
                    ->orderByDesc('sold_qty')
                    ->first();

                $branchSales = Branch::query()
                    ->select('branches.id', 'branches.name', 'branches.code')
                    ->selectRaw('COALESCE(SUM(sale_transactions.total_amount), 0) as total_sales')
                    ->leftJoin('sale_transactions', function($join) {
                        $join->on('sale_transactions.branch_id', '=', 'branches.id')
                             ->where('sale_transactions.status', SaleTransaction::STATUS_PAID);
                    })
                    ->groupBy('branches.id', 'branches.name', 'branches.code')
                    ->orderByDesc('total_sales')
                    ->get();

                $recentTransactions = SaleTransaction::query()
                    ->with(['branch:id,name', 'table:id,number,name']) // Eager load only needed columns
                    ->orderByDesc('sold_at')
                    ->orderByDesc('id')
                    ->paginate(5)
                    ->withPath(route('superadmin.dashboard'))
                    ->appends($request->except('page'));

                return [
                    'totalSales' => $totalSales,
                    'totalCost' => $totalCost,
                    'profitLoss' => $profitLoss,
                    'todayTransactions' => $todayTransactions,
                    'topMenu' => $topMenu,
                    'branchSales' => $branchSales,
                    'recentTransactions' => $recentTransactions,
                ];
            }
        );
    }
}

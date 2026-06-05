<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Menu;
use App\Models\SaleTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

    public function fragment(Request $request): View|RedirectResponse
    {
        if (! $request->ajax() && ! $request->expectsJson()) {
            return redirect()->route('superadmin.dashboard', $request->except('page'));
        }

        return view('superadmin.live-dashboard', $this->dashboardData($request));
    }

    private function dashboardData(Request $request): array
    {
        $page = (int) $request->query('page', 1);
        $period = $request->string('period')->toString();
        if (! in_array($period, ['today', 'week', 'month'], true)) {
            $period = 'today';
        }

        return Cache::remember(
            'superadmin.dashboard.' . today()->format('Y-m-d-H-i') . '.period.' . $period . '.page.' . $page,
            now()->addSeconds(10),
            function () use ($request, $period) {
                $salesQuery = SaleTransaction::query()->where('status', SaleTransaction::STATUS_PAID);
                $branchJoinFilter = function($join) use ($period) {
                    $join->on('sale_transactions.branch_id', '=', 'branches.id')
                         ->where('sale_transactions.status', SaleTransaction::STATUS_PAID);

                    if ($period === 'today') {
                        $join->whereDate('sale_transactions.sold_at', today());
                    } elseif ($period === 'week') {
                        $join->whereBetween('sale_transactions.sold_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    } elseif ($period === 'month') {
                        $join->whereYear('sale_transactions.sold_at', now()->year)
                             ->whereMonth('sale_transactions.sold_at', now()->month);
                    }
                };

                if ($period === 'today') {
                    $salesQuery->whereDate('sold_at', today());
                } elseif ($period === 'week') {
                    $salesQuery->whereBetween('sold_at', [now()->startOfWeek(), now()->endOfWeek()]);
                } elseif ($period === 'month') {
                    $salesQuery->whereYear('sold_at', now()->year)->whereMonth('sold_at', now()->month);
                }

                $totals = (clone $salesQuery)
                    ->selectRaw('COALESCE(SUM(total_amount), 0) as sales, COALESCE(SUM(total_cost), 0) as cost')
                    ->first();
                
                $totalSales = (float) $totals->sales;
                $totalCost = (float) $totals->cost;
                $profitLoss = $totalSales - $totalCost;
                
                $todayTransactions = (clone $salesQuery)->count();

                // Optimized Top Menus query
                $topMenus = Menu::query()
                    ->select('menus.*')
                    ->selectSub(function ($query) use ($period) {
                        $query->from('sale_transaction_items')
                            ->join('sale_transactions', 'sale_transactions.id', '=', 'sale_transaction_items.sale_transaction_id')
                            ->whereColumn('sale_transaction_items.menu_id', 'menus.id')
                            ->where('sale_transactions.status', SaleTransaction::STATUS_PAID);

                        if ($period === 'today') {
                            $query->whereDate('sale_transactions.sold_at', today());
                        } elseif ($period === 'week') {
                            $query->whereBetween('sale_transactions.sold_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        } elseif ($period === 'month') {
                            $query->whereYear('sale_transactions.sold_at', now()->year)
                                  ->whereMonth('sale_transactions.sold_at', now()->month);
                        }

                        $query->selectRaw('SUM(qty)');
                    }, 'sold_qty')
                    ->whereExists(function ($query) use ($period) {
                        $query->from('sale_transaction_items')
                            ->join('sale_transactions', 'sale_transactions.id', '=', 'sale_transaction_items.sale_transaction_id')
                            ->whereColumn('sale_transaction_items.menu_id', 'menus.id')
                            ->where('sale_transactions.status', SaleTransaction::STATUS_PAID);

                        if ($period === 'today') {
                            $query->whereDate('sale_transactions.sold_at', today());
                        } elseif ($period === 'week') {
                            $query->whereBetween('sale_transactions.sold_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        } elseif ($period === 'month') {
                            $query->whereYear('sale_transactions.sold_at', now()->year)
                                  ->whereMonth('sale_transactions.sold_at', now()->month);
                        }
                    })
                    ->orderByDesc('sold_qty')
                    ->limit(6)
                    ->get();

                $branchSales = Branch::query()
                    ->select('branches.id', 'branches.name', 'branches.code')
                    ->selectRaw('COALESCE(SUM(sale_transactions.total_amount), 0) as total_sales')
                    ->leftJoin('sale_transactions', $branchJoinFilter)
                    ->groupBy('branches.id', 'branches.name', 'branches.code')
                    ->orderByDesc('total_sales')
                    ->get();

                $activeBranchesCount = $branchSales->where('total_sales', '>', 0)->count();
                $totalBranchesCount = $branchSales->count();

                $recentTransactions = (clone $salesQuery)
                    ->with(['branch:id,name', 'table:id,number,name'])
                    ->orderByDesc('sold_at')
                    ->orderByDesc('id')
                    ->paginate(5)
                    ->withPath(route('superadmin.dashboard'))
                    ->appends($request->except('page'));

                $periodLabel = match ($period) {
                    'week' => 'Minggu Ini',
                    'month' => 'Bulan Ini',
                    default => 'Hari Ini',
                };

                return [
                    'totalSales' => $totalSales,
                    'totalCost' => $totalCost,
                    'profitLoss' => $profitLoss,
                    'todayTransactions' => $todayTransactions,
                    'topMenus' => $topMenus,
                    'branchSales' => $branchSales,
                    'activeBranchesCount' => $activeBranchesCount,
                    'totalBranchesCount' => $totalBranchesCount,
                    'recentTransactions' => $recentTransactions,
                    'currentPeriod' => $period,
                    'periodLabel' => $periodLabel,
                ];
            }
        );
    }
}

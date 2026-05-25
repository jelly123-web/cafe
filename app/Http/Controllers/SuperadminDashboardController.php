<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Menu;
use App\Models\SaleTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SuperadminDashboardController extends Controller
{
    public function index(): View
    {
        $totalSales = SaleTransaction::query()->sum('total_amount');
        $totalCost = SaleTransaction::query()->sum('total_cost');
        $profitLoss = $totalSales - $totalCost;
        $todayTransactions = SaleTransaction::query()
            ->whereDate('sold_at', today())
            ->count();

        $topMenu = Menu::query()
            ->withSum('items as sold_qty', 'qty')
            ->orderByDesc('sold_qty')
            ->first();

        $branchSales = Branch::query()
            ->select('branches.*', DB::raw('COALESCE(SUM(sale_transactions.total_amount), 0) as total_sales'))
            ->leftJoin('sale_transactions', 'sale_transactions.branch_id', '=', 'branches.id')
            ->groupBy('branches.id', 'branches.code', 'branches.name', 'branches.created_at', 'branches.updated_at')
            ->orderByDesc('total_sales')
            ->get();

        return view('superadmin.dashboard', [
            'totalSales' => $totalSales,
            'totalCost' => $totalCost,
            'profitLoss' => $profitLoss,
            'todayTransactions' => $todayTransactions,
            'topMenu' => $topMenu,
            'branchSales' => $branchSales,
        ]);
    }
}

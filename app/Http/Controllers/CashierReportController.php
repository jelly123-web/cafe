<?php

namespace App\Http\Controllers;

use App\Models\SaleTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CashierReportController extends Controller
{
    public function index(): View
    {
        return view('cashier.reports.index', $this->reportData());
    }

    public function live(): View
    {
        return view('cashier.reports.live', $this->reportData());
    }

    private function reportData(): array
    {
        $today = now()->toDateString();
        $hasStatusColumn = Schema::hasColumn('sale_transactions', 'status');

        $todayTransactions = SaleTransaction::query()->whereDate('sold_at', $today);
        if ($hasStatusColumn) {
            $todayTransactions->where('status', SaleTransaction::STATUS_PAID);
        }

        $totalToday = (float) $todayTransactions->sum('total_amount');
        $countToday = (int) $todayTransactions->count();

        $history = SaleTransaction::query()
            ->with('table')
            ->orderByDesc('sold_at')
            ->paginate(5);

        $totalIncomeQuery = SaleTransaction::query();
        if ($hasStatusColumn) {
            $totalIncomeQuery->where('status', SaleTransaction::STATUS_PAID);
        }
        $totalIncome = (float) $totalIncomeQuery->sum('total_amount');

        return [
            'totalToday' => $totalToday,
            'countToday' => $countToday,
            'history' => $history,
            'totalIncome' => $totalIncome,
            'hasStatusColumn' => $hasStatusColumn,
        ];
    }

    public function destroy(SaleTransaction $order): RedirectResponse
    {
        $code = $order->code;
        $order->delete();

        return back()->with('success', "Transaksi {$code} berhasil dihapus.");
    }
}

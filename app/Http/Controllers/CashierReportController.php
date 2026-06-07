<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\SaleTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CashierReportController extends Controller
{
    public function index(Request $request): View
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

        $todayTransactionsQuery = SaleTransaction::query()->whereDate('sold_at', $today);
        if ($hasStatusColumn) {
            $todayTransactionsQuery->where('status', 'paid');
        }

        $totalToday = (float) $todayTransactionsQuery->sum('total_amount');
        $countToday = (int) $todayTransactionsQuery->count();

        $history = SaleTransaction::query()
            ->with('table')
            ->orderByDesc('sold_at')
            ->paginate(15);

        $totalIncome = (float) SaleTransaction::query()
            ->when($hasStatusColumn, fn($q) => $q->where('status', 'paid'))
            ->sum('total_amount');

        $allTransactions = SaleTransaction::all();
        $totalCost = (float) $allTransactions->sum('total_cost');
        $profitLoss = $totalIncome - $totalCost;

        $summary = [
            'transaction_count' => $allTransactions->count(),
            'total_sales' => 'Rp ' . number_format($totalIncome, 0, ',', '.'),
            'total_cost' => 'Rp ' . number_format($totalCost, 0, ',', '.'),
            'total_payroll' => 'Rp 0',
            'profit_loss' => 'Rp ' . number_format(abs($profitLoss), 0, ',', '.'),
            'profit_class' => $profitLoss >= 0 ? 'profit' : 'loss',
            'period_range' => 'Semua Waktu',
        ];

        // Format history for both index view and live partial
        $history->getCollection()->transform(function (SaleTransaction $transaction) {
            $profitLoss = (float) $transaction->total_amount - (float) $transaction->total_cost;
            $transaction->profit_loss = 'Rp ' . number_format(abs($profitLoss), 0, ',', '.');
            $transaction->profit_class = $profitLoss >= 0 ? 'profit' : 'loss';
            $transaction->total_amount_label = 'Rp ' . number_format((float) $transaction->total_amount, 0, ',', '.');
            $transaction->total_cost_label = 'Rp ' . number_format((float) $transaction->total_cost, 0, ',', '.');
            $transaction->sold_at_label = $transaction->sold_at?->format('d M Y, H:i') ?? '-';
            $transaction->status_label = ucfirst($transaction->status ?? 'pending');
            return $transaction;
        });

        return [
            'summary' => $summary,
            'totalToday' => $totalToday,
            'countToday' => $countToday,
            'totalIncome' => $totalIncome,
            'history' => $history,
            'rows' => $history, // For index view consistency
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

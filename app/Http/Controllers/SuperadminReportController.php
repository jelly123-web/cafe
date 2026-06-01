<?php

namespace App\Http\Controllers;

use App\Exports\SalesReportExport;
use App\Models\Payroll;
use App\Models\SaleTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class SuperadminReportController extends Controller
{
    public function index(Request $request): View
    {
        $report = $this->buildReport($request);

        return view('superadmin.reports.index', $report);
    }

    public function live(Request $request): JsonResponse|RedirectResponse
    {
        if (! ($request->expectsJson() || $request->ajax())) {
            return redirect()->route('superadmin.reports.index', $request->query());
        }

        $report = $this->buildReport($request);

        // Ensure pagination links point to the index page, not the live endpoint
        $report['paginator']->setPath(route('superadmin.reports.index'));

        $rows = $report['transactions']->map(function (array $row): array {
            return [
                'id' => $row['id'],
                'code' => $row['code'],
                'branch_name' => $row['branch_name'],
                'sold_at' => $row['sold_at']?->format('d M Y H:i') ?? '-',
                'total_amount' => 'Rp ' . number_format((float) $row['total_amount'], 0, ',', '.'),
                'total_cost' => 'Rp ' . number_format((float) $row['total_cost'], 0, ',', '.'),
                'profit_loss' => 'Rp ' . number_format(abs((float) $row['profit_loss']), 0, ',', '.'),
                'profit_class' => ((float) $row['profit_loss']) >= 0 ? 'profit' : 'loss',
                'status' => $row['status'],
                'status_label' => match ($row['status']) {
                    SaleTransaction::STATUS_PAID => 'Lunas',
                    SaleTransaction::STATUS_CANCELLED => 'Batal',
                    default => 'Pending',
                },
            ];
        })->values();

        return response()->json([
            'summary' => [
                'period_label' => $report['period_label'],
                'period_range' => $report['date_from']->format('d M Y') . ' - ' . $report['date_to']->format('d M Y'),
                'transaction_count' => number_format((int) $report['transaction_count'], 0, ',', '.'),
                'total_sales' => 'Rp ' . number_format((float) $report['total_sales'], 0, ',', '.'),
                'total_cost' => 'Rp ' . number_format((float) $report['total_cost'], 0, ',', '.'),
                'total_payroll' => 'Rp ' . number_format((float) $report['total_payroll'], 0, ',', '.'),
                'total_cash_in' => 'Rp ' . number_format((float) $report['total_cash_in'], 0, ',', '.'),
                'total_cash_out' => 'Rp ' . number_format((float) $report['total_cash_out'], 0, ',', '.'),
                'profit_loss' => 'Rp ' . number_format(abs((float) $report['profit_loss']), 0, ',', '.'),
                'profit_class' => ((float) $report['profit_loss']) >= 0 ? 'profit' : 'loss',
            ],
            'rows' => $rows,
            'pagination' => $report['paginator']->links('components.pagination')->render(),
        ]);
    }

    public function destroy(SaleTransaction $transaction)
    {
        $transaction->delete();

        return back()->with('status', 'Transaksi berhasil dihapus.');
    }

    public function destroyAll(Request $request)
    {
        $validated = $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
        ]);

        SaleTransaction::query()
            ->whereBetween('sold_at', [
                Carbon::parse($validated['date_from'])->startOfDay(),
                Carbon::parse($validated['date_to'])->endOfDay()
            ])
            ->delete();

        return back()->with('status', 'Semua transaksi pada periode ini berhasil dihapus.');
    }

    public function exportPdf(Request $request)
    {
        $report = $this->buildReport($request);

        return Pdf::loadView('superadmin.reports.pdf', $report)
            ->setPaper('a4', 'landscape')
            ->download($report['filename_base'].'.pdf');
    }

    public function exportExcel(Request $request)
    {
        $report = $this->buildReport($request);

        return Excel::download(
            new SalesReportExport($report),
            $report['filename_base'].'.xlsx'
        );
    }

    private function buildReport(Request $request): array
    {
        $validated = $request->validate([
            'period' => ['nullable', 'in:daily,weekly,monthly,yearly,custom'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $period = $validated['period'] ?? 'daily';
        $now = now();

        [$dateFrom, $dateTo, $periodLabel] = match ($period) {
            'weekly' => [
                $now->copy()->startOfWeek(Carbon::MONDAY)->startOfDay(),
                $now->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay(),
                'Laporan Mingguan',
            ],
            'monthly' => [
                $now->copy()->startOfMonth()->startOfDay(),
                $now->copy()->endOfMonth()->endOfDay(),
                'Laporan Bulanan',
            ],
            'yearly' => [
                $now->copy()->startOfYear()->startOfDay(),
                $now->copy()->endOfYear()->endOfDay(),
                'Laporan Tahunan',
            ],
            'custom' => [
                Carbon::parse($validated['date_from'] ?? $now->toDateString())->startOfDay(),
                Carbon::parse($validated['date_to'] ?? $now->toDateString())->endOfDay(),
                'Laporan Kustom',
            ],
            default => [
                $now->copy()->startOfDay(),
                $now->copy()->endOfDay(),
                'Laporan Harian',
            ],
        };

        $transactionsQuery = SaleTransaction::query()
            ->with(['branch'])
            ->whereBetween('sold_at', [$dateFrom, $dateTo])
            ->orderBy('sold_at');

        $allTransactionsForStats = (clone $transactionsQuery)->get();
         $paginatedTransactions = $transactionsQuery->paginate(5)->withQueryString();

        // Chart Data Logic
        $chartData = [];
        if ($period === 'yearly') {
            // Group by month
            for ($i = 1; $i <= 12; $i++) {
                $monthName = Carbon::create(null, $i, 1)->translatedFormat('M');
                $monthlyTotal = $allTransactionsForStats->filter(fn($t) => $t->sold_at->month === $i && $t->status === SaleTransaction::STATUS_PAID)->sum('total_amount');
                $chartData['labels'][] = $monthName;
                $chartData['values'][] = (float) $monthlyTotal;
            }
        } elseif ($period === 'monthly') {
            // Group by day of month
            $daysInMonth = $now->daysInMonth;
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $dailyTotal = $allTransactionsForStats->filter(fn($t) => $t->sold_at->day === $i && $t->status === SaleTransaction::STATUS_PAID)->sum('total_amount');
                $chartData['labels'][] = $i;
                $chartData['values'][] = (float) $dailyTotal;
            }
        } elseif ($period === 'weekly') {
            // Group by day of week
            for ($i = 0; $i < 7; $i++) {
                $day = $dateFrom->copy()->addDays($i);
                $weeklyTotal = $allTransactionsForStats->filter(fn($t) => $t->sold_at->isSameDay($day) && $t->status === SaleTransaction::STATUS_PAID)->sum('total_amount');
                $chartData['labels'][] = $day->translatedFormat('D');
                $chartData['values'][] = (float) $weeklyTotal;
            }
        }

        $mappedTransactions = $paginatedTransactions->getCollection()->map(function (SaleTransaction $transaction): array {
            $profitLoss = (float) $transaction->total_amount - (float) $transaction->total_cost;

            return [
                'id' => $transaction->id,
                'code' => $transaction->code,
                'branch_name' => $transaction->branch?->name ?? '-',
                'sold_at' => $transaction->sold_at,
                'total_amount' => (float) $transaction->total_amount,
                'total_cost' => (float) $transaction->total_cost,
                'profit_loss' => $profitLoss,
                'status' => $transaction->status,
            ];
        });

        $paidTransactions = $allTransactionsForStats->where('status', SaleTransaction::STATUS_PAID);
        $totalSales = $paidTransactions->sum('total_amount');
        $totalCost = $paidTransactions->sum('total_cost');
        $grossProfit = $totalSales - $totalCost;

        $totalPayroll = (float) Payroll::query()
            ->whereBetween('paid_at', [$dateFrom, $dateTo])
            ->sum('net_salary');

        $cashIn = (float) \App\Models\CashFlowEntry::query()
            ->where('type', 'in')
            ->whereBetween('happened_at', [$dateFrom, $dateTo])
            ->sum('amount');
        $cashOut = (float) \App\Models\CashFlowEntry::query()
            ->where('type', 'out')
            ->whereBetween('happened_at', [$dateFrom, $dateTo])
            ->sum('amount');

        $profitLoss = $grossProfit - $totalPayroll + $cashIn - $cashOut;

        return [
            'period' => $period,
            'period_label' => $periodLabel,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'transactions' => $mappedTransactions,
            'paginator' => $paginatedTransactions,
            'transaction_count' => $allTransactionsForStats->count(),
            'total_sales' => $totalSales,
            'total_cost' => $totalCost,
            'total_payroll' => $totalPayroll,
            'gross_profit' => $grossProfit,
            'profit_loss' => $profitLoss,
            'total_cash_in' => $cashIn,
            'total_cash_out' => $cashOut,
            'chart_data' => $chartData,
            'filename_base' => sprintf(
                'laporan-%s-%s-sampai-%s',
                $period,
                $dateFrom->format('Ymd'),
                $dateTo->format('Ymd')
            ),
        ];
    }
}

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
        $report['rows']->setPath(route('superadmin.reports.index'));

        $rows = $report['rows']->getCollection()->map(function ($row): array {
            return [
                'id' => $row->id,
                'code' => $row->code,
                'order_source' => $row->table?->name ? ('Meja ' . $row->table->name) : 'Pesanan Langsung',
                'sold_at' => $row->sold_at_label,
                'total_amount' => $row->total_amount_label,
                'total_cost' => $row->total_cost_label,
                'profit_loss' => $row->profit_loss,
                'profit_class' => $row->profit_class,
                'status' => $row->status,
                'status_label' => $row->status_label,
            ];
        })->values();

        return response()->json([
            'summary' => $report['summary'],
            'rows' => $rows,
            'pagination' => $report['rows']->links('components.pagination')->render(),
            'chart_data' => $report['chart_data'],
            'pie_data' => $report['pie_data'],
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
            'period' => ['nullable', 'in:today,yesterday,this_week,this_month,last_month,this_year,daily,weekly,monthly,yearly,custom'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $period = $validated['period'] ?? 'daily';
        $now = now();

        [$dateFrom, $dateTo, $periodLabel] = match ($period) {
            'yesterday' => [
                $now->copy()->subDay()->startOfDay(),
                $now->copy()->subDay()->endOfDay(),
                'Laporan Kemarin',
            ],
            'this_week', 'weekly' => [
                $now->copy()->startOfWeek(Carbon::MONDAY)->startOfDay(),
                $now->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay(),
                'Laporan Mingguan',
            ],
            'this_month', 'monthly' => [
                $now->copy()->startOfMonth()->startOfDay(),
                $now->copy()->endOfMonth()->endOfDay(),
                'Laporan Bulanan',
            ],
            'last_month' => [
                $now->copy()->subMonthNoOverflow()->startOfMonth()->startOfDay(),
                $now->copy()->subMonthNoOverflow()->endOfMonth()->endOfDay(),
                'Laporan Bulan Lalu',
            ],
            'this_year', 'yearly' => [
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
            ->with(['branch', 'table'])
            ->whereBetween('sold_at', [$dateFrom, $dateTo])
            ->orderBy('sold_at');

        $allTransactionsForStats = (clone $transactionsQuery)->get();
        $paginatedTransactions = $transactionsQuery->paginate(15)->withQueryString();

        // Chart Data Logic
        $chartData = ['labels' => [], 'values' => []];
        if (in_array($period, ['this_year', 'yearly'], true)) {
            // Group by month
            for ($i = 1; $i <= 12; $i++) {
                $monthName = Carbon::create(null, $i, 1)->translatedFormat('M');
                $monthlyTotal = $allTransactionsForStats->filter(fn($t) => $t->sold_at->month === $i && $t->status === SaleTransaction::STATUS_PAID)->sum('total_amount');
                $chartData['labels'][] = $monthName;
                $chartData['values'][] = (float) $monthlyTotal;
            }
        } elseif (in_array($period, ['this_month', 'monthly', 'last_month'], true)) {
            // Group by day of month
            $daysInMonth = $dateFrom->daysInMonth;
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $dailyTotal = $allTransactionsForStats->filter(fn($t) => $t->sold_at->day === $i && $t->status === SaleTransaction::STATUS_PAID)->sum('total_amount');
                $chartData['labels'][] = $i;
                $chartData['values'][] = (float) $dailyTotal;
            }
        } elseif (in_array($period, ['this_week', 'weekly'], true)) {
            // Group by day of week
            for ($i = 0; $i < 7; $i++) {
                $day = $dateFrom->copy()->addDays($i);
                $weeklyTotal = $allTransactionsForStats->filter(fn($t) => $t->sold_at->isSameDay($day) && $t->status === SaleTransaction::STATUS_PAID)->sum('total_amount');
                $chartData['labels'][] = $day->translatedFormat('D');
                $chartData['values'][] = (float) $weeklyTotal;
            }
        } else {
            // Daily: group by hours
            for ($i = 0; $i < 24; $i++) {
                $hourlyTotal = $allTransactionsForStats->filter(fn($t) => $t->sold_at->hour === $i && $t->status === SaleTransaction::STATUS_PAID)->sum('total_amount');
                $chartData['labels'][] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                $chartData['values'][] = (float) $hourlyTotal;
            }
        }

        $paginatedTransactions->getCollection()->transform(function (SaleTransaction $transaction) {
            $profitLoss = (float) $transaction->total_amount - (float) $transaction->total_cost;
            $transaction->profit_loss = 'Rp ' . number_format(abs($profitLoss), 0, ',', '.');
            $transaction->profit_class = $profitLoss >= 0 ? 'profit' : 'loss';
            $transaction->total_amount_label = 'Rp ' . number_format((float) $transaction->total_amount, 0, ',', '.');
            $transaction->total_cost_label = 'Rp ' . number_format((float) $transaction->total_cost, 0, ',', '.');
            $transaction->sold_at_label = $transaction->sold_at?->format('d M Y, H:i') ?? '-';
            $transaction->status_label = match ($transaction->status) {
                SaleTransaction::STATUS_PAID => 'Lunas',
                SaleTransaction::STATUS_CANCELLED => 'Batal',
                default => 'Pending',
            };
            return $transaction;
        });

        $paidTransactions = $allTransactionsForStats->where('status', SaleTransaction::STATUS_PAID);
        $totalSales = (float) $paidTransactions->sum('total_amount');
        $totalCost = (float) $paidTransactions->sum('total_cost');
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

        $summary = [
            'period_label' => $periodLabel,
            'period_range' => $dateFrom->format('d M Y') . ' - ' . $dateTo->format('d M Y'),
            'transaction_count' => number_format($allTransactionsForStats->count(), 0, ',', '.'),
            'total_sales' => 'Rp ' . number_format($totalSales, 0, ',', '.'),
            'total_cost' => 'Rp ' . number_format($totalCost, 0, ',', '.'),
            'total_payroll' => 'Rp ' . number_format($totalPayroll, 0, ',', '.'),
            'total_cash_in' => 'Rp ' . number_format($cashIn, 0, ',', '.'),
            'total_cash_out' => 'Rp ' . number_format($cashOut, 0, ',', '.'),
            'profit_loss' => 'Rp ' . number_format(abs($profitLoss), 0, ',', '.'),
            'profit_class' => $profitLoss >= 0 ? 'profit' : 'loss',
        ];

        $pieData = [
            'labels' => ['Penjualan', 'Modal', 'Gaji', 'Laba Bersih'],
            'values' => [
                max($totalSales, 0),
                max($totalCost, 0),
                max($totalPayroll, 0),
                max($profitLoss, 0),
            ],
        ];

        return [
            'period' => $period,
            'summary' => $summary,
            'rows' => $paginatedTransactions,
            'chart_data' => $chartData,
            'pie_data' => $pieData,
            'filename_base' => sprintf(
                'laporan-%s-%s-sampai-%s',
                $period,
                $dateFrom->format('Ymd'),
                $dateTo->format('Ymd')
            ),
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => $dateTo->format('Y-m-d'),
        ];
    }
}

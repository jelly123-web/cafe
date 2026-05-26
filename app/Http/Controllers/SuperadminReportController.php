<?php

namespace App\Http\Controllers;

use App\Exports\SalesReportExport;
use App\Models\SaleTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class SuperadminReportController extends Controller
{
    public function index(Request $request): View
    {
        $report = $this->buildReport($request);

        return view('superadmin.reports.index', $report);
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
            'period' => ['nullable', 'in:daily,weekly,monthly,custom'],
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

        $transactions = SaleTransaction::query()
            ->with(['branch'])
            ->whereBetween('sold_at', [$dateFrom, $dateTo])
            ->orderBy('sold_at')
            ->get()
            ->map(function (SaleTransaction $transaction): array {
                $profitLoss = (float) $transaction->total_amount - (float) $transaction->total_cost;

                return [
                    'code' => $transaction->code,
                    'branch_name' => $transaction->branch?->name ?? '-',
                    'sold_at' => $transaction->sold_at,
                    'total_amount' => (float) $transaction->total_amount,
                    'total_cost' => (float) $transaction->total_cost,
                    'profit_loss' => $profitLoss,
                ];
            });

        $totalSales = $transactions->sum('total_amount');
        $totalCost = $transactions->sum('total_cost');
        $profitLoss = $totalSales - $totalCost;

        return [
            'period' => $period,
            'period_label' => $periodLabel,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'transactions' => $transactions,
            'transaction_count' => $transactions->count(),
            'total_sales' => $totalSales,
            'total_cost' => $totalCost,
            'profit_loss' => $profitLoss,
            'filename_base' => sprintf(
                'laporan-%s-%s-sampai-%s',
                $period,
                $dateFrom->format('Ymd'),
                $dateTo->format('Ymd')
            ),
        ];
    }
}

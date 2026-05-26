<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SalesReportExport implements FromView, ShouldAutoSize
{
    public function __construct(private readonly array $report)
    {
    }

    public function view(): View
    {
        return view('superadmin.reports.excel', $this->report);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\DiningTable;
use App\Models\SaleTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashierTableController extends Controller
{
    public function index(): View
    {
        $tables = DiningTable::query()
            ->orderByRaw('CAST(number AS UNSIGNED) ASC')
            ->get();

        return view('cashier.tables.index', ['tables' => $tables]);
    }

    public function open(DiningTable $table): RedirectResponse
    {
        $table->update(['service_status' => DiningTable::STATUS_OCCUPIED]);

        return back()->with('success', "Meja {$table->number} dibuka.");
    }

    public function close(DiningTable $table): RedirectResponse
    {
        $table->update(['service_status' => DiningTable::STATUS_EMPTY]);

        return back()->with('success', "Meja {$table->number} ditutup.");
    }

    public function destroy(DiningTable $table): RedirectResponse
    {
        $number = $table->number;
        SaleTransaction::query()->where('table_id', $table->id)->update(['table_id' => null]);
        $table->delete();

        return back()->with('success', "Meja {$number} berhasil dihapus.");
    }
}

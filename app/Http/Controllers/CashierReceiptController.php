<?php

namespace App\Http\Controllers;

use App\Models\SaleTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashierReceiptController extends Controller
{
    public function index(): View
    {
        $orders = SaleTransaction::query()
            ->with(['table', 'items.menu'])
            ->orderByDesc('sold_at')
            ->paginate(5);

        return view('cashier.receipts.index', ['orders' => $orders]);
    }

    public function print(SaleTransaction $order): View
    {
        $order->load(['table', 'items.menu']);

        return view('cashier.receipts.print', ['order' => $order]);
    }

    public function sendDigital(Request $request, SaleTransaction $order): RedirectResponse
    {
        $data = $request->validate([
            'destination' => ['required', 'string', 'max:12'],
        ]);

        return back()->with('success', "Struk {$order->code} berhasil dikirim ke {$data['destination']}.");
    }

    public function destroy(SaleTransaction $order): RedirectResponse
    {
        $code = $order->code;
        $order->delete();

        return back()->with('success', "Data struk {$code} berhasil dihapus.");
    }
}

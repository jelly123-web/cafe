<?php

namespace App\Http\Controllers;

use App\Models\SaleTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CashierPaymentController extends Controller
{
    public function index(): View
    {
        return view('cashier.payments.index', $this->paymentData());
    }

    public function live(): View
    {
        return view('cashier.payments.live', $this->paymentData());
    }

    private function paymentData(): array
    {
        $orders = SaleTransaction::query()
            ->with('table')
            ->withCount('items')
            ->where('status', SaleTransaction::STATUS_PENDING)
            ->orderByDesc('sold_at')
            ->paginate(12);

        return [
            'orders' => $orders,
        ];
    }

    public function pay(Request $request, SaleTransaction $order): RedirectResponse
    {
        $data = $request->validate([
            'payment_method' => ['required', 'in:cash,qris,transfer_ewallet'],
        ]);

        if ($order->status === SaleTransaction::STATUS_CANCELLED) {
            return back()->with('error', 'Pesanan dibatalkan, tidak bisa dibayar.');
        }

        $hasStatusColumn = Schema::hasColumn('sale_transactions', 'status');
        $updateData = [];
        if ($hasStatusColumn) {
            $updateData['status'] = SaleTransaction::STATUS_PAID;
        }
        if (Schema::hasColumn('sale_transactions', 'payment_method')) {
            $updateData['payment_method'] = $data['payment_method'];
        }
        if (Schema::hasColumn('sale_transactions', 'paid_at')) {
            $updateData['paid_at'] = now();
        }

        if (! empty($updateData)) {
            $order->update($updateData);
        }

        $message = "Pembayaran {$order->code} diproses.";
        if ($hasStatusColumn) {
            $message = "Pembayaran {$order->code} berhasil, status menjadi Lunas.";
        } else {
            $message .= ' Catatan: kolom status belum tersedia, jalankan migrate agar status bisa berubah ke Lunas.';
        }
        if (! Schema::hasColumn('sale_transactions', 'payment_method')) {
            $message .= ' Catatan: kolom metode pembayaran belum tersedia, jalankan migrate.';
        }

        return back()->with('success', $message);
    }

    public function destroyAll(): RedirectResponse
    {
        $count = SaleTransaction::query()->count();
        SaleTransaction::query()->delete();

        return back()->with('success', "Semua data pembayaran berhasil dihapus ({$count} transaksi).");
    }
}

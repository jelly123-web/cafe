<?php

namespace App\Http\Controllers;

use App\Models\CashierCart;
use App\Models\CashierCartItem;
use App\Models\Menu;
use App\Models\SaleTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CashierPaymentController extends Controller
{
    public function index(): View
    {
        return view('cashier.payments.index', $this->paymentData());
    }

    public function superadminIndex(): View
    {
        return view('superadmin.payments.index', $this->paymentData());
    }

    public function live(): View
    {
        return view('cashier.payments.live', $this->paymentData());
    }

    public function superadminLive(): View
    {
        return view('superadmin.payments.live', $this->paymentData());
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
            'cart' => $this->cartData((int) auth()->id()),
            'menuCategories' => \App\Models\MenuCategory::query()->orderBy('name')->get(),
        ];
    }

    public function checkoutFromCart(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user, 403);

        $cart = $this->cartModel((int) $user->id);
        $items = $cart->items()->with('menu')->get();
        if ($items->isEmpty()) {
            return back()->with('error', 'Keranjang pembayaran masih kosong. Scan barcode menu terlebih dahulu.');
        }

        $code = $this->nextTransactionCode();

        DB::transaction(function () use ($items, $cart, $code): void {
            $totalAmount = $items->sum(fn (CashierCartItem $item) => (float) $item->unit_price * (int) $item->qty);
            $totalCost = $items->sum(fn (CashierCartItem $item) => (float) $item->unit_cost * (int) $item->qty);

            $sale = SaleTransaction::query()->create([
                'code' => $code,
                'branch_id' => 1,
                'sold_at' => now(),
                'total_amount' => $totalAmount,
                'total_cost' => $totalCost,
                'notes' => 'Scan barcode pembayaran',
                'status' => SaleTransaction::STATUS_PENDING,
            ]);

            foreach ($items as $item) {
                $sale->items()->create([
                    'menu_id' => $item->menu_id,
                    'qty' => $item->qty,
                    'unit_price' => $item->unit_price,
                    'unit_cost' => $item->unit_cost,
                    'line_total' => (float) $item->unit_price * (int) $item->qty,
                    'line_cost' => (float) $item->unit_cost * (int) $item->qty,
                ]);
            }

            $cart->items()->delete();
        });

        return back()->with('success', "Tagihan {$code} berhasil dibuat dan masuk ke daftar pembayaran.");
    }

    public function removeCartItem(Request $request, Menu $menu)
    {
        $user = $request->user();
        abort_unless($user, 403);

        $cart = $this->cartModel((int) $user->id);
        $cart->items()->where('menu_id', $menu->id)->delete();

        return response()->json([
            'message' => "{$menu->name} dihapus dari keranjang pembayaran.",
            'menu_id' => $menu->id,
        ]);
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

    private function cartModel(int $userId): CashierCart
    {
        return CashierCart::query()->firstOrCreate(['user_id' => $userId]);
    }

    private function cartData(int $userId): array
    {
        $cart = $this->cartModel($userId);
        $items = $cart->items()->with('menu')->get();

        $rows = [];
        $total = 0.0;
        foreach ($items as $item) {
            $lineTotal = (float) $item->unit_price * (int) $item->qty;
            $total += $lineTotal;

            $rows[] = [
                'menu_id' => (int) $item->menu_id,
                'name' => $item->menu?->name ?? 'Menu',
                'code' => $item->menu?->code ?? '-',
                'barcode' => $item->menu?->barcode ?? $item->menu?->code ?? '-',
                'qty' => (int) $item->qty,
                'unit_price' => (float) $item->unit_price,
                'line_total' => $lineTotal,
            ];
        }

        return [
            'items' => $rows,
            'count' => count($rows),
            'total' => $total,
        ];
    }

    private function nextTransactionCode(): string
    {
        $max = 0;
        SaleTransaction::query()
            ->where('code', 'like', 'TRX-%')
            ->pluck('code')
            ->each(function (string $code) use (&$max): void {
                if (preg_match('/^TRX-(\d+)$/', $code, $matches)) {
                    $max = max($max, (int) $matches[1]);
                }
            });

        $next = $max + 1;
        do {
            $code = 'TRX-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
            $next++;
        } while (SaleTransaction::query()->where('code', $code)->exists());

        return $code;
    }
}

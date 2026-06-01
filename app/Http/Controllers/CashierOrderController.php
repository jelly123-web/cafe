<?php

namespace App\Http\Controllers;

use App\Models\SaleTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashierOrderController extends Controller
{
    private function baseOrdersQuery()
    {
        return SaleTransaction::query()
            ->with(['table', 'items.menu'])
            ->withCount('items')
            ->whereIn('status', [
                SaleTransaction::STATUS_PENDING,
                SaleTransaction::STATUS_PROCESSING,
                SaleTransaction::STATUS_READY,
            ])
            ->orderByDesc('sold_at')
            ->orderByDesc('id');
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $role = strtolower(trim((string) $user?->role));
        $canAccessOrders = $user && (
            in_array($role, ['kasir', 'staff', 'admin', 'superadmin', 'leader_cashier'], true)
            || $user->hasPermission('manage_orders')
            || $user->hasPermission('view_all_orders')
        );
        abort_unless($canAccessOrders, 403, 'Anda tidak memiliki akses ke halaman pesanan.');

        $orders = $this->baseOrdersQuery()->paginate(10);

        return view('cashier.orders.index', [
            'orders' => $orders,
            'canCancelOrders' => in_array($role, ['kasir', 'staff', 'admin', 'superadmin', 'leader_cashier'], true) || $user->hasPermission('cancel_orders'),
        ]);
    }

    public function live(Request $request): JsonResponse
    {
        $user = $request->user();
        $role = strtolower(trim((string) $user?->role));
        $canAccessOrders = $user && (
            in_array($role, ['kasir', 'staff', 'admin', 'superadmin', 'leader_cashier'], true)
            || $user->hasPermission('manage_orders')
            || $user->hasPermission('view_all_orders')
        );
        abort_unless($canAccessOrders, 403, 'Anda tidak memiliki akses ke halaman pesanan.');

        $orders = $this->baseOrdersQuery()->paginate(10);
        $latestOrder = $orders->first();

        return response()->json([
            'ok' => true,
            'html' => view('cashier.orders._list', [
                'orders' => $orders,
                'canCancelOrders' => in_array($role, ['kasir', 'staff', 'admin', 'superadmin', 'leader_cashier'], true) || $user->hasPermission('cancel_orders'),
            ])->render(),
            'latest' => $latestOrder ? [
                'id' => $latestOrder->id,
                'code' => $latestOrder->code,
            ] : null,
            'count' => $orders->total(),
        ]);
    }

    public function cancel(Request $request, SaleTransaction $order): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $role = strtolower(trim((string) $user?->role));
        $canCancel = $user && (
            in_array($role, ['kasir', 'staff', 'admin', 'superadmin', 'leader_cashier'], true)
            || $user->hasPermission('cancel_orders')
        );
        abort_unless($canCancel, 403, 'Anda tidak memiliki izin membatalkan pesanan.');

        if (! $order->canBeCancelled()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Pesanan ini tidak bisa dibatalkan.',
                ], 422);
            }

            return back()->with('error', 'Pesanan ini tidak bisa dibatalkan.');
        }

        $order->update([
            'status' => SaleTransaction::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancelled_by' => $user->id,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => "Pesanan {$order->code} berhasil dibatalkan.",
            ]);
        }

        return back()->with('success', "Pesanan {$order->code} berhasil dibatalkan.");
    }
}

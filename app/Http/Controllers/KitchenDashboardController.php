<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\SaleTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class KitchenDashboardController extends Controller
{
    public function dashboard(): View
    {
        $today = now()->toDateString();
        $hasStatus = Schema::hasColumn('sale_transactions', 'status');

        $todayQuery = SaleTransaction::query()->whereDate('sold_at', $today);
        $totalToday = (clone $todayQuery)->count();

        $pendingCount = $hasStatus
            ? (clone $todayQuery)->where('status', SaleTransaction::STATUS_PENDING)->count()
            : 0;
        $processingCount = $hasStatus
            ? (clone $todayQuery)->where('status', SaleTransaction::STATUS_PROCESSING)->count()
            : 0;
        $completedCount = $hasStatus
            ? (clone $todayQuery)->where('status', SaleTransaction::STATUS_COMPLETED)->count()
            : 0;

        return view('kitchen.overview', [
            'hasStatus' => $hasStatus,
            'totalToday' => $totalToday,
            'pendingCount' => $pendingCount,
            'processingCount' => $processingCount,
            'completedCount' => $completedCount,
        ]);
    }

    public function dashboardLive(): JsonResponse
    {
        $today = now()->toDateString();
        $hasStatus = Schema::hasColumn('sale_transactions', 'status');
        $todayQuery = SaleTransaction::query()->whereDate('sold_at', $today);

        return response()->json([
            'ok' => true,
            'totalToday' => (clone $todayQuery)->count(),
            'pendingCount' => $hasStatus ? (clone $todayQuery)->where('status', SaleTransaction::STATUS_PENDING)->count() : 0,
            'processingCount' => $hasStatus ? (clone $todayQuery)->where('status', SaleTransaction::STATUS_PROCESSING)->count() : 0,
            'completedCount' => $hasStatus ? (clone $todayQuery)->where('status', SaleTransaction::STATUS_COMPLETED)->count() : 0,
        ]);
    }

    public function index(Request $request): View
    {
        $hasCustomerName = Schema::hasColumn('sale_transactions', 'customer_name');
        $hasStatus = Schema::hasColumn('sale_transactions', 'status');

        $orders = SaleTransaction::query()
            ->with(['table', 'items.menu', 'items.foodPackage'])
            ->withCount('items')
            ->whereDate('sold_at', now()->toDateString())
            ->when($hasStatus, function ($q) {
                $q->whereIn('status', [
                    SaleTransaction::STATUS_PENDING,
                    SaleTransaction::STATUS_PROCESSING,
                    SaleTransaction::STATUS_READY,
                ]);
            })
            ->orderByDesc('sold_at')
            ->paginate(8);

        return view('kitchen.dashboard', [
            'orders' => $orders,
            'hasCustomerName' => $hasCustomerName,
            'hasStatus' => $hasStatus,
            'kitchenStatuses' => SaleTransaction::kitchenStatuses(),
        ]);
    }

    public function liveOrders(): JsonResponse
    {
        $hasCustomerName = Schema::hasColumn('sale_transactions', 'customer_name');
        $hasStatus = Schema::hasColumn('sale_transactions', 'status');

        $orders = SaleTransaction::query()
            ->with(['table', 'items.menu', 'items.foodPackage'])
            ->withCount('items')
            ->whereDate('sold_at', now()->toDateString())
            ->when($hasStatus, function ($q) {
                $q->whereIn('status', [
                    SaleTransaction::STATUS_PENDING,
                    SaleTransaction::STATUS_PROCESSING,
                    SaleTransaction::STATUS_READY,
                ]);
            })
            ->orderByDesc('sold_at')
            ->get();

        $latestOrderTs = optional($orders->first()?->sold_at)?->timestamp ?? 0;

        return response()->json([
            'count' => $orders->count(),
            'latest_ts' => $latestOrderTs,
            'html' => view('kitchen.partials.orders', [
                'orders' => $orders,
                'hasCustomerName' => $hasCustomerName,
                'hasStatus' => $hasStatus,
                'kitchenStatuses' => SaleTransaction::kitchenStatuses(),
            ])->render(),
        ]);
    }

    public function history(): View
    {
        $hasStatus = Schema::hasColumn('sale_transactions', 'status');

        $history = SaleTransaction::query()
            ->with(['table', 'items.menu', 'items.foodPackage'])
            ->when($hasStatus, fn ($q) => $q->whereIn('status', [
                SaleTransaction::STATUS_COMPLETED,
                SaleTransaction::STATUS_PAID,
            ]))
            ->orderByDesc('updated_at')
            ->paginate(12);

        return view('kitchen.history', [
            'history' => $history,
            'hasStatus' => $hasStatus,
        ]);
    }

    public function historyLive(Request $request): JsonResponse
    {
        $hasStatus = Schema::hasColumn('sale_transactions', 'status');
        $history = SaleTransaction::query()
            ->with(['table', 'items.menu', 'items.foodPackage'])
            ->when($hasStatus, fn ($q) => $q->whereIn('status', [
                SaleTransaction::STATUS_COMPLETED,
                SaleTransaction::STATUS_PAID,
            ]))
            ->orderByDesc('updated_at')
            ->paginate(12, ['*'], 'page', (int) $request->query('page', 1));

        return response()->json([
            'ok' => true,
            'html' => view('kitchen.partials.history_rows', ['history' => $history])->render(),
            'pagination' => $history->links('components.pagination')->render(),
        ]);
    }

    public function menus(): View
    {
        $menus = Menu::query()
            ->with('category')
            ->where('code', '!=', 'A01')
            ->orderBy('name')
            ->paginate(12);

        return view('kitchen.menus', [
            'menus' => $menus,
        ]);
    }

    public function menusLive(Request $request): JsonResponse
    {
        $menus = Menu::query()
            ->with('category')
            ->where('code', '!=', 'A01')
            ->orderBy('name')
            ->paginate(12, ['*'], 'page', (int) $request->query('page', 1));

        return response()->json([
            'ok' => true,
            'html' => view('kitchen.partials.menu_rows', ['menus' => $menus])->render(),
            'pagination' => $menus->links('components.pagination')->render(),
        ]);
    }

    public function toggleMenuStock(Request $request, Menu $menu): RedirectResponse|JsonResponse
    {
        if (! Schema::hasColumn('menus', 'is_sold_out')) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Kolom stok menu belum tersedia. Jalankan migrate terlebih dahulu.',
                ], 422);
            }
            return back()->with('error', 'Kolom stok menu belum tersedia. Jalankan migrate terlebih dahulu.');
        }

        $menu->update([
            'is_sold_out' => ! $menu->is_sold_out,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => $menu->is_sold_out
                    ? "{$menu->name} ditandai habis."
                    : "{$menu->name} ditandai tersedia kembali.",
                'is_sold_out' => (bool) $menu->is_sold_out,
            ]);
        }

        return back()->with('success', $menu->is_sold_out
            ? "{$menu->name} ditandai habis."
            : "{$menu->name} ditandai tersedia kembali.");
    }

    public function updateStatus(Request $request, SaleTransaction $order): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys(SaleTransaction::kitchenStatuses()))],
        ]);

        if (! Schema::hasColumn('sale_transactions', 'status')) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Kolom status belum tersedia. Jalankan migrate terlebih dahulu.',
                ], 422);
            }
            return back()->with('error', 'Kolom status belum tersedia. Jalankan migrate terlebih dahulu.');
        }

        $order->update([
            'status' => $data['status'],
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => "Status {$order->code} diubah menjadi {$order->statusLabel()}.",
                'status' => $order->status,
                'status_label' => $order->statusLabel(),
            ]);
        }

        return back()->with('success', "Status {$order->code} diubah menjadi {$order->statusLabel()}.");
    }

    public function destroy(SaleTransaction $order): RedirectResponse
    {
        $code = $order->code;
        $order->delete();

        return back()->with('success', "Pesanan {$code} dihapus dari antrean dapur.");
    }
}

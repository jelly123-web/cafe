<?php

namespace App\Http\Controllers;

use App\Models\SaleTransaction;
use Illuminate\Http\JsonResponse;

class LiveSyncController extends Controller
{
    public function orders(): JsonResponse
    {
        $latestOrder = SaleTransaction::query()
            ->with(['table', 'items.menu', 'items.foodPackage'])
            ->whereDate('sold_at', now()->toDateString())
            ->whereIn('status', [
                SaleTransaction::STATUS_PENDING,
                SaleTransaction::STATUS_PROCESSING,
                SaleTransaction::STATUS_READY,
            ])
            ->orderByDesc('sold_at')
            ->orderByDesc('id')
            ->first();

        $activeCount = SaleTransaction::query()
            ->whereDate('sold_at', now()->toDateString())
            ->whereIn('status', [
                SaleTransaction::STATUS_PENDING,
                SaleTransaction::STATUS_PROCESSING,
                SaleTransaction::STATUS_READY,
            ])
            ->count();

        return response()->json([
            'ok' => true,
            'active_count' => $activeCount,
            'latest' => $latestOrder ? [
                'id' => $latestOrder->id,
                'code' => $latestOrder->code,
                'status' => $latestOrder->status,
                'status_label' => $latestOrder->statusLabel(),
                'table_label' => $latestOrder->table
                    ? 'Meja ' . $latestOrder->table->number
                    : 'Tanpa meja',
                'items_label' => $latestOrder->items
                    ->map(function ($item) {
                        $name = $item->food_package_id
                            ? ($item->foodPackage?->name ?? 'Paket')
                            : ($item->menu?->name ?? 'Menu');

                        return $item->qty . 'x ' . $name;
                    })
                    ->implode(', '),
                'updated_ts' => optional($latestOrder->updated_at ?? $latestOrder->sold_at)?->timestamp ?? 0,
            ] : null,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\SaleTransaction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class KitchenDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $orders = SaleTransaction::query()
            ->with(['table', 'items.menu'])
            ->withCount('items')
            ->whereDate('sold_at', now()->toDateString())
            ->orderByDesc('sold_at')
            ->get();

        return view('kitchen.dashboard', [
            'orders' => $orders,
            'orderCount' => $orders->count(),
            'itemCount' => $orders->sum('items_count'),
        ]);
    }
}

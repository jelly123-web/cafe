<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user?->role === 'kitchen') {
            return redirect()->route('kitchen.dashboard');
        }

        if ($user?->role === 'inventory') {
            return redirect()->route('inventory.index');
        }

        if ($user?->role === 'leader_cashier') {
            return redirect()->route('leader-cashier.index');
        }

        if (in_array((string) $user?->role, ['kasir', 'staff', 'admin'], true)) {
            return redirect()->route('cashier.orders.index');
        }

        return view('dashboard', [
            'user' => $user,
            'permissions' => collect(\App\Models\User::PERMISSIONS)
                ->map(fn (string $label, string $key) => [
                    'key' => $key,
                    'label' => $label,
                    'enabled' => $user?->hasPermission($key) ?? false,
                ])
                ->values(),
        ]);
    }
}

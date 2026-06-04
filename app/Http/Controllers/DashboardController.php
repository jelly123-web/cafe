<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private function normalizeRole(?string $role): string
    {
        return match (strtolower(trim((string) $role))) {
            'dapur' => 'kitchen',
            default => strtolower(trim((string) $role)),
        };
    }

    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $role = $this->normalizeRole($user?->role);

        if ($role === 'kitchen') {
            return redirect()->route('kitchen.dashboard');
        }

        if ($role === 'inventory') {
            return redirect()->route('inventory.index');
        }

        if ($role === 'leader_cashier') {
            return redirect()->route('leader-cashier.index');
        }

        if (in_array($role, ['kasir', 'staff', 'admin'], true)) {
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

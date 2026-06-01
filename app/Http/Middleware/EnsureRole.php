<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    private const ROUTE_PERMISSION_MAP = [
        'superadmin.dashboard' => 'superadmin_dashboard',
        'superadmin.users.' => 'superadmin_users',
        'superadmin.access.' => 'superadmin_access',
        'superadmin.menus.' => 'superadmin_menus',
        'superadmin.employees.' => 'superadmin_employees',
        'superadmin.payrolls.' => 'superadmin_payrolls',
        'superadmin.menu-categories.' => 'superadmin_menu_categories',
        'superadmin.tables.' => 'superadmin_tables',
        'superadmin.reports.' => 'superadmin_reports',
        'superadmin.settings.' => 'superadmin_settings',

        'cashier.orders.' => 'cashier_orders',
        'cashier.transactions.' => 'cashier_transactions',
        'cashier.payments.' => 'cashier_payments',
        'cashier.receipts.' => 'cashier_receipts',
        'cashier.tables.' => 'cashier_tables',
        'cashier.reports.' => 'cashier_reports',

        'kitchen.dashboard' => 'kitchen_orders',
        'kitchen.orders.' => 'kitchen_orders',
        'kitchen.history' => 'kitchen_history',
        'kitchen.history.' => 'kitchen_history',
        'kitchen.menus.index' => 'kitchen_menus',
        'kitchen.menus.' => 'kitchen_menus',

        'inventory.index' => 'inventory_index',
        'inventory.in.' => 'inventory_movement',
        'inventory.out.' => 'inventory_movement',
        'inventory.stock.' => 'inventory_movement',
        'inventory.items.' => 'inventory_index',
        'inventory.categories.' => 'inventory_index',

        'leader-cashier.index' => 'leader_monitoring',
        'leader-cashier.cash-flow.' => 'leader_cashflow',
    ];

    private function mappedPermissionKey(?string $routeName): ?string
    {
        if (! $routeName) {
            return null;
        }

        foreach (self::ROUTE_PERMISSION_MAP as $prefix => $permissionKey) {
            if ($routeName === $prefix || str_starts_with($routeName, $prefix)) {
                return $permissionKey;
            }
        }

        return null;
    }

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->guest(route('login'));
        }

        $user = Auth::user();
        
        // Robust bypass for superadmin
        if ($user && ($user->role === 'superadmin' || strtolower(trim((string)$user->role)) === 'superadmin')) {
            return $next($request);
        }

        $userRole = strtolower(trim((string) $user?->role));
        $normalizedRoles = array_map(static fn (string $role): string => strtolower(trim($role)), $roles);
        $hasRole = in_array($userRole, $normalizedRoles, true);
        if (! $hasRole) {
            $permissionKey = $this->mappedPermissionKey($request->route()?->getName());
            $hasPermission = $permissionKey && method_exists($user, 'hasPermission')
                ? $user->hasPermission($permissionKey)
                : false;

            if (! $hasPermission) {
                abort(403, 'Maaf, akun Anda tidak memiliki izin untuk mengakses halaman ini.');
            }
        }

        return $next($request);
    }
}

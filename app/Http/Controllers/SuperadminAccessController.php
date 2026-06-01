<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuperadminAccessController extends Controller
{
    /**
     * Normalize legacy role names to the current role vocabulary.
     */
    private function normalizeRole(?string $role): string
    {
        return match ((string) $role) {
            'staff' => 'kasir',
            default => (string) $role,
        };
    }

    public function index(Request $request): View
    {
        $users = User::query()
            ->latest()
            ->get();

        $roleColumns = collect(array_keys(User::ROLE_LABELS))
            ->map(fn ($role) => $this->normalizeRole($role))
            ->unique()
            ->sortBy(function ($role) {
                return match ($role) {
                    'superadmin' => 0,
                    'leader_cashier' => 1,
                    'kitchen' => 2,
                    'inventory' => 3,
                    'kasir' => 4,
                    'admin' => 5,
                    default => 99,
                };
            })
            ->values()
            ->map(function ($role) use ($users) {
                $user = $users->firstWhere('role', $role)
                    ?? $users->firstWhere('role', $role === 'kasir' ? 'staff' : $role);

                return (object) [
                    'role' => $role,
                    'label' => User::ROLE_LABELS[$role] ?? ucfirst(str_replace('_', ' ', $role)),
                    'user' => $user,
                    'permissions' => $user?->permissions ?? [],
                ];
            });

        return view('superadmin.access.index', [
            'users' => $users,
            'permissionDefinitions' => User::PERMISSIONS,
            'roleColumns' => $roleColumns,
        ]);
    }

    public function edit(User $user): View
    {
        return view('superadmin.access.edit', [
            'user' => $user,
            'permissionDefinitions' => User::PERMISSIONS,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'permissions' => ['nullable', 'array'],
        ]);

        $permissions = [];

        foreach (array_keys(User::PERMISSIONS) as $key) {
            $permissions[$key] = $request->boolean("permissions.$key");
        }

        $user->permissions = $permissions;
        $user->save();

        return redirect()
            ->route('superadmin.access.index')
            ->with('status', 'Hak akses berhasil diperbarui.');
    }

    public function updateMatrix(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'permissions' => ['nullable', 'array'],
        ]);

        $permissionKeys = array_keys(User::PERMISSIONS);
        $roles = User::query()
            ->select('role')
            ->where('role', '!=', 'superadmin')
            ->distinct()
            ->get();

        foreach ($roles as $roleRow) {
            $role = $this->normalizeRole((string) $roleRow->role);
            $row = data_get($request->input('permissions', []), $role, []);
            $permissions = [];
            foreach ($permissionKeys as $key) {
                $permissions[$key] = isset($row[$key]);
            }

            $targetRoles = $role === 'kasir'
                ? ['kasir', 'staff']
                : [$role];

            User::query()
                ->whereIn('role', $targetRoles)
                ->update(['permissions' => $permissions]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Checklist hak akses berhasil disinkronkan ke database.',
            ]);
        }

        return redirect()
            ->route('superadmin.access.index')
            ->with('status', 'Checklist hak akses berhasil disinkronkan ke database.');
    }
}

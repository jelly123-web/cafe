<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuperadminAccessController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->string('search')->toString(), function ($query, string $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('superadmin.access.index', [
            'users' => $users,
            'permissionDefinitions' => User::PERMISSIONS,
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
}

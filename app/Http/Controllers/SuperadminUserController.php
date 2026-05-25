<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SuperadminUserController extends Controller
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

        return view('superadmin.users.index', [
            'users' => $users,
        ]);
    }

    public function create(): View
    {
        return view('superadmin.users.create', [
            'user' => new User([
                'role' => 'staff',
                'is_active' => true,
            ]),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateUser($request);

        $user = new User();
        $user->fill($data);
        $user->email = "{$data['username']}@cafe.local";
        $user->password = $data['password'];
        $user->is_active = $request->boolean('is_active');
        $user->save();

        return redirect()
            ->route('superadmin.users.index')
            ->with('status', 'Akun berhasil dibuat.');
    }

    public function edit(User $user): View
    {
        return view('superadmin.users.edit', [
            'user' => $user,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validateUser($request, $user->id);

        $user->fill([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => "{$data['username']}@cafe.local",
            'role' => $data['role'],
        ]);

        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->is_active = $request->boolean('is_active');
        $user->save();

        return redirect()
            ->route('superadmin.users.index')
            ->with('status', 'Akun berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()?->id === $user->id) {
            return back()->with('error', 'Akun sendiri tidak bisa dihapus.');
        }

        if ($user->role === 'superadmin') {
            return back()->with('error', 'Akun superadmin tidak bisa dihapus dari sini.');
        }

        $user->delete();

        return redirect()
            ->route('superadmin.users.index')
            ->with('status', 'Akun berhasil dihapus.');
    }

    private function validateUser(Request $request, ?int $ignoreId = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($ignoreId),
            ],
            'role' => ['required', 'in:superadmin,admin,staff'],
            'password' => [$ignoreId ? 'nullable' : 'required', 'string', 'min:6'],
            'is_active' => ['nullable', 'boolean'],
        ];

        return $request->validate($rules);
    }
}

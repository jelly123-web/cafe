<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\CroppedImageStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SuperadminUserController extends Controller
{
    public function index(Request $request): View|\Illuminate\Http\JsonResponse
    {
        $users = User::query()
            ->when($request->string('search')->toString(), function ($query, string $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->expectsJson()) {
            return response()->json([
                'users' => $users->getCollection()->map(fn (User $user) => $this->userPayload($user))->values(),
                'total' => $users->total(),
                'pagination' => $users->links('components.pagination')->toHtml(),
            ]);
        }

        return view('superadmin.users.index', [
            'users' => $users,
        ]);
    }

    public function create(): View
    {
        return view('superadmin.users.create', [
            'user' => new User([
                'role' => 'kasir',
                'is_active' => true,
            ]),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $this->validateUser($request);

        $user = new User();
        $user->fill($data);
        $user->email = "{$data['username']}@cafe.local";
        $user->password = $data['password'];
        $user->is_active = $request->boolean('is_active');

        if ($request->filled('cropped_profile_photo') || $request->hasFile('profile_photo')) {
            $user->profile_photo_path = $request->filled('cropped_profile_photo')
                ? CroppedImageStore::store($request->string('cropped_profile_photo')->toString(), 'profile-photos', 'profile')
                : $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $user->save();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Akun berhasil dibuat.',
                'user' => $this->userPayload($user->fresh()),
            ]);
        }

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

    public function update(Request $request, User $user): RedirectResponse|JsonResponse
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

        if ($request->filled('cropped_profile_photo') || $request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->profile_photo_path = $request->filled('cropped_profile_photo')
                ? CroppedImageStore::store($request->string('cropped_profile_photo')->toString(), 'profile-photos', 'profile')
                : $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $user->is_active = $request->boolean('is_active');
        $user->save();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Akun berhasil diperbarui.',
                'user' => $this->userPayload($user->fresh()),
            ]);
        }

        return redirect()
            ->route('superadmin.users.index')
            ->with('status', 'Akun berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse|JsonResponse
    {
        $deletedId = $user->id;

        if ($request->user()?->id === $user->id) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akun sendiri tidak bisa dihapus.'], 422);
            }
            return back()->with('error', 'Akun sendiri tidak bisa dihapus.');
        }

        if ($user->role === 'superadmin') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akun superadmin tidak bisa dihapus dari sini.'], 422);
            }
            return back()->with('error', 'Akun superadmin tidak bisa dihapus dari sini.');
        }

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Akun berhasil dihapus.',
                'id' => $deletedId,
            ]);
        }

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
                'max:30',
                Rule::unique('users', 'username')->ignore($ignoreId),
            ],
            'role' => ['required', 'in:superadmin,admin,kasir,staff,leader_cashier,kitchen,inventory'],
            'password' => [$ignoreId ? 'nullable' : 'required', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'profile_photo' => ['nullable', 'image', 'max:1024'],
            'cropped_profile_photo' => ['nullable', 'string'],
        ];

        $data = $request->validate($rules);
        if (($data['role'] ?? null) === 'staff') {
            $data['role'] = 'kasir';
        }

        return $data;
    }

    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'role' => $user->role,
            'role_label' => $user->roleLabel(),
            'is_active' => (bool) $user->is_active,
            'profile_photo_url' => $user->profile_photo_url,
            'can_delete' => $user->role !== 'superadmin',
        ];
    }
}

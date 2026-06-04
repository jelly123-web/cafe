<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    private function normalizeRole(?string $role): string
    {
        return match (strtolower(trim((string) $role))) {
            'dapur' => 'kitchen',
            default => strtolower(trim((string) $role)),
        };
    }

    private function homeUrlForRole(?string $role): string
    {
        return route(match ($this->normalizeRole($role)) {
            'superadmin' => 'superadmin.dashboard',
            'kitchen' => 'kitchen.orders.index',
            'inventory' => 'inventory.index',
            'leader_cashier' => 'leader-cashier.index',
            'kasir', 'admin', 'staff' => 'cashier.orders.index',
            default => 'dashboard',
        });
    }

    public function create(Request $request): View|RedirectResponse|Response
    {
        if (Auth::check()) {
            return redirect()->intended($this->homeUrlForRole(Auth::user()?->role));
        }

        $request->session()->regenerateToken();

        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                if ($request->ajax()) {
                    return response()->json(['error' => 'Akun Anda sudah tidak aktif.'], 403);
                }
                return back()->withErrors(['username' => 'Akun Anda sudah tidak aktif.'])->onlyInput('username');
            }

            $request->session()->regenerate();

            $redirectUrl = $this->homeUrlForRole($user->role);

            $intendedUrl = session()->pull('url.intended', $redirectUrl);

            if ($request->ajax()) {
                return response()->json(['redirect' => $intendedUrl]);
            }

            return redirect()->to($intendedUrl);
        }

        if ($request->ajax()) {
            return response()->json(['error' => 'Username atau password salah.'], 401);
        }

        return back()
            ->withErrors(['username' => 'Username atau password salah.'])
            ->onlyInput('username');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

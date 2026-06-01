<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
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

            $redirectUrl = route(match ($user->role) {
                'superadmin' => 'superadmin.dashboard',
                'kitchen' => 'kitchen.orders.index',
                'inventory' => 'inventory.index',
                'leader_cashier' => 'leader-cashier.index',
                'kasir', 'admin', 'staff' => 'cashier.orders.index',
                default => 'dashboard',
            });

            if ($request->ajax()) {
                return response()->json(['redirect' => $redirectUrl]);
            }

            return redirect()->to($redirectUrl);
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

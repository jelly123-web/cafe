<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

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

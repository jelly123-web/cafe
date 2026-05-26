<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            abort(403, 'Silakan login terlebih dahulu.');
        }

        if (! in_array((string) Auth::user()?->role, $roles, true)) {
            abort(403, 'Halaman ini tidak tersedia untuk akun Anda.');
        }

        return $next($request);
    }
}

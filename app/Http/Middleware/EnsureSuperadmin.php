<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperadmin
{
    private function normalizeRole(?string $role): string
    {
        return strtolower(trim((string) $role));
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->guest(route('login'));
        }

        if ($this->normalizeRole(Auth::user()?->role) !== 'superadmin') {
            abort(403, 'Halaman ini khusus superadmin.');
        }

        return $next($request);
    }
}

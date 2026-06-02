<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'user') {
            abort(403, 'Halaman ini hanya bisa diakses oleh pengguna.');
        }

        return $next($request);
    }
}

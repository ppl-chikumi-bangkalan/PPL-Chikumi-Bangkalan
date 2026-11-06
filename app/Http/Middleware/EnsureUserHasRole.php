<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! in_array($user->role, $roles, true)) {
            if ($request->expectsJson()) {
                abort(403, 'Anda tidak memiliki hak akses untuk menu ini.');
            }

            return redirect()
                ->route('home')
                ->with('error', 'Anda tidak memiliki hak akses untuk menu tersebut.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            abort(403, 'No autorizado');
        }

        foreach ($roles as $role) {
            if (Auth::user()->hasRole(strtolower($role))) {
                return $next($request);
            }
        }

        abort(403, 'No autorizado');
    }
}

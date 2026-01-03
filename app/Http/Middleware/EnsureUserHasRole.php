<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user()) {
             abort(403, 'Unauthorized access.');
        }

        if ($request->user()->hasRole($role)) {
            return $next($request);
        }

        // Admin has full access
        if ($request->user()->hasRole('admin')) {
            return $next($request);
        }

        abort(403, 'Unauthorized access.');
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsTipster
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || (! $user->hasRole('tipster') && ! $user->hasRole('admin'))) {
            abort(403, 'Access denied. Tipster role required.');
        }

        return $next($request);
    }
}

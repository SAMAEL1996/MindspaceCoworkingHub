<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExternalApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('auth_token')) {
            if ($request->auth_token == config('app.external_api_token')) {
                return $next($request);
            } else {
                abort(401, 'Invalid token');
            }
        }

        abort(404);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictAdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (!setting('ip_whitelist')) {
            return $next($request);
        }

        $whitelistedIps = json_decode(setting('ip_whitelist_list', '["127.0.0.1"]'), true);


        if (!in_array($request->ip(), $whitelistedIps)) {
            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyCacheSettings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!file_exists(storage_path('installed'))) {
            return $next($request);
        }

        $cacheEnabled = \App\Models\Setting::get('cache_enabled', false);

        if (!$cacheEnabled) {
            config(['cache.default' => 'array']);
        }

        return $next($request);
    }
}

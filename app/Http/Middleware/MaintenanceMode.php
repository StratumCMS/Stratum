<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next): Response
    {
        if (!file_exists(storage_path('installed'))) {
            return $next($request);
        }

        $isMaintenance = setting('maintenance_mode');
        $user = auth()->user();
        $isAdmin = $user && $user->roles()->first()?->role === 'admin';

        $excludedRoutes = [
            'login',
            'register',
            'password.*',
            'admin.settings',
            'admin.settings.update',
        ];

        $excludedPrefixes = [
            'admin',
        ];

        if ($isMaintenance && !$isAdmin) {
            if ($request->route()?->getName() && $request->routeIs(...$excludedRoutes)) {
                return $next($request);
            }

            foreach ($excludedPrefixes as $prefix) {
                if ($request->is($prefix . '*')) {
                    return $next($request);
                }
            }

            return response()->view('maintenance', [], 503);
        }

        return $next($request);
    }
}

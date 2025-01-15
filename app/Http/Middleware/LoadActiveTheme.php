<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use App\Models\Theme;
class LoadActiveTheme
{
    public function handle($request, Closure $next)
    {
        if (Route::is('admin.*') || Route::is('install.*')) {
            return $next($request);
        }

        $activeTheme = Theme::where('active', true)->first();

        if ($activeTheme) {
            View::addNamespace('theme', resource_path('themes/' . $activeTheme->slug . '/views'));
            config(['theme.assets' => asset('resources/themes/' . $activeTheme->slug . '/assets')]);
        }

        return $next($request);
    }

}

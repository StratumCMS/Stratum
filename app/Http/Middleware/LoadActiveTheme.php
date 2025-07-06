<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;
use App\Models\Theme;

class LoadActiveTheme
{
    public function handle($request, Closure $next)
    {
        $slug = $request->query('preview');

        $theme = $slug
            ? Theme::where('slug', $slug)->first()
            : Theme::where('active', true)->first();

        if ($theme) {
            View::flushFinderCache();

            View::addNamespace('theme', resource_path("themes/{$theme->slug}/views"));
            View::addNamespace('themes', resource_path("themes/{$theme->slug}/"));

            config(['theme.assets' => asset("resources/themes/{$theme->slug}/assets")]);
        }

        $themesPublicPath = public_path('themes-assets');
        $themesResourcePath = resource_path('themes');

        if (!file_exists($themesPublicPath)) {
            File::link($themesResourcePath, $themesPublicPath);
        }

        return $next($request);
    }
}

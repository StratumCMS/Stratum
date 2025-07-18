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
        if (!file_exists(storage_path('installed'))) {
            return $next($request);
        }

        $slug = $request->query('preview') ?? Theme::where('active', true)->value('slug');
        if (!$slug) return $next($request);

        $themePath = resource_path("themes/{$slug}");

        if (File::exists("{$themePath}/views")) {
            View::flushFinderCache();
            View::getFinder()->prependLocation("{$themePath}/views");

            View::addNamespace('theme', "{$themePath}/views");
            View::addNamespace('themes', $themePath);

            config(['theme.assets' => asset("themes-assets/{$slug}/assets")]);
        }

        $publicAssets = public_path('themes-assets');
        if (!file_exists($publicAssets)) {
            symlink(resource_path('themes'), $publicAssets);
        }

        if (str_starts_with($request->route()?->getName() ?? '', 'admin.')) {
            return $next($request);
        }


        return $next($request);
    }

}

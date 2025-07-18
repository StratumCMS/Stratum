<?php

namespace App\Support;

use App\Models\Theme;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class ThemeManager
{
    protected string $themesPath;

    public function __construct()
    {
        $this->themesPath = resource_path('themes');
    }

    public function scan(): void
    {
        $directories = File::directories($this->themesPath);

        foreach ($directories as $dir) {
            $manifestPath = $dir . '/theme.json';
            if (!File::exists($manifestPath)) continue;

            $manifest = json_decode(File::get($manifestPath), true);
            if (!isset($manifest['slug'])) continue;

            Theme::updateOrCreate(
                ['slug' => $manifest['slug']],
                [
                    'name' => $manifest['name'] ?? ucfirst($manifest['slug']),
                    'version' => $manifest['version'] ?? '1.0.0',
                    'author' => $manifest['author'] ?? 'Anonyme',
                    'description' => $manifest['description'] ?? '',
                    'path' => 'themes/' . basename($dir),
                ]
            );
        }
    }

    public function activate(string $slug): bool
    {
        $theme = Theme::where('slug', $slug)->first();
        if (!$theme) return false;

        Theme::where('active', true)->update(['active' => false]);
        $theme->update(['active' => true]);

        return true;
    }

    public function deactivate(string $slug): bool
    {
        $theme = Theme::where('slug', $slug)->first();
        if (!$theme) return false;

        $theme->update(['active' => false]);
        return true;
    }


    public function registerViewNamespaces(): void
    {
        $theme = Theme::where('active', true)->first();
        if (!$theme) return;

        $themeViews = resource_path("themes/{$theme->slug}/views");
        $themeRoot = resource_path("themes/{$theme->slug}");

        View::flushFinderCache();

        View::getFinder()->prependLocation($themeViews);

        View::addNamespace('theme', $themeViews);
        View::addNamespace('themes', $themeRoot);

        config(['theme.assets' => asset("themes-assets/{$theme->slug}/assets")]);
    }

}

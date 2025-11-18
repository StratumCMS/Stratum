<?php

use App\Models\Module;
use App\Models\NavbarElement;
use App\Models\Setting;
use App\Models\Theme;
use App\Support\ModuleComponentRenderer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

if (!function_exists('setting')) {
    function setting($key, $default = null) {
        if (!app()->runningInConsole() && !Schema::hasTable('settings')) {
            return $default;
        }

        try {
            return Setting::get($key, $default);
        } catch (\Throwable $e) {
            return $default;
        }
    }
}

if (!function_exists('theme_view')) {
    function theme_view(string $view, array $data = []) {
        if (app()->runningInConsole() || !Schema::hasTable('themes')) {
            return view($view, $data);
        }

        try {
            $activeTheme = Theme::where('active', true)->first();

            if ($activeTheme && view()->exists("theme::$view")) {
                return view("theme::$view", $data);
            }
        } catch (\Throwable $e) {
        }

        return view($view, $data);
    }
}

if (!function_exists('theme_asset')) {
    function theme_asset(string $path): string {
        try {
            if (!app()->runningInConsole() && Schema::hasTable('themes')) {
                $theme = Theme::where('active', true)->first();

                if ($theme) {
                    return asset("themes/{$theme->slug}/assets/" . ltrim($path, '/'));
                }
            }
        } catch (\Throwable $e) {}

        return asset("themes/default/assets/" . ltrim($path, '/'));
    }
}

if (!function_exists('theme_config')) {
    function theme_config(?string $key = null, mixed $default = null): mixed
    {
        return $key === null ? config('theme') : config('theme.'.$key, $default);
    }
}

if (!function_exists('module_asset')) {
    function module_asset(string|array $path, ?string $slug = null): string
    {
        try {
            if (is_array($path)) $path = implode('/', $path);

            if (app()->runningInConsole() || !Schema::hasTable('modules')) {
                return asset(ltrim($path, '/'));
            }

            if (!$slug) {
                $module = Module::where('active', true)->first();
                if (!$module) return asset(ltrim($path, '/'));
                $slug = $module->slug;
            }

            $fullPath = public_path("modules_public/{$slug}/assets/" . ltrim($path, '/'));
            if (!file_exists($fullPath)) {
                Log::warning("module_asset: fichier introuvable {$fullPath}");
                return asset(ltrim($path, '/'));
            }

            return asset("modules_public/{$slug}/assets/" . ltrim($path, '/'));
        } catch (\Throwable $e) {
            Log::error('module_asset error: ' . $e->getMessage());
        }

        return asset(ltrim(is_array($path) ? implode('/', $path) : $path, '/'));
    }
}

if (!function_exists('module_assets')) {
    function module_assets(?string $type = null): \Illuminate\Support\HtmlString
    {
        if (app()->runningInConsole() || !Schema::hasTable('modules')) return new \Illuminate\Support\HtmlString('');

        $html = '';
        $modules = Module::where('active', true)->get();

        foreach ($modules as $module) {
            $slug = $module->slug;
            $baseUrl = asset("modules_public/{$slug}/assets");

            $pluginJson = base_path("modules_public/{$slug}/plugin.json");
            $declared = [];

            if (File::exists($pluginJson)) {
                $content = json_decode(File::get($pluginJson), true);
                $declared = $content['assets'] ?? [];
            }

            $addFile = function ($relativePath) use (&$html, $baseUrl) {
                $ext = pathinfo($relativePath, PATHINFO_EXTENSION);
                $url = rtrim($baseUrl, '/') . '/' . ltrim($relativePath, '/');
                if ($ext === 'css') $html .= '<link rel="stylesheet" href="' . e($url) . '">' . PHP_EOL;
                if ($ext === 'js') $html .= '<script src="' . e($url) . '"></script>' . PHP_EOL;
            };

            if (!empty($declared)) {
                if (($type === null || $type === 'css') && !empty($declared['css'])) {
                    foreach ($declared['css'] as $f) $addFile($f);
                }
                if (($type === null || $type === 'js') && !empty($declared['js'])) {
                    foreach ($declared['js'] as $f) $addFile($f);
                }
                continue;
            }

            $publicDir = public_path("modules_public/{$slug}/assets");
            if (File::isDirectory($publicDir)) {
                $files = File::allFiles($publicDir);
                foreach ($files as $file) {
                    $ext = $file->getExtension();
                    if ($type && $type !== $ext) continue;
                    $relativePath = str_replace($publicDir . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $relativePath = str_replace('\\', '/', $relativePath);
                    $addFile($relativePath);
                }
            }
        }

        return new \Illuminate\Support\HtmlString($html);
    }
}


if (!function_exists('render_module_components')) {
    function render_module_components(string $content): string
    {
        try {
            return app(ModuleComponentRenderer::class)->render($content);
        } catch (\Throwable $e) {
            \Log::error("Erreur lors du rendu des composants: {$e->getMessage()}");
            return $content;
        }
    }
}

if (!function_exists('available_module_components')) {
    function available_module_components(): array
    {
        try {
            return app(ModuleComponentRenderer::class)->available();
        } catch (\Throwable $e) {
            return [];
        }
    }
}

if (!function_exists('format_date')) {
    function format_date($date, $format = 'd M Y') {
        return $date ? \Carbon\Carbon::parse($date)->translatedFormat($format) : '';
    }
}


if (!function_exists('format_date')) {
    function format_date($date, $format = 'd M Y') {
        return $date ? \Carbon\Carbon::parse($date)->translatedFormat($format) : '';
    }
}

if (!function_exists('site_name')) {
    function site_name()
    {
        return setting('site_name', config('app.name'));
    }
}

if (!function_exists('site_logo')) {
    function site_logo()
    {
        return setting('site_logo', '/default-logo.png');
    }
}

if (!function_exists('favicon')) {
    function favicon()
    {
        return setting('site_favicon', '/default-logo.png');
    }
}

if (!function_exists('get_navigation_items')) {
    function get_navigation_items()
    {
        try {
            if (!file_exists(storage_path('installed')) || !Schema::hasTable('navbar_elements')) {
                return collect();
            }

            return NavbarElement::with(['elements' => fn($q) => $q->orderBy('position')])
                ->whereNull('parent_id')
                ->orderBy('position')
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }
}

if (!function_exists('safe_url')) {
    function safe_url($url)
    {
        if (is_string($url) && Str::startsWith($url, ['http://', 'https://', '/'])) {
            return $url;
        }

        if ($url instanceof \Illuminate\Contracts\Support\Htmlable || is_object($url)) {
            return (string) $url;
        }

        return url($url);
    }
}


if (!function_exists('plugin_has')){
    function plugin_has(string $slug): bool {
        try {
            if (!file_exists(storage_path('installed')) || !Schema::hasTable('modules')) {
                return false;
            }


            $isActive = Module::where('slug', $slug)->where('active', true)->exists();
            if (!$isActive){
                return false;
            }

            return app()->bound('plugin.'.$slug);
        } catch (\Throwable $e) {
            return false;
        }
    }
}


if (!function_exists('plugin_load')) {

    function plugin_load(string $slug) {
        if (!plugin_has($slug)) {
            throw new \RuntimeException("Le plugin [$slug] n'est pas actif ou n'a pas exposé d'API.");
        }

        return app('plugin.'.$slug);
    }
}

if (!function_exists('plugin_call')){

    function plugin_call(string $slug, string $method, ...$args){
        $api = plugin_load($slug);

        if (!method_exists($api, $method)) {
            $class = is_object($api) ? get_class($api) : (string) $api;
            throw new \RuntimeException("La méthode [$method] n'existe pas sur l'API $class du plugin [$slug].");
        }

        return $api->{$method}(...$args);
    }

}


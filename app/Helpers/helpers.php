<?php

use App\Models\NavbarElement;
use App\Models\Setting;
use App\Models\Theme;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

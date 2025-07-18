<?php

use App\Models\NavbarElement;
use App\Models\Setting;
use App\Models\Theme;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

if (!function_exists('setting')) {
    function setting($key, $default = null) {
        return \App\Models\Setting::get($key, $default);
    }
}

if (!function_exists('theme_view')) {
    function theme_view(string $view, array $data = []) {
        $activeTheme = Theme::where('active', true)->first();

        if ($activeTheme && view()->exists("theme::$view")) {
            return view("theme::$view", $data);
        }

        return view("pages.$view", $data);
    }
}

if (!function_exists('theme_asset')) {
    function theme_asset(string $path): string {
        $theme = Theme::where('active', true)->first();

        if ($theme) {
            return asset("themes-assets/{$theme->slug}/assets/" . ltrim($path, '/'));
        }

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


if (!function_exists('site_name')) {
    function site_name()
    {
        return Setting::get('site_name', config('app.name'));
    }
}

if (!function_exists('site_logo')) {
    function site_logo()
    {
        return Setting::get('site_logo', '/default-logo.png');
    }
}

if (!function_exists('favicon')) {
    function favicon()
    {
        return Setting::get('site_favicon', '/default-logo.png');
    }
}

function get_navigation_items()
{
    if (!file_exists(storage_path('installed'))) {
        return collect();
    }
    return \App\Models\NavbarElement::with(['elements' => fn($q) => $q->orderBy('position')])
        ->whereNull('parent_id')
        ->orderBy('position')
        ->get();
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

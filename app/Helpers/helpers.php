<?php

use App\Models\Theme;
use Illuminate\Support\Facades\DB;

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

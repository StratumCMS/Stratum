<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use App\Models\Module;

class ModuleManager{

    public static function all(): array {
        $paths = File::directories(base_path('modules'));
        $modules = [];

        foreach ($paths as $path) {
            $manifestPath = $path . '/plugin.json';
            if (!File::exists($manifestPath)) continue;

            $config = json_decode(File::get($manifestPath), true);
            $config['slug'] = basename($path);
            $config['path'] = $path;
            $modules[] = $config;
        }

        return $modules;
    }

    public static function active(): array
    {
        return Module::where('active', true)->pluck('slug')->toArray();
    }

    public static function registerActiveModules(): void
    {
        foreach (self::all() as $module) {
            if (!in_array($module['slug'], self::active())) continue;

            foreach ($module['providers'] ?? [] as $provider) {
                app()->register($provider);
            }
        }
    }

}

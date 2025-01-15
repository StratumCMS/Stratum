<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Theme;
class ThemeServiceProvider extends ServiceProvider{

    public function boot()
    {

        if ($this->app->runningInConsole()) {
            return;
        }

        if (Schema::hasTable('themes')) {
            $activeTheme = Theme::where('active', true)->first();

            if ($activeTheme) {
                View::addNamespace('theme', resource_path('themes/' . $activeTheme->slug . '/views'));

                config(['theme.assets' => asset('resources/themes/' . $activeTheme->slug . '/assets')]);
            }
        }
    }

}

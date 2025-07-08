<?php

namespace App\Providers;

use App\Models\Theme;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (str_starts_with($view->getName(), 'admin.')) {
                return;
            }

            $view->with('navigationItems', get_navigation_items());
        });


        View::composer('admin.partials.sidebar', function ($view) {
            $navigationItems = [
                ['route'=>'admin.dashboard','icon'=>'fa-chart-line','label'=>'Dashboard'],
                ['route'=>'admin.pages','icon'=>'fa-file-alt','label'=>'Pages'],
                ['route'=>'navbar.index','icon'=>'fa-bars','label'=>'Navbar'],
                ['route'=>'admin.articles','icon'=>'fa-book','label'=>'Articles'],
                ['route'=>'admin.media','icon'=>'fa-image','label'=>'Médias'],
                ['route'=>'themes.index','icon'=>'fa-star','label'=>'Thèmes'],
                ['route'=>'modules.index','icon'=>'fa-th','label'=>'Modules'],
                ['route'=>'admin.users','icon'=>'fa-users','label'=>'Utilisateurs'],
                ['route'=>'admin.roles.index','icon'=>'fa-solid fa-shield','label'=>'Roles'],
                ['route'=>'admin.stats','icon'=>'fa-chart-pie','label'=>'Statistiques'],
                ['route'=>'admin.settings','icon'=>'fa-cog','label'=>'Paramètres'],
            ];

            $moduleNavigationItems = collect(config('modules.sidebar_links', []))->toArray();

            $view->with(compact('navigationItems', 'moduleNavigationItems'));
        });
    }
}

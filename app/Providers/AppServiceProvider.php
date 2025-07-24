<?php

namespace App\Providers;

use App\Models\Theme;
use App\Support\ModuleNavigationManager;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ModuleNavigationManager::class, fn () => new ModuleNavigationManager());

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
                ['route'=>'admin.roles.index','icon'=>'fa-solid fa-shield','label'=>'Rôles'],
                ['route'=>'admin.stats','icon'=>'fa-chart-pie','label'=>'Statistiques'],
                ['route'=>'admin.update','icon'=>'fa-wrench','label'=>'Mise à jour'],
                ['route'=>'admin.settings','icon'=>'fa-cog','label'=>'Paramètres'],
            ];

            $moduleNavigationRaw = app(ModuleNavigationManager::class)->all();

            \Log::info('Final sidebar module links:', $moduleNavigationRaw);

            $moduleNavigationItems = [];

            foreach ($moduleNavigationRaw as $key => $module) {
                if (($module['type'] ?? 'link') === 'dropdown' && isset($module['items'])) {
                    $items = [];

                    foreach ($module['items'] as $route => $item) {
                        $items[] = [
                            'route' => $route,
                            'label' => $item['name'] ?? ucfirst($route),
                            'icon' => $item['icon'] ?? 'fa-circle',
                        ];
                    }

                    $moduleNavigationItems[] = [
                        'type' => 'dropdown',
                        'icon' => $module['icon'] ?? 'fa-puzzle-piece',
                        'label' => $module['name'] ?? ucfirst($key),
                        'items' => $items,
                    ];
                } else {
                    $moduleNavigationItems[] = [
                        'type' => 'link',
                        'route' => $module['route'] ?? 'admin.' . $key . '.index',
                        'icon' => $module['icon'] ?? 'fa-puzzle-piece',
                        'label' => $module['name'] ?? ucfirst($key),
                    ];
                }
            }

            $view->with(compact('navigationItems', 'moduleNavigationItems'));
        });



        if (Schema::hasTable('settings')) {

            $appUrl = setting('site_url');
            if ($appUrl) {
                Config::set('app.url', $appUrl);
                URL::forceRootUrl($appUrl);
            }

            Config::set('mail.mailers.smtp.host', setting('mail.host'));
            Config::set('mail.mailers.smtp.port', setting('mail.port'));
            Config::set('mail.mailers.smtp.encryption', setting('mail.encryption'));
            Config::set('mail.mailers.smtp.username', setting('mail.username'));
            Config::set('mail.mailers.smtp.password', setting('mail.password'));

            Config::set('mail.default', setting('mail.driver', 'smtp'));
            Config::set('mail.from.address', setting('mail.from_address'));
            Config::set('mail.from.name', setting('mail.from_name'));
        }

        if (\Schema::hasTable('modules')) {
            \App\Support\ModuleManager::registerActiveModules();
        }

        if (File::isDirectory(resource_path('themes'))) {
            app(\App\Support\ThemeManager::class)->registerViewNamespaces();
        }


    }
}

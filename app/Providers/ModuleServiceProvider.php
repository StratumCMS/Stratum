<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\Module;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {

        if (!file_exists(storage_path('installed'))) {
            return;
        }

        if (!Schema::hasTable('modules')) {
            return;
        }

        $modules = Module::where('active', true)->get();

        foreach ($modules as $module) {
            try {
                $modulePath = base_path('modules/' . $module->slug);

                if (File::isDirectory($modulePath . '/resources/views')) {
                    $this->loadViewsFrom($modulePath . '/resources/views', $module->slug);
                }

                if (File::isDirectory($modulePath . '/resources/lang')) {
                    $this->loadTranslationsFrom($modulePath . '/resources/lang', $module->slug);
                }

                if (File::isDirectory($modulePath . '/database/migrations')) {
                    $this->loadMigrationsFrom($modulePath . '/database/migrations');
                }

                if (File::exists($modulePath . '/config/module.php')) {
                    $this->mergeConfigFrom($modulePath . '/config/module.php', $module->slug);
                }

                if (File::exists($modulePath . '/src/Helpers/helper.php')) {
                    require_once $modulePath . '/src/Helpers/helper.php';
                }

                $this->registerMiddlewares($modulePath);

                $this->registerCommands($modulePath);

                $this->registerModuleProviders($modulePath);

            } catch (\Throwable $e) {
                Log::error("Erreur lors du chargement du module [{$module->slug}] : {$e->getMessage()}");
            }
        }
    }

    protected function registerMiddlewares(string $modulePath): void
    {
        $middlewarePath = $modulePath . '/src/Http/Middleware';
        if (File::isDirectory($middlewarePath)) {
            foreach (File::allFiles($middlewarePath) as $file) {
                $class = $this->getClassFromFile($file, $modulePath);
                if (class_exists($class)) {
                    app('router')->aliasMiddleware(class_basename($class), $class);
                }
            }
        }
    }

    protected function registerCommands(string $modulePath): void
    {
        $commandPath = $modulePath . '/src/Console';
        if (File::isDirectory($commandPath)) {
            foreach (File::allFiles($commandPath) as $file) {
                $class = $this->getClassFromFile($file, $modulePath);
                if (class_exists($class)) {
                    $this->commands([$class]);
                }
            }
        }
    }

    protected function registerModuleProviders(string $modulePath): void
    {
        $providerPath = $modulePath . '/src/Providers';
        if (!\File::isDirectory($providerPath)) {
            return;
        }

        foreach (\File::allFiles($providerPath) as $file) {
            $class = $this->getClassFromFile($file, $modulePath);

            if (class_exists($class) && is_subclass_of($class, \Illuminate\Support\ServiceProvider::class)) {
                // Enregistrer le provider via le container (instanciation correcte)
                $this->app->register($class);

                // Utiliser l'instance enregistrée (et non app->make())
                $instance = $this->app->getProvider($class);

                if (method_exists($instance, 'adminNavigation')) {
                    try {
                        $links = $instance->adminNavigation();

                        \Log::info("✅ Module links from {$class}:", $links);

                        $this->app->make(\App\Support\ModuleNavigationManager::class)->add($links);
                    } catch (\Throwable $e) {
                        \Log::error("❌ Failed to get navigation from {$class}: {$e->getMessage()}");
                    }
                }
            }
        }
    }




    protected function getClassFromFile($file, $modulePath): string
    {
        $relative = str_replace($modulePath . '/', '', $file->getPathname());
        $relative = str_replace(['/', '\\'], '\\', $relative);
        $relative = str_replace('.php', '', $relative);

        // On retire le "src\" s'il existe
        if (str_starts_with($relative, 'src\\')) {
            $relative = substr($relative, 4);
        }

        $slug = basename($modulePath);

        return "Modules\\{$slug}\\{$relative}";
    }




}

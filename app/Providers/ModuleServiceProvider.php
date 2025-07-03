<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Models\Module;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        if (Schema::hasTable('modules')) {
            $modules = Module::where('active', true)->get();

            foreach ($modules as $module) {
                try {
                    $modulePath = resource_path('modules/' . $module->slug);

                    if (File::exists($modulePath . '/routes/web.php')) {
                        $this->loadRoutesFrom($modulePath . '/routes/web.php');
                    }

                    if (File::exists($modulePath . '/routes/api.php')) {
                        $this->loadRoutesFrom($modulePath . '/routes/api.php');
                    }

                    if (File::isDirectory($modulePath . '/views')) {
                        $this->loadViewsFrom($modulePath . '/views', $module->slug);
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

                } catch (\Exception $e) {
                    Log::error("Erreur lors du chargement du module {$module->slug}: " . $e->getMessage());
                }
            }
        }
    }

    public function register()
    {
        //
    }

    protected function registerMiddlewares(string $modulePath)
    {
        $middlewarePath = $modulePath . '/src/Middleware';
        if (File::isDirectory($middlewarePath)) {
            $middlewareFiles = File::allFiles($middlewarePath);

            foreach ($middlewareFiles as $file) {
                $middlewareClass = $this->getClassFromFile($file, $modulePath);
                if (class_exists($middlewareClass)) {
                    app('router')->aliasMiddleware(class_basename($middlewareClass), $middlewareClass);
                }
            }
        }
    }

    protected function registerCommands(string $modulePath)
    {
        $commandPath = $modulePath . '/src/Commands';
        if (File::isDirectory($commandPath)) {
            $commandFiles = File::allFiles($commandPath);

            foreach ($commandFiles as $file) {
                $commandClass = $this->getClassFromFile($file, $modulePath);
                if (class_exists($commandClass)) {
                    $this->commands([$commandClass]);
                }
            }
        }
    }

    protected function registerModuleProviders(string $modulePath)
    {
        $providerPath = $modulePath . '/src/Providers';
        if (File::isDirectory($providerPath)) {
            $providerFiles = File::allFiles($providerPath);

            foreach ($providerFiles as $file) {
                $providerClass = $this->getClassFromFile($file, $modulePath);
                if (class_exists($providerClass) && is_subclass_of($providerClass, ServiceProvider::class)) {
                    $this->app->register($providerClass);
                }
            }
        }
    }

    protected function getClassFromFile($file, $modulePath)
    {
        $relativePath = str_replace($modulePath, '', $file->getPathname());
        $className = 'App\\Modules' . str_replace(['/', '.php'], ['\\', ''], $relativePath);

        return $className;
    }
}

<?php

namespace App\Providers;

use App\Support\ModuleComponentRenderer;
use App\Support\ModuleNavigationManager;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\Module;
use Illuminate\Support\Facades\Cache;

class ModuleServiceProvider extends ServiceProvider
{
    protected array $loadedProviders = [];
    protected bool $navigationRegistered = false;

    protected bool $componentsRegistered = false;

    public function register(): void
    {
    }

    public function boot(): void
    {
        if (!file_exists(storage_path('installed')) || !Schema::hasTable('modules')) {
            return;
        }

        $modules = Module::where('active', true)->get();

        foreach ($modules as $module) {
            try {
                $this->loadModule($module);
            } catch (\Throwable $e) {
                Log::error("Erreur lors du chargement du module [{$module->slug}] : {$e->getMessage()}");
            }
        }

        $this->registerModuleNavigation();
        $this->registerModuleComponents();
    }

    protected function loadModule(Module $module): void
    {
        $modulePath = base_path('modules/' . $module->slug);

        $this->loadModuleResources($modulePath, $module->slug);
        $this->publishAssets($modulePath, $module->slug);

        $this->loadModuleProviders($modulePath, $module->slug);
    }

    protected function loadModuleResources(string $modulePath, string $slug): void
    {
        if (File::isDirectory($modulePath . '/resources/views')) {
            $this->loadViewsFrom($modulePath . '/resources/views', $slug);
        }

        if (File::isDirectory($modulePath . '/resources/lang')) {
            $this->loadTranslationsFrom($modulePath . '/resources/lang', $slug);
        }

        if (File::isDirectory($modulePath . '/database/migrations')) {
            $this->loadMigrationsFrom($modulePath . '/database/migrations');
        }

        if (File::exists($modulePath . '/config/module.php')) {
            $this->mergeConfigFrom($modulePath . '/config/module.php', $slug);
        }

        if (File::exists($modulePath . '/src/Helpers/helper.php')) {
            require_once $modulePath . '/src/Helpers/helper.php';
        }

        $this->registerMiddlewares($modulePath);
        $this->registerCommands($modulePath);
    }

    protected function loadModuleProviders(string $modulePath, string $slug): void
    {
        $providerPath = $modulePath . '/src/Providers';
        if (!File::isDirectory($providerPath)) {
            return;
        }

        foreach (File::allFiles($providerPath) as $file) {
            $class = $this->getClassFromFile($file, $modulePath);

            if (in_array($class, $this->loadedProviders)) {
                continue;
            }

            if (class_exists($class) && is_subclass_of($class, \Illuminate\Support\ServiceProvider::class)) {
                $this->loadedProviders[] = $class;

                $this->app->register($class);
            }
        }
    }

    protected function registerModuleNavigation(): void
    {
        if ($this->navigationRegistered) {
            return;
        }

        $this->navigationRegistered = true;

        $cacheKey = 'module_navigation_' . md5(serialize($this->loadedProviders));

        $navigationManager = $this->app->make(ModuleNavigationManager::class);

        $navigationManager->clear();

        foreach ($this->loadedProviders as $providerClass) {
            $this->handleSingleProviderNavigation($providerClass, $navigationManager);
        }
    }

    protected function registerModuleComponents(): void {
        if ($this->componentsRegistered) {
            return;
        }

        $this->componentsRegistered = true;

        $renderer = $this->app->make(ModuleComponentRenderer::class);

        $renderer->clear();

        foreach ($this->loadedProviders as $providerClass) {
            $this->handleSingleProviderComponents($providerClass, $renderer);
        }
    }

    protected function handleSingleProviderNavigation(string $providerClass, $navigationManager): void
    {
        $instance = $this->app->getProvider($providerClass);

        if (method_exists($instance, 'adminNavigation')) {
            try {
                $links = $instance->adminNavigation();

                if (config('app.debug')) {
                    \Log::debug("Navigation chargée depuis {$providerClass}", ['links' => array_keys($links)]);
                }

                $navigationManager->add($links);
            } catch (\Throwable $e) {
                \Log::error("Échec du chargement de la navigation depuis {$providerClass}: {$e->getMessage()}");
            }
        }
    }

    protected function handleSingleProviderComponents(string $providerClass, $renderer): void {
        $instance = $this->app->getProvider($providerClass);

        if (method_exists($instance, 'registerComponent')) {
            try {
                $instance->registerComponent($renderer);

                if (config('app.debug')) {
                    \Log::debug("Composants enregistrés depuis {$providerClass}");
                }

            }catch (\Throwable $e) {
                \Log::error("Échec de l'enregistrement des composants depuis {$providerClass}: {$e->getMessage()}");
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

    protected function publishAssets(string $modulePath, string $slug): void
    {
        $candidateDirs = [
            $modulePath . '/assets',
            $modulePath . '/public',
            $modulePath . '/resources/assets',
        ];

        $source = null;
        foreach ($candidateDirs as $dir) {
            if (File::isDirectory($dir)) {
                $source = $dir;
                break;
            }
        }

        if (!$source) return;

        $target = public_path("modules_public/{$slug}/assets");

        if (File::exists($target)) {
            File::deleteDirectory($target);
        }

        File::copyDirectory($source, $target);

        if (config('app.debug')) {
            Log::debug("Assets copiés pour module {$slug} depuis {$source}");
        }
    }


    protected function getClassFromFile($file, $modulePath): string
    {
        $relative = str_replace($modulePath . '/', '', $file->getPathname());
        $relative = str_replace(['/', '\\'], '\\', $relative);
        $relative = str_replace('.php', '', $relative);

        if (str_starts_with($relative, 'src\\')) {
            $relative = substr($relative, 4);
        }

        $slug = basename($modulePath);

        return "Modules\\{$slug}\\{$relative}";
    }
}

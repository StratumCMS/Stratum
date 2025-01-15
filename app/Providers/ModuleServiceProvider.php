<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use App\Models\Module;
class ModuleServiceProvider extends ServiceProvider{

    public function boot(){

        if ($this->app->runningInConsole()) {
            return;
        }


        if (Schema::hasTable('modules')) {
            $modules = Module::where('active', true)->get();

            foreach ($modules as $module) {
                $modulePath = base_path($module->path);

                if (File::exists($modulePath . '/routes/web.php')) {
                    $this->loadRoutesFrom($modulePath . '/routes/web.php');
                }

                if (File::exists($modulePath . '/views')) {
                    $this->loadViewsFrom($modulePath . '/views', $module->slug);
                }

                if (File::exists($modulePath . '/database/migrations')) {
                    $this->loadMigrationsFrom($modulePath . '/database/migrations');
                }

                if (File::exists($modulePath . '/config/module.php')) {
                    $this->mergeConfigFrom($modulePath . '/config/module.php', $module->slug);
                }
            }
        }
    }

}

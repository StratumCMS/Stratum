<?php

namespace App\Support;

use App\Models\Module;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class ModuleRouteLoader
{
    public static function loadAll()
    {
        $modules = Module::where('active', true)->get();

        foreach ($modules as $module) {
            $modulePath = base_path('modules/' . $module->slug);

            if (File::exists($modulePath . '/routes/web.php')) {
                Route::middleware(['web'])
                    ->group($modulePath . '/routes/web.php');
            }

            if (File::exists($modulePath . '/routes/api.php')) {
                Route::prefix('api')
                    ->middleware('api')
                    ->group($modulePath . '/routes/api.php');
            }
        }
    }
}

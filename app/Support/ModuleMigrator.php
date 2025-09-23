<?php

namespace App\Support;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ModuleMigrator
{
    public static function migrate(string $slug): void
    {
        $path = base_path("modules/{$slug}/database/migrations");

        if (File::isDirectory($path)) {
            Log::info("🔧 Migrating module: $slug");
            Artisan::call('migrate', [
                '--path'     => base_path("modules/{$slug}/database/migrations"),
                '--realpath' => true,
                '--force'    => true,
            ]);

        }
    }

    public static function rollback(string $slug): void
    {
        $path = base_path("modules/{$slug}/database/migrations");

        if (File::isDirectory($path)) {
            Log::info("↩️ Rolling back module: $slug");
            Artisan::call('migrate:rollback', [
                '--path'     => base_path("modules/{$slug}/database/migrations"),
                '--realpath' => true,
                '--force'    => true,
            ]);

        }
    }
}

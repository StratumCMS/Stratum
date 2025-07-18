<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrateModuleCommand extends Command
{
    protected $signature = 'module:migrate {slug}';
    protected $description = 'Exécute les migrations d’un module';

    public function handle()
    {
        $slug = $this->argument('slug');
        $path = base_path("modules/{$slug}/database/migrations");

        if (!is_dir($path)) {
            $this->error("❌ Aucune migration trouvée pour le module « $slug ».");
            return;
        }

        Artisan::call('migrate', ['--path' => "modules/{$slug}/database/migrations"]);
        $this->info(Artisan::output());
    }
}

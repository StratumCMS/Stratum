<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class RollbackModuleCommand extends Command
{
    protected $signature = 'module:rollback {slug}';
    protected $description = 'Supprime les tables créées par un module';

    public function handle()
    {
        $slug = $this->argument('slug');
        $path = base_path("modules/{$slug}/database/migrations");

        if (!File::isDirectory($path)) {
            $this->error("❌ Aucune migration trouvée.");
            return;
        }

        $dropped = [];

        foreach (File::allFiles($path) as $file) {
            preg_match_all("/Schema::create\(['\"](.*?)['\"]/", File::get($file), $matches);
            foreach ($matches[1] ?? [] as $table) {
                Schema::dropIfExists($table);
                $dropped[] = $table;
            }
        }

        if (count($dropped)) {
            $this->info("✅ Tables supprimées : " . implode(', ', $dropped));
        } else {
            $this->info("ℹ️ Aucune table à supprimer.");
        }
    }
}

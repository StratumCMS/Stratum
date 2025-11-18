<?php

namespace App\Console\Commands;

use App\Models\Module;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SyncModuleAssets extends Command
{
    protected $signature = 'module:sync-assets {slug?}';
    protected $description = 'Synchronise les assets public des modules.';

    public function handle()
    {
        $slug = $this->argument('slug');

        $modules = $slug ? Module::where('slug', $slug)->get() : Module::where('active', true)->get();

        if ($modules->isEmpty()) {
            $this->warn('Aucun module trouvé.');
            return 0;
        }

        foreach ($modules as $module) {
            $this->info("➡ Sync module : {$module->slug}");
            $modulePath = base_path("modules/{$module->slug}");
            $this->syncAssets($modulePath, $module->slug);
        }

        $this->info('Synchronisation terminée.');
        return 0;
    }

    protected function syncAssets(string $modulePath, string $slug)
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

        if (!$source) {
            $this->warn("Aucun dossier assets trouvé pour {$slug}");
            return;
        }

        $target = public_path("modules_public/{$slug}/assets");

        if (File::exists($target)) File::deleteDirectory($target);

        File::copyDirectory($source, $target);

        $this->info("✔ Assets copiés : {$slug}");
    }

}

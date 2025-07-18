<?php

namespace App\Console\Commands;

use App\Models\Module;
use Illuminate\Console\Command;

class DisableModuleCommand extends Command
{
    protected $signature = 'module:disable {slug}';
    protected $description = 'Désactive un module et supprime ses tables si présentes';

    public function handle()
    {
        $slug = $this->argument('slug');
        $module = Module::where('slug', $slug)->first();

        if (!$module) {
            $this->error("❌ Module « $slug » introuvable.");
            return;
        }

        if (!$module->active) {
            $this->info("ℹ️ Module « $slug » est déjà désactivé.");
            return;
        }

        (new \App\Http\Controllers\ModuleController)->deactivate($slug);

        $this->info("✅ Module « $slug » désactivé.");
    }
}

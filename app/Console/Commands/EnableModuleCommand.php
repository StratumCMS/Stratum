<?php

namespace App\Console\Commands;

use App\Models\Module;
use Illuminate\Console\Command;

class EnableModuleCommand extends Command
{
    protected $signature = 'module:enable {slug}';
    protected $description = 'Active un module';

    public function handle()
    {
        $slug = $this->argument('slug');
        $module = Module::where('slug', $slug)->first();

        if (!$module) {
            $this->error("❌ Module « $slug » introuvable.");
            return;
        }

        if ($module->active) {
            $this->info("✅ Module « $slug » est déjà activé.");
            return;
        }

        (new \App\Http\Controllers\ModuleController)->activate($slug);

        $this->info("✅ Module « $slug » activé avec succès.");
    }
}

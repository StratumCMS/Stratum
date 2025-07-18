<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;

class MakeModuleMigrationCommand extends Command
{
    protected $signature = 'module:make:migration {module} {name}';
    protected $description = 'Crée une migration pour un module';

    public function handle()
    {
        $module = Str::slug($this->argument('module'));
        $name = $this->argument('name');

        $path = "modules/{$module}/database/migrations";

        Artisan::call('make:migration', [
            'name' => $name,
            '--path' => $path
        ]);

        $this->info("✅ Migration « $name » créée dans {$path}");
    }
}

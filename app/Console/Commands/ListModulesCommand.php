<?php

namespace App\Console\Commands;

use App\Models\Module;
use Illuminate\Console\Command;

class ListModulesCommand extends Command
{
    protected $signature = 'module:list';
    protected $description = 'Liste tous les modules installés';

    public function handle()
    {
        $modules = Module::all(['name', 'slug', 'version', 'active']);

        $this->table(['Name', 'Slug', 'Version', 'Status'], $modules->map(fn ($m) => [
            $m->name, $m->slug, $m->version, $m->active ? '✅ Actif' : '❌ Inactif'
        ]));
    }
}

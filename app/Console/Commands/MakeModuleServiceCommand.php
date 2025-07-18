<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleServiceCommand extends Command
{
    protected $signature = 'module:make:service {module} {name}';
    protected $description = 'Crée un service dans un module';

    public function handle()
    {
        $module = Str::slug($this->argument('module'));
        $name = Str::studly($this->argument('name'));
        $namespace = Str::studly($module);

        $path = base_path("modules/{$module}/src/Services/{$name}.php");

        File::ensureDirectoryExists(dirname($path));

        File::put($path, <<<PHP
<?php

namespace Modules\\{$namespace}\\Services;

class {$name}
{
    public function handle()
    {
        //
    }
}
PHP);

        $this->info("✅ Service {$name} créé dans le module « {$module} ».");
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MakeModuleControllerCommand extends Command
{
    protected $signature = 'module:make:controller {module} {name}';
    protected $description = 'Crée un controller pour un module';

    public function handle()
    {
        $module = Str::slug($this->argument('module'));
        $name = Str::studly($this->argument('name'));
        $namespace = Str::studly($module);

        $path = base_path("modules/{$module}/src/Http/Controllers/{$name}.php");

        if (File::exists($path)) {
            $this->error("❌ Le controller existe déjà.");
            return;
        }

        File::ensureDirectoryExists(dirname($path));

        File::put($path, <<<PHP
<?php

namespace Modules\\{$namespace}\\Http\\Controllers;

use Illuminate\\Routing\\Controller;

class {$name} extends Controller
{
    public function index()
    {
        //
    }
}
PHP);

        $this->info("✅ Controller {$name} créé dans le module « {$module} ».");
    }
}

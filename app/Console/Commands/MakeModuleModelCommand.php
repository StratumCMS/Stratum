<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MakeModuleModelCommand extends Command
{
    protected $signature = 'module:make:model {module} {name}';
    protected $description = 'Crée un model dans un module';

    public function handle()
    {
        $module = Str::slug($this->argument('module'));
        $name = Str::studly($this->argument('name'));
        $namespace = Str::studly($module);

        $path = base_path("modules/{$module}/src/Models/{$name}.php");

        File::ensureDirectoryExists(dirname($path));

        File::put($path, <<<PHP
<?php

namespace Modules\\{$namespace}\\Models;

use Illuminate\\Database\\Eloquent\\Model;

class {$name} extends Model
{
    protected \$guarded = [];
}
PHP);

        $this->info("✅ Model {$name} créé dans le module « {$module} ».");
    }
}

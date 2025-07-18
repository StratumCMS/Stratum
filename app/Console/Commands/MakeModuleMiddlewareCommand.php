<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleMiddlewareCommand extends Command
{
    protected $signature = 'module:make:middleware {module} {name}';
    protected $description = 'Crée un middleware dans un module';

    public function handle()
    {
        $module = Str::slug($this->argument('module'));
        $name = Str::studly($this->argument('name'));
        $namespace = Str::studly($module);

        $path = base_path("modules/{$module}/src/Http/Middleware/{$name}.php");

        File::ensureDirectoryExists(dirname($path));

        File::put($path, <<<PHP
<?php

namespace Modules\\{$namespace}\\Http\\Middleware;

use Closure;
use Illuminate\\Http\\Request;

class {$name}
{
    public function handle(Request \$request, Closure \$next)
    {
        // Logic here
        return \$next(\$request);
    }
}
PHP);

        $this->info("✅ Middleware {$name} créé dans le module « {$module} ».");
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MakeModuleProviderCommand extends Command
{
    protected $signature = 'module:make:provider {module} {name}';
    protected $description = 'Crée un ServiceProvider dans un module';

    public function handle()
    {
        $module = Str::slug($this->argument('module'));
        $name = Str::studly($this->argument('name'));
        $namespace = Str::studly($module);

        $path = base_path("modules/{$module}/src/Providers/{$name}.php");

        File::ensureDirectoryExists(dirname($path));

        File::put($path, <<<PHP
<?php

namespace Modules\\{$namespace}\\Providers;

use Illuminate\\Support\\ServiceProvider;

class {$name} extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }
}
PHP);

        $this->info("✅ Provider {$name} créé dans le module « {$module} ».");
    }
}

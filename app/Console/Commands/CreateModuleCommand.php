<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreateModuleCommand extends Command
{
    protected $signature = 'module:create {name}';
    protected $description = 'Crée un nouveau module avec structure complète';

    public function handle(): void
    {
        $name = $this->argument('name');
        $slug = Str::slug($name);
        $namespace = Str::studly($slug);
        $path = base_path("modules/{$slug}");

        if (File::exists($path)) {
            $this->error("❌ Le module '{$slug}' existe déjà.");
            return;
        }

        foreach ([
                     "/src/Http/Controllers",
                     "/src/Http/Middleware",
                     "/src/Console",
                     "/src/Models",
                     "/src/Helpers",
                     "/src/Providers",
                     "/routes",
                     "/resources/views",
                     "/resources/lang",
                     "/database/migrations",
                     "/assets",
                 ] as $folder) {
            File::makeDirectory("{$path}{$folder}", 0755, true);
        }

        File::put("{$path}/plugin.json", json_encode([
            'id' => $slug,
            'name' => $name,
            'version' => '1.0.0',
            'description' => '',
            'authors' => ['YourName'],
            'providers' => [
                "\\Modules\\{$namespace}\\Providers\\{$namespace}ServiceProvider"
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        File::put("{$path}/routes/web.php", "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\nRoute::prefix('admin/{$slug}')->middleware(['web', 'auth'])->group(function () {\n    Route::get('/', [\\Modules\\{$namespace}\\Http\\Controllers\\{$namespace}Controller::class, 'index'])->name('admin.{$slug}.index');\n});");

        File::put("{$path}/resources/views/index.blade.php", "<h1>{$name} module - vue par défaut</h1>");

        File::put("{$path}/src/Http/Controllers/{$namespace}Controller.php", $this->defaultController($namespace));

        File::put("{$path}/src/Providers/{$namespace}ServiceProvider.php", $this->defaultProvider($namespace));

        $this->info("✅ Module '{$name}' créé avec succès !");
    }

    protected function defaultController(string $namespace): string
    {
        return <<<PHP
<?php

namespace Modules\\{$namespace}\\Http\\Controllers;

use Illuminate\\Routing\\Controller;

class {$namespace}Controller extends Controller
{
    public function index()
    {
        return view('{$namespace}::index');
    }
}
PHP;
    }

    protected function defaultProvider(string $namespace): string
    {
        return <<<PHP
<?php

namespace Modules\\{$namespace}\\Providers;

use Illuminate\\Support\\ServiceProvider;

class {$namespace}ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }

    public function adminNavigation(): array
    {
        return [
            strtolower('{$namespace}') => [
                'name' => '{$namespace}',
                'type' => 'link',
                'icon' => 'bi bi-box',
                'route' => 'admin.' . strtolower('{$namespace}') . '.index',
            ],
        ];
    }
}
PHP;
    }
}

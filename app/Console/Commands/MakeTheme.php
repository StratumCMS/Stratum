<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeTheme extends Command
{
    protected $signature = 'make:theme {slug} {--framework=customCSS}';
    protected $description = 'Créer un thème frontend avec un framework CSS préconfiguré (tailwind, bootstrap5, materialUI, customCSS)';

    public function handle(): int
    {
        $slug = Str::slug($this->argument('slug'));
        $framework = strtolower($this->option('framework') ?? 'customCSS');
        $themePath = resource_path("themes/{$slug}");

        if (File::exists($themePath)) {
            $this->error("❌ Le thème '{$slug}' existe déjà.");
            return 1;
        }

        $this->info("🚀 Création du thème '{$slug}' avec framework : {$framework}");
        File::makeDirectory($themePath, 0755, true);

        File::makeDirectory("{$themePath}/views");
        File::makeDirectory("{$themePath}/assets/css", 0755, true);
        File::makeDirectory("{$themePath}/assets/js", 0755, true);
        File::makeDirectory("{$themePath}/public/css", 0755, true);
        File::makeDirectory("{$themePath}/public/js", 0755, true);
        File::makeDirectory("{$themePath}/config");

        File::put("{$themePath}/theme.json", json_encode([
            'name' => Str::headline($slug),
            'slug' => $slug,
            'version' => '1.0.0',
            'author' => config('app.name'),
            'description' => 'Thème personnalisé généré par artisan',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        File::put("{$themePath}/views/home.blade.php", "<h1>Bienvenue sur le thème <strong>{$slug}</strong></h1>");

        match ($framework) {
            'tailwind' => $this->setupTailwind($themePath),
            'bootstrap5' => $this->setupBootstrap($themePath),
            'materialui' => $this->setupMaterialUI($themePath),
            default => $this->setupCustomCSS($themePath),
        };

        $this->info("✅ Thème '{$slug}' généré avec succès !");
        return 0;
    }

    protected function setupTailwind(string $path): void
    {
        $this->info("📦 Configuration TailwindCSS...");

        File::put("{$path}/tailwind.config.js", <<<JS
module.exports = {
    content: ["./views/**/*.blade.php"],
    theme: {
        extend: {},
    },
    plugins: [],
}
JS);

        File::put("{$path}/postcss.config.js", <<<JS
module.exports = {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
    }
}
JS);

        File::put("{$path}/assets/css/app.css", <<<CSS
@tailwind base;
@tailwind components;
@tailwind utilities;
CSS);
    }

    protected function setupBootstrap(string $path): void
    {
        $this->info("📦 Configuration Bootstrap 5...");

        File::put("{$path}/assets/css/app.css", <<<CSS
@import url('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
CSS);

        File::put("{$path}/assets/js/app.js", <<<JS
import 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js';
JS);
    }

    protected function setupMaterialUI(string $path): void
    {
        $this->info("📦 Configuration Material UI...");

        File::put("{$path}/assets/css/app.css", <<<CSS
@import url('https://fonts.googleapis.com/icon?family=Material+Icons');
@import url('https://cdn.jsdelivr.net/npm/material-components-web@latest/dist/material-components-web.min.css');
CSS);

        File::put("{$path}/assets/js/app.js", <<<JS
import 'https://cdn.jsdelivr.net/npm/material-components-web@latest/dist/material-components-web.min.js';
JS);
    }

    protected function setupCustomCSS(string $path): void
    {
        $this->info("📦 Configuration CSS personnalisé...");

        File::put("{$path}/assets/css/app.css", <<<CSS
/* Ajoutez ici votre CSS personnalisé */
body {
    font-family: sans-serif;
    padding: 2rem;
}
CSS);
    }
}

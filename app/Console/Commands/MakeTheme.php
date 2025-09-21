<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeTheme extends Command
{
    protected $signature = 'make:theme {slug} {--framework=tailwind}';
    protected $description = 'Créer un thème frontend (Tailwind par défaut) avec un vite.config.js prêt pour un adapter Tailwind si le thème n’est pas Tailwind.';

    public function handle(): int
    {
        $slug = Str::slug($this->argument('slug'));
        $framework = strtolower($this->option('framework') ?? 'tailwind');
        $themePath = resource_path("themes/{$slug}");

        if (File::exists($themePath)) {
            $this->error("❌ Le thème '{$slug}' existe déjà.");
            return 1;
        }

        $this->info("🚀 Création du thème '{$slug}' (framework: {$framework})");

        File::makeDirectory("{$themePath}/views", 0755, true);
        File::makeDirectory("{$themePath}/assets/css", 0755, true);
        File::makeDirectory("{$themePath}/assets/js", 0755, true);
        File::makeDirectory("{$themePath}/assets/images", 0755, true);
        File::makeDirectory("{$themePath}/config", 0755, true);

        File::put("{$themePath}/theme.json", json_encode([
            'name'        => Str::headline($slug),
            'slug'        => $slug,
            'version'     => '1.0.0',
            'author'      => config('app.name'),
            'description' => 'Thème généré par artisan',
            'framework'   => $framework, // utilisé par ThemeCssCompile
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        File::put("{$themePath}/views/home.blade.php", "<h1 class=\"text-2xl font-bold\">Bienvenue sur le thème <strong>{$slug}</strong></h1>");

        File::put("{$themePath}/assets/js/app.js", "/* JS thème {$slug} */\n");

        match ($framework) {
            'tailwind'   => $this->setupTailwind($themePath),
            'bootstrap5' => $this->setupBootstrap($themePath),
            'materialui' => $this->setupMaterialUI($themePath),
            default      => $this->setupCustomCSS($themePath),
        };

        $this->ensureTailwindAdapterFiles($themePath);

        File::put("{$themePath}/vite.config.js", $this->viteConfigContent());

        $this->info("✅ Thème '{$slug}' généré !");
        $this->line("ℹ️ Dev deps conseillées : tailwindcss, autoprefixer, vite-plugin-static-copy.");
        return 0;
    }

    protected function setupTailwind(string $path): void
    {
        $this->info("📦 Configuration TailwindCSS…");
         File::put("{$path}/tailwind.config.js", <<<JS
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./views/**/*.blade.php",
    "../../../modules/*/resources/views/**/*.blade.php",
    "../../../resources/views/**/*.blade.php",
  ],
  theme: { extend: {} },
  plugins: [],
}
JS);
         File::put("{$path}/assets/css/app.css", "@tailwind base;\n@tailwind components;\n@tailwind utilities;\n");
    }

    protected function setupBootstrap(string $path): void
    {
        $this->info("📦 Configuration Bootstrap 5…");
        File::put("{$path}/assets/css/tokens.css", <<<CSS
:root{
  --twc-primary: 59 130 246;
  --twc-muted:   100 116 139;
  --radius:      12px;
}
CSS);
        File::put("{$path}/assets/css/app.css", <<<CSS
@import './tokens.css';
@import url('https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
CSS);
        File::put("{$path}/assets/js/app.js", "import 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js';\n");
    }

    protected function setupMaterialUI(string $path): void
    {
        $this->info("📦 Configuration Material UI…");
        File::put("{$path}/assets/css/tokens.css", <<<CSS
:root{
  --twc-primary: 59 130 246;
  --twc-muted:   100 116 139;
  --radius:      12px;
}
CSS);
        File::put("{$path}/assets/css/app.css", <<<CSS
@import './tokens.css';
@import url('https://fonts.googleapis.com/icon?family=Material+Icons');
@import url('https://cdn.jsdelivr.net/npm/material-components-web@latest/dist/material-components-web.min.css');
CSS);
        File::put("{$path}/assets/js/app.js", "import 'https://cdn.jsdelivr.net/npm/material-components-web@latest/dist/material-components-web.min.js';\n");
    }

    protected function setupCustomCSS(string $path): void
    {
        $this->info("📦 Configuration CSS personnalisé…");
        File::put("{$path}/assets/css/tokens.css", <<<CSS
:root{
  --twc-primary: 59 130 246;
  --twc-muted:   100 116 139;
  --radius:      12px;
}
CSS);
        File::put("{$path}/assets/css/app.css", <<<CSS
@import './tokens.css';
body { font-family: system-ui, sans-serif; padding: 2rem; }
CSS);
    }

    protected function ensureTailwindAdapterFiles(string $themePath): void
    {
        $entry = "{$themePath}/assets/css/tw-adapter.entry.css";
        if (!File::exists($entry)) {
            File::put($entry, "/* Tailwind adapter (utilities-only, sans preflight) */\n@tailwind utilities;\n");
        }

        $cfg = "{$themePath}/tailwind-adapter.config.cjs";
        if (!File::exists($cfg)) {
            File::put($cfg, <<<CJS
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './views/**/*.blade.php',
    '../../../modules/*/resources/views/**/*.blade.php',
    '../../../resources/views/**/*.blade.php',
  ],
  corePlugins: { preflight: false },
  theme: {
    extend: {
      colors: {
        primary: 'rgb(var(--twc-primary, 59 130 246) / <alpha-value>)',
        muted:   'rgb(var(--twc-muted, 100 116 139) / <alpha-value>)',
      },
      borderRadius: { lg: 'var(--radius, 12px)' },
    }
  },
  plugins: [],
};
CJS);
        }
    }

    protected function viteConfigContent(): string
    {
        return <<<'JS'
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import { viteStaticCopy } from 'vite-plugin-static-copy'
import path from 'path'
import fs from 'fs'
import tailwindcss from 'tailwindcss'
import autoprefixer from 'autoprefixer'

const themeDir  = __dirname
const themeSlug = path.basename(themeDir)

const useAdapter    = process.env.VITE_TW_ADAPTER === '1'
const adapterEntry  = `resources/themes/${themeSlug}/assets/css/tw-adapter.entry.css`
const adapterConfig = path.resolve(themeDir, 'tailwind-adapter.config.cjs')

const hasTailwindConfig = fs.existsSync(path.resolve(themeDir, 'tailwind.config.js'))

const inputs = [
    `resources/themes/${themeSlug}/assets/css/app.css`,
    `resources/themes/${themeSlug}/assets/js/app.js`,
]

if (useAdapter) {
    inputs.push(adapterEntry)
}

export default defineConfig({
    plugins: [
        laravel({
            input: inputs,
            buildDirectory: `themes/${themeSlug}`,
            refresh: true,
        }),
        viteStaticCopy({
            targets: [
                {
                    src: `resources/themes/${themeSlug}/assets/images/**/*`,
                    dest: `assets/images`,
                },
            ],
        }),
    ],
    css: {
        postcss: {
            plugins: [
                ...(useAdapter
                    ? [tailwindcss({ config: adapterConfig })]
                    : (hasTailwindConfig ? [tailwindcss({ config: path.resolve(themeDir, 'tailwind.config.js') })] : [])
                ),
                autoprefixer(),
            ],
        },
    },
})
JS;
    }
}

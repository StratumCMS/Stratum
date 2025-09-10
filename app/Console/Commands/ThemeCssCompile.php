<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class ThemeCssCompile extends Command
{
    protected $signature   = 'theme:css:compile {slug : Slug du thème} {--minify : Post-minification via lightningcss/terser}';
    protected $description = 'Build des assets du thème via Vite (vite.config.js du thème), avec post-minification optionnelle (lightningcss/terser).';

    public function handle(): int
    {
        $slug      = $this->argument('slug');
        $withMin   = (bool) $this->option('minify');
        $themePath = resource_path("themes/{$slug}");

        if (!File::exists($themePath)) {
            $this->error("❌ Le thème '{$slug}' n'existe pas (recherché: {$themePath}).");
            return 1;
        }

        $viteConfig = "{$themePath}/vite.config.js";
        if (!File::exists($viteConfig)) {
            $this->error("❌ Aucun vite.config.js trouvé pour le thème '{$slug}'. Chaque thème (tailwind, bootstrap5, materialUI, customCSS) doit fournir son propre Vite config : {$viteConfig}");
            return 1;
        }

        if (!File::exists(base_path('node_modules'))) {
            $this->warn("ℹ️  node_modules absent à la racine. Lance `npm i` / `pnpm i` / `yarn` avant le build.");
        }

        $cmd = ['npx', 'vite', 'build', '--config', $viteConfig, '--minify=false'];

        $this->info("🚀 Build Vite du thème '{$slug}' (minify=false)...");
        $build = new Process($cmd, base_path(), null, null, 600);
        $build->run(function ($type, $buffer) {
            echo $buffer;
        });

        if (!$build->isSuccessful()) {
            $this->error("❌ Échec du build Vite pour le thème '{$slug}'.");
            $this->line(trim($build->getErrorOutput()));
            return 1;
        }

        $this->info("✅ Build Vite terminé.");

        if ($withMin) {
            $manifest = public_path("themes/{$slug}/manifest.json");
            if (!File::exists($manifest)) {
                $this->error("❌ manifest.json introuvable : {$manifest}. Vérifie buildDirectory dans le vite.config.js du thème.");
                return 1;
            }

            $files = $this->collectBuiltFilesFromManifest($manifest);
            if (empty($files)) {
                $this->warn("ℹ️  Aucun fichier CSS/JS détecté dans le manifest. Rien à minifier.");
                return 0;
            }

            $this->info("🔧 Post-minification (lightningcss / terser)...");
            $okAll = true;

            foreach ($files as $absPath) {
                if (!File::exists($absPath)) {
                    $this->warn("⏭️  Fichier manquant, ignoré : {$absPath}");
                    continue;
                }

                if (str_ends_with($absPath, '.css')) {
                    $ok = $this->minifyCssLightning($absPath);
                } elseif (str_ends_with($absPath, '.js')) {
                    $ok = $this->minifyJsTerser($absPath);
                } else {
                    $ok = true;
                }

                $okAll = $okAll && $ok;
            }

            if (!$okAll) {
                $this->error("❌ Une ou plusieurs minifications ont échoué.");
                return 1;
            }

            $this->info("✅ Post-minification terminée (fichiers écrasés en place).");
        }

        $this->info("🏁 Terminé. Manifest & assets dans public/themes/{$slug}.");
        return 0;
    }

    protected function collectBuiltFilesFromManifest(string $manifestPath): array
    {
        $json = json_decode(File::get($manifestPath), true, 512, JSON_THROW_ON_ERROR);

        $relatives = [];
        $push = function (?string $rel) use (&$relatives) {
            if (!$rel) return;
            $rel = ltrim(str_replace(['\\', '..'], ['/', ''], $rel), '/');
            $relatives[$rel] = true;
        };

        foreach ($json as $entry) {
            if (isset($entry['file'])) {
                $push($entry['file']);
            }
            if (!empty($entry['css']) && is_array($entry['css'])) {
                foreach ($entry['css'] as $css) {
                    $push($css);
                }
            }
            if (!empty($entry['imports']) && is_array($entry['imports'])) {
                foreach ($entry['imports'] as $importKey) {
                    if (!isset($json[$importKey])) {
                        continue;
                    }
                    $imp = $json[$importKey];
                    if (isset($imp['file'])) {
                        $push($imp['file']);
                    }
                    if (!empty($imp['css']) && is_array($imp['css'])) {
                        foreach ($imp['css'] as $css) {
                            $push($css);
                        }
                    }
                }
            }
        }

        $abs = [];
        foreach (array_keys($relatives) as $rel) {
            $abs[] = public_path($rel);
        }
        return $abs;
    }

    protected function minifyCssLightning(string $absPath): bool
    {
        $cmd = ['npx', 'lightningcss', '-m', '--targets', 'last 2 versions', '-o', $absPath, $absPath];

        $p = new Process($cmd, base_path(), null, null, 120);
        $p->run(function ($type, $buffer) {
            echo $buffer;
        });

        if (!$p->isSuccessful()) {
            $this->error("❌ Minification CSS échouée : {$absPath}");
            $this->line(trim($p->getErrorOutput()));
            return false;
        }
        $this->line("✅ CSS minifié : {$absPath}");
        return true;
    }

    protected function minifyJsTerser(string $absPath): bool
    {
        $cmd = [
            'npx', 'terser', $absPath,
            '-o', $absPath,
            '-c',
            '-m',
            '--ecma', '2019',
            '--toplevel',
        ];

        // $cmd[] = '--module';

        $p = new Process($cmd, base_path(), null, null, 120);
        $p->run(function ($type, $buffer) {
            echo $buffer;
        });

        if (!$p->isSuccessful()) {
            $this->error("❌ Minification JS échouée : {$absPath}");
            $this->line(trim($p->getErrorOutput()));
            return false;
        }
        $this->line("✅ JS minifié : {$absPath}");
        return true;
    }
}

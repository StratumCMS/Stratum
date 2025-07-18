<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class ThemeCssCompile extends Command
{
    protected $signature = 'theme:css:compile {slug} {file=app.css} {--minify}';
    protected $description = 'Compile le CSS du thème (ex : Tailwind) avec option --minify';

    public function handle(): int
    {
        $slug = $this->argument('slug');
        $file = $this->argument('file');
        $minify = $this->option('minify');
        $themePath = resource_path("themes/{$slug}");

        if (!File::exists($themePath)) {
            $this->error("❌ Le thème '{$slug}' n'existe pas.");
            return 1;
        }

        $cssPath = "{$themePath}/assets/css/{$file}";
        $outputPath = "{$themePath}/public/css/{$file}";

        File::ensureDirectoryExists(dirname($outputPath));

        $tailwindConfig = "{$themePath}/tailwind.config.js";
        if (File::exists($tailwindConfig)) {
            return $this->compileTailwind($themePath, $cssPath, $outputPath, $tailwindConfig, $minify);
        }

        $this->error("❌ Aucun système de compilation supporté détecté pour '{$slug}' (ex: tailwind.config.js manquant)");
        return 1;
    }

    protected function compileTailwind(string $themePath, string $input, string $output, string $config, bool $minify): int
    {
        if (!File::exists($input)) {
            $this->error("❌ Fichier CSS source introuvable : {$input}");
            return 1;
        }

        $cmd = [
            'npx', 'tailwindcss',
            '-i', $input,
            '-o', $output,
            '--config', $config,
        ];

        if ($minify) {
            $cmd[] = '--minify';
        }

        $process = new Process($cmd, $themePath);
        $process->setTimeout(60);

        $this->info("🚀 Compilation de Tailwind CSS...");
        $process->run(function ($type, $buffer) {
            echo $buffer;
        });

        if (!$process->isSuccessful()) {
            $this->error("❌ Échec de la compilation Tailwind.");
            return 1;
        }

        $this->info("✅ CSS compilé avec succès : {$output}");
        return 0;
    }
}

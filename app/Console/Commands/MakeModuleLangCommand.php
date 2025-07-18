<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleLangCommand extends Command
{
    protected $signature = 'module:make:lang {module} {locale=fr}';
    protected $description = 'Crée un fichier de langue JSON dans un module (ex: fr.json)';

    public function handle(): void
    {
        $moduleSlug = Str::slug($this->argument('module'));
        $locale = $this->argument('locale');
        $path = base_path("modules/{$moduleSlug}/resources/lang");

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $filePath = "{$path}/{$locale}.json";

        if (File::exists($filePath)) {
            $this->warn("⚠️ Le fichier de langue {$locale}.json existe déjà.");
            return;
        }

        File::put($filePath, json_encode([
            "hello" => "Bonjour",
            "welcome" => "Bienvenue dans le module {$moduleSlug}"
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("✅ Fichier de langue {$locale}.json créé pour le module {$moduleSlug} !");
    }
}

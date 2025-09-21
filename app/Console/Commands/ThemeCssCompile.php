<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class ThemeCssCompile extends Command
{
    protected $signature = 'theme:css:compile
        {slug : Slug du thème}
        {--minify : Post-minification via lightningcss/terser}
        {--framework= : Force le framework (tailwind|bootstrap5|materialui|customCSS), sinon lu depuis theme.json}
        {--tw-adapter=auto : auto|on|off → inclure l’adapter Tailwind (utilities-only, sans preflight) si le thème n’est pas Tailwind}';

    protected $description = 'Build Vite du thème, avec Tailwind adapter optionnel pour rendre les classes Tailwind des vues de modules sous un thème non-Tailwind.';

    public function handle(): int
    {
        $slug       = (string) $this->argument('slug');
        $withMin    = (bool)   $this->option('minify');
        $framework  = (string) ($this->option('framework') ?? '');
        $adapterOpt = strtolower((string) ($this->option('tw-adapter') ?: 'auto'));

        $themePath  = resource_path("themes/{$slug}");
        $viteConfig = "{$themePath}/vite.config.js";

        if (!File::isDirectory($themePath)) {
            $this->error("❌ Thème introuvable: {$themePath}");
            return 1;
        }
        if (!File::exists($viteConfig)) {
            $this->error("❌ vite.config.js manquant : {$viteConfig}");
            return 1;
        }
        if (!File::exists(base_path('node_modules'))) {
            $this->warn("ℹ️  node_modules manquant. Lance `npm i` / `pnpm i` / `yarn`.");
        }

        $fw = $framework ?: $this->detectFrameworkFromThemeJson($themePath) ?: 'tailwind';

        $useAdapter = match ($adapterOpt) {
            'on'  => true,
            'off' => false,
            default /* auto */ => $fw !== 'tailwind',
        };

        if ($useAdapter) {
            $this->ensureAdapterFiles($themePath);
            if (!File::exists(base_path('node_modules/tailwindcss'))) {
                $this->warn("ℹ️  L’adapter nécessite tailwindcss/autoprefixer en devDependencies.");
            }
        }

        $env = [
            'VITE_TW_ADAPTER' => $useAdapter ? '1' : '0',
            'NODE_ENV'        => 'production',
        ];
        $cmd = ['npx','vite','build','--config',$viteConfig,'--minify=false'];

        $this->info("🚀 Build Vite du thème '{$slug}' (framework={$fw}, adapter=".($useAdapter?'ON':'OFF').") …");
        $p = new Process($cmd, base_path(), $env, null, 900);
        $p->run(function ($type, $buffer) { echo $buffer; });

        if (!$p->isSuccessful()) {
            $this->error("❌ Build Vite échoué.");
            $this->line(trim($p->getErrorOutput()));
            return 1;
        }
        $this->info("✅ Build terminé.");

        if ($withMin) {
            $manifest = public_path("themes/{$slug}/manifest.json");
            if (!File::exists($manifest)) {
                $this->error("❌ manifest.json introuvable : {$manifest}");
                return 1;
            }
            $files = $this->collectBuiltFilesFromManifest($manifest);
            if (!$files) { $this->warn("ℹ️  Aucun fichier CSS/JS détecté."); return 0; }

            $this->info("🔧 Post-minification (lightningcss / terser) …");
            $okAll = true;
            foreach ($files as $abs) {
                if (!File::exists($abs)) { $this->warn("⏭️  Introuvable : {$abs}"); continue; }
                $ok = true;
                if (str_ends_with($abs, '.css')) $ok = $this->minifyCssLightning($abs);
                elseif (str_ends_with($abs, '.js')) $ok = $this->minifyJsTerser($abs);
                $okAll = $okAll && $ok;
            }
            if (!$okAll) { $this->error("❌ Minification partielle échouée."); return 1; }
            $this->info("✅ Minification OK.");
        }

        $this->info("🏁 Terminé. Assets dans public/themes/{$slug}.");
        return 0;
    }

    protected function detectFrameworkFromThemeJson(string $themePath): ?string
    {
        $json = "{$themePath}/theme.json";
        if (!File::exists($json)) return null;
        try {
            $data = json_decode(File::get($json), true, 512, JSON_THROW_ON_ERROR);
            $fw = strtolower((string)($data['framework'] ?? ''));
            return in_array($fw, ['tailwind','bootstrap5','materialui','customcss'], true) ? $fw : null;
        } catch (\Throwable) { return null; }
    }

    protected function ensureAdapterFiles(string $themePath): void
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

    protected function collectBuiltFilesFromManifest(string $manifestPath): array
    {
        $json = json_decode(File::get($manifestPath), true, 512, JSON_THROW_ON_ERROR);
        $rel = [];
        $push = function ($p) use (&$rel) {
            if (!$p) return; $p = ltrim(str_replace(['\\','..'],['/',''],$p),'/'); $rel[$p] = true;
        };
        foreach ($json as $e) {
            if (isset($e['file'])) $push($e['file']);
            if (!empty($e['css'])) foreach ((array)$e['css'] as $c) $push($c);
            if (!empty($e['imports'])) foreach ((array)$e['imports'] as $k) {
                if (!isset($json[$k])) continue; $imp = $json[$k];
                if (isset($imp['file'])) $push($imp['file']);
                if (!empty($imp['css'])) foreach ((array)$imp['css'] as $c) $push($c);
            }
        }
        return array_map(fn($p) => public_path($p), array_keys($rel));
    }

    protected function minifyCssLightning(string $absPath): bool
    {
        $cmd = ['npx','lightningcss','-m','--targets','last 2 versions','-o',$absPath,$absPath];
        $p = new Process($cmd, base_path(), null, null, 180);
        $p->run(fn($t,$b) => print $b);
        if (!$p->isSuccessful()) { $this->error("❌ CSS minify KO: {$absPath}"); $this->line(trim($p->getErrorOutput())); return false; }
        $this->line("✅ CSS minifié : {$absPath}"); return true;
    }

    protected function minifyJsTerser(string $absPath): bool
    {
        $cmd = ['npx','terser',$absPath,'-o',$absPath,'-c','-m','--ecma','2019','--toplevel'];
        $p = new Process($cmd, base_path(), null, null, 180);
        $p->run(fn($t,$b) => print $b);
        if (!$p->isSuccessful()) { $this->error("❌ JS minify KO: {$absPath}"); $this->line(trim($p->getErrorOutput())); return false; }
        $this->line("✅ JS minifié : {$absPath}"); return true;
    }
}

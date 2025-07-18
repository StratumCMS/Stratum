<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\License;
use App\Helpers\LicenseServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ModuleController extends Controller
{
    public function index()
    {
        $this->scanAndAddModules();
        $modules = Module::all();

        $licenseKey = setting('site_key');
        $marketModules = collect();
        $licensedIds = [];

        $response = Http::get('http://stratumcom.test/api/v1/products');

        if ($response->successful()) {
            $products = collect($response->json());

            $marketModules = $products
                ->filter(fn($item) => $item['type'] === 'module')
                ->map(function ($item) {
                    $item['price'] = (float) $item['price'];
                    return $item;
                })
                ->values();

            if ($licenseKey) {
                $licensedData = LicenseServer::getLicensedProducts($licenseKey);
                if ($licensedData && isset($licensedData['products'])) {
                    $licensedIds = collect($licensedData['products'])->pluck('id')->toArray();
                }
            }
        }

        $installedSlugs = Module::pluck('slug')->toArray();

        return view('admin.modules', compact('modules', 'marketModules', 'licensedIds', 'installedSlugs'));
    }

    public function scanAndAddModules()
    {
        $modulesDir = base_path('modules');
        $directories = File::directories($modulesDir);

        foreach ($directories as $directory) {
            $manifestPath = $directory . '/plugin.json';

            if (!File::exists($manifestPath)) {
                continue;
            }

            $manifest = json_decode(File::get($manifestPath), true);

            if (!is_array($manifest) || !isset($manifest['id']) || !isset($manifest['name'])) {
                continue;
            }

            $slug = basename($directory);
            $author = is_array($manifest['authors']) ? implode(', ', $manifest['authors']) : ($manifest['authors'] ?? 'Inconnu');

            Module::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $manifest['name'],
                    'version' => $manifest['version'] ?? '1.0.0',
                    'author' => $author,
                    'description' => $manifest['description'] ?? '',
                    'path' => 'modules/' . $slug,
                ]
            );
        }
    }

    public function install(Request $request, $id)
    {
        $licenseKey = setting('site_key');

        $productResponse = Http::get("http://stratumcom.test/api/v1/products/{$id}");
        if (!$productResponse->successful()) {
            return back()->with('error', 'Impossible de récupérer les informations du produit.');
        }

        $product = $productResponse->json();
        if (!$product || $product['type'] !== 'module') {
            return back()->with('error', 'Produit invalide ou non supporté.');
        }

        $requiresLicense = floatval($product['price']) > 0;

        if ($requiresLicense && (!$licenseKey || !LicenseServer::canAccessProduct($licenseKey, $id))) {
            return back()->with('error', 'Ce produit nécessite une licence valide.');
        }

        $zipPath = LicenseServer::downloadProduct($id, $requiresLicense ? $licenseKey : null);
        if (!$zipPath || !file_exists($zipPath)) {
            return back()->with('error', 'Le téléchargement du module a échoué.');
        }

        $tempDir = storage_path('app/temp/module_' . uniqid());
        File::makeDirectory($tempDir);

        $zip = new \ZipArchive;
        if ($zip->open($zipPath) === true) {
            $zip->extractTo($tempDir);
            $zip->close();
        } else {
            File::deleteDirectory($tempDir);
            return back()->with('error', 'Impossible d’ouvrir l’archive ZIP.');
        }

        $manifestPath = $tempDir . '/plugin.json';
        if (!File::exists($manifestPath)) {
            File::deleteDirectory($tempDir);
            return back()->with('error', 'Le fichier plugin.json est manquant.');
        }

        $manifest = json_decode(File::get($manifestPath), true);
        $slug = $manifest['id'] ?? basename($tempDir);
        if (!$slug) {
            File::deleteDirectory($tempDir);
            return back()->with('error', 'ID du plugin manquant dans le manifest.');
        }

        $finalPath = base_path("modules/{$slug}");
        File::deleteDirectory($finalPath);
        File::moveDirectory($tempDir, $finalPath);
        File::delete($zipPath);

        $author = is_array($manifest['authors']) ? implode(', ', $manifest['authors']) : ($manifest['authors'] ?? 'Inconnu');

        Module::updateOrCreate(
            ['slug' => $slug],
            [
                'name' => $manifest['name'],
                'version' => $manifest['version'] ?? '1.0.0',
                'author' => $author,
                'description' => $manifest['description'] ?? '',
                'path' => 'modules/' . $slug,
            ]
        );

        return back()->with('success', 'Module installé avec succès.');
    }

    public function activate($slug)
    {
        $module = Module::where('slug', $slug)->firstOrFail();
        $licenseKey = setting('site_key');

        // Vérifie si ce module est payant et doit être sous licence
        $marketResponse = Http::get('http://stratumcom.test/api/v1/products');
        $marketModules = $marketResponse->successful()
            ? collect($marketResponse->json())->filter(fn($item) => $item['type'] === 'module')->keyBy('slug')
            : collect();

        $product = $marketModules->get($slug);

        if ($product && floatval($product['price']) > 0) {
            if (!$licenseKey) {
                return back()->with('error', 'Aucune licence valide trouvée.');
            }

            $licensed = LicenseServer::getLicensedProducts($licenseKey);
            $isLicensed = collect($licensed['products'] ?? [])->pluck('slug')->contains($slug);

            if (!$isLicensed) {
                return back()->with('error', 'Ce module est payant et non présent dans votre licence.');
            }
        }

        // 1. Active le module en base
        $module->update(['active' => true]);

        // 2. Charge dynamiquement ses providers, routes, vues, etc.
        $modulePath = base_path("modules/{$slug}");
        $manifestPath = "{$modulePath}/plugin.json";

        if (!File::exists($manifestPath)) {
            return back()->with('error', "Le fichier plugin.json du module est introuvable.");
        }

        $manifest = json_decode(File::get($manifestPath), true);

        // Enregistre les providers
        foreach ($manifest['providers'] ?? [] as $providerClass) {
            if (class_exists($providerClass)) {
                $instance = app()->register($providerClass);

                // Appel de adminNavigation() s'il existe
                if (method_exists($instance, 'adminNavigation')) {
                    $existing = config('modules.sidebar_links', []);
                    $newLinks = $instance->adminNavigation();
                    config(['modules.sidebar_links' => array_merge($existing, $newLinks)]);
                }
            }
        }

        // Charge routes web
        if (File::exists($modulePath . '/routes/web.php')) {
            require_once $modulePath . '/routes/web.php';
        }

        // Charge vues
        if (File::isDirectory($modulePath . '/resources/views')) {
            view()->addNamespace($slug, $modulePath . '/resources/views');
        }

        // Charge traductions
        if (File::isDirectory($modulePath . '/resources/lang')) {
            app('translator')->addNamespace($slug, $modulePath . '/resources/lang');
        }

        // Charge helpers
        if (File::exists($modulePath . '/src/Helpers/helper.php')) {
            require_once $modulePath . '/src/Helpers/helper.php';
        }

        $migrationPath = "{$modulePath}/database/migrations";
        if (File::isDirectory($migrationPath)) {
            \Artisan::call('migrate', [
                '--path' => str_replace(base_path() . '/', '', $migrationPath),
                '--force' => true,
            ]);
        }

        // 3. Clear caches pour que tout se mette à jour
        \Artisan::call('config:clear');
        \Artisan::call('route:clear');
        \Artisan::call('view:clear');

        return redirect()->route('modules.index')->with('success', "✅ Module « {$module->name} » activé avec succès.");
    }



    public function deactivate($slug)
    {
        $module = Module::where('slug', $slug)->firstOrFail();
        $module->update(['active' => false]);

        $modulePath = base_path("modules/{$slug}");
        $migrationPath = "{$modulePath}/database/migrations";

        $droppedTables = [];

        if (File::isDirectory($migrationPath)) {
            foreach (File::allFiles($migrationPath) as $file) {
                $content = File::get($file);

                // Recherche toutes les tables créées via Schema::create
                preg_match_all("/Schema::create\(['\"](.*?)['\"]/", $content, $matches);

                foreach ($matches[1] as $table) {
                    try {
                        Schema::dropIfExists($table);
                        $droppedTables[] = $table;
                    } catch (\Throwable $e) {
                        \Log::warning("Impossible de supprimer la table {$table} du module {$slug} : " . $e->getMessage());
                    }
                }
            }
        }

        \Artisan::call('config:clear');
        \Artisan::call('route:clear');

        $message = "🛑 Module « {$module->name} » désactivé";
        if (count($droppedTables) > 0) {
            $message .= " et " . count($droppedTables) . " table" . (count($droppedTables) > 1 ? "s" : "") . " supprimée" . (count($droppedTables) > 1 ? "s" : "") . ".";
        } else {
            $message .= ".";
        }

        return back()->with('success', $message);
    }


    public function update($slug)
    {
        $module = Module::where('slug', $slug)->firstOrFail();
        $licenseKey = setting('site_key');

        if (!$licenseKey) {
            return back()->with('error', 'Aucune licence valide trouvée.');
        }

        $licensedProducts = LicenseServer::getLicensedProducts($licenseKey);
        $product = collect($licensedProducts['products'] ?? [])
            ->firstWhere('slug', $slug);

        if (!$product || $product['type'] !== 'module') {
            return back()->with('error', 'Ce module n’est pas disponible pour la mise à jour.');
        }

        $zipPath = LicenseServer::downloadProduct($product['id'], $licenseKey);
        if (!$zipPath || !file_exists($zipPath)) {
            return back()->with('error', 'Échec du téléchargement de la mise à jour.');
        }

        $tempDir = storage_path('app/temp/module_' . uniqid());
        File::makeDirectory($tempDir);

        $zip = new \ZipArchive;
        if ($zip->open($zipPath) === true) {
            $zip->extractTo($tempDir);
            $zip->close();
        } else {
            File::deleteDirectory($tempDir);
            return back()->with('error', 'Impossible d’extraire l’archive ZIP.');
        }

        $manifestPath = $tempDir . '/plugin.json';
        if (!File::exists($manifestPath)) {
            File::deleteDirectory($tempDir);
            return back()->with('error', 'Le plugin.json est manquant dans l’archive.');
        }

        $manifest = json_decode(File::get($manifestPath), true);
        $newVersion = $manifest['version'] ?? null;

        if (!$newVersion || version_compare($newVersion, $module->version, '<=')) {
            File::deleteDirectory($tempDir);
            return back()->with('error', 'Aucune version plus récente trouvée.');
        }

        // Backup & remplacement
        $modulePath = base_path("modules/{$slug}");
        File::deleteDirectory($modulePath);
        File::moveDirectory($tempDir, $modulePath);
        File::delete($zipPath);

        $module->update(['version' => $newVersion]);

        \Artisan::call('config:clear');
        \Artisan::call('route:clear');

        return back()->with('success', 'Module mis à jour avec succès.');
    }
}

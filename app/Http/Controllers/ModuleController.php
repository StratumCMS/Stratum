<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\License;
use App\Helpers\LicenseServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
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

        $response = Http::get('https://stratumcms.com/api/v1/products');

        if ($response->successful()) {
            $products = collect($response->json());

            $marketModules = $products
                ->filter(fn($item) => isset($item['type']) && $item['type'] === 'module')
                ->map(function ($item) {
                    $item['price'] = number_format((float) ($item['price'] ?? 0), 2, '.', '');
                    $item['version'] = $item['version'] ?? null;
                    $item['thumbnail'] = $item['thumbnail'] ?? null;
                    $item['short_description'] = $item['short_description'] ?? ($item['description'] ?? '');
                    return $item;
                })
                ->values()
                ->toArray();

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

    public function scanAndAddModules(){
        $modulesDir = base_path('modules');

        if (!File::isDirectory($modulesDir)) {
            if (request()->isMethod('post')) {
                return back()->with('error', 'Le dossier modules/ n\'existe pas.');
            }
            return;
        }

        $directories = File::directories($modulesDir);
        $scannedSlugs = [];
        $scannedCount = 0;
        $addedCount = 0;

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
            $scannedSlugs[] = $slug;

            $author = is_array($manifest['authors'])
                ? implode(', ', $manifest['authors'])
                : ($manifest['authors'] ?? 'Inconnu');

            $module = Module::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $manifest['name'],
                    'version' => $manifest['version'] ?? '1.0.0',
                    'author' => $author,
                    'description' => $manifest['description'] ?? '',
                    'path' => 'modules/' . $slug,
                ]
            );

            if ($module->wasRecentlyCreated) {
                $module->update(['active' => false]);
                $addedCount++;
            }

            $scannedCount++;
        }

        $deletedCount = Module::whereNotIn('slug', $scannedSlugs)->count();
        if ($deletedCount > 0) {
            Module::whereNotIn('slug', $scannedSlugs)->delete();
        }

        if (request()->isMethod('post')) {
            if ($addedCount > 0 || $deletedCount > 0) {
                $message = "✅ ";
                if ($addedCount > 0) {
                    $message .= "{$addedCount} nouveau(x) module(s) ajouté(s). ";
                }
                if ($deletedCount > 0) {
                    $message .= "{$deletedCount} module(s) supprimé(s) de la liste.";
                }
                return back()->with('success', $message);
            } elseif ($scannedCount > 0) {
                return back()->with('success', "✅ {$scannedCount} module(s) scanné(s), aucun changement détecté.");
            } else {
                return back()->with('error', 'Aucun module valide trouvé dans le dossier modules/.');
            }
        }
    }

    public function install(Request $request, $id)
    {
        $licenseKey = setting('site_key');

        $productResponse = Http::get("https://stratumcms.com/api/v1/products/{$id}");
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
                'active' => false,
            ]
        );

        return back()->with('success', 'Module installé avec succès.');
    }

    public function activate($slug)
    {
        $module = Module::where('slug', $slug)->firstOrFail();

        if ($module->active) {
            return back()->with('error', 'Ce module est déjà activé.');
        }

        $licenseKey = setting('site_key');

        $marketResponse = Http::get('https://stratumcms.com/api/v1/products');
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

        $module->update(['active' => true]);

        $modulePath = base_path("modules/{$slug}");
        $manifestPath = "{$modulePath}/plugin.json";

        if (!File::exists($manifestPath)) {
            return back()->with('error', "Le fichier plugin.json du module est introuvable.");
        }

        $manifest = json_decode(File::get($manifestPath), true);

        foreach ($manifest['providers'] ?? [] as $providerClass) {
            if (class_exists($providerClass)) {
                $instance = app()->register($providerClass);

                if (method_exists($instance, 'adminNavigation')) {
                    $existing = config('modules.sidebar_links', []);
                    $newLinks = $instance->adminNavigation();
                    config(['modules.sidebar_links' => array_merge($existing, $newLinks)]);
                }
            }
        }

        if (File::exists($modulePath . '/routes/web.php')) {
            require_once $modulePath . '/routes/web.php';
        }

        if (File::isDirectory($modulePath . '/resources/views')) {
            view()->addNamespace($slug, $modulePath . '/resources/views');
        }

        if (File::isDirectory($modulePath . '/resources/lang')) {
            app('translator')->addNamespace($slug, $modulePath . '/resources/lang');
        }

        if (File::exists($modulePath . '/src/Helpers/helper.php')) {
            require_once $modulePath . '/src/Helpers/helper.php';
        }

        $migrationPath = "{$modulePath}/database/migrations";
        if (File::isDirectory($migrationPath)) {
            $real = realpath($migrationPath);
            if ($real !== false) {
                $exit = \Artisan::call('migrate', [
                    '--path' => $real,
                    '--realpath' => true,
                    '--force' => true,
                ]);
            }
        }

        \Artisan::call('config:clear');
        \Artisan::call('route:clear');
        \Artisan::call('view:clear');

        return redirect()->route('modules.index')->with('success', "✅ Module « {$module->name} » activé avec succès.");
    }



    public function deactivate($slug)
    {
        $module = Module::where('slug', $slug)->firstOrFail();

        if (!$module->active) {
            return back()->with('error', 'Ce module est déjà désactivé.');
        }

        $module->update(['active' => false]);

        $modulePath = base_path("modules/{$slug}");
        $migrationPath = "{$modulePath}/database/migrations";

        $droppedTables = [];

        if (File::isDirectory($migrationPath)) {
            foreach (File::allFiles($migrationPath) as $file) {
                $content = File::get($file);

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

        $marketResponse = Http::get('https://stratumcms.com/api/v1/products');
        if (!$marketResponse->successful()) {
            return back()->with('error', 'Impossible de contacter le serveur de modules.');
        }

        $marketModules = collect($marketResponse->json())
            ->filter(fn($item) => $item['type'] === 'module')
            ->keyBy('slug');

        $product = $marketModules->get($slug);

        if (!$product) {
            return back()->with('error', 'Ce module n\'est pas disponible pour la mise à jour.');
        }

        $requiresLicense = floatval($product['price']) > 0;

        if ($requiresLicense) {
            if (!$licenseKey) {
                return back()->with('error', 'Aucune licence valide trouvée pour ce module payant.');
            }

            $licensedProducts = LicenseServer::getLicensedProducts($licenseKey);
            $isLicensed = collect($licensedProducts['products'] ?? [])
                ->pluck('slug')
                ->contains($slug);

            if (!$isLicensed) {
                return back()->with('error', 'Ce module payant n\'est pas présent dans votre licence.');
            }
        }

        $zipPath = LicenseServer::downloadProduct($product['id'], $requiresLicense ? $licenseKey : null);
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
            return back()->with('error', 'Impossible d\'extraire l\'archive ZIP.');
        }

        $manifestPath = $tempDir . '/plugin.json';
        if (!File::exists($manifestPath)) {
            File::deleteDirectory($tempDir);
            return back()->with('error', 'Le plugin.json est manquant dans l\'archive.');
        }

        $manifest = json_decode(File::get($manifestPath), true);
        $newVersion = $manifest['version'] ?? null;

        if (!$newVersion || version_compare($newVersion, $module->version, '<=')) {
            File::deleteDirectory($tempDir);
            return back()->with('error', 'Aucune version plus récente trouvée.');
        }

        $modulePath = base_path("modules/{$slug}");
        File::deleteDirectory($modulePath);
        File::moveDirectory($tempDir, $modulePath);
        File::delete($zipPath);

        $module->update(['version' => $newVersion]);

        \Artisan::call('config:clear');
        \Artisan::call('route:clear');

        return back()->with('success', "✅ Module mis à jour vers la version {$newVersion}.");
    }
}

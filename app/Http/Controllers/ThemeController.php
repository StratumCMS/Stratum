<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use App\Models\License;
use App\Helpers\LicenseServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class ThemeController extends Controller
{
    public function index()
    {
        $this->scanAndAddThemes();
        $themes = Theme::all();

        $licenseKey = setting('site_key');
        $marketThemes = collect();
        $licensedIds = [];

        $response = Http::get('http://stratumcom.test/api/v1/products');

        if ($response->successful()) {
            $products = collect($response->json());

            $marketThemes = $products
                ->filter(fn ($item) => $item['type'] === 'theme')
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

        return view('admin.themes', compact('themes', 'marketThemes', 'licensedIds'));
    }



    public function install(Request $request, $id)
    {
        $licenseKey = setting('site_key');

        // Récupération du produit depuis l'API (pas besoin de licence ici)
        $productResponse = Http::get("http://stratumcom.test/api/v1/products/{$id}");

        if (!$productResponse->successful()) {
            return back()->with('error', 'Impossible de récupérer les informations du produit.');
        }

        $product = $productResponse->json();

        if (!$product) {
            return back()->with('error', 'Produit introuvable.');
        }

        $price = floatval($product['price']);
        $requiresLicense = $price > 0;

        // Si le produit est payant, il faut vérifier la licence
        if ($requiresLicense) {
            if (!$licenseKey || !LicenseServer::canAccessProduct($licenseKey, $id)) {
                return back()->with('error', 'Ce produit nécessite une licence valide.');
            }
        }

        // Téléchargement du produit via LicenseServer (il s’occupe du paramètre license)
        $zipPath = LicenseServer::downloadProduct($id, $requiresLicense ? $licenseKey : null);

        if (!$zipPath || !file_exists($zipPath)) {
            return back()->with('error', 'Le téléchargement du thème a échoué.');
        }

        // Extraction
        $tempDir = storage_path('app/temp/tmp_' . uniqid());
        File::makeDirectory($tempDir);

        $zip = new \ZipArchive;
        if ($zip->open($zipPath) === true) {
            $zip->extractTo($tempDir);
            $zip->close();
        } else {
            File::deleteDirectory($tempDir);
            return back()->with('error', 'Impossible d’ouvrir l’archive ZIP.');
        }

        // Lecture du manifest
        $manifestPath = $tempDir . '/theme.json';
        if (!File::exists($manifestPath)) {
            File::deleteDirectory($tempDir);
            return back()->with('error', 'Le fichier theme.json est manquant.');
        }

        $manifest = json_decode(File::get($manifestPath), true);
        $slug = $manifest['slug'] ?? null;

        if (!$slug) {
            File::deleteDirectory($tempDir);
            return back()->with('error', 'Le slug est introuvable dans le manifest.');
        }

        $finalPath = resource_path("themes/{$slug}");

        File::deleteDirectory($finalPath);
        File::moveDirectory($tempDir, $finalPath);
        File::delete($zipPath);

        Theme::updateOrCreate(
            ['slug' => $slug],
            [
                'name' => $manifest['name'],
                'version' => $manifest['version'],
                'author' => $manifest['author'],
                'description' => $manifest['description'] ?? '',
                'path' => 'themes/' . $slug,
            ]
        );

        return back()->with('success', 'Thème installé avec succès.');
    }


    public function activate($slug)
    {
        Theme::where('active', true)->update(['active' => false]);

        $theme = Theme::where('slug', $slug)->firstOrFail();
        $theme->update(['active' => true]);

        return back()->with('success', "Thème {$theme->name} activé avec succès.");
    }

    public function deactivate($slug)
    {
        $theme = Theme::where('slug', $slug)->firstOrFail();
        $theme->update(['active' => false]);

        $defaultTheme = Theme::where('slug', 'default')->first();
        if ($defaultTheme) {
            $defaultTheme->update(['active' => true]);
        }

        return back()->with('success', 'Thème désactivé avec succès. Le thème par défaut a été activé.');
    }

    public function scanAndAddThemes()
    {
        $themesDir = resource_path('themes');
        $directories = File::directories($themesDir);

        foreach ($directories as $directory) {
            $manifestPath = $directory . '/theme.json';

            if (File::exists($manifestPath)) {
                $manifest = json_decode(File::get($manifestPath), true);

                Theme::updateOrCreate(
                    ['slug' => $manifest['slug']],
                    [
                        'name' => $manifest['name'],
                        'version' => $manifest['version'],
                        'author' => $manifest['author'],
                        'description' => $manifest['description'] ?? '',
                        'path' => 'themes/' . basename($directory),
                    ]
                );
            }
        }
    }

    public function customize($slug)
    {
        $theme = Theme::where('slug', $slug)->firstOrFail();
        $rulesPath = resource_path("themes/{$theme->slug}/config/rules.php");
        $configViewPath = resource_path("themes/{$theme->slug}/config/config.blade.php");

        if (!File::exists($rulesPath) || !File::exists($configViewPath)) {
            return back()->with('error', 'Ce thème ne supporte pas la personnalisation.');
        }

        $fields = require $rulesPath;
        $values = cache()->get("theme_config_{$theme->slug}", []);
        $configViewContent = File::get($configViewPath);

        return view('admin.customize-theme', compact('theme', 'fields', 'values', 'configViewContent'));
    }

    public function saveCustomization(Request $request, $slug)
    {
        $theme = Theme::where('slug', $slug)->firstOrFail();
        $rules = require resource_path("themes/{$theme->slug}/config/rules.php");

        $validated = [];

        foreach ($rules as $key => $field) {
            $validated[$key] = $field['type'] === 'checkbox' ? $request->has($key) : $request->input($key, $field['default']);
        }

        cache()->put("theme_config_{$theme->slug}", $validated);

        return back()->with('success', 'Personnalisation enregistrée avec succès.');
    }
}

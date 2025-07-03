<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use App\Models\License;
use App\Helpers\LicenseServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ThemeController extends Controller
{
    public function index()
    {
        $themes = Theme::all();
        return view('admin.themes', compact('themes'));
    }

    public function activate($slug)
    {
        $theme = Theme::where('slug', $slug)->firstOrFail();

        $license = License::first();
        if (!$license) {
            return back()->with('error', 'Aucune licence valide trouvée.');
        }

        $validation = LicenseServer::validateResource($license->license_key, $theme->slug, 'theme');
        if (!$validation || !$validation['valid']) {
            return back()->with('error', 'Licence invalide pour ce thème.');
        }

        Theme::where('active', true)->update(['active' => false]);
        $theme->update(['active' => true]);

        return back()->with('success', 'Thème activé avec succès.');
    }

    public function deactivate($slug){
        $theme = Theme::where('slug', $slug)->firstOrFail();
        $theme->update(['active' => false]);

        $defaultTheme = Theme::where('slug', 'default')->first();
        if ($defaultTheme) {
            $defaultTheme->update(['active' => true]);
        }

        return back()->with('success', 'Thème désactivé avec succès. Le thème par défaut a été activé.');
    }

    public function scanAndAddThemes(){
        $themesDir = base_path('themes');
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

        return back()->with('success', 'Les thèmes locaux ont été scannés et ajoutés.');
    }

    public function update($slug){
        $theme = Theme::where('slug', $slug)->firstOrFail();

        $license = License::first();
        if (!$license) {
            return back()->with('error', 'Aucune licence valide trouvée.');
        }

        $manifest = LicenseServer::validateResource($license->license_key, $slug, 'theme');
        if (!$manifest || !$manifest['valid']) {
            return back()->with('error', 'Mise à jour invalide pour ce thème.');
        }

        $downloadedFile = storage_path("app/temp/{$slug}.zip");
        file_put_contents($downloadedFile, file_get_contents($manifest['download_url']));

        $calculatedHash = hash_file('sha256', $downloadedFile);
        if ($calculatedHash !== $manifest['signature']) {
            return back()->with('error', 'Le fichier de mise à jour est corrompu.');
        }

        $themePath = base_path('themes/' . $slug);
        File::deleteDirectory($themePath);
        File::makeDirectory($themePath);
        $zip = new \ZipArchive;
        $zip->open($downloadedFile);
        $zip->extractTo($themePath);
        $zip->close();

        $theme->update(['version' => $manifest['version']]);

        return back()->with('success', 'Thème mis à jour avec succès.');
    }

    public static function activateDefaultTheme(){
        $activeTheme = Theme::where('active', true)->first();
        if (!$activeTheme) {
            $defaultTheme = Theme::where('slug', 'default')->first();
            if ($defaultTheme) {
                $defaultTheme->update(['active' => true]);
            }
        }
    }

}

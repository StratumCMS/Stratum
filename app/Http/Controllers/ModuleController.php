<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\License;
use App\Helpers\LicenseServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ModuleController extends Controller
{
    public function index(){
        $modules = Module::all();
        return view('admin.modules.index', compact('modules'));
    }

    public function activate($slug){
        $module = Module::where('slug', $slug)->firstOrFail();

        $license = License::first();
        if (!$license) {
            return back()->with('error', 'Aucune licence valide trouvée.');
        }

        $validation = LicenseServer::validateResource($license->license_key, $module->slug, 'module');
        if (!$validation || !$validation['valid']) {
            return back()->with('error', 'Licence invalide pour ce module.');
        }

        $module->update(['active' => true]);

        return back()->with('success', 'Module activé avec succès.');
    }

    public function deactivate($slug){
        $module = Module::where('slug', $slug)->firstOrFail();
        $module->update(['active' => false]);

        return back()->with('success', 'Module désactivé avec succès.');
    }

    public function scanAndAddModules(){
        $modulesDir = base_path('modules');
        $directories = File::directories($modulesDir);

        foreach ($directories as $directory) {
            $manifestPath = $directory . '/module.json';

            if (File::exists($manifestPath)) {
                $manifest = json_decode(File::get($manifestPath), true);

                Module::updateOrCreate(
                    ['slug' => $manifest['slug']],
                    [
                        'name' => $manifest['name'],
                        'version' => $manifest['version'],
                        'author' => $manifest['author'],
                        'description' => $manifest['description'] ?? '',
                        'path' => 'modules/' . basename($directory),
                    ]
                );
            }
        }

        return back()->with('success', 'Les modules locaux ont été scannés et ajoutés.');
    }

    public function update($slug){
        $module = Module::where('slug', $slug)->firstOrFail();

        $license = License::first();
        if (!$license) {
            return back()->with('error', 'Aucune licence valide trouvée.');
        }

        $manifest = LicenseServer::validateResource($license->license_key, $slug, 'module');
        if (!$manifest || !$manifest['valid']) {
            return back()->with('error', 'Mise à jour invalide pour ce module.');
        }

        $downloadedFile = storage_path("app/temp/{$slug}.zip");
        file_put_contents($downloadedFile, file_get_contents($manifest['download_url']));

        $calculatedHash = hash_file('sha256', $downloadedFile);
        if ($calculatedHash !== $manifest['signature']) {
            return back()->with('error', 'Le fichier de mise à jour est corrompu.');
        }

        $modulePath = base_path('modules/' . $slug);
        File::deleteDirectory($modulePath);
        File::makeDirectory($modulePath);
        $zip = new \ZipArchive;
        $zip->open($downloadedFile);
        $zip->extractTo($modulePath);
        $zip->close();

        $module->update(['version' => $manifest['version']]);

        return back()->with('success', 'Module mis à jour avec succès.');
    }
}

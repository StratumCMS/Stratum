<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Setting;

class InstallController extends Controller
{
    public function step1()
    {
        return view('install.step1');
    }

    public function step2()
    {
        return view('install.step2');
    }

    public function storeStep2(Request $request)
    {
        $request->validate([
            'db_host' => 'required',
            'db_port' => 'required|integer',
            'db_database' => 'required',
            'db_username' => 'required',
            'db_password' => 'nullable',
        ]);

        $this->setEnv([
            'DB_HOST' => $request->db_host,
            'DB_PORT' => $request->db_port,
            'DB_DATABASE' => $request->db_database,
            'DB_USERNAME' => $request->db_username,
            'DB_PASSWORD' => $request->db_password,
        ]);

        try {
            Artisan::call('migrate', ['--force' => true]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors des migrations : ' . $e->getMessage()]);
        }

        return redirect()->route('install.step3');
    }

    public function step3()
    {
        $this->createRolesAndPermissions();

        return redirect()->route('install.step4');
    }

    public function step4()
    {
        return view('install.step4');
    }

    public function storeStep4(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string',
            'site_description' => 'required|string',
            'site_keywords' => 'nullable|string',
            'site_url' => 'required|url',
        ]);

        Setting::set('site_name', $request->site_name);
        Setting::set('site_description', $request->site_description);
        Setting::set('site_keywords', $request->site_keywords);
        Setting::set('site_url', $request->site_url);

        return redirect()->route('install.step5');
    }

    public function step5()
    {
        return view('install.step5');
    }

    public function storeStep5(Request $request)
    {
        $request->validate([
            'admin_name' => 'required|string',
            'admin_email' => 'required|email',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);

        $adminRole = Role::where('name', 'admin')->first();
        $admin = User::create([
            'name' => $request->admin_name,
            'email' => $request->admin_email,
            'password' => Hash::make($request->admin_password),
        ]);
        $admin->roles()->attach($adminRole);

        $this->installDefaultTheme();

        file_put_contents(storage_path('installed'), 'installed');

        return redirect('/login')->with('success', 'Installation terminée !');
    }

    private function createRolesAndPermissions()
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        $permissions = [
            'manage_users', 'manage_roles', 'manage_settings', 'manage_themes',
            'manage_modules', 'view_logs', 'access_dashboard', 'edit_profile',
        ];

        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName]);

            $adminRole->permissions()->syncWithoutDetaching([$permission->id]);

            if (in_array($permissionName, ['access_dashboard', 'edit_profile'])) {
                $userRole->permissions()->syncWithoutDetaching([$permission->id]);
            }
        }
    }

    private function setEnv(array $data)
    {
        foreach ($data as $key => $value) {
            file_put_contents(app()->environmentFilePath(), preg_replace(
                "/^{$key}=.*/m",
                "{$key}={$value}",
                file_get_contents(app()->environmentFilePath())
            ));
        }
    }

    private function installDefaultTheme(){
        $themeSlug = 'default';
        $themePath = resource_path("themes/{$themeSlug}");
        $manifestPath = "{$themePath}/theme.json";

        if (!File::exists($manifestPath)) {
            throw new \Exception("Le thème par défaut n'existe pas dans le dossier 'resources/themes'.");
        }

        $manifest = json_decode(File::get($manifestPath), true);

        $defaultTheme = Theme::updateOrCreate(
            ['slug' => $themeSlug],
            [
                'name' => $manifest['name'],
                'version' => $manifest['version'],
                'author' => $manifest['author'],
                'description' => $manifest['description'] ?? '',
                'path' => "resources/themes/{$themeSlug}",
                'active' => true,
            ]
        );

        Theme::where('id', '!=', $defaultTheme->id)->update(['active' => false]);
    }
}

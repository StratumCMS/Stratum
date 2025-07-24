<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use App\Support\EnvEditor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
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

    public function storeStep2(Request $request) {
        $request->validate([
            'db_connection' => 'required|in:mysql,pgsql,sqlite',
            'db_database' => 'required_if:db_connection,mysql,pgsql',
            'db_host' => 'required_if:db_connection,mysql,pgsql',
            'db_port' => 'required_if:db_connection,mysql,pgsql|nullable|integer',
            'db_username' => 'required_if:db_connection,mysql,pgsql',
            'db_database_sqlite' => 'required_if:db_connection,sqlite',
        ]);

        $type = $request->input('db_connection');

        try {
            if ($type === 'sqlite') {
                $sqlitePath = base_path($request->input('db_database_sqlite'));
                File::ensureDirectoryExists(dirname($sqlitePath));

                if (!File::exists($sqlitePath)) {
                    File::put($sqlitePath, '');
                }

                Config::set("database.connections.sqlite.database", $sqlitePath);
                DB::purge('sqlite');
                DB::connection('sqlite')->getPdo();

                EnvEditor::updateEnv([
                    'DB_CONNECTION' => 'sqlite',
                    'DB_DATABASE' => $request->input('db_database_sqlite'),
                ]);
            } else {
                $config = [
                    'driver' => $type,
                    'host' => $request->input('db_host'),
                    'port' => $request->input('db_port'),
                    'database' => $request->input('db_database'),
                    'username' => $request->input('db_username'),
                    'password' => $request->input('db_password'),
                ];

                Config::set("database.connections.temp", $config);
                DB::purge('temp');
                DB::connection('temp')->getPdo();

                EnvEditor::updateEnv([
                    'DB_CONNECTION' => $type,
                    'DB_HOST' => $config['host'],
                    'DB_PORT' => $config['port'],
                    'DB_DATABASE' => $config['database'],
                    'DB_USERNAME' => $config['username'],
                    'DB_PASSWORD' => $config['password'],
                ]);
            }

            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('config:cache');
            Artisan::call('migrate:fresh', ['--force' => true]);
            Artisan::call('storage:link');

            return redirect()->route('install.step3');
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['error' => 'Connexion à la base de données impossible : '.$e->getMessage()]);
        }
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

            if (in_array($permissionName, ['edit_profile'])) {
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

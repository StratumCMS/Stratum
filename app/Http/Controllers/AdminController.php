<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Article;
use App\Models\Module;
use App\Models\Page;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Theme;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AdminController extends Controller
{
    public function dashboard()
    {
        $userCount = User::count();
        $themeCount = Theme::count();
        $moduleCount = Module::where('active', true)->count();
        $articlesPublished = Article::where('is_published', true)->count();

        $visitors30d = Visit::where('visited_at', '>=', now()->subDays(30))
            ->distinct('ip')
            ->count('ip');

        $visitorsPrev30d = Visit::whereBetween('visited_at', [now()->subDays(60), now()->subDays(31)])
            ->distinct('ip')
            ->count('ip');

        $visitorChange = 0;
        if ($visitorsPrev30d > 0) {
            $visitorChange = round((($visitors30d - $visitorsPrev30d) / $visitorsPrev30d) * 100, 1);
        } elseif ($visitors30d > 0) {
            $visitorChange = 100;
        }

        $pageViews = Page::sum('views');

        $lastMonth = now()->subMonth();
        $currentViews = Page::where('updated_at', '>=', $lastMonth)->sum('views');
        $previousViews = Page::whereBetween('updated_at', [now()->subMonths(2), $lastMonth])->sum('views');

        $pageViewsChange = $previousViews > 0 ? round((($currentViews - $previousViews) / $previousViews) * 100, 1) : 0;

        $articlesThisMonth = Article::where('is_published', true)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        $articlesLastMonth = Article::where('is_published', true)
            ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->count();

        $articlesChange = 0;
        if ($articlesLastMonth > 0) {
            $articlesChange = round((($articlesThisMonth - $articlesLastMonth) / $articlesLastMonth) * 100, 1);
        } elseif ($articlesThisMonth > 0) {
            $articlesChange = 100;
        }

        $modulesThisMonth = Module::where('active', true)
            ->where('updated_at', '>=', now()->startOfMonth())
            ->count();

        $modulesLastMonth = Module::where('active', true)
            ->whereBetween('updated_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->count();

        $moduleChange = 0;
        if ($modulesLastMonth > 0) {
            $moduleChange = round((($modulesThisMonth - $modulesLastMonth) / $modulesLastMonth) * 100, 1);
        } elseif ($modulesThisMonth > 0) {
            $moduleChange = 100;
        }

        $stats = [
            [
                'title' => 'Visiteurs (30j)',
                'value' => $visitors30d,
                'change' => $visitorChange,
                'icon' => 'fa-users',
                'color' => 'bg-primary',
            ],
            [
                'title' => 'Pages vues',
                'value' => $pageViews,
                'change' => $pageViewsChange,
                'icon' => 'fa-eye',
                'color' => 'bg-success',
            ],
            [
                'title' => 'Articles publiés',
                'value' => $articlesPublished,
                'change' => $articlesChange,
                'icon' => 'fa-file-alt',
                'color' => 'bg-orange-500',
            ],
            [
                'title' => 'Modules actifs',
                'value' => $moduleCount,
                'change' => $moduleChange,
                'icon' => 'fa-star',
                'color' => 'bg-purple-500',
            ],
        ];

        $recentActivities = ActivityLog::latest()->take(10)->with('user')->get();

        return view('admin.dashboard', compact(
            'userCount',
            'themeCount',
            'moduleCount',
            'stats',
            'recentActivities'
        ));
    }


    public function visitorData($days = 7)
    {
        $startDate = now()->subDays($days - 1)->startOfDay();

        $visits = Visit::where('visited_at', '>=', $startDate)
            ->selectRaw('DATE(visited_at) as date, COUNT(DISTINCT ip) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->pluck('count', 'date');

        $labels = [];
        $data = [];

        for ($i = 0; $i < $days; $i++) {
            $date = now()->subDays($days - 1 - $i);
            $dateStr = $date->format('Y-m-d');

            if ($days <= 7) {
                $labels[] = $date->translatedFormat('D d/m');
            } elseif ($days <= 31) {
                $labels[] = $date->translatedFormat('d M');
            } else {
                $labels[] = $date->translatedFormat('d/m');
            }

            $data[] = (int) $visits->get($dateStr, 0);
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
            'total' => array_sum($data),
            'average' => $days > 0 ? round(array_sum($data) / $days, 1) : 0
        ]);
    }

    public function themePage()
    {
        $themes = Theme::all();
        return view('admin.themes', compact('themes'));
    }

    public function modulePage()
    {
        return view('admin.modules');
    }

    public function stats()
    {
        return view('admin.stats');
    }

    public function users()
    {
        $users = User::with(['roles'])->get();

        $roles = Role::all()->map(function ($role) use ($users) {
            return [
                'name' => $role->name,
                'icon' => match($role->name) {
                    'admin' => 'crown',
                    'Éditeur' => 'pen',
                    'Contributeur' => 'user',
                    'Modérateur' => 'shield-alt',
                    default => 'user'
                },
                'color' => match($role->name) {
                    'admin' => 'bg-red-500',
                    'Éditeur' => 'bg-blue-500',
                    'Contributeur' => 'bg-green-500',
                    'Modérateur' => 'bg-purple-500',
                    default => 'bg-muted'
                },
                'count' => $users->filter(fn($user) => $user->hasRole($role->name))->count(),
            ];
        });

        foreach ($users as $user) {
            $role = $user->roles->first();
            $user->display_role = $role?->name ?? 'Aucun';
            $user->role_color = match($role?->name) {
                'admin' => 'bg-red-500',
                'Éditeur' => 'bg-blue-500',
                'Contributeur' => 'bg-green-500',
                'Modérateur' => 'bg-purple-500',
                default => 'bg-muted'
            };
        }

        return view('admin.users', compact('users', 'roles'));
    }


    public function settings()
    {
        $mediaItems = Media::latest()->get()->map(fn($m) => [
            'id' => $m->id,
            'url' => $m->hasGeneratedConversion('thumb') ? $m->getFullUrl('thumb') : $m->getFullUrl(),
        ]);

        return view('admin.settings', [
            'mediaItems' => $mediaItems
        ]);
    }

    public function updateSettings(Request $request)
    {
        $submitted = $request->except('_token');

        $booleanFields = [
            'maintenance_mode', 'seo_enabled', 'xml_sitemap', 'robots_txt', 'two_factor_auth',
            'login_attempts', 'ip_whitelist', 'email_notifications', 'push_notifications',
            'admin_notifications', 'auto_backup', 'cache_enabled', 'compression_enabled',
            'image_optimization', 'captcha.enabled', 'email_enabled'
        ];

        foreach ($booleanFields as $field) {
            if (!array_key_exists($field, $submitted)) {
                $submitted[$field] = 0;
            }
        }

        if ($request->has('ip_whitelist_list')) {
            $json = $request->input('ip_whitelist_list');
            $decoded = json_decode($json, true);

            if (is_array($decoded)) {
                $filtered = array_unique(array_filter($decoded, fn($ip) => filter_var($ip, FILTER_VALIDATE_IP)));
                $submitted['ip_whitelist_list'] = $filtered;
            }
        }

        $changed = [];

        foreach ($submitted as $key => $value) {
            $current = setting($key);

            if (is_array($value)) {
                $value = json_encode($value);
            }

            if ((string) $current !== (string) $value) {
                Setting::set($key, $value);
                $changed[] = $key;
            }
        }

        if (!empty($changed)) {
            $fields = implode(', ', $changed);
            log_activity('settings', 'Mise à jour', "Paramètres modifiés : {$fields}");
        }

        return redirect()->route('admin.settings')->with('success', 'Paramètres mis à jour avec succès.');
    }


    public function updatePage()
    {
        $changelogs = [];

        try {
            $response = Http::get('https://api.github.com/repos/YuketsuSh/Stratum/releases');

            if ($response->ok()) {
                $changelogs = collect($response->json())
                    ->map(function ($release) {
                        return [
                            'tag_name' => $release['tag_name'] ?? 'v0.1.0',
                            'html_url' => $release['html_url'] ?? '',
                            'name' => $release['name'] ?? '',
                            'body' => $release['body'] ?? '',
                        ];
                    })
                    ->toArray();
            }
        } catch (\Exception $e) {
            logger()->error('Erreur API GitHub (releases) : ' . $e->getMessage());
        }

        return view('admin.update', [
            'changelogs' => $changelogs,
        ]);
    }


    public function checkUpdate(Request $request)
    {
        $currentVersion = config('app.version');

        $response = Http::get('https://api.github.com/repos/YuketsuSh/Stratum/releases/latest');

        if ($response->ok()) {
            $release = $response->json();

            $tag = $release['tag_name'] ?? null;
            $version = $tag ? ltrim($tag, 'v') : null;

            if ($version && version_compare($version, $currentVersion, '>')) {
                $asset = $release['assets'][0] ?? null;

                if (!$asset || !isset($asset['browser_download_url'])) {
                    return response()->json(['error' => 'Aucun fichier ZIP dans la release.'], 422);
                }

                return response()->json([
                    'update_available' => true,
                    'latest_version' => $version,
                    'version_tag' => $tag,
                    'changelog' => $release['body'] ?? '',
                    'download_url' => $asset['browser_download_url'],
                ]);
            }
        }

        return response()->json(['update_available' => false]);
    }

    public function runUpdate(Request $request)
    {
        $url = $request->input('download_url');
        $versionTag = $request->input('version_tag');
        $remoteVersion = ltrim($versionTag, 'v');
        $currentVersion = config('app.version');

        if (version_compare($remoteVersion, $currentVersion, '<=') || !$remoteVersion) {
            return response()->json([
                'success' => false,
                'message' => 'StratumCMS est déjà à jour.'
            ], 200);
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['error' => 'URL invalide.'], 422);
        }

        $tmpDir = storage_path('app/cms-updates');
        File::ensureDirectoryExists($tmpDir);

        $tmpZip = $tmpDir . '/update.zip';
        $extractPath = $tmpDir . '/extracted';

        $zipContent = @file_get_contents($url);
        if (!$zipContent) {
            return response()->json(['error' => 'Téléchargement échoué.'], 500);
        }

        file_put_contents($tmpZip, $zipContent);

        $zip = new \ZipArchive();
        if ($zip->open($tmpZip) === true) {
            $zip->extractTo($extractPath);
            $zip->close();
        } else {
            return response()->json(['error' => 'Extraction impossible.'], 500);
        }

        $extractedRoot = File::directories($extractPath)[0] ?? null;
        if (!$extractedRoot) {
            return response()->json(['error' => 'Fichiers extraits introuvables.'], 500);
        }

        $excluded = ['.env', 'vendor', 'storage', '.git', 'resources/themes', 'public/'];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($extractedRoot, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $relPath = str_replace($extractedRoot . '/', '', $item->getPathname());

            foreach ($excluded as $ignore) {
                if (str_starts_with($relPath, $ignore)) continue 2;
            }

            $dest = base_path($relPath);
            if ($item->isDir()) {
                if (!File::exists($dest)) File::makeDirectory($dest, 0755, true);
            } else {
                File::copy($item->getPathname(), $dest);
            }
        }

        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('migrate', ['--force' => true]);
        Process::run('composer install --no-interaction --prefer-dist --optimize-autoloader');

        File::delete($tmpZip);
        File::deleteDirectory($extractPath);

        return response()->json([
            'success' => true,
            'message' => 'Mise à jour vers la version ' . $remoteVersion . ' effectuée avec succès.'
        ]);
    }

}

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
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
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

        if (!empty($submitted['site_key'])) {
            $submitted['site_key'] = strtoupper(trim($submitted['site_key']));

            $request->validate([
                'site_key' => [
                    'required',
                    'string',
                    'regex:/^ST\-[A-Z0-9]{4}\-[A-Z0-9]{4}$/',
                ],
            ]);

            if (!\App\Helpers\LicenseServer::isLicenseActive($submitted['site_key'])) {
                logger()->info('Licence invalide détectée', ['site_key' => $submitted['site_key'], 'api_response' => \App\Helpers\LicenseServer::getLicensedProducts($submitted['site_key'])]);
                return redirect()->route('admin.settings')
                    ->withErrors(['site_key' => 'Clé de licence invalide ou inactive.'])
                    ->withInput();
            }

        }

        $booleanFields = [
            'maintenance_mode', 'seo_enabled', 'xml_sitemap', 'robots_txt', 'two_factor_auth',
            'login_attempts', 'ip_whitelist', 'email_notifications', 'push_notifications',
            'admin_notifications', 'auto_backup', 'cache_enabled', 'compression_enabled',
            'image_optimization', 'captcha_enabled', 'email_enabled'
        ];

        foreach ($booleanFields as $field) {
            if (!array_key_exists($field, $submitted)) {
                $submitted[$field] = 0;
            }
        }

        if (!empty($submitted['ip_whitelist']) && (int)$submitted['ip_whitelist'] === 1) {
            if ($request->has('ip_whitelist_list')) {
                $json = (string) $request->input('ip_whitelist_list');
                $decoded = json_decode($json, true);

                if (is_array($decoded)) {
                    $filtered = array_values(array_unique(array_filter($decoded, fn($ip) => filter_var($ip, FILTER_VALIDATE_IP))));
                    $submitted['ip_whitelist_list'] = $filtered;
                } else {
                    $submitted['ip_whitelist_list'] = [];
                }
            } else {
                $submitted['ip_whitelist_list'] = [];
            }
        } else {
            unset($submitted['ip_whitelist_list']);
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
            $response = Http::withHeaders([
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'StratumCMS-Updater'
            ])->get('https://api.github.com/repos/StratumCMS/Stratum/releases');

            if ($response->ok() && str_contains($response->header('Content-Type', ''), 'application/json')) {
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
            } else {
                Log::warning('GitHub releases returned non-json content', [
                    'status' => $response->status(),
                    'body_snippet' => substr($response->body(), 0, 1000)
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Erreur API GitHub (releases) : ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }

        return view('admin.update', ['changelogs' => $changelogs]);
    }

    public function checkUpdate(Request $request)
    {
        try {
            $currentVersion = config('app.version');

            $response = Http::withHeaders([
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'StratumCMS-Updater'
            ])->get('https://api.github.com/repos/StratumCMS/Stratum/releases/latest');

            if (! $response->ok() || ! str_contains($response->header('Content-Type', ''), 'application/json')) {
                Log::warning('GitHub latest returned non-json content', [
                    'status' => $response->status(),
                    'body_snippet' => substr($response->body(), 0, 1000)
                ]);
                return response()->json(['error' => 'Impossible de récupérer la release.'], 502);
            }

            $release = $response->json();
            $tag = $release['tag_name'] ?? null;
            $version = $tag ? ltrim($tag, 'v') : null;

            if ($version && version_compare($version, $currentVersion, '>')) {
                $asset = $release['assets'][0] ?? null;

                if (!$asset || !isset($asset['browser_download_url'])) {
                    return response()->json(['error' => 'Aucun fichier ZIP disponible.'], 422);
                }

                return response()->json([
                    'update_available' => true,
                    'latest_version' => $version,
                    'version_tag' => $tag,
                    'changelog' => $release['body'] ?? '',
                    'download_url' => $asset['browser_download_url'],
                ]);
            }

            return response()->json(['update_available' => false]);
        } catch (\Throwable $e) {
            Log::error('Erreur checkUpdate: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Erreur lors de la vérification de la mise à jour.'], 500);
        }
    }

    public function runUpdate(Request $request)
    {
        try {
            $url = $request->input('download_url');
            $versionTag = $request->input('version_tag');
            $remoteVersion = $versionTag ? ltrim($versionTag, 'v') : null;
            $currentVersion = config('app.version');

            if (! $remoteVersion || version_compare($remoteVersion, $currentVersion, '<=')) {
                return response()->json(['success' => false, 'message' => 'StratumCMS est déjà à jour.']);
            }

            if (! filter_var($url, FILTER_VALIDATE_URL)) {
                return response()->json(['error' => 'URL invalide.'], 422);
            }

            $tmpDir = storage_path('app/cms-updates');
            File::ensureDirectoryExists($tmpDir);
            $tmpZip = $tmpDir . '/update.zip';
            $extractPath = $tmpDir . '/extracted';
            File::deleteDirectory($extractPath);
            File::ensureDirectoryExists($extractPath);

            $response = Http::withOptions(['stream' => true])->get($url);
            if (! $response->ok()) {
                return response()->json(['error' => 'Téléchargement échoué.'], 500);
            }
            file_put_contents($tmpZip, $response->body());

            if (!File::exists($tmpZip) || filesize($tmpZip) === 0) {
                return response()->json(['error' => 'Fichier ZIP vide.'], 500);
            }

            $zip = new \ZipArchive();
            if ($zip->open($tmpZip) !== true || ! $zip->extractTo($extractPath)) {
                return response()->json(['error' => 'Extraction du ZIP impossible.'], 500);
            }
            $zip->close();

            $extractedRoot = File::directories($extractPath)[0] ?? $extractPath;
            $excluded = ['.env', 'vendor', 'storage', '.git', 'resources/themes', 'public/', 'modules/'];

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($extractedRoot, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $item) {
                $relPath = ltrim(str_replace($extractedRoot, '', $item->getPathname()), DIRECTORY_SEPARATOR);
                if (collect($excluded)->contains(fn($e) => str_starts_with($relPath, trim($e, '/')))) {
                    continue;
                }

                $dest = base_path($relPath);
                if ($item->isDir()) {
                    File::ensureDirectoryExists($dest);
                } else {
                    File::copy($item->getPathname(), $dest);
                }
            }

            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('migrate', ['--force' => true]);

            try {
                if (class_exists(Process::class)) {
                    $proc = Process::fromShellCommandline('composer install --no-interaction --prefer-dist --optimize-autoloader');
                    $proc->setTimeout(300);
                    $proc->run();
                    if (! $proc->isSuccessful()) {
                        Log::warning('Composer install non-successful', ['output' => $proc->getOutput()]);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Erreur composer install: ' . $e->getMessage());
            }

            File::delete($tmpZip);
            File::deleteDirectory($extractPath);

            return response()->json([
                'success' => true,
                'message' => 'Mise à jour vers la version ' . $remoteVersion . ' effectuée avec succès.'
            ]);
        } catch (\Throwable $e) {
            Log::error('Erreur runUpdate: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'error' => 'Erreur lors de la mise à jour.'], 500);
        }
    }

}

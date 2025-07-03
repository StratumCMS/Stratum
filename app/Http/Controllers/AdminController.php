<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Module;
use App\Models\Setting;
use App\Models\Theme;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $userCount = User::count();
        $themeCount = Theme::count();
        $moduleCount = Module::where('active', true)->count();

        // Visiteurs des 30 derniers jours
        $visitors30d = Visit::where('visited_at', '>=', now()->subDays(30))->count();
        $visitorsPrev30d = Visit::whereBetween('visited_at', [now()->subDays(60), now()->subDays(31)])->count();

        // Variation en %
        $visitorChange = 0;
        if ($visitorsPrev30d > 0) {
            $visitorChange = round((($visitors30d - $visitorsPrev30d) / $visitorsPrev30d) * 100, 1);
        }

        // Pour les autres stats (placeholder en attendant)
        $pageViews = 48392;
        $articlesPublished = 156;
        $pageViewsChange = 8.2;
        $articlesChange = -2.1;

        // Statistiques globales pour la vue
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
                'icon' => 'fa-calendar',
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
                'change' => 5.0,
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
            ->get()
            ->groupBy(fn ($visit) => $visit->visited_at->format('Y-m-d'))
            ->map(fn ($group) => $group->count());

        $data = [];
        for ($i = 0; $i < $days; $i++) {
            $date = now()->subDays($days - 1 - $i)->format('Y-m-d');
            $label = Carbon::parse($date)->translatedFormat('D');
            $data[] = [
                'label' => $label,
                'count' => $visits->get($date, 0)
            ];
        }

        return response()->json($data);
    }

    public function pages()
    {
        return view('admin.page');
    }

    public function articles()
    {
        return view('admin.article');
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
        return view('admin.users');
    }

    public function settings()
    {
        return view('admin.settings');
    }

    public function updateSettings(Request $request)
    {
        $submitted = $request->except('_token');

        $booleanFields = [
            'maintenance_mode', 'seo_enabled', 'xml_sitemap', 'robots_txt', 'two_factor_auth',
            'login_attempts', 'ip_whitelist', 'email_notifications', 'push_notifications',
            'admin_notifications', 'auto_backup', 'cache_enabled', 'compression_enabled',
            'image_optimization'
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




}

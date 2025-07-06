<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Module;
use App\Models\Page;
use App\Models\Role;
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

        $visitors30d = Visit::where('visited_at', '>=', now()->subDays(30))->count();
        $visitorsPrev30d = Visit::whereBetween('visited_at', [now()->subDays(60), now()->subDays(31)])->count();

        $visitorChange = 0;
        if ($visitorsPrev30d > 0) {
            $visitorChange = round((($visitors30d - $visitorsPrev30d) / $visitorsPrev30d) * 100, 1);
        }

        $pageViews = Page::sum('views');

        $lastMonth = now()->subMonth();
        $currentViews = Page::where('updated_at', '>=', $lastMonth)->sum('views');
        $previousViews = Page::whereBetween('updated_at', [now()->subMonths(2), $lastMonth])->sum('views');

        $pageViewsChange = $previousViews > 0 ? round((($currentViews - $previousViews) / $previousViews) * 100, 1) : 0;

        $articlesPublished = 156;
        $articlesChange = -2.1;

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

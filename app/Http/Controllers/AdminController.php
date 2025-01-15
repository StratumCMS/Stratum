<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $userCount = User::count();
        $themeCount = Theme::count();
        $moduleCount = Module::count();

        return view('admin.dashboard', compact('userCount', 'themeCount', 'moduleCount'));
    }

    public function settings()
    {
        return view('admin.settings');
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string',
            'site_description' => 'nullable|string',
            'site_keywords' => 'nullable|string',
        ]);

        config(['app.name' => $request->site_name]);

        foreach ($request->except('_token') as $key => $value) {
            \DB::table('settings')->updateOrInsert(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Settings updated successfully!');
    }
}

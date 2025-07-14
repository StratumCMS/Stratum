<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function adminEdit(Request $request): View
    {
        return view('admin.edit-profile', [
            'user' => $request->user(),
        ]);
    }

    public function updateAdmin(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');

            if ($user->avatar_url && Storage::exists($user->avatar_url)) {
                Storage::delete($user->avatar_url);
            }

            $path = $avatar->store('avatars', 'public');
            $data['avatar_url'] = Storage::url($path);
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->update($data);

        return Redirect::route('admin.profile')->with('success', 'Profil mis à jour');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                Storage::disk('public')->delete($user->avatar_url);
            }
            $path = $avatar->store('avatars', 'public');
            $data['avatar_url'] = Storage::url($path);
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if (isset($data['social_links']) && is_array($data['social_links'])) {
            $data['social_links'] = array_filter($data['social_links']);
        }

        $user->update($data);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function show($name = null){
        $user = User::where('name', $name)->orWhere('name', $name)->firstOrFail();

        $articles = $user->articles()->published()->latest()->get();
        $articlesCount = $articles->count();
        $likesCount = $user->likedArticles()->count();
        $followersCount = 0;

        return view('profile.show', compact('user', 'articles', 'articlesCount', 'likesCount', 'followersCount'));
    }

    public function index(){
        $user = auth()->user();

        $articles = $user->articles()->published()->latest()->get();
        $articlesCount = $articles->count();
        $likesCount = $user->likedArticles()->count();
        $followersCount = 0;

        return view('profile.index', compact('user', 'articles', 'articlesCount', 'likesCount', 'followersCount'));
    }
}

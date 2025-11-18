<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\TwoFactorChallengeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\LoadActiveTheme;
use App\Models\Page;
use App\Models\Theme;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckHeadlessMode;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

require __DIR__.'/auth.php';

require __DIR__.'/admin.php';

Route::middleware(['check.installation', LoadActiveTheme::class, 'headless'])->group(function () {

    Route::get('/', function () {
        $homePage = Page::where('is_home', true)
            ->where('status', 'published')
            ->first();

        if ($homePage) {
            return app(PageController::class)->show($homePage->slug);
        }

        return app(HomeController::class)->index();
    })->name('home');

    Route::get('/articles', [ArticleController::class, 'indexPub'])->name('posts.index');
    Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('posts.show');
    Route::get('/articles/feed/rss', fn () => response()->view('posts.feed', [
        'articles' => \App\Models\Article::where('is_published', true)->latest()->take(20)->get()
    ])->header('Content-Type', 'application/rss+xml'))->name('posts.feed');

    Route::prefix('install')->group(function () {
        Route::get('/', [InstallController::class, 'step1'])->name('install.step1');
        Route::post('/step-1', [InstallController::class, 'storeStep1']);

        Route::get('/step-2', [InstallController::class, 'step2'])->name('install.step2');
        Route::post('/step-2', [InstallController::class, 'storeStep2'])->name('install.storeStep2');
        Route::get('/step2-5', [InstallController::class, 'step2_5'])->name('install.step2_5');
        Route::post('/step2-5', [InstallController::class, 'storeStep2_5'])->name('install.storeStep2_5');

        Route::get('/write-env', [InstallController::class, 'writeEnv'])->name('install.writeEnv');


        Route::get('/step-3', [InstallController::class, 'step3'])->name('install.step3');
        Route::post('/step-3', [InstallController::class, 'storeStep3'])->name('install.storeStep3');

        Route::get('/step-4', [InstallController::class, 'step4'])->name('install.step4');
        Route::post('/step-4', [InstallController::class, 'storeStep4'])->name('install.storeStep4');

        Route::get('/step-5', [InstallController::class, 'step5'])->name('install.step5');
        Route::post('/step-5', [InstallController::class, 'storeStep5'])->name('install.storeStep5');
    });

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::get('/profile/settings', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::post('/articles/{article}/comments', [CommentController::class, 'store'])->name('comments.store');
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

        Route::post('/articles/{article}/like', [LikeController::class, 'toggle'])->name('articles.like');
        Route::get('/2fa', [TwoFactorChallengeController::class, 'show'])->name('2fa.challenge');
        Route::post('/2fa', [TwoFactorChallengeController::class, 'store'])->name('2fa.verify.challenge');;
        Route::post('/2fa/enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
        Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify');
        Route::delete('/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');
    });

    Route::get('/profile/{name}', [ProfileController::class, 'show'])->name('profile.show');

    Route::get('/{slug}', [PageController::class, 'show'])->name('pages.show');

});

<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NavbarElementController;
use App\Http\Middleware\LoadActiveTheme;
use App\Http\Middleware\PreviewTheme;

Route::middleware(['check.installation', 'auth', 'can:access_dashboard', 'restrict.ip'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/articles', [ArticleController::class, 'index'])->name('admin.articles');
    Route::get('/articles/create', [ArticleController::class, 'create'])->name('admin.articles.create');
    Route::post('/articles', [ArticleController::class, 'store'])->name('admin.articles.store');
    Route::post('/articles/{article}/toggle', [ArticleController::class, 'togglePublish'])->name('admin.articles.toggle');
    Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('admin.articles.delete');
    Route::get('/media', [MediaController::class, 'index'])->name('admin.media');
    Route::post('/media/upload', [MediaController::class, 'upload'])->name('admin.media.upload');
    Route::delete('/media/{mediaItem}', [MediaController::class, 'delete'])->name('admin.media.delete');
    Route::get('/themes', [AdminController::class, 'themePage'])->name('admin.themes');
    Route::get('/modules', [AdminController::class, 'modulePage'])->name('admin.modules');
    Route::get('/stats', [AdminController::class, 'stats'])->name('admin.stats');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');

    Route::resource('/navbar', NavbarElementController::class)->except(['show']);
    Route::post('/navbar/reorder', [NavbarElementController::class, 'reorder'])->name('navbar.reorder');

    Route::get('/visitors/data/{days}', [AdminController::class, 'visitorData']);

    Route::prefix('roles')->name('admin.roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
    });

    Route::get('/pages', [PageController::class,'index'])->name('admin.pages');
    Route::post('/pages', [PageController::class,'store'])->name('admin.pages.store');
    Route::put('/pages/{page}', [PageController::class,'update'])->name('admin.pages.update');
    Route::delete('/pages/{page}', [PageController::class,'destroy'])->name('admin.pages.destroy');
    Route::get('/pages/create', [PageController::class, 'create'])->name('admin.pages.create');
    Route::get('/pages/{page}/edit', [PageController::class, 'edit'])->name('admin.pages.edit');


    Route::get('/themes', [ThemeController::class, 'index'])->name('themes.index');
    Route::post('/themes/activate/{slug}', [ThemeController::class, 'activate'])->name('themes.activate');
    Route::post('/themes/deactivate/{slug}', [ThemeController::class, 'deactivate'])->name('themes.deactivate');
    Route::post('/themes/scan', [ThemeController::class, 'scanAndAddThemes'])->name('themes.scan');
    Route::post('/themes/update/{slug}', [ThemeController::class, 'update'])->name('themes.update');
    Route::get('/themes/{slug}/customize', [ThemeController::class, 'customize'])->name('themes.customize');
    Route::post('/themes/{slug}/customize', [ThemeController::class, 'saveCustomization'])->name('themes.customize.save');

    Route::get('/modules', [ModuleController::class, 'index'])->name('modules.index');
    Route::post('/modules/activate/{slug}', [ModuleController::class, 'activate'])->name('modules.activate');
    Route::post('/modules/deactivate/{slug}', [ModuleController::class, 'deactivate'])->name('modules.deactivate');
    Route::post('/modules/scan', [ModuleController::class, 'scanAndAddModules'])->name('modules.scan');
    Route::post('/modules/update/{slug}', [ModuleController::class, 'update'])->name('modules.update');

    Route::get('{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    Route::get('/profile', [ProfileController::class, 'adminEdit'])->name('admin.profile');
    Route::post('/profile/update', [ProfileController::class, 'updateAdmin'])->name('admin.profile.update');

});

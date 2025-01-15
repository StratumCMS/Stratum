<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['check.installation'])->group(function () {



    Route::get('/', function () {
        return view('theme::home');
    });

    Route::prefix('install')->group(function () {
        Route::get('/', [InstallController::class, 'step1'])->name('install.step1');
        Route::post('/step-1', [InstallController::class, 'storeStep1']);

        Route::get('/step-2', [InstallController::class, 'step2'])->name('install.step2');
        Route::post('/step-2', [InstallController::class, 'storeStep2'])->name('install.storeStep2');

        Route::get('/step-3', [InstallController::class, 'step3'])->name('install.step3');
        Route::post('/step-3', [InstallController::class, 'storeStep3'])->name('install.storeStep3');

        Route::get('/step-4', [InstallController::class, 'step4'])->name('install.step4');
        Route::post('/step-4', [InstallController::class, 'storeStep4'])->name('install.storeStep4');

        Route::get('/step-5', [InstallController::class, 'step5'])->name('install.step5');
        Route::post('/step-5', [InstallController::class, 'storeStep5'])->name('install.storeStep5');
    });

    Route::middleware(['auth', 'can:access_dashboard'])->prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');

        Route::get('/themes', [ThemeController::class, 'index'])->name('themes.index');
        Route::post('/themes/activate/{slug}', [ThemeController::class, 'activate'])->name('themes.activate');
        Route::post('/themes/deactivate/{slug}', [ThemeController::class, 'deactivate'])->name('themes.deactivate');
        Route::post('/themes/scan', [ThemeController::class, 'scanAndAddThemes'])->name('themes.scan');
        Route::post('/themes/update/{slug}', [ThemeController::class, 'update'])->name('themes.update');

        Route::get('/modules', [ModuleController::class, 'index'])->name('modules.index');
        Route::post('/modules/activate/{slug}', [ModuleController::class, 'activate'])->name('modules.activate');
        Route::post('/modules/deactivate/{slug}', [ModuleController::class, 'deactivate'])->name('modules.deactivate');
        Route::post('/modules/scan', [ModuleController::class, 'scanAndAddModules'])->name('modules.scan');
        Route::post('/modules/update/{slug}', [ModuleController::class, 'update'])->name('modules.update');
    });

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

});

require __DIR__.'/auth.php';

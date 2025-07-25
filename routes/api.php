<?php

use App\Http\Controllers\Api\ArticleApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\CommentApiController;
use App\Http\Controllers\Api\LikeApiController;
use App\Http\Controllers\Api\MediaApiController;
use App\Http\Controllers\Api\ModuleApiController;
use App\Http\Controllers\Api\PageApiController;
use App\Http\Controllers\Api\SettingApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/auth')->name('auth.')->group(function () {
    Route::post('/login', [AuthApiController::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/logout', [AuthApiController::class, 'logout'])->name('logout');
        Route::get('/verify', [AuthApiController::class, 'verify'])->name('verify');
    });

});

Route::get('/articles', [ArticleApiController::class, 'index']);
Route::get('/articles/{article}', [ArticleApiController::class, 'show']);

Route::get('/pages', [PageApiController::class, 'index']);
Route::get('/pages/{page}', [PageApiController::class, 'show']);

Route::get('/media', [MediaApiController::class, 'index']);
Route::get('/media/{media}', [MediaApiController::class, 'show']);
Route::get('/articles/{article}/media', [MediaApiController::class, 'forArticle']);
Route::get('/media-items', [MediaApiController::class, 'mediaItems']);
Route::get('/modules', [ModuleApiController::class, 'index']);

Route::get('/settings', [SettingApiController::class, 'index']);
Route::get('/settings/{key}', [SettingApiController::class, 'show']);

Route::prefix('/articles/{article}/comments')->group(function () {
    Route::get('/', [CommentApiController::class, 'index']);
    Route::middleware('auth:sanctum')->post('/', [CommentApiController::class, 'store']);
});

Route::middleware('auth:sanctum')->prefix('/articles/{article}')->group(function () {
    Route::post('/like', [LikeApiController::class, 'toggle']);
    Route::get('/like', [LikeApiController::class, 'isLiked']);
});

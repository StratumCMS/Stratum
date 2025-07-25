<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModuleResource;
use App\Support\ModuleManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ModuleApiController extends Controller
{
    public function index(): JsonResponse
    {
        $allModules  = ModuleManager::all();
        $activeSlugs = ModuleManager::active();

        $activeModules = array_filter($allModules, fn ($module) => in_array($module['slug'], $activeSlugs));

        return response()->json([
            'modules' => ModuleResource::collection($activeModules),
        ]);
    }
}

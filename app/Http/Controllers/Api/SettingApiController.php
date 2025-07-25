<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingApiController extends Controller
{
    public function index(): JsonResponse {
        $settings = Setting::all();
        return response()->json([
            'settings' => SettingResource::collection($settings),
        ]);
    }

    public function show(string $key): JsonResponse {
        $setting = Setting::where('key', $key)->first();

        if (! $setting) {
            return response()->json(['error' => 'Setting introuvable.'], 404);
        }

        return response()->json(new SettingResource($setting));
    }
}

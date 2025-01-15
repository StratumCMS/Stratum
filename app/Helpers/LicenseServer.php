<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class LicenseServer {
    protected static $apiUrl = 'https://license.velyorix.com/api';

    /**
     * Get paid resources.
     *
     * @param string $licenseKey
     * @return array|null
     */
    public static function fetchPurchasedResources($licenseKey){
        $response = Http::post(self::$apiUrl . '/license/resources', [
            'licenseKey' => $licenseKey
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    /**
     * Validate resources if paid or not
     *
     * @param string $licenseKey
     * @param string $slug
     * @param string $type
     * @return array|null
     */
    public static function validateResource($licenseKey, $slug, $type){
        $response = Http::post(self::$apiUrl . '/resource/validate', [
            'license_key' => $licenseKey,
            'slug' => $slug,
            'type' => $type,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

}

<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class LicenseServer
{
    protected static string $apiBase = 'https://stratumcms.com/api/v1';
    protected static int $cacheTtl = 300;

    /**
     * Récupère les informations d'une licence depuis StratumCMS.
     */
    public static function getLicensedProducts(string $licenseKey): ?array
    {
        $licenseKey = trim($licenseKey);
        if ($licenseKey === '') {
            return null;
        }

        $cacheKey = 'stratum_license_' . sha1($licenseKey);

        if (self::$cacheTtl > 0 && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $url = rtrim(self::$apiBase, '/') . "/license/{$licenseKey}";

        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->get($url);

            if ($response->successful()) {
                $data = $response->json();

                if (is_array($data) && !empty($data['key'])) {
                    if (isset($data['status'])) {
                        $data['status'] = strtolower(trim((string)$data['status']));
                    }

                    if (self::$cacheTtl > 0) {
                        Cache::put($cacheKey, $data, self::$cacheTtl);
                    }

                    return $data;
                }

                Log::warning('LicenseServer: réponse inattendue (format)', [
                    'licenseKey' => $licenseKey,
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'json' => $response->json(),
                ]);
            } else {
                Log::warning('LicenseServer: requête échouée', [
                    'licenseKey' => $licenseKey,
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 1000),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('LicenseServer: exception HTTP', [
                'licenseKey' => $licenseKey,
                'message' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Vérifie si la licence est active (status === 'active').
     */
    public static function isLicenseActive(string $licenseKey): bool
    {
        $data = self::getLicensedProducts($licenseKey);

        if (!$data || empty($data['key'])) {
            Log::warning('LicenseServer: réponse invalide ou vide pour isLicenseActive', [
                'licenseKey' => $licenseKey,
                'data' => $data,
            ]);
            return false;
        }

        $status = isset($data['status']) ? strtolower(trim((string)$data['status'])) : '';
        return $status === 'active';
    }

    /**
     * Vérifie si un produit est accessible via sa licence.
     */
    public static function canAccessProduct(string $licenseKey, int $productId): bool
    {
        $data = self::getLicensedProducts($licenseKey);

        if (!$data || empty($data['products']) || !is_array($data['products'])) {
            return false;
        }

        return collect($data['products'])->contains(fn ($product) => isset($product['id']) && $product['id'] === $productId);
    }

    /**
     * Télécharge un produit donné s'il est accessible.
     */
    public static function downloadProduct(int $productId, ?string $licenseKey = null): ?string
    {
        $url = rtrim(self::$apiBase, '/') . "/products/{$productId}/download";

        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'Accept' => 'application/zip',
                ])
                ->get($url, [
                    'license' => $licenseKey,
                ]);

            if (!$response->ok()) {
                Log::warning('LicenseServer: downloadProduct requête échouée', [
                    'productId' => $productId,
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 1000),
                ]);
                return null;
            }

            if (stripos($response->header('Content-Type', ''), 'zip') === false) {
                Log::warning('LicenseServer: downloadProduct content-type inattendu', [
                    'productId' => $productId,
                    'contentType' => $response->header('Content-Type', ''),
                ]);
                return null;
            }

            $tempDir = storage_path("app/temp");
            if (!File::exists($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }

            $filePath = "{$tempDir}/{$productId}.zip";
            file_put_contents($filePath, $response->body());

            return $filePath;
        } catch (\Throwable $e) {
            Log::error('LicenseServer: exception downloadProduct', [
                'productId' => $productId,
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }
}

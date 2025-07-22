<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class LicenseServer
{
    protected static string $apiBase = 'https://stratumcms.com/api/v1';

    /**
     * Récupère la liste des produits liés à une licence.
     */
    public static function getLicensedProducts(string $licenseKey): ?array
    {
        $url = self::$apiBase . "/license/{$licenseKey}";

        $response = Http::get($url);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    /**
     * Vérifie si un produit est accessible via sa licence.
     */
    public static function canAccessProduct(string $licenseKey, int $productId): bool
    {
        $data = self::getLicensedProducts($licenseKey);

        if (!$data || empty($data['products'])) {
            return false;
        }

        return collect($data['products'])->contains(fn ($product) => $product['id'] === $productId);
    }

    /**
     * Télécharge un produit donné s'il est accessible.
     */
    public static function downloadProduct(int $productId, ?string $licenseKey = null): ?string
    {
        $url = self::$apiBase . "/products/{$productId}/download";

        $response = Http::withHeaders([
            'Accept' => 'application/zip',
        ])->get($url, [
            'license' => $licenseKey,
        ]);

        if (!$response->ok() || $response->header('Content-Type') !== 'application/zip') {
            return null;
        }

        $tempDir = storage_path("app/temp");
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $filePath = "{$tempDir}/{$productId}.zip";
        file_put_contents($filePath, $response->body());

        return $filePath;
    }
}

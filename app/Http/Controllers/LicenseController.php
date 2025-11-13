<?php

namespace App\Http\Controllers;

use App\Helpers\LicenseServer;
use App\Models\License;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string|unique:licenses,license_key|regex:/^ST\-[A-Z0-9]{4}\-[A-Z0-9]{4}$/',
        ]);

        $licenseKey = strtoupper(trim($request->license_key));

        if (!LicenseServer::isLicenseActive($licenseKey)) {
            return back()->with('error', 'Clé de licence invalide ou inactive.');
        }

        License::create(['license_key' => $licenseKey]);

        return back()->with('success', 'Licence enregistrée avec succès.');
    }

    public function getPurchasedResources()
    {
        $license = License::first();

        if (!$license) {
            return response()->json(['error' => 'Aucune licence trouvée.'], 404);
        }

        if (!LicenseServer::isLicenseActive($license->license_key)) {
            return response()->json(['error' => 'Licence invalide ou inactive.'], 403);
        }

        $data = LicenseServer::getLicensedProducts($license->license_key);

        return response()->json($data['products'] ?? []);
    }
}

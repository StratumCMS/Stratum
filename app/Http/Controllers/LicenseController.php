<?php

namespace App\Http\Controllers;

use App\Helpers\LicenseServer;
use App\Models\License;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'license_key' => 'required|string|unique:licenses,license_key',
        ]);

        $licenseKey = $request->license_key;

        $resources = LicenseServer::fetchPurchasedResources($licenseKey);

        if (!$resources || !isset($resources['valid']) || !$resources['valid']) {
            return back()->with('error', 'Clé de licence invalide.');
        }

        License::create(['license_key' => $licenseKey]);

        return back()->with('success', 'Licence enregistrée avec succès.');
    }

    public function getPurchasedResources(){
        $license = License::first();

        if (!$license) {
            return response()->json(['error' => 'Aucune licence trouvée.'], 404);
        }

        $resources = LicenseServer::fetchPurchasedResources($license->license_key);

        if (!$resources || !isset($resources['valid']) || !$resources['valid']) {
            return response()->json(['error' => 'Licence invalide.'], 403);
        }

        return response()->json($resources['resources']);
    }
}

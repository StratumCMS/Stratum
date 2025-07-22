<?php

namespace App\Http\Controllers;

use App\Services\CustomAssetsService;
use Illuminate\Http\Request;

class CustomAssetsController extends Controller
{
    protected CustomAssetsService $assetsService;

    public function __construct(CustomAssetsService $assetsService) {
        $this->assetsService = $assetsService;
    }

    public function edit(){
        return view ('admin.custom-assets.edit', [
            'css' => $this->assetsService->getFileContent('css'),
            'js' => $this->assetsService->getFileContent('js'),
        ]);
    }

    public function update(Request $request){
        $request->validate([
            'css' => 'nullable|string',
            'js' => 'nullable|string',
        ]);

        $this->assetsService->saveFileContent('css', $request->input('css'));
        $this->assetsService->saveFileContent('js', $request->input('js'));

        return redirect()->back()->with('success', 'Fichiers personnalisés mis à jour avec succès.');
    }
}

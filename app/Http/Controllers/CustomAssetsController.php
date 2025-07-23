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

    public function update(Request $request)
    {
        $css = $request->input('css');
        $js = $request->input('js');

        if (!is_string($css)) $css = '';
        if (!is_string($js)) $js = '';

        $this->assetsService->saveFileContent('css', $css);
        $this->assetsService->saveFileContent('js', $js);

        return redirect()->back()->with('success', 'Fichiers personnalisés mis à jour avec succès.');
    }
}

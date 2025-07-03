<?php

namespace App\Services;

use Spatie\ImageOptimizer\OptimizerChainFactory;

class ImageOptimizerService
{
    public function optimize(string $path): void
    {
        if (!setting('image_optimization')) {
            return;
        }

        $optimizerChain = OptimizerChainFactory::create();
        $optimizerChain->optimize($path);
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class CustomAssetsService
{
    protected string $resourcePath = 'resources/custom/';
    protected string $publicPath = 'public/custom/';

    public function ensureFilesExist(): void
    {
        if (!is_dir(base_path($this->resourcePath))) {
            mkdir(base_path($this->resourcePath), 0755, true);
        }

        $this->createFileIfMissing('custom.css');
        $this->createFileIfMissing('custom.js');
    }

    protected function createFileIfMissing(string $filename): void
    {
        $path = base_path($this->resourcePath . $filename);
        if (!file_exists($path)) {
            file_put_contents($path, '');
        }
    }

    public function getFileContent(string $type): string
    {
        $this->ensureFilesExist();
        return file_get_contents(base_path($this->resourcePath . 'custom.' . $type)) ?: '';
    }

    public function saveFileContent(string $type, string $content): void
    {
        $this->ensureFilesExist();

        $filename = 'custom.' . $type;

        $resourceFile = base_path($this->resourcePath . $filename);
        $publicFile   = public_path('custom/' . $filename);

        file_put_contents($resourceFile, $content ?? '');

        if (!is_dir(public_path('custom'))) {
            mkdir(public_path('custom'), 0755, true);
        }

        file_put_contents($publicFile, $content ?? '');
    }

    public function getCssPath(): string
    {
        return asset('custom/custom.css');
    }

    public function getJsPath(): string
    {
        return asset('custom/custom.js');
    }
}

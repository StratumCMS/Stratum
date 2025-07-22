<?php

namespace App\Services;

class CustomAssetsService {

    protected string $basePath = 'resources/custom/';

    public function ensureFilesExist(): void
    {
        if (!is_dir(base_path($this->basePath))) {
            mkdir(base_path($this->basePath), 0755, true);
        }

        $this->createFileIfMissing('custom.css');
        $this->createFileIfMissing('custom.js');
    }

    protected function createFileIfMissing(string $filename): void
    {
        $path = base_path($this->basePath . $filename);
        if (!file_exists($path)) {
            file_put_contents($path, '');
        }
    }

    public function getFileContent(string $type): string
    {
        $this->ensureFilesExist();
        return file_get_contents(base_path($this->basePath . 'custom.' . $type));
    }

    public function saveFileContent(string $type, string $content): void
    {
        $this->ensureFilesExist();
        file_put_contents(base_path($this->basePath . 'custom.' . $type), $content);
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

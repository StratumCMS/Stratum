<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class BuilderManager
{
    protected array $blocks = [];

    public function __construct()
    {
        $this->loadBlocksFromDirectory(resource_path('blocks'));
    }

    public function registerBlock(string $key, array $config): void
    {
        $this->blocks[$key] = $config;
    }

    public function getAvailableBlocks(): array
    {
        return $this->blocks;
    }

    public function renderLayout(array $layout): string
    {
        return $this->renderBlock([
            'type' => 'root',
            'children' => $layout,
            'settings' => [],
        ]);
    }

    public function renderBlock(array $block): string
    {
        $type = $block['type'] ?? null;

        if (!$type || !isset($this->blocks[$type])) {
            Log::warning("Bloc non reconnu ou invalide : " . json_encode($block));
            return '';
        }

        $view = $this->blocks[$type]['view'] ?? null;

        if (!$view || !View::exists($view)) {
            Log::warning("Vue non trouvée pour le bloc « $type » ($view)");
            return '';
        }

        $settings = $block['settings'] ?? [];

        if (!empty($block['children']) && is_array($block['children'])) {
            $settings['children_html'] = collect($block['children'])
                ->map(fn($child) => $this->renderBlock($child))
                ->join('');
        }

        return view($view, ['settings' => $settings])->render();
    }

    public function getBlockSchema(string $type): ?array
    {
        return $this->blocks[$type]['settings_schema'] ?? null;
    }

    public function allBlockConfigs(): array
    {
        return collect($this->blocks)
            ->map(fn($config, $key) => [
                'type' => $key,
                'label' => $config['label'] ?? ucfirst($key),
                'icon' => $config['icon'] ?? '',
                'category' => $config['category'] ?? 'Divers',
                'settings_schema' => $config['settings_schema'] ?? [],
            ])
            ->values()
            ->toArray();
    }

    protected function loadBlocksFromDirectory(string $directory): void
    {
        $bladeFiles = File::allFiles($directory);

        foreach ($bladeFiles as $file) {
            if ($file->getExtension() !== 'blade.php') continue;

            $relativePath = str_replace(resource_path('views') . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $viewPath = str_replace(['/', '.blade.php'], ['.', ''], $relativePath);
            $key = str_replace(['/', '\\'], '-', $file->getRelativePathname());
            $key = str_replace('.blade.php', '', $key);
            $jsonPath = str_replace('.blade.php', '.json', $file->getPathname());

            $schema = [];
            $meta = [];

            if (File::exists($jsonPath)) {
                $json = json_decode(File::get($jsonPath), true);
                $meta['label'] = $json['label'] ?? ucfirst(basename($key));
                $meta['icon'] = $json['icon'] ?? '🧩';
                $meta['category'] = $json['category'] ?? 'Divers';
                $schema = $json['settings_schema'] ?? $json;
            }

            $this->registerBlock($key, [
                'view' => $viewPath,
                'label' => $meta['label'],
                'icon' => $meta['icon'],
                'category' => $meta['category'],
                'settings_schema' => $schema,
            ]);
        }
    }
}

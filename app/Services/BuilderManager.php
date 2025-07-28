<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class BuilderManager
{
    protected array $blocks = [];

    public function registerBlock(string $key, array $config): void {
        $this->blocks[$key] = $config;
    }

    public function getAvailableBlocks(): array {
        return $this->blocks;
    }

    public function renderLayout(array $layout): string {
        return $this->renderBlock([
            'type' => 'root',
            'children' => $layout,
            'settings' => [],
        ]);
    }

    public function renderBlock(array $block): string {
        $type = $block['type'] ?? null;

        if (!$type || !isset($this->blocks[$type])) {
            if (app()->environment('local')) {
                Log::warning("Bloc non reconnu ou invalide : " . json_encode($block));
            }
            return '';
        }

        $view = $this->blocks[$type]['view'] ?? null;

        if (!$view || !View::exists($view)) {
            if (app()->environment('local')) {
                Log::warning("Vue non trouvée pour le bloc « $type » ($view)");
            }
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

    public function getBlockSchema(string $type): ?array {
        return $this->blocks[$type]['settings_schema'] ?? null;
    }

    public function allBlockConfigs(): array {
        return collect($this->blocks)
            ->map(fn($config, $key) => [
                'type' => $key,
                'label' => $config['label'] ?? ucfirst($key),
                'icon' => $config['icon'] ?? '',
                'settings_schema' => $config['settings_schema'] ?? [],
            ])
            ->values()
            ->toArray();
    }

}

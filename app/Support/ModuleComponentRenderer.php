<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class ModuleComponentRenderer {

    protected array $components = [];

    public function register(string $slug, string $viewPath, ?callable $dataCallback = null): void {
        $this->components[$slug] = [
            'view' => $viewPath,
            'data' => $dataCallback,
        ];
    }

    public function render(string $content): string {

        return preg_replace_callback(
            '/\{\{\s*([a-z0-9\-_]+\.[a-z0-9\-_]+)\s*\}\}/i',
            function ($matches) {
                return $this->renderComponent($matches[1]);
            },
            $content
        );
    }

    protected function renderComponent(string $slug): string {

        if (!isset($this->components[$slug])) {
            Log::warning("Composant de module non trouvé : ${slug}");

            if (config('app.debug')) {
                return "<div class='border border-red-500 bg-red-50 text-red-700 p-4 rounded'>
                    <strong>Composant non trouvé:</strong> {$slug}
                </div>";
            }
            return '<!-- Composant non trouvé -->';
        }

        try {
            $component = $this->components[$slug];
            $data = [];

            if (is_callable($component['data'])) {
                $data = call_user_func($component['data']);
            }

            if (!View::exists($component['view'])) {
                throw new \Exception("Vue non trouvée: {$component['view']}");
            }

            return View::make($component['view'], $data)->render();

        } catch (\Throwable $e) {
            Log::error("Erreur lors du rendu du composant {$slug}: {$e->getMessage()}");

            if (config('app.debug')) {
                return "<div class='border border-red-500 bg-red-50 text-red-700 p-4 rounded'>
                    <strong>Erreur lors du rendu du composant {$slug}:</strong><br>
                    {$e->getMessage()}
                </div>";
            }

            return '<!-- Erreur de rendu du composant -->';
        }

    }

    public function available(): array {
        return array_keys($this->components);
    }

    public function has(string $slug): bool {
        return isset($this->components[$slug]);
    }

    public function get(string $slug): ?array {
        return $this->components[$slug] ?? null;
    }

    public function clear(): void {
        $this->components = [];
    }

}

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

        $pattern = '/\{\{\s*([a-z0-9\-_]+(?:\.[a-z0-9\-_]+)+)(?:\s+([^}]+?))?\s*\}\}/i';

        return preg_replace_callback(
            $pattern,
            function ($matches) {
                $slug = $matches[1];
                $paramString = $matches[2] ?? '';
                $params = $this->parseParams($paramString);
                return $this->renderComponent($slug, $params);
            },
            $content
        );
    }

    protected function renderComponent(string $slug, array $params = []): string {

        if (!isset($this->components[$slug])) {
            Log::warning("Composant de module non trouvé : {$slug}");

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
                $data = call_user_func($component['data'], $params);
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

    protected function parseParams(string $paramString): array
    {
        $params = [];

        if (trim($paramString) === '') {
            return $params;
        }

        $regex = '/([a-z0-9_\-]+)\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s]+))/i';

        if (preg_match_all($regex, $paramString, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $key = $m[1];
                $value = null;

                if ($m[2] !== '') {
                    $value = $m[2];
                } elseif ($m[3] !== '') {
                    $value = $m[3];
                } else {
                    $value = $m[4];
                }

                if (is_numeric($value)) {
                    $value = (strpos($value, '.') !== false) ? (float)$value : (int)$value;
                } else {
                    $lower = strtolower($value);
                    if ($lower === 'true') {
                        $value = true;
                    } elseif ($lower === 'false') {
                        $value = false;
                    } elseif ($lower === 'null') {
                        $value = null;
                    }
                }

                $params[$key] = $value;
            }
        }

        return $params;
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

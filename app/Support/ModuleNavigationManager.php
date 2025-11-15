<?php

namespace App\Support;

class ModuleNavigationManager
{
    protected array $links = [];
    protected bool $initialized = false;

    public function add(array $links): void
    {
        if ($this->initialized) {
            return;
        }

        if (config('app.debug')) {
            \Log::debug('ModuleNavigationManager: adding links', ['count' => count($links)]);
        }

        $this->links = array_merge($this->links, $links);
    }

    public function all(): array
    {
        $this->initialized = true;
        return $this->links;
    }

    public function clear(): void
    {
        $this->links = [];
        $this->initialized = false;
    }

    public function isInitialized(): bool
    {
        return $this->initialized;
    }
}

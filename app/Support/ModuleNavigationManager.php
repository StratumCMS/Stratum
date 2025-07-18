<?php

namespace App\Support;


class ModuleNavigationManager
{
    protected array $links = [];

    public function add(array $links): void
    {
        \Log::info('Adding links to ModuleNavigationManager:', $links);

        $this->links = array_merge($this->links, $links);
    }

    public function all(): array
    {
        return $this->links;
    }
}

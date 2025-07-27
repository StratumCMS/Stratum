<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug'        => $this['slug'],
            'name'        => $this['name'] ?? ucfirst($this['slug']),
            'description' => $this['description'] ?? null,
            'version'     => $this['version'] ?? null,
            'icon'        => $this['icon'] ?? 'fa-puzzle-piece',
            'type'        => $this['type'] ?? 'link',
            'route'       => $this['route'] ?? null,
            'items'       => $this['items'] ?? [],
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'slug'          => $this->slug,
            'excerpt'       => $this->excerpt,
            'content'       => $this->content,
            'cover_image'   => $this->cover_image,
            'author'        => [
                'id'   => $this->author->id ?? null,
                'name' => $this->author->name ?? null,
            ],
            'published_at'  => optional($this->published_at)->toIso8601String(),
        ];
    }
}

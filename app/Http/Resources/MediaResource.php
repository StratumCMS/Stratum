<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'file_name'    => $this->file_name,
            'mime_type'    => $this->mime_type,
            'size'         => $this->size,
            'url'          => $this->getFullUrl(),
            'preview_url'  => $this->hasGeneratedConversion('thumb') ? $this->getFullUrl('thumb') : null,
            'created_at'   => $this->created_at?->toIso8601String(),
            'model_type'   => class_basename($this->model_type),
            'model_id'     => $this->model_id,
            'collection'   => $this->collection_name,
        ];

    }

}

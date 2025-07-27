<?php

namespace App\GraphQL\Types;

use Rebing\GraphQL\Support\Type as GraphQLType;
use GraphQL\Type\Definition\Type;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Media',
        'description' => 'Fichier média',
        'model' => Media::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
            ],
            'file_name' => [
                'type' => Type::string(),
            ],
            'name' => [
                'type' => Type::string(),
            ],
            'mime_type' => [
                'type' => Type::string(),
            ],
            'size' => [
                'type' => Type::int(),
            ],
            'url' => [
                'type' => Type::string(),
                'resolve' => fn ($media) => $media->getFullUrl(),
            ],
            'created_at' => [
                'type' => Type::string(),
            ],
            'updated_at' => [
                'type' => Type::string(),
            ],
        ];
    }

}

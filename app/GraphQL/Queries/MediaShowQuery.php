<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaShowQuery extends Query
{
    protected $attributes = [
        'name' => 'mediaShow',
        'description' => 'Détail d’un média spécifique',
    ];

    public function type(): Type
    {
        return GraphQL::type('Media');
    }

    public function args(): array
    {
        return [
            'id' => ['type' => Type::nonNull(Type::int())],
        ];
    }

    public function resolve($root, $args)
    {
        return Media::findOrFail($args['id']);
    }
}

<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaIndexQuery extends Query
{
    protected $attributes = [
        'name' => 'mediaIndex',
        'description' => 'Liste paginée des médias',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Media'));
    }

    public function resolve($root, $args)
    {
        return Media::latest()->paginate(15)->items();
    }
}

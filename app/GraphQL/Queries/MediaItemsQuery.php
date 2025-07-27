<?php

namespace App\GraphQL\Queries;

use App\Models\MediaItem;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class MediaItemsQuery extends Query
{
    protected $attributes = [
        'name' => 'mediaItems',
        'description' => 'Tous les médias liés aux media_items',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Media'));
    }

    public function resolve($root, $args)
    {
        return MediaItem::with('media')->get()->flatMap(fn ($item) => $item->getMedia());
    }
}

<?php

namespace App\GraphQL\Types;

use App\Models\Page;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PageType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Page',
        'description' => 'Une page personnalisée publiée',
        'model' => Page::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
            ],
            'title' => [
                'type' => Type::string(),
            ],
            'slug' => [
                'type' => Type::string(),
            ],
            'content' => [
                'type' => Type::string(),
            ],
            'meta_description' => [
                'type' => Type::string(),
            ],
            'template' => [
                'type' => Type::string(),
            ],
            'is_home' => [
                'type' => Type::boolean(),
            ],
            'views' => [
                'type' => Type::int(),
            ],
            'created_at' => [
                'type' => Type::string(),
                'resolve' => fn($page) => $page->created_at?->toIso8601String(),
            ],
            'updated_at' => [
                'type' => Type::string(),
                'resolve' => fn($page) => $page->updated_at?->toIso8601String(),
            ],
        ];
    }
}

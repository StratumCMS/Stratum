<?php

namespace App\GraphQL\Queries;

use App\Models\Page;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;

class PagesShowQuery extends Query
{
    protected $attributes = [
        'name' => 'pageShow',
        'description' => 'Affiche une page spécifique publiée',
    ];

    public function type(): Type
    {
        return GraphQL::type('Page');
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::int(),
            ],
            'slug' => [
                'type' => Type::string(),
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $query = Page::where('status', 'published');

        if (isset($args['id'])) {
            return $query->find($args['id']);
        }

        if (isset($args['slug'])) {
            return $query->where('slug', $args['slug'])->first();
        }

        return null;
    }
}

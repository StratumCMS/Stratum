<?php

namespace App\GraphQL\Queries;

use App\Models\Article;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ArticlesShowQuery extends Query
{

    protected $attributes = [
        'name' => 'articlesShow',
        'description' => 'Récupère un article spécifique',
    ];

    public function type(): Type
    {
        return GraphQL::type('Article');
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
        if (isset($args['id'])) {
            return Article::with('author')->find($args['id']);
        }

        if (isset($args['slug'])) {
            return Article::with('author')->where('slug', $args['slug'])->first();
        }

        return null;
    }
}

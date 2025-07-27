<?php

namespace App\GraphQL\Queries;

use App\Models\Article;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class MediaForArticleQuery extends Query
{
    protected $attributes = [
        'name' => 'mediaForArticle',
        'description' => 'Liste des médias d’un article',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Media'));
    }

    public function args(): array
    {
        return [
            'article_id' => ['type' => Type::nonNull(Type::int())],
        ];
    }

    public function resolve($root, $args)
    {
        $article = Article::findOrFail($args['article_id']);
        return $article->getMedia();
    }
}

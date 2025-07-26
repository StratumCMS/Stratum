<?php

namespace App\GraphQL\Types;
use App\Models\Article;
use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\Type as GraphQLBaseType;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ArticleType extends GraphQLType
{

    protected $attributes = [
        'name' => 'Article',
        'description' => 'Un article publié',
        'model' => Article::class,
    ];

    public function fields(): array {
        return [
            'id' => [
                'type' => GraphQLBaseType::nonNull(GraphQLBaseType::int()),
            ],
            'title' => [
                'type' => GraphQLBaseType::string(),
            ],
            'description' => [
                'type' => GraphQLBaseType::string(),
            ],
            'content' => [
                'type' => GraphQLBaseType::string(),
            ],
            'tags' => [
                'type' => GraphQLBaseType::listOf(GraphQLBaseType::string()),
            ],
            'is_published' => [
                'type' => GraphQLBaseType::boolean(),
            ],
            'published_at' => [
                'type' => GraphQLBaseType::string(),
            ],
            'thumbnail_url' => [
                'type' => GraphQLBaseType::string(),
                'resolve' => fn ($article) => $article->getFirstMediaUrl('thumbnails')  ?: $article->thumbnail(),
            ],
            'author' => [
                'type' => GraphQL::type('User'),
                'resolve' => fn($article) => $article->author,
            ],
        ];
    }

}

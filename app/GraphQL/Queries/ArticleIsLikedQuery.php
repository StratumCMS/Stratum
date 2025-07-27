<?php

namespace App\GraphQL\Queries;

use App\Models\Article;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Illuminate\Support\Facades\Auth;

class ArticleIsLikedQuery extends Query
{
    protected $attributes = [
        'name' => 'isArticleLiked',
        'description' => 'Vérifie si l’utilisateur connecté a liké un article',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function args(): array
    {
        return [
            'article_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID de l’article',
            ],
        ];
    }

    public function authorize(mixed $root, array $args, mixed $ctx, ?\GraphQL\Type\Definition\ResolveInfo $info = null, ?\Closure $getSelectFields = null): bool
    {
        return auth()->check();
    }

    public function resolve($root, $args)
    {
        $user = Auth::user();
        $article = Article::findOrFail($args['article_id']);

        return $article->likes()->where('user_id', $user->id)->exists();
    }
}

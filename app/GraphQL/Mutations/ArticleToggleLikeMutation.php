<?php

namespace App\GraphQL\Mutations;

use App\Models\Article;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Auth;

class ArticleToggleLikeMutation extends Mutation
{
    protected $attributes = [
        'name' => 'toggleArticleLike',
        'description' => 'Ajoute ou supprime un like sur un article',
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
                'description' => 'ID de l’article à liker ou disliker',
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

        $like = $article->likes()->where('user_id', $user->id);

        if ($like->exists()) {
            $like->delete();
            return false;
        }

        $article->likes()->create(['user_id' => $user->id]);
        return true;
    }
}

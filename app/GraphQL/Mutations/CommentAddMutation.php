<?php

namespace App\GraphQL\Mutations;

use App\Models\Article;
use App\Models\Comment;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Mutation;
use GraphQL;

class CommentAddMutation extends Mutation
{
    protected $attributes = [
        'name' => 'addComment',
        'description' => 'Ajoute un commentaire à un article',
    ];

    public function type(): Type
    {
        return GraphQL::type('Comment');
    }

    public function authorize(mixed $root, array $args, mixed $ctx, ?\GraphQL\Type\Definition\ResolveInfo $info = null, ?\Closure $getSelectFields = null): bool
    {
        return Auth::check();
    }

    public function args(): array
    {
        return [
            'article_id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'ID de l’article',
            ],
            'content' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Contenu du commentaire',
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $article = Article::findOrFail($args['article_id']);

        $comment = $article->comments()->create([
            'user_id' => Auth::id(),
            'content' => $args['content'],
        ]);

        return $comment->load('user');
    }
}

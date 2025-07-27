<?php

namespace App\GraphQL\Mutations;

use App\Models\Comment;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Mutation;

class CommentDeleteMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteComment',
        'description' => 'Supprime un commentaire de l’utilisateur connecté',
    ];

    public function type(): Type
    {
        return Type::boolean();
    }

    public function authorize(mixed $root, array $args, mixed $ctx, ?\GraphQL\Type\Definition\ResolveInfo $info = null, ?\Closure $getSelectFields = null): bool
    {
        return Auth::check();
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'ID du commentaire à supprimer',
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $comment = Comment::findOrFail($args['id']);

        if ($comment->user_id !== Auth::id()) {
            return false;
        }

        return $comment->delete();
    }
}

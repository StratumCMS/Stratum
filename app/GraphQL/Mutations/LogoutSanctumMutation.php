<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class LogoutSanctumMutation extends Mutation
{
    protected $attributes = [
        'name' => 'logout',
        'description' => 'Déconnexion',
    ];

    public function type(): Type
    {
        return Type::string();
    }

    public function authorize(mixed $root, array $args, mixed $ctx, ?\GraphQL\Type\Definition\ResolveInfo $info = null, ?\Closure $getSelectFields = null): bool
    {
        return auth()->check();
    }

    public function resolve($root, array $args)
    {
        auth()->user()->currentAccessToken()->delete();
        return 'Déconnecté avec succès.';
    }
}

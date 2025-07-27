<?php

namespace App\GraphQL\Queries;

use App\Models\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;

class UsersShowQuery extends Query
{

    protected $attributes = [
        'name' => 'usersShow',
        'description' => 'Récupère un utilisateur spécifique',
    ];
    public function type(): Type
    {
        return GraphQL::type('User');
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::int(),
            ],
            'email' => [
                'type' => Type::string(),
            ],
        ];
    }

    public function resolve($root, $args)
    {
        if (isset($args['id'])) {
            return User::with('roles')->find($args['id']);
        }

        if (isset($args['email'])) {
            return User::with('roles')->where('email', $args['email'])->first();
        }

        return null;
    }
}

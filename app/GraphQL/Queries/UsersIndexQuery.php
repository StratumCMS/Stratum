<?php

namespace App\GraphQL\Queries;

use App\Models\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class UsersIndexQuery extends Query
{
    protected $attributes = [
        'name' => 'users',
        'description' => 'Récupère la liste des utilisateurs',
    ];
    public function type(): Type
    {
        return Type::listOf(GraphQL::type('User'));
    }

    public function resolve($root, $args)
    {
        return User::with('roles')->get();
    }
}

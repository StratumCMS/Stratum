<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Hash;
use GraphQL;

class RegisterSanctumMutation extends Mutation
{
    protected $attributes = [
        'name' => 'register',
        'description' => 'Enregistrement d’un nouvel utilisateur',
    ];

    public function type(): Type
    {
        return GraphQL::type('LoginResult');
    }

    public function args(): array
    {
        return [
            'name' => ['type' => Type::nonNull(Type::string())],
            'email' => ['type' => Type::nonNull(Type::string())],
            'password' => ['type' => Type::nonNull(Type::string())],
        ];
    }

    public function resolve($root, array $args)
    {
        $user = User::create([
            'name' => $args['name'],
            'email' => $args['email'],
            'password' => Hash::make($args['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }
}

<?php

namespace App\GraphQL\Mutations;

use Rebing\GraphQL\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserProfileUpdateMutation extends Mutation
{

    protected $attributes = [
        'name' => 'updateProfile',
        'description' => 'Met à jour le profil de l’utilisateur connecté',
    ];

    public function type(): Type
    {
        return \GraphQL::type('User');
    }



    public function authorize(mixed $root, array $args, mixed $ctx, ?\GraphQL\Type\Definition\ResolveInfo $info = null, ?\Closure $getSelectFields = null): bool
    {
        return auth()->check();
    }


    public function args(): array
    {
        return [
            'name' => [
                'type' => Type::string(),
                'description' => 'Nom de l’utilisateur',
            ],
            'email' => [
                'type' => Type::string(),
                'description' => 'Adresse e-mail',
            ],
            'password' => [
                'type' => Type::string(),
                'description' => 'Mot de passe (sera hashé)',
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $user = Auth::user();

        if (isset($args['name'])) {
            $user->name = $args['name'];
        }

        if (isset($args['email'])) {
            $user->email = $args['email'];
        }

        if (isset($args['password'])) {
            $user->password = Hash::make($args['password']);
        }

        $user->save();

        return $user;
    }
}

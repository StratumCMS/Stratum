<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class ResetPasswordMutation extends Mutation
{
    protected $attributes = [
        'name' => 'resetPassword',
        'description' => 'Réinitialisation du mot de passe',
    ];

    public function type(): Type
    {
        return Type::string();
    }

    public function args(): array
    {
        return [
            'token' => Type::nonNull(Type::string()),
            'email' => Type::nonNull(Type::string()),
            'password' => Type::nonNull(Type::string()),
        ];
    }

    public function resolve($root, array $args)
    {
        $status = Password::reset(
            [
                'email' => $args['email'],
                'password' => $args['password'],
                'token' => $args['token'],
            ],
            function (User $user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? 'Mot de passe mis à jour.'
            : 'Échec.';
    }
}

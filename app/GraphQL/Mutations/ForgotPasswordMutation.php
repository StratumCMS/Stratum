<?php

namespace App\GraphQL\Mutations;
use Illuminate\Support\Facades\Password;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class ForgotPasswordMutation extends Mutation
{
    protected $attributes = [
        'name' => 'forgotPassword',
        'description' => 'Envoie un lien de réinitialisation de mot de passe',
    ];

    public function type(): Type
    {
        return Type::string();
    }

    public function args(): array
    {
        return [
            'email' => ['type' => Type::nonNull(Type::string())],
        ];
    }

    public function resolve($root, array $args)
    {
        $status = Password::sendResetLink(['email' => $args['email']]);

        return $status === Password::RESET_LINK_SENT
            ? 'Email envoyé.'
            : 'Échec de l’envoi.';
    }
}

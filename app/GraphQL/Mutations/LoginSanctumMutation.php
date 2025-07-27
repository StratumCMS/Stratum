<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use GraphQL;
use PragmaRX\Google2FA\Google2FA;

class LoginSanctumMutation extends Mutation
{

    protected $attributes = [
        'name' => 'login',
        'description' => 'Connexion d’un utilisateur via Sanctum avec prise en charge 2FA',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type('LoginResult'));
    }

    public function args(): array
    {
        return [
            'email' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'password' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'code' => [
                'type' => Type::string(),
                'description' => 'Code 2FA si activé',
            ],
        ];
    }

    public function resolve($root, array $args)
    {
        if (!Auth::attempt(['email' => $args['email'], 'password' => $args['password']])) {
            throw ValidationException::withMessages([
                'email' => ['Identifiants invalides.'],
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        if ($user->two_factor_enabled && !isset($args['code'])) {
            throw ValidationException::withMessages([
                'code' => ['Un code 2FA est requis.'],
            ]);
        }

        if ($user->two_factor_enabled && $user->google2fa_secret) {
            $secret = Crypt::decrypt($user->google2fa_secret);
            $google2fa = new Google2FA();

            if (! $google2fa->verifyKey($secret, $args['code'])) {
                throw ValidationException::withMessages([
                    'code' => ['Le code 2FA est invalide.'],
                ]);
            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }
}

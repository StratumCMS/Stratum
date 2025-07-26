<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use GraphQL;

class LoginResultType extends GraphQLType
{

    protected $attributes = [
        'name' => 'LoginResult',
        'description' => 'Résultat de la connexion',
    ];

    public function fields(): array
    {
        return [
            'user' => [
                'type' => GraphQL::type('User'),
            ],
            'access_token' => [
                'type' => Type::string(),
            ],
            'token_type' => [
                'type' => Type::string(),
            ],
        ];
    }
}

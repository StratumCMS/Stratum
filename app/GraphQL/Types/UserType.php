<?php

namespace App\GraphQL\Types;

use App\Models\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserType extends GraphQLType
{

    protected $attributes = [
        'name' => 'User',
        'description' => 'Un utilisateur du système',
        'model' => User::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
            ],
            'name' => [
                'type' => Type::string(),
            ],
            'email' => [
                'type' => Type::string(),
            ],
            'created_at' => [
                'type' => Type::string(),
            ],
            'updated_at' => [
                'type' => Type::string(),
            ],
            'roles' => [
                'type' => Type::listOf(Type::string()),
                'resolve' => fn($user) => $user->roles->pluck('name')->toArray(),
            ],
        ];
    }


}

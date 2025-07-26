<?php

namespace App\GraphQL\Types;
use App\Models\User;
use GraphQL\Type\Definition\Type as GraphQLBaseType;
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
                'type' => GraphQLBaseType::nonNull(GraphQLBaseType::int()),
            ],
            'name' => [
                'type' => GraphQLBaseType::string(),
            ],
            'email' => [
                'type' => GraphQLBaseType::string(),
            ],
            'created_at' => [
                'type' => GraphQLBaseType::string(),
            ],
        ];
    }

}

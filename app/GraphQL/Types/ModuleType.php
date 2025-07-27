<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ModuleType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Module',
        'description' => 'Un module du système',
    ];

    public function fields(): array
    {
        return [
            'name' => [
                'type' => Type::string(),
            ],
            'slug' => [
                'type' => Type::string(),
            ],
            'version' => [
                'type' => Type::string(),
            ],
            'author' => [
                'type' => Type::string(),
            ],
            'description' => [
                'type' => Type::string(),
            ],
        ];
    }
}

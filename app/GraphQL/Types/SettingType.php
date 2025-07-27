<?php

namespace App\GraphQL\Types;

use App\Models\Setting;
use Rebing\GraphQL\Support\Type as GraphQLType;
use GraphQL\Type\Definition\Type;

class SettingType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Setting',
        'description' => 'Clé de configuration',
        'model' => Setting::class,
    ];

    public function fields(): array
    {
        return [
            'key' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'value' => [
                'type' => Type::string(),
            ],
        ];
    }
}

<?php

namespace App\GraphQL\Queries;

use App\Models\Setting;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;

class SettingsShowQuery extends Query
{
    protected $attributes = [
        'name' => 'setting',
        'description' => 'Récupère une clé de configuration spécifique',
    ];

    public function type(): Type
    {
        return GraphQL::type('Setting');
    }

    public function args(): array
    {
        return [
            'key' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Clé de configuration',
            ],
        ];
    }

    public function resolve($root, $args)
    {
        return Setting::where('key', $args['key'])->first();
    }
}

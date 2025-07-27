<?php

namespace App\GraphQL\Queries;

use App\Models\Setting;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;

class SettingsIndexQuery extends Query
{
    protected $attributes = [
        'name' => 'settings',
        'description' => 'Liste toutes les clés de configuration',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Setting'));
    }

    public function resolve($root, $args)
    {
        return Setting::all();
    }
}

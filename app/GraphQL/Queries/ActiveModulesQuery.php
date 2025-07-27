<?php

namespace App\GraphQL\Queries;

use App\Support\ModuleManager;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class ActiveModulesQuery extends Query
{
    protected $attributes = [
        'name' => 'activeModules',
        'description' => 'Récupère les modules activés dans le système',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Module'));
    }

    public function resolve($root, $args)
    {
        $allModules  = ModuleManager::all();
        $activeSlugs = ModuleManager::active();

        return array_values(array_filter($allModules, function ($module) use ($activeSlugs) {
            return in_array($module['slug'], $activeSlugs);
        }));
    }
}

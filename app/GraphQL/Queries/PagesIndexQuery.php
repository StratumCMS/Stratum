<?php

namespace App\GraphQL\Queries;

use App\Models\Page;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class PagesIndexQuery extends Query
{
    protected $attributes = [
        'name' => 'pages',
        'description' => 'Liste toutes les pages publiées',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Page'));
    }

    public function resolve($root, $args)
    {
        return Page::where('status', 'published')
            ->latest()
            ->get();
    }
}

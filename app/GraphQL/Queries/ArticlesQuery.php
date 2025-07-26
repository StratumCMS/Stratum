<?php

namespace App\GraphQL\Queries;

use App\Models\Article;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class ArticlesQuery extends Query
{
    protected $attributes = [
        'name' => 'articles',
    ];


    public function type(): \GraphQL\Type\Definition\Type
    {
        return Type::listOf(GraphQL::type('Article'));
    }

    public function resolve($root, $args)
    {
        return Article::published()->get();
    }

}

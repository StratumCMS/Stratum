<?php

namespace App\GraphQL\Queries;

use App\Models\Article;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class ArticlesIndexQuery extends Query
{
    protected $attributes = [
        'name' => 'articles',
        'description' => 'Liste tous les articles',
    ];


    public function type(): \GraphQL\Type\Definition\Type
    {
        return Type::listOf(GraphQL::type('Article'));
    }

    public function resolve($root, $args)
    {
        return Article::with(['author', 'comments.user', 'likes'])
            ->where('is_published', true)
            ->where(function ($q) {
                $q->whereDate('published_at', '<=', now())->orWhereNull('published_at');
            })
            ->latest()
            ->get();
    }

}

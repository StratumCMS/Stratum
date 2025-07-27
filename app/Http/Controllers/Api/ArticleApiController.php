<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleApiController extends Controller
{
    public function index()
    {
        $articles = Article::with('author')
            ->where('is_published', true)
            ->where(function ($q) {
                $q->whereDate('published_at', '<=', now())->orWhereNull('published_at');
            })
            ->latest()
            ->paginate(10);

        return ArticleResource::collection($articles);
    }

    public function show(Article $article)
    {
        abort_unless($article->is_published, 404);

        $article->load('author');

        return new ArticleResource($article);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;

class HomeController extends Controller
{

    public function index()
    {
        $recentArticles = Article::with('author')
            ->where('is_published', true)
            ->where(function ($query) {
                $query->whereDate('published_at', '<=', now())
                    ->orWhereNull('published_at');
            })

            ->latest()
            ->take(3)
            ->get();

        return view('home', compact('recentArticles'));
    }

}

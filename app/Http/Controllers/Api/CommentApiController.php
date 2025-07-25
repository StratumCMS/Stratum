<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Article;
use Illuminate\Http\Request;

class CommentApiController extends Controller
{
    public function index(Article $article)
    {
        $comments = $article->comments()->with('user')->latest()->paginate(10);
        return CommentResource::collection($comments);
    }

    public function store(Request $request, Article $article)
    {
        $request->validate([
            'content' => 'required|string|min:2',
        ]);

        $comment = $article->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $request->input('content'),
        ]);

        return new CommentResource($comment->load('user'));
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class LikeApiController extends Controller
{
    public function toggle(Request $request, Article $article){
        $user = $request->user();

        $like = $article->likes()->where('user_id', $user->id);

        if ($like->exists()) {
            $like->delete();
            return response()->json(['liked' => false]);
        } else {
            $article->likes()->create(['user_id' => $user->id]);
            return response()->json(['liked' => true]);
        }
    }

    public function isLiked(Request $request, Article $article){
        $user = $request->user();

        $liked = $article->likes()->where('user_id', $user->id)->exists();

        return response()->json(['liked' => $liked]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Like;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle(Article $article){
        $like = $article->likes()->where('user_id', auth()->id());

        if ($like->exists()) {
            $like->delete();
            return response()->json(['liked' => false]);
        } else {
            $article->likes()->create(['user_id' => auth()->id()]);
            return response()->json(['liked' => true]);
        }
    }
}

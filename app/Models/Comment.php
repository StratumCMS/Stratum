<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['content', 'user_id', 'article_id'];

    public function article(){
        return $this->belongsTo(Article::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}

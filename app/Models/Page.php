<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = ['title','slug','content','meta_description','status','template','is_home','views'];
    protected $casts = ['is_home'=>'boolean'];
}

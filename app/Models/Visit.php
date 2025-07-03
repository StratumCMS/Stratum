<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    public $timestamps = false;
    protected $fillable = ['ip', 'visited_at'];
    protected $casts = [
        'visited_at' => 'datetime',
    ];
}

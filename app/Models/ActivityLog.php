<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'action', 'description', 'user_id',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}

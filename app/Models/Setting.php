<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];
    public $timestamps = false;

    public static function get($key, $default = null)
    {
        return static::query()->where('key', $key)->value('value') ?? $default;
    }

    public static function set($key, $value)
    {
        return static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function allAsArray()
    {
        return static::pluck('value', 'key')->toArray();
    }
}

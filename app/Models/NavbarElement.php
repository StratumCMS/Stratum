<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class NavbarElement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'icon', 'value', 'position', 'type', 'parent_id'
    ];

    protected static function booted()
    {
        static::creating(function ($element) {
            $element->position = static::max('position') + 1;
        });
    }

    public function hasParent(): bool
    {
        return $this->parent_id !== null;
    }

    public function getTypeValue(string $type): string
    {
        return $this->type === $type ? $this->value : '';
    }

    public function scopeParent(Builder $query): void
    {
        $query->whereNull('parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(NavbarElement::class, 'parent_id')->orderBy('position');
    }

    public function elements()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('position');
    }

    public function isDropdown()
    {
        return $this->type === 'dropdown';
    }

    public function getLink()
    {
        return match ($this->type) {
            'home' => route('home'),
            'page' => route('pages.show', $this->value),
            'module' => Route::has($this->value) ? route($this->value) : '#',
            'post' => route('posts.show', $this->value),
            'posts' => route('posts.index'),
            'external_link' => $this->value,
            default => '#',
        };
    }

    public function isCurrent(): bool
    {
        $request = request();

        return match ($this->type) {
            'home' => $request->routeIs('home'),
            'link' => $request->is($this->value),
            'page' => $request->routeIs('pages.show') && $request->route('path') === $this->value,
            'post' => $request->routeIs('posts.show') && $request->route('post.slug') === $this->value,
            'posts' => $request->routeIs('posts.*'),
            'module' => $request->routeIs(Str::beforeLast($this->value, '.').'.*'),
            'dropdown' => $this->elements
                ->contains(
                    fn (self $element) => ! $element->isDropdown() && $element->isCurrent()
                ),
            default => false,
        };
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MediaItem extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['name'];

    public function getFileTypeAttribute(): string
    {
        $mime = $this->getFirstMedia()?->mime_type ?? '';

        return match (true) {
            str_starts_with($mime, 'image/') => 'image',
            str_starts_with($mime, 'video/') => 'video',
            default => 'document',
        };
    }

    public function getFileSizeAttribute(): int|null
    {
        return $this->getFirstMedia()?->size;
    }

    public function getFilePathAttribute(): string|null
    {
        return $this->getFirstMedia()?->getUrl();
    }

    public function getMimeTypeAttribute(): string|null
    {
        return $this->getFirstMedia()?->mime_type;
    }

    public function getOriginalNameAttribute(): string|null
    {
        return $this->getFirstMedia()?->file_name;
    }

    public function getUploadedAtAttribute()
    {
        return $this->getFirstMedia()?->created_at;
    }
}

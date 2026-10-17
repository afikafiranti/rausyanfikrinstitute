<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','slug','excerpt','content','cover_url','published_at',
    ];

    protected $casts = ['published_at' => 'datetime'];

    public function getPublicCoverUrlAttribute(): ?string
    {
        return \App\Support\PublicImageUrl::resolve($this->cover_url, 'covers');
    }

    protected static function booted()
    {
        static::saving(function (Post $post) {
            if (filled($post->slug) && (! $post->exists || ! $post->isDirty('title'))) {
                return;
            }

            // Independent allocation avoids a check-then-insert race for equal titles.
            $base = Str::substr(Str::slug($post->title) ?: 'refleksi', 0, 228);
            $post->slug = $base.'-'.Str::lower((string) Str::ulid());
        });
    }

    public function scopePublished($q)
    {
        return $q->whereNotNull('published_at')
                 ->where('published_at', '<=', now());
    }
}

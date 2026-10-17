<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = ['title','image_url','caption'];

    public function getPublicImageUrlAttribute(): ?string
    {
        return \App\Support\PublicImageUrl::resolve($this->image_url, 'galleries');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','youtube_playlist_id','level_id','description','tags','is_active','order_index'
    ];

    protected $casts = [
        'tags'      => 'array',
        'is_active' => 'boolean',
    ];

    public function level()  { return $this->belongsTo(Level::class); }
    public function videos() { return $this->hasMany(Video::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'playlist_id','title','youtube_video_id','duration_sec','order_index','tags'
    ];

    protected $casts = ['tags' => 'array'];

    public function playlist() { return $this->belongsTo(Playlist::class); }
    public function progresses() { return $this->hasMany(VideoProgress::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoProgress extends Model
{
    use HasFactory;

    protected $table = 'video_progress';

    public const STATUS_STARTED   = 'started';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'user_id','video_id','watched_sec','status','last_watched_at'
    ];

    protected $casts = [
        'last_watched_at' => 'datetime',
    ];

    public function user()  { return $this->belongsTo(User::class); }
    public function video() { return $this->belongsTo(Video::class); }
}

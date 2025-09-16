<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    protected $fillable = ['name','description','criteria_json','is_active'];

    protected $casts = [
        'criteria_json' => 'array',
        'is_active'     => 'boolean',
    ];

    public function users() { return $this->hasMany(User::class); }
    public function playlists() { return $this->hasMany(Playlist::class); }
}

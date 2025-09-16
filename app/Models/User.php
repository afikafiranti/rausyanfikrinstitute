<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    // Status profil
    public const STATUS_PENDING   = 'pending';
    public const STATUS_ACTIVE    = 'active';
    public const STATUS_SUSPENDED = 'suspended';

    protected $fillable = [
        'name','email','phone','photo_url','angkatan','pekerjaan',
        'wilayah_id','level_id','status',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'consent_at'        => 'datetime',
    ];

    public function wilayah()
    {
        return $this->belongsTo(\App\Models\Wilayah::class);
    }

    public function roles()
    {
        return $this->belongsToMany(\App\Models\Role::class);
    }
}

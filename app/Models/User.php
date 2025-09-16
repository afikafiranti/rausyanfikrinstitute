<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    public const STATUS_PENDING   = 'pending';
    public const STATUS_ACTIVE    = 'active';
    public const STATUS_SUSPENDED = 'suspended';

    protected $fillable = [
        'name','email','phone','password','photo_url','angkatan','pekerjaan',
        'wilayah_id','level_id','status','consent_at','email_verified_at',
    ];

    protected $hidden = ['password','remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'consent_at'        => 'datetime',
    ];

    // ==== Relations ====
    public function roles()    { return $this->belongsToMany(Role::class); }             // role_user
    public function level()    { return $this->belongsTo(Level::class); }                 // levels
    public function wilayah()  { return $this->belongsTo(Wilayah::class); }               // wilayah
    public function reports()  { return $this->hasMany(KajianReport::class); }            // kajian_reports
    public function audits()   { return $this->hasMany(AuditLog::class, 'actor_id'); }    // audit_logs

    // ==== Helpers ====
    public function isKoorda(): bool { return $this->roles()->where('name','koorda')->exists(); }
    public function hasRole(string $name): bool {
    return $this->roles()->where('name', $name)->exists();
    }
    public function isAdminLike(): bool {
    return $this->roles()->whereIn('name',['admin','super_admin'])->exists();
    }
}

<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;
    
    public const STATUS_PENDING   = 'pending';
    public const STATUS_ACTIVE    = 'active';
    public const STATUS_SUSPENDED = 'suspended';

    protected $fillable = [
        'name','email','phone','password','photo_url','angkatan','pekerjaan',
        'wilayah_id','level_id','status','consent_at','email_verified_at', 'tempat_lahir','tanggal_lahir','pendidikan_terakhir','kampus','status_pernikahan','ab'
        
    ];

    protected $hidden = ['password','remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'consent_at'        => 'datetime',
    ];

    public function scopeAlumniListing(Builder $query, User $actor, array $filters): Builder
    {
        $canViewAll = $actor->hasAnyRole(['super_admin', 'admin']);
        abort_if(!$canViewAll && !$actor->wilayah_id, 403, 'Wilayah belum ditetapkan pada akun.');
        if (!$canViewAll) {
            $query->where('users.wilayah_id', $actor->wilayah_id);
        }

        $search = trim($filters['q'] ?? '');
        if ($search !== '') {
            $query->where(fn ($match) => $match->where('users.name', 'like', "%{$search}%")
                ->orWhere('users.email', 'like', "%{$search}%"));
        }
        foreach (['angkatan', 'wilayah_id', 'level_id', 'ab'] as $field) {
            if (isset($filters[$field]) && $filters[$field] !== '') {
                $query->where('users.'.$field, $filters[$field]);
            }
        }

        $sortMap = [
            'name' => 'users.name', 'email' => 'users.email', 'angkatan' => 'users.angkatan',
            'wilayah' => 'wilayah.name', 'level' => 'levels.description',
            'status' => 'users.status', 'created_at' => 'users.created_at',
        ];
        $sort = $sortMap[$filters['sort'] ?? 'created_at'] ?? 'users.created_at';
        if ($sort === 'wilayah.name') {
            $query->leftJoin('wilayah', 'wilayah.id', '=', 'users.wilayah_id');
        } elseif ($sort === 'levels.description') {
            $query->leftJoin('levels', 'levels.id', '=', 'users.level_id');
        }

        return $query->select('users.*')->orderBy($sort, ($filters['dir'] ?? '') === 'asc' ? 'asc' : 'desc')
            ->orderBy('users.id');
    }

    // Relations
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }
    public function level()   { return $this->belongsTo(Level::class); }
    public function wilayah() { return $this->belongsTo(Wilayah::class); }

    // Helpers
    public function hasRole(string|array $roles): bool
    {
        $roles = (array) $roles;
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    public function hasAnyRole(string|array $roles): bool
    {
        return $this->hasRole($roles);
    }

    public function isAdminLike(): bool
    {
        // sesuaikan dengan data DB Anda: super_admin, admin, koorda
        return $this->roles()->whereIn('name', ['super_admin','admin','koorda'])->exists();
    }
    protected $appends = ['avatar_url','has_avatar'];

    public function getAvatarUrlAttribute(): ?string
    {
        $url = $this->attributes['avatar_url'] ?? $this->attributes['photo_url'] ?? null;
        if ($url) {
            if (Str::startsWith($url, ['http://','https://','/storage/'])) {
                return $url;
            }

            if (Str::startsWith($url, 'storage/')) {
                return "/{$url}";
            }

            if (Storage::disk('public')->exists($url)) {
                return Storage::url($url);
            }
        }

        $path = $this->attributes['avatar_path'] ?? $this->attributes['avatar'] ?? $this->attributes['photo'] ?? null;
        if ($path) {
            if (Str::startsWith($path, ['http://','https://'])) return $path;
            if (Storage::disk('public')->exists($path)) return Storage::url($path);
            if (!Str::contains($path, '/')) {
                $guess = "avatars/{$path}";
                if (Storage::disk('public')->exists($guess)) return Storage::url($guess);
            }
        }
        return null; // tidak ada foto -> pakai ikon
    }

    public function getHasAvatarAttribute(): bool
    {
        return filled($this->avatar_url);
    }
}

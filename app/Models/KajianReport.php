<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class KajianReport extends Model
{
    use HasFactory;

    protected $table = 'kajian_reports';

    public const STATUS_DRAFT     = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_APPROVED  = 'approved';
    public const STATUS_REJECTED  = 'rejected';

    protected $fillable = [
        'user_id','wilayah_id','tanggal','tempat','judul','pemateri',
        'peserta_l','peserta_p','total_peserta','durasi_menit',
        'catatan','foto_url','status','reviewed_by','reviewed_at','reject_reason',
    ];

    protected $casts = [
        'tanggal'     => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function user()     { return $this->belongsTo(User::class); }
    public function wilayah()  { return $this->belongsTo(Wilayah::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return $query;
        }

        abort_unless($user->wilayah_id, 403);

        return $query->where('wilayah_id', $user->wilayah_id)
            ->where(fn (Builder $query) => $query->where('status', '!=', self::STATUS_DRAFT)->orWhere('user_id', $user->id));
    }

    public function scopeMatchingFilters(Builder $query, array $filters): Builder
    {
        return $query->when($filters['wilayah_id'] ?? null, fn (Builder $query, $value) => $query->where('wilayah_id', $value))
            ->when($filters['status'] ?? null, fn (Builder $query, $value) => $query->where('status', $value))
            ->when($filters['dari'] ?? null, fn (Builder $query, $value) => $query->where('tanggal', '>=', $value))
            ->when($filters['sampai'] ?? null, fn (Builder $query, $value) => $query->where('tanggal', '<=', $value));
    }
}

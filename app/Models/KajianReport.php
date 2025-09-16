<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberReflection extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_REVIEWED = 'reviewed';

    protected $fillable = [
        'user_id', 'wilayah_id', 'title', 'context', 'activity_name', 'body',
        'status', 'submitted_at', 'reviewed_by', 'reviewed_at', 'feedback',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function wilayah() { return $this->belongsTo(Wilayah::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
}

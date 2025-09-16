<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false; // tabel kita pakai created_at useCurrent
    protected $fillable = [
        'actor_id','action','entity_type','entity_id','payload_json','created_at',
    ];
}
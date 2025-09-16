<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false; // pakai created_at useCurrent di DB
    protected $fillable = ['actor_id','action','entity_type','entity_id','payload_json','created_at'];

    protected $casts = ['payload_json' => 'array', 'created_at' => 'datetime'];

    public function actor() { return $this->belongsTo(User::class, 'actor_id'); }
}

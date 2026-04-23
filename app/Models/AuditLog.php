<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = ['user_id', 'auditable_type', 'auditable_id', 'action', 'old', 'new', 'note', 'ip_address'];
    protected $casts    = ['old' => 'array', 'new' => 'array'];

    public function user()      { return $this->belongsTo(User::class); }
    public function auditable() { return $this->morphTo(); }
}

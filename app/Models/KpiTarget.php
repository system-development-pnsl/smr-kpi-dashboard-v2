<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiTarget extends Model
{
    protected $fillable = ['kpi_id', 'period', 'target_value', 'set_by', 'approved_by', 'approved_at', 'notes'];
    protected $casts    = ['period' => 'date', 'target_value' => 'float', 'approved_at' => 'datetime'];

    public function kpi()      { return $this->belongsTo(Kpi::class); }
    public function setter()   { return $this->belongsTo(User::class, 'set_by'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
}

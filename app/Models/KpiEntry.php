<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiEntry extends Model
{
    protected $fillable = ['kpi_id', 'value', 'period', 'note', 'source', 'submitted_by'];
    protected $casts    = ['period' => 'date', 'value' => 'float'];

    public function kpi()       { return $this->belongsTo(Kpi::class); }
    public function submitter() { return $this->belongsTo(User::class, 'submitted_by'); }
}

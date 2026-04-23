<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentKpiConfig extends Model
{
    protected $fillable = ['department_id', 'kpi_id', 'is_visible', 'sort_order', 'custom_target'];
    protected $casts    = ['is_visible' => 'boolean', 'custom_target' => 'float'];

    public function department() { return $this->belongsTo(Department::class); }
    public function kpi()        { return $this->belongsTo(Kpi::class); }

    public function getEffectiveTargetAttribute(): float
    {
        return $this->custom_target ?? $this->kpi->target;
    }
}

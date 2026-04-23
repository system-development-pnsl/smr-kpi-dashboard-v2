<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyReport extends Model
{
    protected $fillable = [
        'department_id', 'period', 'report_type', 'status', 'commentary',
        'commentary_by', 'commentary_at', 'finalized_at', 'finalized_by',
        'acknowledged_at', 'acknowledged_by', 'pdf_path', 'generated_at',
    ];
    protected $casts = [
        'period'          => 'date',
        'commentary_at'   => 'datetime',
        'finalized_at'    => 'datetime',
        'acknowledged_at' => 'datetime',
        'generated_at'    => 'datetime',
    ];

    public function department()    { return $this->belongsTo(Department::class); }
    public function finalizedBy()   { return $this->belongsTo(User::class, 'finalized_by'); }
    public function acknowledgedBy(){ return $this->belongsTo(User::class, 'acknowledged_by'); }

    public function getPeriodLabelAttribute(): string { return $this->period->format('F Y'); }
}

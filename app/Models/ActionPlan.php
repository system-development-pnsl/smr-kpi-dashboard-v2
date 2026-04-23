<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActionPlan extends Model
{
    protected $fillable = [
        'department_id', 'quarter', 'year', 'mission',
        'created_by', 'submitted_at', 'acknowledged_at', 'acknowledged_by',
    ];

    protected $casts = [
        'submitted_at'    => 'datetime',
        'acknowledged_at' => 'datetime',
    ];

    public function department()     { return $this->belongsTo(Department::class); }
    public function creator()        { return $this->belongsTo(User::class, 'created_by'); }
    public function acknowledgedBy() { return $this->belongsTo(User::class, 'acknowledged_by'); }
    public function goals()          { return $this->hasMany(StrategicGoal::class)->orderBy('sort_order'); }

    public function getQuarterLabelAttribute(): string { return "Q{$this->quarter} {$this->year}"; }

    public function getOverallProgressAttribute(): float
    {
        $goals = $this->goals()->with('actionItems')->get();
        if ($goals->isEmpty()) return 0.0;
        return round($goals->avg(fn($g) => $g->action_items_progress), 1);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoalActionItem extends Model
{
    protected $fillable = [
        'strategic_goal_id', 'description', 'due_date',
        'assignee_id', 'completion_pct', 'status', 'sort_order', 'completed_at',
    ];

    protected $casts = [
        'due_date'       => 'date',
        'completion_pct' => 'integer',
        'completed_at'   => 'datetime',
    ];

    public function goal()     { return $this->belongsTo(StrategicGoal::class, 'strategic_goal_id'); }
    public function assignee() { return $this->belongsTo(User::class, 'assignee_id'); }

    public function updateProgress(int $pct): void
    {
        $this->update([
            'completion_pct' => min(100, max(0, $pct)),
            'status'         => $pct >= 100 ? 'completed' : ($pct > 0 ? 'in_progress' : 'not_started'),
            'completed_at'   => $pct >= 100 ? now() : null,
        ]);
        $this->goal->recalculateStatus();
    }
}

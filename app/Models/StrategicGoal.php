<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StrategicGoal extends Model
{
    protected $fillable = [
        'action_plan_id', 'title', 'description',
        'target_date', 'owner_id', 'status', 'sort_order',
    ];

    protected $casts = ['target_date' => 'date'];

    public function actionPlan()  { return $this->belongsTo(ActionPlan::class); }
    public function owner()       { return $this->belongsTo(User::class, 'owner_id'); }
    public function actionItems() { return $this->hasMany(GoalActionItem::class, 'strategic_goal_id')->orderBy('sort_order'); }

    public function getActionItemsProgressAttribute(): float
    {
        $items = $this->actionItems;
        return $items->isEmpty() ? 0.0 : round($items->avg('completion_pct'), 1);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'on_track'  => 'green',
            'at_risk'   => 'amber',
            'off_track' => 'red',
            'completed' => 'blue',
            default     => 'gray',
        };
    }
}

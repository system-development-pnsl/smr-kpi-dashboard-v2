<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecurringTaskTemplate extends Model
{
    protected $fillable = [
        'department_id', 'title', 'description', 'priority', 'category',
        'tags', 'recurrence', 'interval_value', 'day_of_week', 'day_of_month',
        'assignee_ids', 'is_active', 'last_generated', 'created_by',
    ];
    protected $casts = [
        'tags'           => 'array',
        'assignee_ids'   => 'array',
        'is_active'      => 'boolean',
        'last_generated' => 'date',
    ];

    public function department() { return $this->belongsTo(Department::class); }
    public function creator()    { return $this->belongsTo(User::class, 'created_by'); }
}

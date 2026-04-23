<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'task_number', 'title', 'description', 'priority', 'status',
        'due_date', 'start_date', 'completed_at', 'department_id',
        'parent_task_id', 'created_by', 'category', 'tags',
        'recurrence', 'recurrence_interval',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'start_date'   => 'date',
        'completed_at' => 'datetime',
        'tags'         => 'array',
    ];

    public function assignees()   { return $this->belongsToMany(User::class, 'task_assignees')->withTimestamps(); }
    public function department()  { return $this->belongsTo(Department::class); }
    public function creator()     { return $this->belongsTo(User::class, 'created_by'); }
    public function parentTask()  { return $this->belongsTo(Task::class, 'parent_task_id'); }
    public function subtasks()    { return $this->hasMany(Task::class, 'parent_task_id'); }
    public function comments()    { return $this->hasMany(TaskComment::class); }
    public function attachments() { return $this->hasMany(TaskAttachment::class); }
    public function auditLogs()   { return $this->morphMany(AuditLog::class, 'auditable'); }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date->isPast()
            && ! in_array($this->status, ['DONE', 'CANCELLED']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                     ->whereNotIn('status', ['DONE', 'CANCELLED']);
    }

    public function scopeForDepartment($query, string $code)
    {
        return $query->whereHas('department', fn($q) => $q->where('code', $code));
    }
}

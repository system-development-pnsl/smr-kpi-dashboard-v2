<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'code',
        'full_name',
        'full_name_km',
        'email',
        'password',
        'gender',
        'phone',
        'profile_photo',
        'emergency_contact',
        'job_title',
        'department_id',
        'role',
        'reporting_manager_id',
        'start_date',
        'access_modules',
        'employment_type',
        'work_schedule',
        'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'access_modules'    => 'array',
        'start_date'        => 'date',
        'email_verified_at' => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function manager()
    {
        return $this->belongsTo(User::class, 'reporting_manager_id');
    }
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_assignees');
    }

    public function hasFinancialAccess(): bool
    {
        return in_array($this->role, ['owner', 'general_manager', 'agm', 'finance_director']);
    }

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', $this->full_name))
            ->map(fn($n) => strtoupper($n[0] ?? ''))
            ->take(2)->join('');
    }
}

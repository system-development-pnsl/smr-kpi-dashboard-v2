<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// ─────────────────────────────────────────────────────────────────────────────
// Department
// ─────────────────────────────────────────────────────────────────────────────
class Department extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'label', 'color', 'sort_order', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];

    public function users()     { return $this->hasMany(User::class); }
    public function tasks()     { return $this->hasMany(Task::class); }
    public function kpis()      { return $this->hasMany(Kpi::class); }
    public function documents() { return $this->hasMany(Document::class); }
}

// ─────────────────────────────────────────────────────────────────────────────
// User
// ─────────────────────────────────────────────────────────────────────────────
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'code', 'full_name', 'full_name_km', 'email', 'password',
        'gender', 'phone', 'profile_photo', 'emergency_contact',
        'job_title', 'department_id', 'role', 'reporting_manager_id',
        'start_date', 'access_modules', 'employment_type',
        'work_schedule', 'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'access_modules'    => 'array',
        'start_date'        => 'date',
        'email_verified_at' => 'datetime',
    ];

    public function department()  { return $this->belongsTo(Department::class); }
    public function manager()     { return $this->belongsTo(User::class, 'reporting_manager_id'); }
    public function tasks()       { return $this->belongsToMany(Task::class, 'task_assignees'); }

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

// ─────────────────────────────────────────────────────────────────────────────
// Task
// ─────────────────────────────────────────────────────────────────────────────
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

// ─────────────────────────────────────────────────────────────────────────────
// TaskComment
// ─────────────────────────────────────────────────────────────────────────────
class TaskComment extends Model
{
    protected $fillable = ['task_id', 'user_id', 'body', 'mentions'];
    protected $casts    = ['mentions' => 'array'];

    public function user() { return $this->belongsTo(User::class); }
    public function task() { return $this->belongsTo(Task::class); }
}

// ─────────────────────────────────────────────────────────────────────────────
// TaskAttachment
// ─────────────────────────────────────────────────────────────────────────────
class TaskAttachment extends Model
{
    protected $fillable = ['task_id', 'original_name', 'stored_path', 'mime_type', 'size_bytes', 'uploaded_by'];

    public function task()     { return $this->belongsTo(Task::class); }
    public function uploader() { return $this->belongsTo(User::class, 'uploaded_by'); }

    public function getSizeForHumansAttribute(): string
    {
        $bytes = $this->size_bytes;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024) . ' KB';
        return $bytes . ' B';
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Kpi
// ─────────────────────────────────────────────────────────────────────────────
class Kpi extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'department_id', 'unit', 'target',
        'is_lower_better', 'update_frequency', 'description',
        'is_active', 'is_hotel_wide',
    ];

    protected $casts = [
        'is_lower_better' => 'boolean',
        'is_active'       => 'boolean',
        'is_hotel_wide'   => 'boolean',
        'target'          => 'float',
    ];

    public function department()  { return $this->belongsTo(Department::class); }
    public function entries()     { return $this->hasMany(KpiEntry::class); }
    public function latestEntry() { return $this->hasOne(KpiEntry::class)->latestOfMany('period'); }
    public function targets()     { return $this->hasMany(KpiTarget::class); }

    public function currentStatus(): string
    {
        $entry = $this->latestEntry;
        if (! $entry) return 'no_data';

        $ratio = $this->is_lower_better
            ? $this->target / max($entry->value, 0.001)
            : $entry->value / max($this->target, 0.001);

        return match(true) {
            $ratio >= 1.0 => 'green',
            $ratio >= 0.8 => 'amber',
            default       => 'red',
        };
    }

    public function changeVsPrevious(): ?float
    {
        $entries = $this->entries()->latest('period')->limit(2)->pluck('value');
        return $entries->count() < 2 ? null : round($entries[0] - $entries[1], 2);
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// KpiEntry
// ─────────────────────────────────────────────────────────────────────────────
class KpiEntry extends Model
{
    protected $fillable = ['kpi_id', 'value', 'period', 'note', 'source', 'submitted_by'];
    protected $casts    = ['period' => 'date', 'value' => 'float'];

    public function kpi()         { return $this->belongsTo(Kpi::class); }
    public function submitter()   { return $this->belongsTo(User::class, 'submitted_by'); }
}

// ─────────────────────────────────────────────────────────────────────────────
// KpiTarget
// ─────────────────────────────────────────────────────────────────────────────
class KpiTarget extends Model
{
    protected $fillable = ['kpi_id', 'period', 'target_value', 'set_by', 'approved_by', 'approved_at', 'notes'];
    protected $casts    = ['period' => 'date', 'target_value' => 'float', 'approved_at' => 'datetime'];

    public function kpi()      { return $this->belongsTo(Kpi::class); }
    public function setter()   { return $this->belongsTo(User::class, 'set_by'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
}

// ─────────────────────────────────────────────────────────────────────────────
// ActionPlan
// ─────────────────────────────────────────────────────────────────────────────
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

// ─────────────────────────────────────────────────────────────────────────────
// StrategicGoal
// ─────────────────────────────────────────────────────────────────────────────
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

// ─────────────────────────────────────────────────────────────────────────────
// GoalActionItem
// ─────────────────────────────────────────────────────────────────────────────
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

    public function goal()    { return $this->belongsTo(StrategicGoal::class, 'strategic_goal_id'); }
    public function assignee(){ return $this->belongsTo(User::class, 'assignee_id'); }

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

// ─────────────────────────────────────────────────────────────────────────────
// BankAccount
// ─────────────────────────────────────────────────────────────────────────────
class BankAccount extends Model
{
    protected $fillable = ['code', 'name', 'bank', 'currency', 'type', 'min_threshold', 'sort_order', 'is_active'];

    public function balances()      { return $this->hasMany(BankBalance::class); }
    public function latestBalance() { return $this->hasOne(BankBalance::class)->latestOfMany('balance_date'); }
    public function transactions()  { return $this->hasMany(Transaction::class); }
}

// ─────────────────────────────────────────────────────────────────────────────
// BankBalance
// ─────────────────────────────────────────────────────────────────────────────
class BankBalance extends Model
{
    protected $fillable = [
        'bank_account_id', 'balance_date', 'opening_balance',
        'closing_balance', 'source', 'remark', 'recorded_by',
    ];
    protected $casts = [
        'balance_date'    => 'date',
        'opening_balance' => 'float',
        'closing_balance' => 'float',
    ];
}

// ─────────────────────────────────────────────────────────────────────────────
// CashFlowEntry
// ─────────────────────────────────────────────────────────────────────────────
class CashFlowEntry extends Model
{
    protected $fillable = [
        'period', 'category_code', 'category', 'type',
        'amount', 'note', 'submitted_by', 'approval_status',
    ];
    protected $casts = ['period' => 'date', 'amount' => 'float'];
}

// ─────────────────────────────────────────────────────────────────────────────
// Transaction
// ─────────────────────────────────────────────────────────────────────────────
class Transaction extends Model
{
    protected $fillable = [
        'description', 'amount', 'category_code',
        'transaction_date', 'bank_account_id', 'recorded_by',
    ];
    protected $casts = ['transaction_date' => 'date', 'amount' => 'float'];

    public function bankAccount() { return $this->belongsTo(BankAccount::class); }
    public function recordedBy()  { return $this->belongsTo(User::class, 'recorded_by'); }
}

// ─────────────────────────────────────────────────────────────────────────────
// CashFlowForecast
// ─────────────────────────────────────────────────────────────────────────────
class CashFlowForecast extends Model
{
    protected $fillable = [
        'iso_year', 'iso_week', 'week_start', 'week_end',
        'forecast_amount', 'actual_amount', 'alert_triggered', 'updated_by', 'notes',
    ];
    protected $casts = [
        'week_start'      => 'date',
        'week_end'        => 'date',
        'forecast_amount' => 'float',
        'actual_amount'   => 'float',
        'alert_triggered' => 'boolean',
    ];
}

// ─────────────────────────────────────────────────────────────────────────────
// Document
// ─────────────────────────────────────────────────────────────────────────────
class Document extends Model
{
    protected $fillable = [
        'original_name', 'stored_path', 'mime_type', 'size_bytes', 'file_type',
        'sha256', 'department_id', 'description', 'uploaded_by',
        'ai_status', 'extracted_data', 'confirmed_fields', 'confidence_scores',
        'confirmed_at', 'confirmed_by',
    ];
    protected $casts = [
        'extracted_data'    => 'array',
        'confirmed_fields'  => 'array',
        'confidence_scores' => 'array',
        'confirmed_at'      => 'datetime',
    ];

    public function department()  { return $this->belongsTo(Department::class); }
    public function uploadedBy()  { return $this->belongsTo(User::class, 'uploaded_by'); }
    public function confirmedBy() { return $this->belongsTo(User::class, 'confirmed_by'); }

    public function getSizeForHumansAttribute(): string
    {
        $bytes = $this->size_bytes;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024) . ' KB';
        return $bytes . ' B';
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// MonthlyReport
// ─────────────────────────────────────────────────────────────────────────────
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

// ─────────────────────────────────────────────────────────────────────────────
// RecurringTaskTemplate
// ─────────────────────────────────────────────────────────────────────────────
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

// ─────────────────────────────────────────────────────────────────────────────
// DepartmentKpiConfig
// ─────────────────────────────────────────────────────────────────────────────
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

// ─────────────────────────────────────────────────────────────────────────────
// AuditLog
// ─────────────────────────────────────────────────────────────────────────────
class AuditLog extends Model
{
    protected $fillable = ['user_id', 'auditable_type', 'auditable_id', 'action', 'old', 'new', 'note', 'ip_address'];
    protected $casts    = ['old' => 'array', 'new' => 'array'];

    public function user()      { return $this->belongsTo(User::class); }
    public function auditable() { return $this->morphTo(); }
}

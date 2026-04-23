<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

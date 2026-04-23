<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

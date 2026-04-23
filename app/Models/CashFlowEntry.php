<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashFlowEntry extends Model
{
    protected $fillable = [
        'period', 'category_code', 'category', 'type',
        'amount', 'note', 'submitted_by', 'approval_status',
    ];
    protected $casts = ['period' => 'date', 'amount' => 'float'];
}

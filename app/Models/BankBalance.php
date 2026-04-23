<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

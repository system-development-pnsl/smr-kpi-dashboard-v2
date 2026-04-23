<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

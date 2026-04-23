<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = ['code', 'name', 'bank', 'currency', 'type', 'min_threshold', 'sort_order', 'is_active'];

    public function balances()      { return $this->hasMany(BankBalance::class); }
    public function latestBalance() { return $this->hasOne(BankBalance::class)->latestOfMany('balance_date'); }
    public function transactions()  { return $this->hasMany(Transaction::class); }
}

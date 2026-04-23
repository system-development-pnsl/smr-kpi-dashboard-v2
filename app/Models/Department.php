<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

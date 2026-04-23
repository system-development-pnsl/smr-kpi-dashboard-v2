<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

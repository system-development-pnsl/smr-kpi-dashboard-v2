<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

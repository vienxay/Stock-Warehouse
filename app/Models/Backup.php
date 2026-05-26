<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    protected $fillable = ['filename', 'path', 'size', 'type', 'created_by'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFormatAttribute(): string
    {
        return strtolower(pathinfo($this->filename, PATHINFO_EXTENSION));
    }

    public function getFormattedSizeAttribute(): string
    {
        $b = (int) $this->size;
        if ($b >= 1048576) return number_format($b / 1048576, 2) . ' MB';
        if ($b >= 1024)    return number_format($b / 1024, 2) . ' KB';
        return $b . ' B';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ErrorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'subjectable_type',
        'subjectable_id',
        'reason',
        'data',
    ];

    protected $appends = [
        'subject'
    ];

    public function subjectable(): MorphTo
    {
        return $this->morphTo();
    }
    
    public function staff()
    {
        return $this->belongsTo(\App\Models\Staff::class, 'staff_id');
    }
}

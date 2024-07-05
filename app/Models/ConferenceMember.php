<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConferenceMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id',
        'card_id',
        'guest',
        'status',
        'time_in',
        'time_out'
    ];

    public function conference()
    {
        return $this->belongsTo(\App\Models\Conference::class);
    }
}

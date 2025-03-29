<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUid;

class Rate extends Model
{
    use HasUid;

    protected $fillable = [
        'type',
        'name',
        'consumable',
        'validity',
        'price',
        'status'
    ];
}

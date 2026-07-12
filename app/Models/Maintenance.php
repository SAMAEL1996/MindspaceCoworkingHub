<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUid;

class Maintenance extends Model
{
    use HasFactory, HasUid;

    protected $fillable = [
        'title',
        'date',
    ];
}

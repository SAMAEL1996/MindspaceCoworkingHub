<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashLogItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_log_id',
        'in',
        'out',
        'description'
    ];
}

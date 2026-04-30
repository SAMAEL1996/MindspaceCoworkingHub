<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'day',
        'month',
        'year',
        'total_daily_users',
        'total_flexi_users',
        'total_monthly_users',
        'total_conference_users',
        'total_sales'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUid;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SaleReport extends Model
{
    use HasFactory, HasUid, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    protected $fillable = [
        'staff_id',
        'daily_sale_id',
        'date',
        'staff_sales',
        'total_sales',
        'filename',
        'status'
    ];

    public function staff()
    {
        return $this->belongsTo(\App\Models\Staff::class, 'staff_id');
    }
}

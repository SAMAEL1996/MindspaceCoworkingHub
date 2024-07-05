<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'check_in',
        'check_out',
    ];

    protected $appends = [
        'check_in_carbon',
        'check_out_carbon',
    ];

    public function staff()
    {
        return $this->belongsTo(\App\Models\Staff::class, 'staff_id');
    }

    public function getCheckInCarbonAttribute()
    {
        return Carbon::parse($this->check_in);
    }

    public function getCheckOutCarbonAttribute()
    {
        return Carbon::parse($this->check_out);
    }
}

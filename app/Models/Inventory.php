<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Appstract\Meta\Metable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Inventory extends Model
{
    use HasUid, SoftDeletes, Metable, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
    
    protected $fillable = [
        'uid',
        'user_id',
        'item',
        'quantity',
        'unit',
        'date',
        'status',
        'is_active',
    ];

    protected $appends = [
        'status_color',
    ];

    public function getStatusColorAttribute()
    {
        if($this->status == 'In Stock') {
            $color = 'success';
        } elseif($this->status == 'Running Out') {
            $color = 'warning';
        } elseif($this->status == 'Out of Stock') {
            $color = 'danger';
        } else {
            $color = '';
        }

        return $color;
    }
}

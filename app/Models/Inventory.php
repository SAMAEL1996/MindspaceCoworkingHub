<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUid;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasUid, SoftDeletes;
    
    protected $fillable = [
        'uid',
        'user_id',
        'item',
        'quantity',
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

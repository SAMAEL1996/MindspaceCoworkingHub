<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cash_in',
        'date_cash_in',
        'cash_out',
        'date_cash_out',
        'total_sales',
        'status'
    ];

    protected $appends = [
        'credit',
        'debit'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(\App\Models\CashLogItem::class, 'cash_log_id');
    }

    public function getCreditAttribute()
    {
        return $this->items()->sum('in');
    }

    public function getDebitAttribute()
    {
        return $this->items()->sum('out');
    }
}

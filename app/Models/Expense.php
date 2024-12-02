<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUid;

class Expense extends Model
{
    use HasFactory, HasUid;

    protected $fillable = [
        'quantity',
        'item',
        'amount'
    ];
}

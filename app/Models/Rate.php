<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUid;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Rate extends Model
{
    use HasUid, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    protected $fillable = [
        'type',
        'name',
        'consumable',
        'validity',
        'price',
        'status'
    ];

    public static function getConferenceRates($packageId)
    {
        return self::where('type', 'Conference')
            ->where('name', 'like', '%Package '.$packageId.'%')
            ->where('status', true)
            ->pluck('price', 'consumable')
            ->toArray();
    }
}

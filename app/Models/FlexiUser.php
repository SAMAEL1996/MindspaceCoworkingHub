<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUid;
use Appstract\Meta\Metable;

class FlexiUser extends Model
{
    use HasFactory, HasUid, Metable;

    public static function boot() {
        parent::boot();

        static::created(function ($flexi) {
            $month = $flexi->start_at_carbon->format('F');
            $year = $flexi->start_at_carbon->format('Y');

            $sale = \App\Models\Sale::where('month', $month)->where('year', $year)->first();
            if(!$sale) {
                $sale = \App\Models\Sale::create(['month' => $month, 'year' => $year]);
            }

            $sale->total_flexi_users += 1;
            $sale->total_sales += (double)$flexi->amount;
            $sale->save();
        });
    }

    protected $fillable = [
        'card_id',
        'name',
        'contact_no',
        'facebook',
        'start_at',
        'end_at',
        'is_active',
        'status',
        'paid',
        'amount',
    ];

    protected $appends = [
        'remaining_time',
        'start_at_carbon',
        'end_at_carbon',
    ];

    public function card()
    {
        return $this->hasOne(\App\Models\Card::class, 'card_id');
    }

    public function getRemainingTimeAttribute()
    {
        $interval = $this->start_at_carbon->diff($this->end_at_carbon);
        $hours = $interval->h;
        $minutes = $interval->i;

        $hours += ($interval->d * 24);

        return $hours . ' hours and ' . $minutes . ' minutes';
    }

    public function getStartAtCarbonAttribute()
    {
        return \Carbon\Carbon::parse($this->start_at);
    }

    public function getEndAtCarbonAttribute()
    {
        return \Carbon\Carbon::parse($this->end_at);
    }

    public function recalculateTimeRemaining($daily_sale_id)
    {
        $dailySale = \App\Models\DailySale::find($daily_sale_id);

        $timeInCarbon = $dailySale->time_in_carbon;
        $timeOutCarbon = $dailySale->time_out_carbon;

        $consumed = $timeInCarbon->diffInMinutes($timeOutCarbon);

        $this->end_at = \Carbon\Carbon::parse($this->end_at)->subMinutes($consumed);
        $this->save();
    }

    public function checkRemainingTime()
    {
        $startAt = $this->start_at_carbon;
        $endAt = $this->end_at_carbon;

        if ($startAt->diffInMinutes($endAt) === 0 && $startAt->diffInHours($endAt) === 0) {
            $this->status = false;
            $this->save();
        }
    }
}

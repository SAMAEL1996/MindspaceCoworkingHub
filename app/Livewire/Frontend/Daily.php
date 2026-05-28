<?php

namespace App\Livewire\Frontend;

use Livewire\Component;
use App\Models\DailySale;
use Carbon\Carbon;

class Daily extends Component
{
    public $code;

    public $time;
    public $guest;

    public $timeIn;
    public $hours;
    public $minutes;
    public $seconds;
    public $endTime;
    public $blink = false;
    public $showTimeConsume = false;

    public function render()
    {
        return view('livewire.frontend.daily');
    }

    public function checkTime()
    {
        $this->showTimeConsume = true;

        $this->timeIn = null;
        $this->hours = null;
        $this->minutes = null;
        $this->seconds = null;
        $this->endTime = null;
        $this->blink = false;

        $this->guest = Dailysale::with('card')->whereHas('card', function($query) {
                $query->where('code', $this->code);
            })
            ->where('status', true)
            ->latest()
            ->first();

        if($this->guest) {
            $this->timeIn = Carbon::parse($this->guest->time_in);
            $this->calculateRemainingConsume();
        }
    }

    public function calculateRemainingConsume()
    {
        $currentTime = Carbon::now();
        $dailyPassConsume = $this->timeIn->diffInSeconds($currentTime);
        
        $this->time = [
            'hours' => intdiv($dailyPassConsume, 3600),
            'minutes' => intdiv($dailyPassConsume % 3600, 60),
            'seconds' => $dailyPassConsume % 60,
        ];

        $this->blink = ($this->time['seconds'] === 0);
    }
}

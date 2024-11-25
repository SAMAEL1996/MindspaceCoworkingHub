<?php

namespace App\Livewire\Frontend\Flexi;

use Livewire\Component;
use App\Models\FlexiUser;
use Carbon\Carbon;

class Timer extends Component
{
    public FlexiUser $flexi;
    public $time;

    public $timeIn;
    public $hours;
    public $minutes;
    public $seconds;
    public $endTime;
    public $blink = false;

    public function mount()
    {
        $dailyPass = \App\Models\DailySale::where('card_id', $this->flexi->card_id)->where('is_flexi', true)->latest()->first();
        $this->timeIn = Carbon::parse($dailyPass->time_in);
        $this->hours = $this->time['hours'];
        $this->minutes = $this->time['minutes'];
        $this->seconds = $this->time['seconds'];

        $this->endTime = $this->timeIn->addHours($this->hours)->addMinutes($this->minutes);
        $this->calculateRemainingTime();
    }

    public function render()
    {
        return view('livewire.frontend.flexi.timer');
    }

    public function calculateRemainingTime()
    {
        $currentTime = Carbon::now();

        // Calculate remaining time
        $remaining = $this->timeIn->diffInSeconds($currentTime);
        if ($remaining <= 0) {
            $this->time = [
                'hours' => 0,
                'minutes' => 0,
                'seconds' => 0,
            ];
        } else {
            $this->time = [
                'hours' => intdiv($remaining, 3600),
                'minutes' => intdiv($remaining % 3600, 60),
                'seconds' => $remaining % 60,
            ];

            $this->blink = ($this->time['seconds'] === 0);
        }
    }
}

<?php

namespace App\Livewire\Frontend;

use Livewire\Component;
use App\Models\FlexiUser;
use Carbon\Carbon;

class Flexi extends Component
{
    public $contact;

    public $flexi;
    public $time;

    public $showFlexiTime = false;

    public $timeIn;
    public $hours;
    public $minutes;
    public $seconds;
    public $endTime;
    public $blink = false;

    public function render()
    {
        return view('livewire.frontend.flexi');
    }

    public function checkTime()
    {
        $this->showFlexiTime = true;

        $this->flexi = FlexiUser::where('contact_no', $this->contact)->where('status', true)->latest()->first();
        if($this->flexi) {
            $this->time = $this->flexi->getRemainingTimeArray();
            
            $dailyPass = \App\Models\DailySale::where('card_id', $this->flexi->card_id)->where('is_flexi', true)->latest()->first();
            $this->timeIn = Carbon::parse($dailyPass->time_in);
            $this->hours = $this->time['hours'];
            $this->minutes = $this->time['minutes'];
            $this->seconds = $this->time['seconds'];

            $this->calculateRemainingTime();
        }
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

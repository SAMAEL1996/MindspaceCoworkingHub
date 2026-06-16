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

    private function normalizeCode(?string $code): string
    {
        $prefix = 'MS-D-0';
        $code = strtoupper((string) $code);

        if (str_starts_with($code, $prefix)) {
            $suffix = substr($code, strlen($prefix));
        } else {
            $suffix = preg_replace('/^MSD0?/', '', preg_replace('/[^A-Z0-9]/', '', $code));
        }

        $suffix = preg_replace('/\D/', '', $suffix);

        return $prefix . substr($suffix, 0, 2);
    }

    private function hasValidCodeFormat(string $code): bool
    {
        return preg_match('/^MS-D-0\d{2}$/', $code) === 1;
    }

    public function checkTime()
    {
        $this->resetErrorBag('code');
        $this->code = $this->normalizeCode($this->code);
        $this->showTimeConsume = false;
        $this->guest = null;
        $this->timeIn = null;
        $this->hours = null;
        $this->minutes = null;
        $this->seconds = null;
        $this->endTime = null;
        $this->blink = false;

        if (! $this->hasValidCodeFormat($this->code)) {
            $this->addError('code', 'Use the format MS-D-0**.');

            return;
        }

        $this->showTimeConsume = true;

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

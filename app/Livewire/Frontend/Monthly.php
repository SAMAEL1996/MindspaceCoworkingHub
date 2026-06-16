<?php

namespace App\Livewire\Frontend;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DailySale;
use App\Models\MonthlyUser;
use Carbon\Carbon;

class Monthly extends Component
{
    use WithPagination;

    public $contact;

    public $monthly;
    public $time;
    public $perPage = 10;

    public $showMonthlyTime = false;

    public $timeIn;
    public $endTime;
    public $blink = false;
    public $currentlyCheckin = false;
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $monthlyVisits = null;

        if ($this->monthly) {
            $monthlyVisits = $this->getMonthlyVisitsQuery()->paginate($this->perPage, ['*'], 'monthly-visits-page');
        }

        return view('livewire.frontend.monthly', [
            'monthlyVisits' => $monthlyVisits,
        ]);
    }

    public function checkTime()
    {
        $this->showMonthlyTime = true;
        $this->resetPage('monthly-visits-page');

        $this->monthly = null;
        $this->time = null;
        $this->timeIn = null;
        $this->endTime = null;
        $this->blink = false;
        $this->currentlyCheckin = false;

        $this->monthly = MonthlyUser::where('contact_no', $this->contact)->where('is_expired', false)->latest()->first();

        if($this->monthly) {
            $dailyPass = DailySale::where('card_id', $this->monthly->card_id)
                ->where('is_monthly', true)
                ->where('status', true)
                ->latest()
                ->first();

            if($dailyPass) {
                $currentTime = Carbon::now();

                $this->timeIn = $dailyPass->time_in_carbon;

                $dailyPassConsume = $this->timeIn->diffInSeconds($currentTime);

                $this->time = [
                    'hours' => intdiv($dailyPassConsume, 3600),
                    'minutes' => intdiv($dailyPassConsume % 3600, 60),
                    'seconds' => $dailyPassConsume % 60,
                ];

                $this->currentlyCheckin = true;
            }
        }
    }

    public function calculateTimeConsume()
    {
        if (! $this->currentlyCheckin || ! $this->monthly || ! $this->timeIn) {
            return;
        }

        $activeDailyPass = DailySale::where('card_id', $this->monthly->card_id)
            ->where('is_monthly', true)
            ->where('status', true)
            ->latest('time_in')
            ->first();

        if (! $activeDailyPass) {
            $this->currentlyCheckin = false;
            $this->time = null;
            $this->timeIn = null;
            $this->blink = false;

            return;
        }

        $this->showMonthlyTime = true;
        $this->timeIn = $activeDailyPass->time_in_carbon;

        $currentTime = Carbon::now();
        $dailyPassConsume = $this->timeIn->diffInSeconds($currentTime);

        $this->time = [
            'hours' => intdiv($dailyPassConsume, 3600),
            'minutes' => intdiv($dailyPassConsume % 3600, 60),
            'seconds' => $dailyPassConsume % 60,
        ];

        $this->blink = ($this->time['seconds'] === 0);
    }

    private function getMonthlyVisitsQuery()
    {
        return DailySale::where('card_id', $this->monthly->card_id)
            ->whereBetween('time_in', [
                Carbon::parse($this->monthly->date_start)->startOfDay(),
                Carbon::parse($this->monthly->date_finish)->endOfDay(),
            ])
            ->where('is_monthly', true)
            ->latest('time_in');
    }
}

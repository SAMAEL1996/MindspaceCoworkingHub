<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    public $staffLogin;

    public function mount()
    {
        $this->staffLogin = session()->has('staff-shift');
    }

    public function startShift()
    {
        $staff = auth()->user()->staff;

        $attendance = \App\Models\Attendance::create([
            'staff_id' => $staff->id,
            'check_in' => \Carbon\Carbon::now()
        ]);

        session(['staff-shift' => true]);

        return redirect()->intended(Filament::getUrl());
    }

    public function justVisit()
    {
        session(['staff-shift' => false]);

        return redirect()->intended(Filament::getUrl());
    }
}

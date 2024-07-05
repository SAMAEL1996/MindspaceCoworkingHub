<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Session;

class StaffShiftWidget extends Widget
{
    protected static string $view = 'filament.widgets.staff-shift-widget';

    public function getColumns(): int | string | array
    {
        return 2;
    }

    protected static bool $isLazy = false;
    
    public $hasShift;
    public $timeCheck;
    
    public function mount()
    {
        $staff = auth()->user()->staff;
        $attendance = $staff->attendances()->latest()->first();

        if(Session::get('on_shift')) {
            $this->timeCheck = \Carbon\Carbon::parse($attendance->check_in)->diffForHumans();
        }
    }

    public function startShift()
    {
        $staff = auth()->user()->staff;

        $attendance = \App\Models\Attendance::create([
            'staff_id' => $staff->id,
            'check_in' => \Carbon\Carbon::now()
        ]);

        Session::put('on_shift', true);

        Notification::make()
            ->title('You are now started your shift!')
            ->success()
            ->send();

        return redirect()->intended(Filament::getUrl());
    }

    public function endShift()
    {
        $staff = auth()->user()->staff;
        $attendance = $staff->attendances()->latest()->first();

        $attendance->update([
            'check_out' => \Carbon\Carbon::now()
        ]);

        Session::put('on_shift', false);

        Notification::make()
            ->title('You ended your shift!')
            ->success()
            ->send();

        $filename = $attendance->check_in_carbon->format('Y-m-d') . '-' . $attendance->check_out_carbon->format('Y-m-d') . '-staff_' . $attendance->staff_id . '.xlsx';

        \App\Models\Report::create([
            'staff_id' => $staff->id,
            'attendance_id' => $attendance->id,
            'date' => \Carbon\Carbon::now(),
            'staff_sales' => $staff->dailySales()
                                        ->where('time_out_staff_id', $staff->id)
                                        ->whereBetween('created_at', [$attendance->check_in_carbon, $attendance->check_out_carbon])
                                        ->sum('amount_paid'),
            'total_sales' => $staff->dailySales()
                                        ->whereBetween('created_at', [$attendance->check_in_carbon, $attendance->check_out_carbon])
                                        ->sum('amount_paid'),
            'filename' => $filename
        ]);

        return redirect()->intended(Filament::getUrl());
    }

    public function testlang()
    {
        $this->dispatch('close-modal', id: 'shiftModal');
    }

    public static function canView(): bool
    {
        return auth()->user()->staff ? true : false;
    }
}

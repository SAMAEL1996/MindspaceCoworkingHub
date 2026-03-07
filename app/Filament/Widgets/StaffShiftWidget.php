<?php

namespace App\Filament\Widgets;

use App\Models\Setting;
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
    
    public $hasShift;

    public function startShift()
    {
        $staff = auth()->user()->staff;

        $attendance = \App\Models\Attendance::create([
            'staff_id' => $staff->id,
            'check_in' => \Carbon\Carbon::now()
        ]);

        Notification::make()
            ->title('Success')
            ->body('You are now started your shift!')
            ->success()
            ->send();
    }

    public function endShift()
    {
        $staff = auth()->user()->staff;
        $attendance = $staff->attendances()->latest()->first();

        $attendance->update([
            'check_out' => \Carbon\Carbon::now()
        ]);

        Notification::make()
            ->title('Success')
            ->body('You ended your shift!')
            ->success()
            ->send();
    }

    public function getTimeCheckProperty()
    {
        $staff = auth()->user()->staff;
        $attendance = $staff->attendances()->latest()->first();

        if (!$attendance || $attendance->check_out) {
            return null;
        }

        return \Carbon\Carbon::parse($attendance->check_in)->diffForHumans();
    }

    public function getOnShiftProperty(): bool
    {
        $staff = auth()->user()->staff;

        return $staff->attendances()
            ->whereNull('check_out')
            ->exists();
    }

    public function getValidateByCardProperty(): bool
    {
        return (bool) Setting::getValue('validate-by-card');
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

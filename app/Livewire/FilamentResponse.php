<?php

namespace App\Livewire;

use Livewire\Component;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;

class FilamentResponse extends Component
{
    public function checkRfidScan()
    {
        if (Cache::has('rfid-scanned-response')) {

            $item = Cache::pull('rfid-scanned-response');
            $status = $item['status'];

            Notification::make()
                ->title(ucfirst($status))
                ->body($item['message'])
                ->$status()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.filament-response');
    }
}

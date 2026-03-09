<?php

namespace App\Livewire;

use App\Models\Card;
use App\Models\CashLog;
use Livewire\Component;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class FilamentResponse extends Component
{
    public function checkRfidScan()
    {
        $currentCashier = CashLog::getCurrentCashierUser();

        // FOR ADMIN TEST
        if(!Auth::user()->hasRole('Super Administrator')) {
            return;
        }
        // FOR STAFF
        // if (!$currentCashier || Auth::id() !== $currentCashier->id) {
        //     return;
        // }

        if (Cache::has('rfid-scanned-response')) {
            $item = Cache::pull('rfid-scanned-response');
            $status = $item['status'];

            if($item['card_id']) {
                $card = Card::find($item['card_id']);

                Notification::make()
                    ->title('Success')
                    ->body($card->code . ' successfully scanned')
                    ->success()
                    ->send();
            }

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

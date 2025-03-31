<?php

namespace App\Livewire;

use Livewire\Component;

class CardRfidForm extends Component
{
    public ?string $rfid = '';

    public function mount()
    {
        $this->rfid = session('card', ''); // Get session value if exists
    }

    public function render()
    {
        return view('livewire.card-rfid-form');
    }
}

<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Card;
use Filament\Forms\Components as FormComponents;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;

class CardRfidForm extends Component implements HasForms
{
    use InteractsWithForms;

    public Card $card;

    public ?array $data = [];

    public $rfid;
    public $rfidCache;

    public function mount()
    {
        $this->rfid = session('card');
        $this->form->fill(['rfid' => $this->rfid]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FormComponents\TextInput::make('rfid')
                    ->default($this->card)
                    ->required(),
            ])
            ->statePath('data');
    }

    public function getSessionCard()
    {
        $this->rfidCache = \Cache::get('card');
        $this->rfid = session('card');
        $this->form->fill(['rfid' => $this->rfid]); // Sync form value with session
    }

    public function render()
    {
        return view('livewire.card-rfid-form');
    }
}

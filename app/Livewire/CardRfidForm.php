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
    public $rfid_fetched;

    public function mount()
    {
        $this->data = [
            'code' => $this->card->code,
            'rfid' => $this->card->rfid,
            'type' => $this->card->type,
            'status' => $this->card->status
        ];
        $this->form->fill($this->data);
    }

    public function save()
    {
        $data = $this->form->getState();
        dd($data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FormComponents\Grid::make(2)
                    ->schema([
                        FormComponents\TextInput::make('code')
                            ->label('Code')
                            ->required(),
                        FormComponents\TextInput::make('rfid')
                            ->label('RFID')
                            ->required(),
                        FormComponents\Select::make('type')
                            ->label('Type')
                            ->options(Card::getTypeSelectOptions())
                            ->native(false),
                        FormComponents\Select::make('status')
                            ->label('Status')
                            ->options(Card::getStatusSelectOptions())
                            ->native(false)
                    ])
            ])
            ->statePath('data')
            ->model($this->card);
    }

    public function getSessionCard()
    {
        $this->rfid = \App\Models\Setting::getValue('card');
        $this->rfid_fetched = $this->rfid;
    }

    public function render()
    {
        return view('livewire.card-rfid-form');
    }

    public function fetchRfidScanned()
    {
        $this->rfid = \App\Models\Setting::getValue('card');
        $this->rfid_fetched = $this->rfid;
    }

    public function getRfidScanned()
    {
        $this->data['rfid'] = $this->rfid_fetched;
        $this->form->fill($this->data);
    }
}

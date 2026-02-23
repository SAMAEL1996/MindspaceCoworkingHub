<?php

namespace App\Livewire;

use App\Filament\Resources\CardResource;
use Livewire\Component;
use App\Models\Card;
use Filament\Forms\Components as FormComponents;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;

class CardRfidForm extends Component implements HasForms
{
    use InteractsWithForms;

    public Card $card;

    public ?array $data = [];

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
        $this->card->update([
            'code' => $data['code'],
            'rfid' => $data['rfid'],
            'type' => $data['type'],
            'status' => $data['status']
        ]);

        Notification::make()
            ->title('Success')
            ->body('Card successfully updated.')
            ->success()
            ->send();

        return redirect()->to(CardResource::getUrl('index'));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FormComponents\Grid::make(2)
                    ->schema([
                        FormComponents\TextInput::make('code')
                            ->label('Code')
                            ->unique(ignoreRecord: true)
                            ->required(),
                        FormComponents\TextInput::make('rfid')
                            ->label('RFID')
                            ->unique(ignoreRecord: true)
                            ->disabled()
                            ->dehydrated(),
                        FormComponents\Select::make('type')
                            ->label('Type')
                            ->options(Card::getTypeSelectOptions())
                            ->native(false)
                            ->required(),
                        FormComponents\Select::make('status')
                            ->label('Status')
                            ->options(Card::getStatusSelectOptions())
                            ->native(false)
                            ->required()
                    ])
            ])
            ->statePath('data')
            ->model($this->card);
    }

    public function render()
    {
        return view('livewire.card-rfid-form');
    }

    public function checkScannedRfid()
    {
        $rfid = Cache::pull('scanned_rfid');

        if ($rfid) {
            $this->data['rfid'] = $rfid;

            // Important: update Filament form state
            $this->form->fill($this->data);
        }
    }
}

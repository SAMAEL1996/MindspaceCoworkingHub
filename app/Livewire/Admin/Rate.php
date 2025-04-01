<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use App\Models\Rate as RateModel;
use Filament\Infolists\Components as InfolistComponents;
use Filament\Forms\Form;
use Filament\Forms\Components as FormComponents;
use Filament\Notifications\Notification;

class Rate extends Component implements HasForms, HasInfolists
{
    use InteractsWithInfolists;
    use InteractsWithForms;

    public ?array $data = [];

    public $dataInfolist = [];

    public function mount()
    {
        $this->form->fill();

        $rates = \DB::table('rates')
            ->get()
            ->groupBy('type')
            ->toArray();

        $this->dataInfolist['rates'] = [];
        $this->data['rates'] = [];
        foreach($rates as $key => $items) {
            $rate = [
                'label' => $key,
                'items' => array_map(fn($item) => (array) $item, $items)
            ];

            $this->dataInfolist['rates'][] = $rate;
            $this->data['rates'][] = $rate;
        }
    }

    public function render()
    {
        return view('livewire.admin.rate');
    }

    public function rateInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->state($this->dataInfolist)
            ->schema([
                InfolistComponents\RepeatableEntry::make('rates')
                    ->label('')
                    ->schema([
                        InfolistComponents\Section::make(fn ($state) => $state['label'])
                            ->schema([
                                InfolistComponents\RepeatableEntry::make('items')
                                    ->label('')
                                    ->schema([
                                        InfolistComponents\TextEntry::make('name')
                                        ->columnSpan(1),
                                        InfolistComponents\TextEntry::make('consumable')
                                        ->columnSpan(1),
                                        InfolistComponents\TextEntry::make('validity')
                                        ->columnSpan(1),
                                        InfolistComponents\TextEntry::make('price')
                                        ->columnSpan(1)
                                    ])->columns(4)
                            ])
                    ])
                    ->contained(false)
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FormComponents\Repeater::make('rates')
                    ->label('')
                    ->schema([
                        FormComponents\Section::make(fn ($state) => $state['label'])
                            ->schema([
                                FormComponents\Repeater::make('items')
                                    ->label('')
                                    ->schema([
                                        FormComponents\TextInput::make('name')
                                            ->required(),
                                        FormComponents\TextInput::make('consumable')
                                            ->numeric()
                                            ->minValue(1)
                                            ->required(function($get) {
                                                $label = $get('../../label');
                                                
                                                return $label == 'Monthly' ? false : true;
                                            })
                                            ->hidden(function($get) {
                                                $label = $get('../../label');
                                                
                                                return $label == 'Monthly' ? true : false;
                                            })
                                            ->helperText('Number of hours'),
                                        FormComponents\TextInput::make('validity')
                                            ->numeric()
                                            ->minValue(1)
                                            ->required(function($get) {
                                                $label = $get('../../label');
                                                
                                                return $label == 'Flexi' ? true : false;
                                            })
                                            ->hidden(function($get) {
                                                $label = $get('../../label');
                                                
                                                return $label == 'Flexi' ? false : true;
                                            })
                                            ->helperText('Number of days'),
                                        FormComponents\TextInput::make('price')
                                            ->numeric()
                                            ->minValue(1)
                                            ->required()
                                    ])
                                    ->grid(4)
                                    // ->addable(false)
                                    ->addActionLabel('Add')
                                    ->deletable(false)
                                    ->reorderable(false)
                            ])
                            ->compact()
                    ])
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false)
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach($data['rates'] as $rate) {
            $label = $rate['label'];
            foreach($rate['items'] as $item) {
                if (array_key_exists('id', $item)) {
                    $rateModel = RateModel::find($item['id']);
                    $rateModel->update([
                        'name' => $item['name'],
                        'consumable' => (int)$item['consumable'],
                        'validity' => (int)$item['validity'],
                        'price' => (int)$item['price'],
                    ]);
                } else {
                    $newRate = RateModel::create([
                        'type' => $label,
                        'name' => $item['name'],
                        'consumable' => $item['consumable'],
                        'validity' => $item['validity'],
                        'price' => $item['price'],
                        'status' => true
                    ]);
                }
            }
        }

        Notification::make()
            ->title('Success')
            ->body("Rates successfully upated.")
            ->success()
            ->send();
    }
}

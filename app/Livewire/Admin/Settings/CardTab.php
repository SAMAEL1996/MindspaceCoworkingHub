<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use Livewire\Component;
use Filament\Forms\Components as FormComponents;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;

class CardTab extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];
    
    public function mount(): void
    {
        $data['validate_by_card'] = Setting::getValue('validate-by-card');

        $this->form->fill($data);
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FormComponents\Toggle::make('validate_by_card')
                    ->label('Validate By Card')
            ])
            ->statePath('data');
    }
    
    public function create(): void
    {
        $data = $this->form->getState();
        
        Setting::upsertValue('validate-by-card', $data['validate_by_card']);
    }

    public function render()
    {
        return view('livewire.admin.settings.card-tab');
    }
}

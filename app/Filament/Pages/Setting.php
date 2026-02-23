<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components as InfolistComponents;
use App\Models\Setting as SettingModel;

class Setting extends Page implements HasForms, HasInfolists
{
    use InteractsWithInfolists;
    use InteractsWithForms;

    public $data;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.setting';

    public function mount()
    {
        $data = [
            'card_setting' => true
        ];
    }

    public function settingInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->state($this->data)
            ->schema([
                InfolistComponents\Tabs::make('Tabs')
                    ->tabs([
                        InfolistComponents\Tabs\Tab::make('Card')
                            ->schema([
                                InfolistComponents\Section::make()
                                    ->schema([
                                        InfolistComponents\ViewEntry::make('card_setting')
                                            ->label('')
                                            ->view('infolists.components.admin.settings.card-tab')
                                    ])
                                    ->columnSpanFull()
                            ]),
                    ])
                    ->contained(false)
                    ->columnSpanFull()
            ]);
    }
}

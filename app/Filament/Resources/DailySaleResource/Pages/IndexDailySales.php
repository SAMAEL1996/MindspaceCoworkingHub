<?php

namespace App\Filament\Resources\DailySaleResource\Pages;

use App\Filament\Resources\DailySaleResource;
use Filament\Resources\Pages\Page;
use Filament\Actions;
use Filament\Forms\Components as FormComponents;
use App\Models\Card;
use App\Models\MonthlyUser;
use App\Models\FlexiUser;
use App\Models\DailySale;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class IndexDailySales extends Page
{
    protected static string $resource = DailySaleResource::class;

    protected static string $view = 'filament.resources.daily-sale-resource.pages.index-daily-sales';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('add-monthly')
                ->label('Add Monthly')
                ->icon('heroicon-m-plus-circle')
                ->modalHeading('Add Monthly Customer')
                ->form([
                    FormComponents\Select::make('type')
                        ->options([
                            'new' => 'New Monthly User',
                            'old' => 'Existing Monthly User',
                        ])
                        ->live()
                        ->required()
                        ->native(false),
                    FormComponents\Grid::make(1)
                        ->schema([
                            FormComponents\Select::make('card_id')
                                ->options(function() {
                                    $options = [];
                                    $monthlyIds = \App\Models\MonthlyUser::where('is_expired', false)->pluck('card_id')->toArray();
                                    $availabelGuests = \App\Models\Card::whereNotIn('id', $monthlyIds)->where('type', 'Monthly')->get();
                                    foreach($availabelGuests as $guest) {
                                        $options[$guest->id] = $guest->code;
                                    }
        
                                    return $options;
                                })
                                ->preload()
                                ->searchable('code')
                                ->required()
                                ->native(false),
                            FormComponents\TextInput::make('name')
                                ->required(),
                            FormComponents\TextInput::make('contact_no'),
                            FormComponents\TextInput::make('facebook'),
                            FormComponents\Textarea::make('social_media')
                                ->label('Other Social Media')
                                ->rows(5)
                                ->helperText('This field is optional.'),
                            FormComponents\TextInput::make('amount')
                                ->label('Amount Paid')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(3500)
                                ->required()
                                ->helperText('Monthly Pass Rate: PHP 3,500.00')
                        ])
                        ->visible(function($get) {
                            return $get('type') == 'new' ? true : false;
                        }),
                    FormComponents\Select::make('monthly_user_id')
                        ->label('Monthly User')
                        ->options(function() {
                            $options = [];
                            $monthlyUsers = MonthlyUser::with('card')->where('is_active', false)->where('is_expired', false)->get();
                            foreach($monthlyUsers as $monthly) {
                                $options[$monthly->id] = $monthly->name . ' (' . $monthly->card->code . ')';
                            }

                            return $options;
                        })
                        ->preload()
                        ->searchable('name')
                        ->required()
                        ->native(false)
                        ->visible(function($get) {
                            return $get('type') == 'old' ? true : false;
                        }),
                ])
                ->visible(function() {
                    return auth()->user()->can('create daily-sales');
                })
                ->action(function($data) {
                    if($data['type'] == 'new') {
                        $data['date_start'] = \Carbon\Carbon::now();
                        $data['date_finish'] = \Carbon\Carbon::now()->addMonth()->subDay();
                        $data['is_active'] = false;
                        $data['is_expired'] = false;
                        $data['paid'] = $data['amount'] >= 3500 ? true : false;
    
                        $monthly = \App\Models\MonthlyUser::create($data);
                    } else {
                        $monthly = MonthlyUser::find($data['monthly_user_id']);
                    }

                    $card_id = $monthly->card_id;

                    $saleData = [
                        'date' => \Carbon\Carbon::now()->toDateString(),
                        'time_in_staff_id' => auth()->user()->staff ? auth()->user()->staff->id : null,
                        'card_id' => $card_id,
                        'name' => $monthly->name,
                        'description' => 'Monthly',
                        'default_amount' => 0,
                        'apply_discount' => true,
                        'discount' => 100,
                        'time_in' => \Carbon\Carbon::now(),
                        'status' => true,
                        'is_monthly' => true
                    ];
        
                    $dailySale = \App\Models\DailySale::create($saleData);

                    $monthly->is_active = true;
                    $monthly->save();

                    return $dailySale;
                }),
            Actions\Action::make('add-flexi')
                ->label('Add Flexi')
                ->icon('heroicon-m-plus-circle')
                ->modalHeading('Add Flexi Customer')
                ->form([
                    FormComponents\Select::make('type')
                        ->options([
                            'new' => 'New Flexi User',
                            'old' => 'Existing Flexi User',
                        ])
                        ->live()
                        ->required()
                        ->native(false),
                    FormComponents\Grid::make(1)
                        ->schema([
                            FormComponents\Select::make('card_id')
                                ->options(function() {
                                    $options = [];
                                    
                                    $takenIds = \App\Models\DailySale::whereNull('time_out')->pluck('card_id')->toArray();
                                    $availabelGuests = \App\Models\Card::whereNotIn('id', $takenIds)->where('type', 'Daily')->get();
                                    foreach($availabelGuests as $guest) {
                                        $options[$guest->id] = $guest->code;
                                    }
        
                                    return $options;
                                })
                                ->preload()
                                ->searchable('code')
                                ->required()
                                ->native(false),
                            FormComponents\TextInput::make('name')
                                ->required(),
                            FormComponents\TextInput::make('contact_no'),
                            FormComponents\TextInput::make('amount')
                                ->label('Amount Paid')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(1500)
                                ->required()
                                ->default(1500)
                                ->helperText('Flexi Pass Rate: PHP 1,500.00')
                        ])
                        ->visible(function($get) {
                            return $get('type') == 'new' ? true : false;
                        }),
                    FormComponents\Grid::make(3)
                        ->schema([
                            FormComponents\Select::make('flexi_user_id')
                                ->label('Flexi User')
                                ->options(FlexiUser::where('is_active', false)->where('status', true)->pluck('name', 'id'))
                                ->preload()
                                ->searchable('name')
                                ->required()
                                ->native(false)
                                ->columnSpan('2'),
                            FormComponents\Select::make('flexi_card_id')
                                ->options(function() {
                                    $options = [];
                                    
                                    $takenIds = \App\Models\DailySale::whereNull('time_out')->pluck('card_id')->toArray();
                                    $availabelGuests = \App\Models\Card::whereNotIn('id', $takenIds)->where('type', 'Daily')->get();
                                    foreach($availabelGuests as $guest) {
                                        $options[$guest->id] = $guest->code;
                                    }
        
                                    return $options;
                                })
                                ->preload()
                                ->searchable('code')
                                ->required()
                                ->native(false)
                                ->columnSpan('1'),
                        ])
                        ->visible(function($get) {
                            return $get('type') == 'old' ? true : false;
                        })
                ])
                ->visible(function() {
                    return auth()->user()->can('create daily-sales');
                })
                ->action(function($data) {
                    if($data['type'] == 'new') {
                        $data['start_at'] = \Carbon\Carbon::now();
                        $data['end_at'] = \Carbon\Carbon::now()->addHours(50);
                        $data['is_active'] = false;
                        $data['paid'] = $data['amount'] >= 1500 ? true : false;
                        $data['status'] = true;
                        $data['remaining'] = \Carbon\Carbon::now()->diffInMinutes(\Carbon\Carbon::now()->addHours(50));
    
                        $flexi = \App\Models\FlexiUser::create($data);

                        $card_id = $data['card_id'];
                    } else {
                        $flexi = \App\Models\FlexiUser::find($data['flexi_user_id']);

                        $card_id = $data['flexi_card_id'];
                    }

                    $saleData = [
                        'date' => \Carbon\Carbon::now()->toDateString(),
                        'time_in_staff_id' => auth()->user()->staff ? auth()->user()->staff->id : null,
                        'card_id' => $card_id,
                        'name' => $flexi->name,
                        'description' => 'Flexi',
                        'default_amount' => 0,
                        'discount' => 100,
                        'time_in' => \Carbon\Carbon::now()->addMinutes(5),
                        'status' => true,
                        'is_flexi' => true
                    ];
        
                    $dailySale = \App\Models\DailySale::create($saleData);

                    $flexi->is_active = true;
                    $flexi->card_id = $card_id;
                    $flexi->save();

                    return $dailySale;
                }),
            Actions\CreateAction::make()
                ->label('Add Daily')
                ->icon('heroicon-m-plus-circle')
                ->visible(function() {
                    return auth()->user()->can('create daily-sales');
                }),
        ];
    }
}

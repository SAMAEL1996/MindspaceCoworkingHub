<?php

namespace App\Filament\Resources\DailySaleResource\Pages;

use App\Filament\Resources\DailySaleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components as FormComponents;
use App\Models\Card;
use App\Models\MonthlyUser;
use App\Models\FlexiUser;
use App\Models\DailySale;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListDailySales extends ListRecords
{
    protected static string $resource = DailySaleResource::class;

    protected static ?string $title = 'Daily Users';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('add-daily')
                ->label('Add Daily')
                ->icon('heroicon-m-plus-circle')
                ->modalHeading('Add Daily Pass')
                ->form([
                    FormComponents\Grid::make(1)
                        ->schema([
                            FormComponents\DatePicker::make('date')
                                ->default(\Carbon\Carbon::now())
                                ->disabled()
                                ->dehydrated(),
                            FormComponents\Grid::make(3)
                                ->schema([
                                    FormComponents\TextInput::make('time_in_staff_id')
                                        ->label('Staff ID')
                                        ->default(function() {
                                            return auth()->user()->staff ? auth()->user()->staff->id : '';
                                        })
                                        ->live()
                                        ->disabled()
                                        ->dehydrated()
                                        ->columnSpan(2),
                                    FormComponents\TextInput::make('staff_name')
                                        ->label('Staff Name')
                                        ->default(function($get) {
                                            $staff = \App\Models\Staff::where('id', $get('time_in_staff_id'))->first();
                                            return $staff ? $staff->user->name : '';
                                        })
                                        ->disabled()
                                        ->columnSpan(1),
                                ]),
                            FormComponents\Grid::make(3)
                                ->schema([
                                    FormComponents\TextInput::make('name')
                                        ->label('Customer Name')
                                        ->columnSpan(2),
                                    FormComponents\Select::make('card_id')
                                        ->label('Card ID')
                                        ->options(function() {
                                            $options = [];
                                            $now = \Carbon\Carbon::now();
                                            $takenIds = \App\Models\DailySale::whereNull('time_out')->pluck('card_id')->toArray();
                                            $availabelGuests = \App\Models\Card::whereNotIn('id', $takenIds)->where('type', 'Daily')->get();
                                            foreach($availabelGuests as $guest) {
                                                $options[$guest->id] = $guest->code;
                                            }
                
                                            return $options;
                                        })
                                        ->preload()
                                        ->required()
                                        ->searchable()
                                        ->native(false)
                                        ->columnSpan(1),
                                    ]),
                            FormComponents\Toggle::make('apply_discount')
                                ->label('Apply Discount')
                                ->columnSpan(1)
                                ->live(),
                            FormComponents\Grid::make(3)
                                ->schema([
                                    FormComponents\TextInput::make('discount')
                                        ->numeric()
                                        ->minValue(1)
                                        ->placeholder('Discount percentage')
                                        ->visible(function($get) {
                                            return $get('apply_discount') ? true : false;
                                        })
                                        ->required(function($get) {
                                            return $get('apply_discount') ? true : false;
                                        })
                                        ->default(20)
                                        ->helperText('20% for Student Discount.')
                                        ->columnSpan(1),
                                ]),
                        ]),
                ])
                ->action(function($data) {
                    $discount = 0;
                    if($data['apply_discount']) {
                        $discount = $data['discount'];
                    }

                    $saleData = [
                        'date' => $data['date'],
                        'time_in_staff_id' => $data['time_in_staff_id'],
                        'card_id' => $data['card_id'],
                        'name' => $data['name'],
                        'description' => 'Daily',
                        'apply_discount' => $data['apply_discount'],
                        'discount' => $discount,
                        'time_in' => \Carbon\Carbon::now()->addMinutes(10),
                        'status' => true,
                        'is_monthly' => false
                    ];

                    $dailyPass = \App\Models\DailySale::create($saleData);

                    $dailyPass->addCheckInToSalesReport();

                    return $dailyPass;
                })
                ->visible(function() {
                    $user = auth()->user();
    
                    if($user->hasRole('Super Administrator')) {
                        return true;
                    }
    
                    return $user->checkLatestCashLogs();
                }),
            Actions\Action::make('add-flexi')
                ->label('Add Flexi')
                ->icon('heroicon-m-user-circle')
                ->modalHeading('Add Flexi Pass')
                ->form([
                    FormComponents\ToggleButtons::make('type')
                        ->label('')
                        ->options([
                            'new' => 'New Flexi User',
                            'old' => 'Existing Flexi User',
                        ])
                        ->live()
                        ->inline()
                        ->extraAttributes([
                            'class' => 'flex justify-center items-center space-x-4',
                        ]),
                        FormComponents\Grid::make(1)
                        ->schema([
                            FormComponents\Select::make('card_id')
                                ->label('Card ID')
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
                            FormComponents\Grid::make(2)
                                ->schema([
                                    FormComponents\TextInput::make('name')
                                        ->required(),
                                    FormComponents\TextInput::make('contact_no')
                                        ->required(),
                                ]),
                            FormComponents\Grid::make(2)
                                ->schema([
                                    FormComponents\TextInput::make('amount')
                                        ->label('Amount Paid')
                                        ->numeric()
                                        ->minValue(1)
                                        ->maxValue(1500)
                                        ->required()
                                        ->default(1500)
                                        ->helperText('Flexi Pass Rate: PHP 1,500.00'),
                                    FormComponents\Select::make('mode_of_payment')
                                        ->options([
                                            'Cash' => 'Cash',
                                            'GCash' => 'GCash',
                                            'Bank Transfer' => 'Bank Transfer'
                                        ])
                                        ->required()
                                        ->native(false)
                                ])
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
                                ->label('Card ID')
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
                ->action(function($data) {
                    if($data['type'] == 'new') {
                        $data['start_at'] = \Carbon\Carbon::now();
                        $data['end_at'] = \Carbon\Carbon::now()->addHours(50);
                        $data['is_active'] = false;
                        $data['paid'] = $data['amount'] >= 1500 ? true : false;
                        $data['status'] = true;
    
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
                        'time_in' => \Carbon\Carbon::now(),
                        'amount_paid' => $data['type'] == 'new' ? $data['amount'] : 0.00,
                        'status' => true,
                        'is_flexi' => true,
                        'mode_of_payment' => $data['type'] == 'new' ? $data['mode_of_payment'] : 'Cash',
                    ];
        
                    $dailySale = \App\Models\DailySale::create($saleData);

                    $flexi->is_active = true;
                    $flexi->card_id = $card_id;
                    $flexi->save();

                    return $dailySale;
                })
                ->visible(function() {
                    $user = auth()->user();
    
                    if($user->hasRole('Super Administrator')) {
                        return true;
                    }
    
                    return $user->checkLatestCashLogs();
                }),
            Actions\Action::make('add-monthly')
                ->label('Add Monthly')
                ->icon('heroicon-m-users')
                ->modalHeading('Add Monthly Pass')
                ->form([
                    FormComponents\ToggleButtons::make('type')
                        ->label('')
                        ->options([
                            'new' => 'New Monthly User',
                            'old' => 'Existing Monthly User',
                        ])
                        ->live()
                        ->inline()
                        ->extraAttributes([
                            'class' => 'flex justify-center items-center space-x-4',
                        ]),
                        FormComponents\Grid::make(1)
                        ->schema([
                            FormComponents\Select::make('card_id')
                                ->label('Card ID')
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
                            FormComponents\Grid::make(2)
                                ->schema([
                                    FormComponents\TextInput::make('name')
                                        ->required(),
                                    FormComponents\TextInput::make('contact_no')
                                        ->required(),
                                ]),
                            FormComponents\Grid::make(2)
                                ->schema([
                                    FormComponents\TextInput::make('amount')
                                        ->label('Amount Paid')
                                        ->numeric()
                                        ->minValue(1)
                                        ->maxValue(3500)
                                        ->default(3500)
                                        ->required()
                                        ->helperText('Monthly Pass Rate: PHP 3,500.00'),
                                    FormComponents\Select::make('mode_of_payment')
                                        ->options([
                                            'Cash' => 'Cash',
                                            'GCash' => 'GCash',
                                            'Bank Transfer' => 'Bank Transfer'
                                        ])
                                        ->required()
                                        ->native(false)
                                ])
                        ])
                        ->visible(function($get) {
                            return $get('type') == 'new' ? true : false;
                        }),
                    FormComponents\Grid::make(3)
                        ->schema([
                            FormComponents\Select::make('monthly_user_id')
                                ->label('Monthly User')
                                ->options(function() {
                                    $options = [];
                                    $monthlyUsers = MonthlyUser::with('card')->where('is_active', false)->where('is_expired', false)->get();
                                    foreach($monthlyUsers as $monthly) {
                                        $options[$monthly->id] = $monthly->name;
                                    }

                                    return $options;
                                })
                                ->preload()
                                ->searchable('name')
                                ->required()
                                ->native(false)
                                ->live()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    // Get the selected user ID
                                    $monthlyUser = MonthlyUser::find($state);

                                    $card = $monthlyUser->card->code;

                                    $set('monthly_user_card_id', $card);
                                })
                                ->columnSpan('2'),
                            FormComponents\TextInput::make('monthly_user_card_id')
                                ->label('Card ID')
                                ->disabled()
                                ->default(function($get) {
                                    return $get('monthly_user_id');
                                })
                                ->visible(function($get) {
                                    return $get('monthly_user_id');
                                })
                        ])
                        ->visible(function($get) {
                            return $get('type') == 'old' ? true : false;
                        }),
                ])
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
                        'amount_paid' => $data['type'] == 'new' ? $data['amount'] : 0.00,
                        'time_in' => \Carbon\Carbon::now(),
                        'status' => true,
                        'is_monthly' => true,
                        'mode_of_payment' => $data['type'] == 'new' ? $data['mode_of_payment'] : 'Cash',
                    ];
        
                    $dailySale = \App\Models\DailySale::create($saleData);

                    $monthly->is_active = true;
                    $monthly->save();

                    return $dailySale;
                })
                ->visible(function() {
                    $user = auth()->user();
    
                    if($user->hasRole('Super Administrator')) {
                        return true;
                    }
    
                    return $user->checkLatestCashLogs();
                })
        ];
    }

    public function getTabs(): array
    {
        return [
            'ongoing' => Tab::make('On-going')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', true))
                ->badge(DailySale::query()->where('status', true)->count()),
            'finished' => Tab::make('Finished')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', false)->where('time_out', '>=', \Carbon\Carbon::now()->subDays(2))),
            'all' => Tab::make(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            
        ];
    }
}

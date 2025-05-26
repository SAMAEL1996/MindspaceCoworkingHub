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
use App\Models\Rate;
use Carbon\Carbon;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class ListDailySales extends ListRecords
{
    protected static string $resource = DailySaleResource::class;

    protected static ?string $title = 'Daily Users';

    public $guestName;
    public $checkInModel;
    public $newMember = false;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('search')
                ->label('Add Guest')
                ->color('info')
                ->icon('heroicon-m-plus-circle')
                ->modalHeading('Guest Check-In')
                ->form([
                    FormComponents\Wizard::make([
                        FormComponents\Wizard\Step::make('Search')
                            ->schema([
                                FormComponents\TextInput::make('search_user')
                                    ->label('Search for Name')
                                    ->live(debounce: 300)
                                    // ->autocomplete(false)
                                    ->required()
                                    ->datalist(fn () => DailySale::query()->select('name')->distinct()->pluck('name')->toArray())
                                    ->extraAttributes([
                                        'x-data' => '{}',
                                        'x-on:input' => "event.target.value = event.target.value.replace(/\b\w/g, c => c.toUpperCase())",
                                    ]),
                                FormComponents\Actions::make([
                                        FormComponents\Actions\Action::make('search')
                                            ->action(function($set, $get) {
                                                $searchUser = $get('search_user');

                                                $set('selected_option', []);
                                                $set('search_result_options', []);

                                                if(!$searchUser) {
                                                    return;
                                                }

                                                $formattedState = ucwords(strtolower($searchUser));
                                                $set('search_user', $formattedState);
                                                $set('guest_name', $formattedState);

                                                $flexiResults = FlexiUser::select('id', 'name')
                                                    ->where('name', 'like', "%{$searchUser}%")
                                                    ->where('status', true)
                                                    ->where('is_active', false)
                                                    ->get()
                                                    ->mapWithKeys(fn ($user) => ['flexi-'.$user->id => ['label' => $user->name, 'description' => 'Flexi User']])
                                                    ->toArray();

                                                $monthlyResults = MonthlyUser::select('id', 'name')
                                                    ->where('name', 'like', "%{$searchUser}%")
                                                    ->where('is_expired', false)
                                                    ->where('is_active', false)
                                                    ->get()
                                                    ->mapWithKeys(fn ($user) => ['monthly-'.$user->id => ['label' => $user->name, 'description' => 'Monthly User']])
                                                    ->toArray();

                                                $results = array_merge($flexiResults, $monthlyResults);
                                                $results['new_daily'] = ['label' => 'Add as New Daily', 'description' => null];
                                                $results['new_flexi'] = ['label' => 'Add as New Flexi', 'description' => null];
                                                $results['new_monthly'] = ['label' => 'Add as New Monthly', 'description' => null];

                                                if (count($results)) {
                                                    $set('search_result_options', $results);
                                                }

                                                $this->checkInModel = null;
                                                $set('card_id', null);
                                            })
                                    ]),
                                FormComponents\Radio::make('selected_option')
                                    ->label('Select Guest')
                                    ->required()
                                    ->options(fn ($get) => collect($get('search_result_options'))->mapWithKeys(fn ($option, $key) => [$key => $option['label']]))
                                    ->descriptions(fn ($get) => collect($get('search_result_options'))->mapWithKeys(fn ($option, $key) => [$key => $option['description'] ?? null]))
                                    ->visible(fn ($get) => count($get('search_result_options') ?? []) > 0)
                                    ->reactive()
                                    ->live(debounce: 300)
                                    ->afterStateUpdated(function($state, $set, $get) {
                                        $this->checkInModel = null;
                                        $set('card_id', null);

                                        if(!in_array($state, ['new_daily', 'new_flexi', 'new_monthly'])) {
                                            $arr = explode('-', $state);
                                            $checkInType = $arr[0];
                                            $checkInId = $arr[1];

                                            switch($checkInType) {
                                                case 'monthly':
                                                    $this->checkInModel = MonthlyUser::find($checkInId);
                                                    $set('card_id', $this->checkInModel->card_id);
                                                    break;

                                                case 'flexi':
                                                    $this->checkInModel = FlexiUser::find($checkInId);
                                                    break;

                                                default:
                                            }
                                        }

                                        if(in_array($state, ['new_flexi', 'new_monthly'])) {
                                            $this->newMember = true;
                                        } else {
                                            $this->newMember = false;
                                        }
                                    })
                            ]),
                        FormComponents\Wizard\Step::make('Detail')
                            ->schema([
                                FormComponents\TextInput::make('guest_name')
                                    ->label('Guest Name')
                                    ->disabled()
                                    ->dehydrated(),
                                FormComponents\Select::make('rate_id')
                                    ->label('Type')
                                    ->options(function($get) {
                                        if($get('selected_option') == 'new_flexi') {
                                            return \App\Models\Rate::where('type', 'Flexi')->get()->pluck('name', 'id');
                                        } else {
                                            return \App\Models\Rate::where('type', 'Monthly')->get()->pluck('name', 'id');
                                        }
                                    })
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function($state, $set) {
                                        $rate = \App\Models\Rate::find($state);
                                        $set('amount', $rate->price);

                                        $helperText = $rate->name . ' Rate: PHP ' . number_format($rate->price, 2);
                                        $set('amount_helper_text', $helperText);
                                    })
                                    ->required()
                                    ->native(false),
                                FormComponents\TextInput::make('contact_no')
                                    ->required(),
                                FormComponents\Select::make('mode_of_payment')
                                    ->options([
                                        'Cash' => 'Cash',
                                        'GCash' => 'GCash',
                                        'Bank Transfer' => 'Bank Transfer'
                                    ])
                                    ->required()
                                    ->native(false),
                                FormComponents\TextInput::make('amount')
                                    ->label('Amount Paid')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required()
                                    ->helperText(function($get) {
                                        return $get('amount_helper_text') ?? '';
                                    }),
                            ])
                            ->columns(2)
                            ->visible(fn () => $this->newMember),
                        FormComponents\Wizard\Step::make('Check-In')
                            ->schema([
                                FormComponents\Grid::make(2)
                                    ->schema([
                                        FormComponents\DatePicker::make('date')
                                            ->default(\Carbon\Carbon::now())
                                            ->native(false)
                                            ->displayFormat('F d, Y')
                                            ->disabled()
                                            ->dehydrated(),
                                        FormComponents\TimePicker::make('time')
                                            ->default(\Carbon\Carbon::now()->addMinutes(10))
                                            ->native(false)
                                            ->displayFormat('h:i A')
                                            ->seconds(false)
                                            ->disabled()
                                            ->dehydrated()
                                    ]),
                                FormComponents\Grid::make(2)
                                    ->schema([
                                        FormComponents\TextInput::make('time_in_staff_id')
                                            ->label('Staff ID')
                                            ->default(fn () => auth()->user()->staff ? auth()->user()->staff->id : auth()->user()->id)
                                            ->dehydrated()
                                            ->hidden(),
                                        FormComponents\TextInput::make('staff_name')
                                            ->label('Staff')
                                            ->default(function($get) {
                                                $staff = \App\Models\Staff::where('id', $get('time_in_staff_id'))->first();
                                                return $staff ? $staff->user->name : '';
                                            })
                                            ->disabled(),
                                        FormComponents\Select::make('card_id')
                                            ->label('Card ID')
                                            ->native(false)
                                            ->placeholder('Select Card ID')
                                            ->options(function($get) {
                                                $options = [];
                                                $now = \Carbon\Carbon::now();

                                                if($this->checkInModel && get_class($this->checkInModel) == 'App\Models\MonthlyUser') {
                                                    $availabelCard = Card::where('type', 'Monthly')->get();
                                                    foreach($availabelCard as $card) {
                                                        $options[$card->id] = $card->code;
                                                    }
                                                } elseif($get('selected_option') == 'new_monthly') {
                                                    $monthlyIds = MonthlyUser::where('is_expired', false)->pluck('card_id')->toArray();
                                                    $availabelCard = Card::whereNotIn('id', $monthlyIds)->where('type', 'Monthly')->get();
                                                    foreach($availabelCard as $card) {
                                                        $options[$card->id] = $card->code;
                                                    }
                                                } else {
                                                    $takenIds = DailySale::whereNull('time_out')->pluck('card_id')->toArray();
                                                    $availabelCard = Card::whereNotIn('id', $takenIds)->where('type', 'Daily')->get();
                                                    foreach($availabelCard as $card) {
                                                        $options[$card->id] = $card->code;
                                                    }
                                                }

                                                return $options;
                                            })
                                            ->disabled(function($get) {
                                                if($this->checkInModel) {
                                                    return get_class($this->checkInModel) == 'App\Models\MonthlyUser' ? true : false;
                                                }

                                                // if(in_array($get('selected_option'), ['new_flexi', 'new_monthly'])) {
                                                //     return 
                                                // }
                                                return false;
                                            })
                                            ->dehydrated(),
                                            // ->disabled(fn (): bool => $this->checkInModel && get_class($this->checkInModel) == 'App\Models\MonthlyUser' ? true : false )
                                            // ->dehydrated(fn (): bool => $this->checkInModel && get_class($this->checkInModel) == 'App\Models\MonthlyUser' ? true : false ),
                                        FormComponents\Grid::make(2)
                                            ->schema([
                                                FormComponents\Toggle::make('apply_discount')
                                                    ->label('Apply Discount')
                                                    ->columnSpan(2)
                                                    ->live(),
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
                                                    ->columnSpan(1)
                                            ])
                                            ->visible(fn ($get): bool => $this->checkInModel || in_array($get('selected_option'), ['new_flexi', 'new_monthly']) ? false : true)
                                    ]),
                            ])
                        ])
                        ->submitAction(new HtmlString(Blade::render(<<<BLADE
                            <x-filament::button
                                type="submit"
                                size="sm"
                            >
                                Submit
                            </x-filament::button>
                        BLADE)))
                ])
                ->modalSubmitAction(false)
                ->action(function($data) {
                    $time_in_staff_id = auth()->user()->staff ? auth()->user()->staff->id : auth()->user()->id;
                    $name = null;
                    if($this->checkInModel) {
                        $name = $this->checkInModel->name;
                    } elseif($this->newMember) {
                        $name = $data['guest_name'];
                    } else {
                        $name = $data['search_user'];
                    }

                    $cardId = $data['card_id'];

                    $description = 'Daily';
                    $timeIn = Carbon::parse($data['date'] . ' ' . $data['time']);

                    $applyDiscount = false;
                    $discount = 0;
                    if($data['apply_discount']) {
                        $applyDiscount = true;
                        $discount = $data['discount'];
                    }

                    $saleData = [
                        'date' => $data['date'],
                        'time_in_staff_id' => $time_in_staff_id,
                        'card_id' => $data['card_id'],
                        'name' => $name,
                        'description' => 'Daily',
                        'apply_discount' => $applyDiscount,
                        'discount' => $discount,
                        'time_in' => Carbon::now()->addMinutes(10),
                        'status' => true,
                        'is_flexi' => false,
                        'is_monthly' => false
                    ];

                    if($this->checkInModel) {
                        $this->checkInModel->is_active = true;
                        if(get_class($this->checkInModel) == 'App\Models\FlexiUser') {
                            $this->checkInModel->card_id = $data['card_id'];

                            $saleData['is_flexi'] = true;
                        } else {
                            $saleData['is_monthly'] = true;
                        }
                        $this->checkInModel->save();

                        $saleData['discount'] = 100;
                        $saleData['apply_discount'] = true;
                    } else {
                        if(in_array($data['selected_option'], ['new_flexi', 'new_monthly'])) {
                            $rate = Rate::find($data['rate_id']);
                            // FOR NEW FLEXI
                            if($data['selected_option'] == 'new_flexi') {
                                $model = FlexiUser::create([
                                    'rate_id' => $rate->id,
                                    'card_id' => $data['card_id'],
                                    'name' => $name,
                                    'contact_no' => $data['contact_no'],
                                    'start_at' => Carbon::now(),
                                    'end_at' => Carbon::now()->addHours($rate->consumable),
                                    'expired_at' => Carbon::now()->addDays($rate->validity),
                                    'is_active' => true,
                                    'status' => true,
                                    'paid' => true,
                                    'amount' => $data['amount'],
                                    'mode_of_payment' => $data['mode_of_payment']
                                ]);
                                $saleData['is_flexi'] = true;
                            } else {
                                // FOR NEW MONTHLY
                                $model = MonthlyUser::create([
                                    'rate_id' => $rate->id,
                                    'card_id' => $data['card_id'],
                                    'name' => $name,
                                    'contact_no' => $data['contact_no'],
                                    'date_start' => Carbon::now(),
                                    'date_finish' => Carbon::now()->addDays($rate->validity),
                                    'is_active' => true,
                                    'is_expired' => false,
                                    'paid' => true,
                                    'amount' => $data['amount'],
                                    'mode_of_payment' => $data['mode_of_payment']
                                ]);
                                $saleData['is_monthly'] = true;
                            }

                            $saleData['amount_paid'] = $data['amount'];
                            $saleData['mode_of_payment'] = $data['mode_of_payment'];
                            $saleData['discount'] = 100;
                            $saleData['apply_discount'] = true;
                        }
                    }

                    $dailyPass = \App\Models\DailySale::create($saleData);

                    // $dailyPass->applyNightOwlDiscount();

                    // $dailyPass->addCheckInToSalesReport();

                    Notification::make()
                        ->title('Success')
                        ->body("Guest successfully added.")
                        ->success()
                        ->send();

                    $this->guestName = null;
                    $this->checkInModel = null;
                    $this->newMember = false;

                    return $dailyPass;
                }),





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

                    // $dailyPass->applyNightOwlDiscount();

                    $dailyPass->addCheckInToSalesReport();

                    Notification::make()
                        ->title('Success')
                        ->body("Guest successfully added.")
                        ->success()
                        ->send();

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
                    FormComponents\Grid::make(2)
                        ->schema([
                            FormComponents\Select::make('rate_id')
                                ->label('Type')
                                ->options(\App\Models\Rate::where('type', 'Flexi')->get()->pluck('name', 'id'))
                                ->preload()
                                ->live()
                                ->afterStateUpdated(function($state, $set) {
                                    $rate = \App\Models\Rate::find($state);
                                    $set('amount', $rate->price);

                                    $helperText = 'Flexi Pass Rate: PHP ' . number_format($rate->price, 2);
                                    $set('amount_helper_text', $helperText);
                                })
                                ->required()
                                ->native(false),
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
                            FormComponents\TextInput::make('name')
                                ->required(),
                            FormComponents\TextInput::make('contact_no')
                                ->required(),
                            FormComponents\TextInput::make('amount')
                                ->label('Amount Paid')
                                ->numeric()
                                ->minValue(1)
                                ->required()
                                ->helperText(function($get) {
                                    return $get('amount_helper_text') ?? '';
                                }),
                            FormComponents\Select::make('mode_of_payment')
                                ->options([
                                    'Cash' => 'Cash',
                                    'GCash' => 'GCash',
                                    'Bank Transfer' => 'Bank Transfer'
                                ])
                                ->required()
                                ->native(false)
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
                        $rate = \App\Models\Rate::find($data['rate_id']);

                        $data['expired_at'] = \Carbon\Carbon::now()->addDays($rate->validity);
                        $data['start_at'] = \Carbon\Carbon::now();
                        $data['end_at'] = \Carbon\Carbon::now()->addHours($rate->consumable);
                        $data['is_active'] = false;
                        $data['paid'] = $data['amount'] >= 1500 ? true : false;
                        $data['status'] = true;
    
                        $flexi = \App\Models\FlexiUser::create($data);

                        $flexi->sendWelcomeMessage();

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

                    Notification::make()
                        ->title('Success')
                        ->body("Guest successfully added.")
                        ->success()
                        ->send();

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
                    FormComponents\Grid::make(2)
                        ->schema([
                            FormComponents\Select::make('rate_id')
                                ->label('Type')
                                ->options(\App\Models\Rate::where('type', 'Monthly')->get()->pluck('name', 'id'))
                                ->preload()
                                ->live()
                                ->afterStateUpdated(function($state, $set) {
                                    $rate = \App\Models\Rate::find($state);
                                    $set('amount', $rate->price);

                                    $helperText = 'Monthly Pass Rate: PHP ' . number_format($rate->price, 2);
                                    $set('amount_helper_text', $helperText);
                                })
                                ->required()
                                ->native(false),
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
                            FormComponents\TextInput::make('name')
                                ->required(),
                            FormComponents\TextInput::make('contact_no')
                                ->required(),
                            FormComponents\TextInput::make('amount')
                                ->label('Amount Paid')
                                ->numeric()
                                ->minValue(1)
                                ->required()
                                ->helperText(function($get) {
                                    return $get('amount_helper_text') ?? '';
                                }),
                            FormComponents\Select::make('mode_of_payment')
                                ->options([
                                    'Cash' => 'Cash',
                                    'GCash' => 'GCash',
                                    'Bank Transfer' => 'Bank Transfer'
                                ])
                                ->required()
                                ->native(false)
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
                        $rate = \App\Models\Rate::find($data['rate_id']);

                        $data['date_start'] = \Carbon\Carbon::now();
                        $data['date_finish'] = \Carbon\Carbon::now()->addMonth()->subDay();
                        $data['is_active'] = false;
                        $data['is_expired'] = false;
                        $data['paid'] = $data['amount'] >= 3500 ? true : false;
    
                        $monthly = \App\Models\MonthlyUser::create($data);

                        $monthly->sendWelcomeMessage();
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

                    Notification::make()
                        ->title('Success')
                        ->body("Guest successfully added.")
                        ->success()
                        ->send();

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

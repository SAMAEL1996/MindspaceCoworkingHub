<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DailySaleResource\Pages;
use App\Filament\Resources\DailySaleResource\RelationManagers;
use App\Models\DailySale;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns as TableColumns;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components as InfolistComponents;
use Filament\Tables\Grouping\Group;
use Filament\Forms\Components as FormComponents;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Filters;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions as TableActions;

class DailySaleResource extends Resource
{
    protected static ?string $model = DailySale::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Daily Users';
    protected static ?string $navigationGroup = 'SALES';

    public static function getEloquentQuery(): Builder
    {
        return DailySale::query()->orderBy('created_at', 'desc');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(DailySale::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup(
                Group::make('date')
                    ->date()
                    ->collapsible()
                    ->titlePrefixedWithLabel(false)
                    ->orderQueryUsing(fn (Builder $query, string $direction) => $query->orderBy('date', 'DESC'))
            )
            ->columns([
                TableColumns\TextColumn::make('card.code')
                    ->label('Guest')
                    ->formatStateUsing(function($state, $record) {
                        return $record->card->code;
                    })
                    ->description(function($state, $record) {
                        return $record->name;
                    })
                    ->searchable(['code', 'name']),
                TableColumns\TextColumn::make('is_monthly')
                    ->label('Type')
                    ->formatStateUsing(function($record) {
                        if($record->is_flexi) {
                            return 'Flexi';
                        }

                        if($record->is_monthly) {
                            return 'Monthly';
                        }
                        
                        return 'Daily';
                    }),
                TableColumns\TextColumn::make('time_in')
                    ->label('Time In')
                    ->formatStateUsing(function($state, $record) {
                        return $record->time_in_carbon->format(config('app.time_format'));
                    })
                    ->description(function($state, $record) {
                        return $record->time_in_carbon->format(config('app.date_format'));
                    }),
                TableColumns\TextColumn::make('time_in_staff_id')
                    ->label('Staff In')
                    ->formatStateUsing(function($state, $record) {
                        return $record->staffIn->user->name;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumns\TextColumn::make('time_out')
                    ->label('Time Out')
                    ->formatStateUsing(function($state, $record) {
                        return $record->time_out_carbon->format(config('app.time_format'));
                    })
                    ->description(function($state, $record) {
                        return $record->time_out_carbon->format(config('app.date_format'));
                    })
                    ->extraAttributes(function($record) {
                        if($record->time_out) {
                            return ['class' => 'block'];
                        } else {
                            return ['class' => 'hidden'];
                        }
                    }),
                TableColumns\TextColumn::make('time_out_staff_id')
                    ->label('Staff Out')
                    ->formatStateUsing(function($state, $record) {
                        return $record->staffOut->user->name;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumns\TextColumn::make('created_at')
                    ->label('Total Hours')
                    ->formatStateUsing(function($state, $record) {
                        if($record->total_time) {
                            return $record->total_time . ' Hour/s';
                        } else {
                            return \Carbon\Carbon::parse($record->time_in)->diffForHumans();
                        }
                    }),
                TableColumns\TextColumn::make('discount')
                    ->label('Discount')
                    ->badge()
                    ->color(function($state, $record) {
                        return $state == 0 ? 'gray' : 'success';
                    })
                    ->formatStateUsing(function($state, $record) {
                        return $state == 0 ? 'No' : $state . '%';
                    }),
                TableColumns\TextColumn::make('mode_of_payment')
                    ->label('M.O.P.'),
                TableColumns\TextColumn::make('amount_paid')
                    ->label('Amount')
                    ->money('PHP'),
            ])
            ->filters([
                Filters\Filter::make('created_at')
                    ->form([
                        Fieldset::make('Check In')
                            ->schema([
                                DatePicker::make('from'),
                                DatePicker::make('to'),
                            ])
                            ->columns(1),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('time_in', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('time_in', '<=', $date),
                            );
                    }),
                Filters\SelectFilter::make('mode_of_payment')
                    ->options([
                        'Cash' => 'Cash',
                        'GCash' => 'GCash',
                        'Bank Transfer' => 'Bank Transfer'
                    ])
            ], layout: FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn (TableActions\Action $action) => $action
                    ->button()
                    ->label('Filter'),
            )
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('End Time')
                        ->form([
                            FormComponents\Grid::make(1)
                                ->schema([
                                    FormComponents\Grid::make(2)
                                        ->schema([
                                            FormComponents\TextInput::make('amount_to_paid')
                                                ->label('Amount to Paid')
                                                ->formatStateUsing(function($record, $get) {
                                                    $computeShowTime = $record->computeShowTime();
                                                    return $computeShowTime['amount_to_paid'];
                                                })
                                                ->disabled()
                                                ->dehydrated()
                                                ->columnSpan(1),
                                            FormComponents\TextInput::make('total_hours_accumulated')
                                                ->label('Total Hours')
                                                ->formatStateUsing(function($record) {
                                                    $computeShowTime = $record->computeShowTime();
                                                    return $computeShowTime['total_hours_accumulated'];
                                                })
                                                ->disabled()
                                                ->dehydrated()
                                                ->columnSpan(1),
                                        ]),
                                    FormComponents\Grid::make(1)
                                        ->schema([
                                            FormComponents\Toggle::make('apply_discount')
                                                ->label('Apply Discount')
                                                ->live()
                                                ->default(function($record) {
                                                    return $record->apply_discount == 1 ? true : false;
                                                })
                                                ->afterStateUpdated(function ($set, $state, $record, $get) {
                                                    $computeShowTime = $record->computeShowTime();
                                                    $amount = $computeShowTime['amount_to_paid'];

                                                    if($state) {
                                                        $computeShowTime = $record->computeShowTime(true);
                                                        $percent = $get('discount') / 100;
                                                        $discount = (double)$computeShowTime['amount_to_paid'] * $percent;
            
                                                        $amount = (double)$computeShowTime['amount_to_paid'] - (double)$discount;
                                                    } else {
                                                        $computeShowTime = $record->computeShowTime(true);
                                                        $amount = $computeShowTime['amount_to_paid'];
                                                    }

                                                    $set('amount_to_paid', $amount);
                                                }),
                                            FormComponents\TextInput::make('discount')
                                                ->numeric()
                                                ->minValue(1)
                                                ->live()
                                                ->placeholder('Discount percentage')
                                                ->visible(function($get) {
                                                    return $get('apply_discount') ? true : false;
                                                })
                                                ->required(function($get) {
                                                    return $get('apply_discount') ? true : false;
                                                })
                                                ->default(function($record) {
                                                    return $record->discount == 0 ? 20 : $record->discount;
                                                })
                                                ->afterStateUpdated(function ($set, $state, $record, $get) {
                                                    $computeShowTime = $record->computeShowTime();
                                                    $amount = $computeShowTime['amount_to_paid'];
                                                    
                                                    if($state != null) {
                                                        $percent = $get('discount') / 100;
                                                        $discount = (double)$computeShowTime['amount_to_paid'] * $percent;
            
                                                        $amount = (double)$computeShowTime['amount_to_paid'] - (double)$discount;
                                                    }

                                                    $set('amount_to_paid', $amount);
                                                })
                                                ->helperText('20% for Student Discount.'),
                                        ])
                                        ->visible(function($record) {
                                            if($record->is_flexi || $record->is_monthly) {
                                                return false;
                                            }

                                            return true;
                                        }),
                                    FormComponents\Select::make('mode_of_payment')
                                        ->label('Mode of Payment')
                                        ->options([
                                            'Cash' => 'Cash',
                                            'GCash' => 'GCash',
                                            'Bank Transfer' => 'Bank Transfer'
                                        ])
                                        ->required()
                                        ->native(false)
                                        ->default(function($state, $record, $get) {
                                            if($get('renew_flexi')) {
                                                return '';
                                            }

                                            return $record->is_flexi || $record->is_monthly ? $record->mode_of_payment : '';
                                        })
                                        ->disabled(function($state, $record, $get) {
                                            return $record->is_flexi || $record->is_monthly ? true : false;
                                        }),
                                ])
                                ->visible(function($get) {
                                    return $get('renew_flexi') ? false : true;
                                }),
                            FormComponents\Grid::make(1)
                                ->schema([
                                    FormComponents\TextInput::make('flexi_amount_to_paid')
                                        ->label('Amount to Paid')
                                        ->default(1500)
                                        ->disabled()
                                        ->dehydrated(),
                                    FormComponents\Select::make('flexi_mode_of_payment')
                                        ->label('Mode of Payment')
                                        ->options([
                                            'Cash' => 'Cash',
                                            'GCash' => 'GCash',
                                            'Bank Transfer' => 'Bank Transfer'
                                        ])
                                        ->required()
                                        ->native(false),
                                    FormComponents\Hidden::make('amount_to_paid')
                                        ->formatStateUsing(function($record, $get) {
                                            $computeShowTime = $record->computeShowTime();
                                            return $computeShowTime['amount_to_paid'];
                                        }),
                                    FormComponents\Hidden::make('total_hours_accumulated')
                                        ->formatStateUsing(function($record) {
                                            $computeShowTime = $record->computeShowTime();
                                            return $computeShowTime['total_hours_accumulated'];
                                        }),
                                ])
                                ->visible(function($get) {
                                    return $get('renew_flexi');
                                }),
                            FormComponents\Fieldset::make('Additional Charge')
                                ->schema([
                                    FormComponents\TextInput::make('additional_charge')
                                        ->default(function($state, $record) {
                                            if(!$record->is_flexi) {
                                                return 0.00;
                                            }
                                            
                                            $flexi = \App\Models\FlexiUser::where('card_id', $record->card_id)->where('is_active', true)->latest()->first();

                                            return $flexi->calculateAdditionalCharge($record->id);
                                        })
                                        ->numeric()
                                        ->disabled()
                                        ->dehydrated()
                                        ->columnSpan('full'),
                                    FormComponents\Toggle::make('flexi_apply_discount')
                                        ->label('Apply Discount')
                                        ->live()
                                        ->afterStateUpdated(function ($set, $state, $record, $get) {
                                            $flexi = \App\Models\FlexiUser::where('card_id', $record->card_id)->where('is_active', true)->latest()->first();

                                            $additionalCharge = $flexi->calculateAdditionalCharge($record->id);

                                            if($state) {
                                                $percent = $get('flexi_discount') / 100;
                                                $discount = (double)$additionalCharge * $percent;
    
                                                $amount = (double)$additionalCharge - (double)$discount;
                                            } else {
                                                $amount = $additionalCharge;
                                            }

                                            $set('additional_charge', $amount);
                                        })
                                        ->columnSpan('full'),
                                    FormComponents\TextInput::make('flexi_discount')
                                        ->label('Discount')
                                        ->numeric()
                                        ->minValue(1)
                                        ->live()
                                        ->placeholder('Discount percentage')
                                        ->visible(function($get) {
                                            return $get('flexi_apply_discount') ? true : false;
                                        })
                                        ->required(function($get) {
                                            return $get('flexi_apply_discount') ? true : false;
                                        })
                                        ->default(function($record) {
                                            return 20;
                                        })
                                        ->afterStateUpdated(function ($set, $state, $record, $get) {
                                            $flexi = \App\Models\FlexiUser::where('card_id', $record->card_id)->where('is_active', true)->latest()->first();

                                            $additionalCharge = $flexi->calculateAdditionalCharge($record->id);
                                            
                                            if($state != null) {
                                                $percent = $state / 100;
                                                $discount = (double)$additionalCharge * $percent;
    
                                                $amount = (double)$additionalCharge - (double)$discount;
                                            } else {
                                                $amount = $additionalCharge;
                                            }

                                            $set('additional_charge', $amount);
                                        })
                                        ->helperText('20% for Student Discount.')
                                        ->columnSpan('full'),
                                ])
                                ->visible(function($state, $record, $get) {
                                    if($record->is_flexi) {
                                        if($get('renew_flexi')) {
                                            return false;
                                        }

                                        $flexi = \App\Models\FlexiUser::where('card_id', $record->card_id)->where('is_active', true)->latest()->first();

                                        return $flexi->checkPassIsExpired($record->id);
                                    }

                                    return false;
                                }),
                            FormComponents\Toggle::make('renew_flexi')
                                ->label('Re-new Flexi Pass')
                                ->live()
                                ->visible(function($state, $record) {
                                    if($record->is_flexi) {
                                        $flexi = \App\Models\FlexiUser::where('card_id', $record->card_id)->where('is_active', true)->latest()->first();

                                        return $flexi->checkPassIsExpired($record->id);
                                    }

                                    return false;
                                })
                        ])
                        ->action(function($data, $record) {
                            if($record->is_flexi || $record->is_monthly) {
                                $applyDisc = true;
                                $disc = 100;
                            } else {
                                $applyDisc = $data['apply_discount'];
                                $disc = $data['apply_discount'] ? $data['discount'] : 0;
                            }
                            $record->time_out = \Carbon\Carbon::now();
                            $record->time_out_staff_id = auth()->user()->staff ? auth()->user()->staff->id : null;
                            $record->status = false;
                            $record->total_time = $data['total_hours_accumulated'];
                            $record->amount_paid = $data['amount_to_paid'];
                            $record->apply_discount = $applyDisc;
                            $record->discount = $disc;
                            if(!$record->is_flexi && !$record->is_monthly) {
                                $record->mode_of_payment = $data['mode_of_payment'];
                            }
                            $record->save();

                            if($record->is_monthly) {
                                $monthly = \App\Models\MonthlyUser::where('card_id', $record->card_id)->where('is_expired', false)->latest()->first();
                                
                                $monthly->is_active = false;
                                $monthly->save();
                            }

                            if($record->is_flexi) {
                                $flexi = \App\Models\FlexiUser::where('card_id', $record->card_id)->where('is_active', true)->latest()->first();

                                if($flexi->checkPassIsExpired($record->id)) {
                                    if($data['renew_flexi']) {
                                        // create new flexi
                                        $newFlexi = $flexi->replicate();

                                        $newFlexi->start_at = \Carbon\Carbon::now();
                                        $newFlexi->end_at = \Carbon\carbon::now()->addHours(50)->subMinutes($flexi->calculateAdditionalCharge($record->id, 'minutes'));
                                        $newFlexi->card_id = null;
                                        $newFlexi->status = true;
                                        $newFlexi->is_active = false;
                                        $newFlexi->save();
                                    } else {
                                        $newRecord = $record->replicate();

                                        $newRecord->time_in = \Carbon\Carbon::now()->subMinutes($flexi->calculateAdditionalCharge($record->id, 'minutes'));
                                        $newRecord->time_out = \Carbon\Carbon::now();
                                        $newRecord->total_time = $flexi->calculateAdditionalCharge($record->id, 'hours');
                                        $newRecord->description = 'Flexi additional charge';
                                        $newRecord->amount_paid = $data['additional_charge'];
                                        $newRecord->apply_discount = $data['flexi_apply_discount'];
                                        $newRecord->discount = $data['flexi_apply_discount'] ? $data['flexi_discount'] : 0;
                                        $newRecord->is_flexi = false;
                                        $newRecord->status = false;
                                        $newRecord->save();

                                        $newRecord->addPaymenToMonthlySalesReport();
                                    }

                                    $flexi->end_at = $flexi->start_at_carbon;
                                    $flexi->status = false;
                                    $flexi->is_active = false;
                                    $flexi->card_id = null;
                                    $flexi->save();
                                } else {
                                    $flexi->recalculateTimeRemaining($record->id);
    
                                    $flexi->checkRemainingTime();
    
                                    $flexi->is_active = false;
                                    $flexi->card_id = null;
                                    $flexi->save();
                                }

                                $flexi->remaining = $flexi->start_at_carbon->diffInMinutes($flexi->end_at_carbon);
                                $flexi->save();

                                if($flexi->start_at_carbon->diffInHours($flexi->end_at_carbon) <= 10) {
                                    $flexi->sendSmsReminder();

                                    Notification::make()
                                        ->title($flexi->name . ' total time is ' . $flexi->remaining_time . ' only.')
                                        ->warning()
                                        ->send();
                                }
                            }

                            // $record->computeAmount();

                            $staff = $record->staffIn;
                            if($staff) {
                                $staff->createReport($record->id);
                            }

                            if($record->time_in_staff_id != $record->time_out_staff_id) {
                                $staffOut = $record->staffOut;
                                if($staffOut) {
                                    $staffOut->createReport($record->id);
                                }
                            }

                            if(!$record->is_flexi && !$record->is_monthly) {
                                $record->addPaymenToMonthlySalesReport();
                            }

                            Notification::make()
                                ->title('Daily user time ends.')
                                ->success()
                                ->send();

                            return $record;
                        })
                        ->modalWidth(MaxWidth::Medium),
                    Tables\Actions\Action::make('change_pass')
                        ->label('Change Pass')
                        ->form([
                            FormComponents\Select::make('pass_type')
                                ->options([
                                    'flexi' => 'Flexi Pass',
                                    'monthly' => 'Monthly Pass',
                                ])
                                ->required()
                                ->live()
                                ->native(false),
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
                                ->required(function($get) {
                                    return $get('pass_type') == 'monthly' ? true : false;
                                })
                                ->visible(function($get) {
                                    return $get('pass_type') == 'monthly' ? true : false;
                                })
                                ->native(false),
                            FormComponents\Grid::make(2)
                                ->schema([
                                    FormComponents\TextInput::make('contact_no')
                                        ->label('Contact No.')
                                        ->required()
                                        ->columnSpan(2),
                                    FormComponents\TextInput::make('amount')
                                        ->label('Amount Paid')
                                        ->numeric()
                                        ->minValue(1)
                                        ->maxValue(3500)
                                        ->required()
                                        ->default(0)
                                        ->helperText(function($get) {
                                            return $get('pass_type') == 'flexi' ?
                                                'Flexi Pass Rate: PHP 1,500.00' :
                                                'Monthly Pass Rate: PHP 3,500.00';
                                        })
                                        ->columnSpan(1),
                                    FormComponents\Select::make('mode_of_payment')
                                        ->label('Mode of Payment')
                                        ->options([
                                            'Cash' => 'Cash',
                                            'GCash' => 'GCash',
                                            'Bank Transfer' => 'Bank Transfer'
                                        ])
                                        ->required()
                                        ->native(false)
                                        ->columnSpan(1)
                                ])
                                ->visible(function($get) {
                                    return $get('pass_type') ? true : false;
                                }),
                        ])
                        ->action(function($data, $record) {
                            $time_in = $record->time_in_carbon;

                            if($data['pass_type'] == 'flexi') {
                                $flexi = \App\Models\FlexiUser::create([
                                    'card_id' => $record->card_id,
                                    'name' => $record->name,
                                    'contact_no' => $data['contact_no'],
                                    'start_at' => $time_in->copy(),
                                    'end_at' => $time_in->copy()->addHours(50),
                                    'is_active' => true,
                                    'status' => true,
                                    'paid' => $data['amount'] >= 1500 ? true : false,
                                    'amount' => $data['amount']
                                ]);

                                $record->is_flexi = true;
                                $record->discount = 100;
                                $record->amount_paid = $flexi->amount;
                                $record->mode_of_payment = $data['mode_of_payment'];
                                $record->save();
                            }

                            if($data['pass_type'] == 'monthly') {
                                $monthly = \App\Models\MonthlyUser::create([
                                    'card_id' => $data['card_id'],
                                    'name' => $record->name,
                                    'contact_no' => $data['contact_no'],
                                    'date_start' => $time_in->copy(),
                                    'date_finish' => $time_in->copy()->addMonth()->subDay(),
                                    'is_active' => true,
                                    'is_expired' => false,
                                    'paid' => $data['amount'] >= 3500 ? true : false,
                                    'amount' => $data['amount']
                                ]);

                                $record->card_id = $data['card_id'];
                                $record->is_monthly = true;
                                $record->discount = 100;
                                $record->amount_paid = $monthly->amount;
                                $record->mode_of_payment = $data['mode_of_payment'];
                                $record->save();
                            }

                            $record->removeSalesReport();

                            Notification::make()
                                ->title('Daily user successfully changed Pass.')
                                ->success()
                                ->send();

                            return $record;
                        })
                        ->modalWidth(MaxWidth::Large)
                        ->visible(function($record) {
                            return !$record->is_flexi && !$record->is_monthly ? true : false;
                        }),
                    Tables\Actions\EditAction::make()
                        ->visible(auth()->user()->hasRole('Super Administrator'))
                ])
                ->visible(function($record) {
                    if(!$record->status) {
                        return false;
                    }

                    $user = auth()->user();
    
                    if($user->hasRole('Super Administrator')) {
                        return true;
                    }

                    return $user->checkLatestCashLogs();
                })
                ->icon('heroicon-o-ellipsis-horizontal'),
            ])
            ->toggleColumnsTriggerAction(
                fn (Tables\Actions\Action $action) => $action
                    ->button()
                    ->label('Columns'),
            )
            ->bulkActions([])
            ->defaultPaginationPageOption(25);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistComponents\Section::make('Information')
                    ->schema([
                        InfolistComponents\TextEntry::make('name')
                            ->label('Guest'),
                        InfolistComponents\TextEntry::make('card_id')
                            ->label('Card ID')
                            ->formatStateUsing(function($record) {
                                return $record->card->code;
                            }),
                        InfolistComponents\TextEntry::make('time_in_staff_id')
                            ->label('Staff In')
                            ->formatStateUsing(function($record) {
                                return $record->staffIn->user->name . ' (<em>' . $record->staffIn->card->code.'</em>)';
                            })
                            ->html(),
                        InfolistComponents\TextEntry::make('time_out_staff_id')
                            ->label('Staff Out')
                            ->formatStateUsing(function($record) {
                                return $record->staffOut->user->name . ' (<em>' . $record->staffOut->card->code.'</em>)';
                            })
                            ->html(),
                        InfolistComponents\TextEntry::make('time_in')
                            ->formatStateUsing(function($state, $record) {
                                return $record->time_in_carbon->format(config('app.date_time_format'));
                            }),
                        InfolistComponents\TextEntry::make('time_out')
                            ->formatStateUsing(function($state, $record) {
                                return $record->time_out_carbon->format(config('app.date_time_format'));
                            }),
                        InfolistComponents\TextEntry::make('amount_paid')
                            ->money('PHP'),
                        InfolistComponents\TextEntry::make('total_time')
                            ->formatStateUsing(function($state, $record) {
                                return $state . ' hour/s';
                            }),
                        InfolistComponents\TextEntry::make('discount')
                            ->formatStateUsing(function($state, $record) {
                                return $state . ' %';
                            }),
                        InfolistComponents\TextEntry::make('description')
                            ->default('-'),
                        InfolistComponents\TextEntry::make('created_at')
                            ->since()
                            ->visible(function($record) {
                                return $record->time_out ? false : true;
                            }),
                    ])
                    ->columns(4)
                    ->columnSpan('full')
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDailySales::route('/'),
            // 'test' => Pages\IndexDailySales::route('/index'),
            'create' => Pages\CreateDailySale::route('/create'),
            'view' => Pages\ViewDailySale::route('/{record}'),
            'edit' => Pages\EditDailySale::route('/{record}/edit'),
        ];
    }
    public static function getScripts(): array
    {
        return [
            asset('js/daily-sale-resource.js'), // Link to your custom script file
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view daily-sales');
    }
}

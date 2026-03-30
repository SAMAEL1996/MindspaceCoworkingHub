<?php

namespace App\Models;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUid;
use Filament\Forms\Components as FormComponents;
use Filament\Infolists\Components as InfolistComponents;
use Carbon\Carbon;

class Conference extends Model
{
    use HasFactory, HasUid;

    protected $casts = [
        'card_ids' => 'array',
    ];

    protected $appends = [
        'remaining_balance',
        'start_at_carbon',
        'end_at_carbon',
    ];

    protected $fillable = [
        'package_id',
        'book_by',
        'start_at',
        'duration',
        'event',
        'members',
        'host',
        'email',
        'contact_no',
        'status',
        'amount',
        'has_reservation_fee',
        'payment',
        'is_paid',
        'mode_of_payment',
    ];

    public function getRemainingBalanceAttribute()
    {
        return $this->amount - $this->payment;
    }

    public function getStartAtCarbonAttribute()
    {
        return Carbon::parse($this->start_at);
    }

    public function getEndAtCarbonAttribute()
    {
        return Carbon::parse($this->start_at)->addHours($this->duration);
    }

    public function conferenceMembers()
    {
        return $this->hasMany(\App\Models\ConferenceMember::class, 'conference_id');
    }

    public static function getCheckTimeSchedules($checkStart, $checkEnd, $selfId = null)
    {
        $query = self::where('status', 'approve');

        if($selfId) {
            $query->where('id', '!=', $selfId);
        }

        $schedules = $query->get();

        if(!$schedules) {
            return false;
        }

        foreach($schedules as $schedule) {
            $start = $schedule->start_at_carbon;
            $end = $schedule->end_at_carbon;

            if ($start->lt($checkEnd) && $end->gt($checkStart)) {
                return true;
            }
        }

        return false;
    }

    public static function resolveScheduleStartTimes(array $data): array
    {
        $dates = !empty($data['recurring'])
            ? ($data['recurring_dates'] ?? [])
            : [$data['date'] ?? null];

        return collect($dates)
            ->filter()
            ->map(fn ($date) => Carbon::parse($date . ' ' . $data['time']))
            ->unique(fn (Carbon $dateTime) => $dateTime->format('Y-m-d H:i'))
            ->sortBy(fn (Carbon $dateTime) => $dateTime->timestamp)
            ->values()
            ->all();
    }

    public function additionalCharges() {
        // for time
        $start = $this->start_at_carbon->addMinutes(15);
        $end = $start->copy()->addHours($this->duration)->addMinutes(15);
        $now = \Carbon\Carbon::now();
        $defaultTime = $start->diffInHours($end);

        $diff = $start->diff($now);
        $hourDiff = $diff->h;
        $minDiff = $diff->i;
        if($minDiff > 15) {
            $hourDiff += 1;
        }

        if($this->duration >= $hourDiff) {
            return 0.00;
        }

        if($hourDiff >= 12) {
            
        }

        $exceed = $hourDiff - $this->duration;
        $package = \App\Library\Helper::getConferencePackageInfo($this->package_id);

        return $exceed * $package['succeeding_hours'];
    }

    public function addCheckInToSalesReport($amount, $isFinished = false)
    {
        $now = Carbon::now();
        $month = $now->copy()->format('F');
        $year = $now->copy()->format('Y');
        $day = $now->copy()->day;

        $monthlySale = \App\Models\Sale::where('type', 'monthly')->where('month', $month)->where('year', $year)->first();
        if(!$monthlySale) {
            $monthlySale = \App\Models\Sale::create(['type' => 'monthly', 'month' => $month, 'year' => $year]);
        }
        if($isFinished) {
            $monthlySale->total_conference_users += 1;
        }
        $monthlySale->total_sales += $amount;
        $monthlySale->save();

        $dailySale = \App\Models\Sale::where('type', 'daily')->where('day', $day)->where('month', $month)->where('year', $year)->first();
        if(!$dailySale) {
            $dailySale = \App\Models\Sale::create(['type' => 'daily', 'day' => $day, 'month' => $month, 'year' => $year]);
        }
        if($isFinished) {
            $dailySale->total_conference_users += 1;
        }
        $dailySale->total_sales += $amount;
        $dailySale->save();
    }

    public static function getRateAmount(int $packageId, int $duration)
    {
        $amount = 0;

        $rates = Rate::getConferenceRates($packageId);

        if($packageId == 1) {
            $additionalHours = Setting::getValue('conference-package-1-succeeding-hours');
        } else {
            $additionalHours = Setting::getValue('conference-package-2-succeeding-hours');
        }

        if($duration <= 3) {
            $amount = $rates[3];
        } elseif($duration == 4) {
            $amount = $rates[3] + $additionalHours;
        } elseif($duration == 5) {
            $amount = $rates[5];
        } elseif($duration == 6) {
            $amount = $rates[5] + $additionalHours;
        } elseif($duration > 6 && $duration <= 8) {
            $amount = $rates[8];
        } elseif($duration > 8 && 12 >= $duration) {
            $exceedingHours = $duration - 8;
            $additionalHoursRate = $additionalHours * $exceedingHours;
            $amount = $rates[8] + $additionalHoursRate;
        } elseif($duration > 12 && 24 >= $duration) {
            $amount = $rates[12];
        } else {
            $exceedingHours = $duration - 24;
            $additionalHoursRate = $additionalHours * $exceedingHours;
            $amount = $rates[12] + $additionalHoursRate;
        }

        return $amount;
    }

    public static function getForm()
    {
        return [
            FormComponents\ToggleButtons::make('package')
                ->label('Package Type')
                ->options([
                    '1' => 'Package 1 (Up to 8 pax)',
                    '2' => 'Package 2 (10 - 15 pax)',
                ])
                ->live()
                ->required()
                ->columnSpan('full')
                ->inline()
                ->extraAttributes([
                    'class' => 'flex justify-center items-center space-x-4',
                ])
                ->afterStateUpdated(function($state, $set, $get) {
                    $rate = Conference::getRateAmount((int)$state, (int)$get('duration'));

                    $set('total_amount', $rate);
                }),
            FormComponents\TextInput::make('event')
                ->label('Event Name')
                ->required(),
            FormComponents\TextInput::make('host')
                ->label('Name of POC')
                ->required(),
            FormComponents\TextInput::make('contact_no')
                ->tel()
                ->required(),
            FormComponents\TextInput::make('members')
                ->label('Total # of guests including POC')
                ->numeric()
                ->minValue(1)
                ->required(),
            FormComponents\Toggle::make('recurring')
                ->label('Recurring Dates')
                ->live(),
            FormComponents\Fieldset::make('Schedule')
                ->schema([
                    FormComponents\DatePicker::make('date')
                        ->label('Date')
                        ->required(fn ($get) => ! $get('recurring'))
                        ->displayFormat('d F Y')
                        ->timezone('Asia/Manila')
                        ->visible(fn ($get) => ! $get('recurring'))
                        ->native(false),
                    FormComponents\ViewField::make('recurring_dates')
                        ->label('Recurring Dates')
                        ->view('forms.components.conference-booking.recurring-dates-calendar')
                        ->visible(fn ($get) => (bool) $get('recurring'))
                        ->dehydrated(true)
                        ->default([])
                        ->rules([
                            fn ($get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                if (! $get('recurring')) {
                                    return;
                                }

                                if (! is_array($value) || blank($value)) {
                                    $fail('Please select at least one recurring date.');
                                }
                            },
                        ])
                        ->columnSpanFull(),
                    FormComponents\Select::make('time')
                        ->label('Time')
                        ->required()
                        ->options(\App\Library\Helper::get12HourTimeSelectOptions())
                        ->placeholder('Select Time')
                        ->native(false),
                    FormComponents\TextInput::make('duration')
                        ->label('Duration')
                        ->integer()
                        ->live()
                        ->minValue(1)
                        ->required()
                        ->afterStateUpdated(function($state, $set, $get) {
                            $rate = Conference::getRateAmount((int)$get('package'), (int)$state);

                            $set('total_amount', $rate);
                        }),
                ])
                ->columns(3)
                ->columnSpan('full'),
            FormComponents\TextInput::make('total_amount')
                ->label('TOTAL AMOUNT TO PAID')
                ->disabled()
                ->visible(function($get) {
                    if($get('package') && $get('duration')) {
                        return true;
                    }

                    return false;
                })
                ->columnSpan('1')
                ->extraAttributes([
                    'class' => 'flex justify-center items-center space-x-4',
                ]),

        ];
    }

    public static function getInfolist()
    {
        return [
            InfolistComponents\Section::make('Information')
                ->id('information')
                ->headerActions([
                    InfolistComponents\Actions\Action::make('edit')
                        ->modalHeading('Edit Information')
                        ->fillForm(fn($record): array => [
                            'package' => $record->package_id,
                            'event' => $record->event,
                            'host' => $record->host,
                            'contact_no' => $record->contact_no,
                            'members' => $record->members,
                            'date' => Carbon::parse($record->start_at)->format('Y-m-d'),
                            'time' => Carbon::parse($record->start_at)->format('H:i A'),
                            'duration' => $record->duration,
                        ])
                        ->form([
                            FormComponents\ToggleButtons::make('package')
                                ->label('Package Type')
                                ->options([
                                    '1' => 'Package 1 (Up to 8 pax)',
                                    '2' => 'Package 2 (10 - 15 pax)',
                                ])
                                ->required()
                                ->columnSpan('full')
                                ->inline()
                                ->extraAttributes([
                                    'class' => 'flex justify-center items-center space-x-4',
                                ]),
                            FormComponents\TextInput::make('event')
                                ->label('Event Name')
                                ->required(),
                            FormComponents\TextInput::make('host')
                                ->label('Name of POC')
                                ->required(),
                            FormComponents\TextInput::make('contact_no')
                                ->tel()
                                ->required(),
                            FormComponents\TextInput::make('members')
                                ->label('Total # of guests including POC')
                                ->numeric()
                                ->required(),
                            FormComponents\Fieldset::make('Schedule')
                                ->schema([
                                    FormComponents\DatePicker::make('date')
                                        ->label('Date')
                                        ->required()
                                        ->displayFormat('d F Y')
                                        ->timezone('Asia/Manila')
                                        ->native(false),
                                    FormComponents\Select::make('time')
                                        ->label('Time')
                                        ->required()
                                        ->options(\App\Library\Helper::get12HourTimeSelectOptions())
                                        ->placeholder('Select Time')
                                        ->native(false),
                                    FormComponents\TextInput::make('duration')
                                        ->label('Duration')
                                        ->integer()
                                        ->live()
                                        ->minValue(1)
                                        ->required()
                                        ->afterStateUpdated(function($state, $set, $get) {
                                            $rate = Conference::getRateAmount((int)$get('package'), (int)$state);

                                            $set('total_amount', $rate);
                                        }),
                                ])
                                ->columns(3)
                                ->columnSpan('full'),
                        ])
                        ->action(function ($data, $record) {
                            $timeOfArrival = Carbon::parse($data['date'] . ' ' . $data['time']);
                            if($timeOfArrival->copy()->subHour()->isPast()) {
                                Notification::make()
                                    ->title('Danger')
                                    ->body('Date is already past.')
                                    ->danger()
                                    ->send();

                                return;
                            }
                            $timeOfLeave = $timeOfArrival->copy()->addHours((int)$data['duration']);
                            $checkStart = $timeOfArrival->copy()->subMinutes(30);
                            $checkEnd = $timeOfLeave->copy()->addMinutes(30);

                            $checkDateTime = Conference::getCheckTimeSchedules($checkStart, $checkEnd, $record->id);

                            if($checkDateTime) {
                                Notification::make()
                                    ->title('Danger')
                                    ->body('Date and time is taken.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $record->update([
                                'package_id' => (int)$data['package'],
                                'start_at' => $timeOfArrival->copy(),
                                'duration' => (int)$data['duration'],
                                'event' => $data['event'],
                                'members' => $data['members'],
                                'host' => $data['host'],
                                'contact_no' => $data['contact_no'],
                            ]);
                            
                            Notification::make()
                                ->title('Success')
                                ->body('Conference information successfully updated.')
                                ->success()
                                ->send();

                            return $record;
                        })
                        ->visible(auth()->user()->hasRole('Super Administrator')),
                ])
                ->schema([
                    InfolistComponents\TextEntry::make('event')
                        ->label('Event'),
                    InfolistComponents\TextEntry::make('start_at')
                        ->label('Schedule')
                        ->formatStateUsing(function($record) {
                            return Carbon::parse($record->start_at)->format(config('app.date_time_format')) . ' - ' . Carbon::parse($record->start_at)->addHours($record->duration)->format(config('app.time_format'));
                        }),
                    InfolistComponents\TextEntry::make('duration')
                        ->label('Duration')
                        ->formatStateUsing(function($state) {
                            return $state . ' hours';
                        }),
                    InfolistComponents\TextEntry::make('host')
                        ->label('Name of POC'),
                    // InfolistComponents\TextEntry::make('email'),
                    InfolistComponents\TextEntry::make('contact_no'),
                    InfolistComponents\TextEntry::make('members')
                        ->label('Total no. of guests'),
                    InfolistComponents\TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->formatStateUsing(function($state, $record) {
                            return ucfirst($state);
                        })
                        ->color(function($state, $record) {
                            if($state == 'approve') {
                                return 'success';
                            } elseif($state == 'pending') {
                                return 'warning';
                            } elseif($state == 'past') {
                                return 'gray';
                            } 
                        }),
                    InfolistComponents\Fieldset::make('')
                        ->schema([
                            InfolistComponents\TextEntry::make('book_by')
                                ->label('Book By')
                                ->formatStateUsing(function($state) {
                                    $user = \App\Models\User::find($state);

                                    return $user->name;
                                }),
                            InfolistComponents\TextEntry::make('created_at')
                                ->label('Date Book')
                                ->formatStateUsing(function($state) {
                                    return \Carbon\Carbon::parse($state)->format(config('app.date_time_format'));
                                }),
                        ])      
                        ->columnSpan('full')          
                ])
                ->columns(3)
                ->columnSpan('full'),
            InfolistComponents\Section::make('Payment Information')
                ->id('payment')
                ->headerActions([
                    InfolistComponents\Actions\Action::make('edit')
                        ->modalHeading('Edit Payment Information')
                        ->fillForm(fn($record): array => [
                            'amount' => (int)$record->amount,
                            'payment' => (int)$record->payment
                        ])
                        ->form([
                            FormComponents\TextInput::make('amount')
                                ->label('Total Amount')
                                ->integer(),
                            FormComponents\TextInput::make('payment')
                                ->label('Amount Paid')
                                ->integer()
                        ])
                        ->action(function ($data, $record) {
                            $record->update([
                                'amount' => $data['amount'],
                                'payment' => $data['payment'],
                            ]);
                            
                            Notification::make()
                                ->title('Success')
                                ->body('Conference payment information successfully updated.')
                                ->success()
                                ->send();

                            return $record;
                        })
                        ->visible(auth()->user()->hasRole('Super Administrator')),
                ])
                ->schema([
                    InfolistComponents\TextEntry::make('amount')
                        ->label('Total Amount')
                        ->money('PHP'),
                    InfolistComponents\TextEntry::make('payment')
                        ->label('Amount Paid')
                        ->money('PHP'),
                    InfolistComponents\TextEntry::make('remaining_balance')
                        ->label('Remaining Balance')
                        ->money('PHP')
                        ->visible(function($record) {
                            return $record->is_paid ? false : true;
                        }),
                    InfolistComponents\TextEntry::make('is_paid')
                        ->label('Is Fully Paid')
                        ->badge()
                        ->formatStateUsing(function($state) {
                            return $state ? 'Yes' : 'No';
                        })
                        ->color(function($state) {
                            return $state ? 'success' : 'danger';
                        }),
                    InfolistComponents\TextEntry::make('mode_of_payment')
                        ->label('Mode of Payment')
                        ->visible(function($record) {
                            return $record->is_paid;
                        }),
                ])
                ->columns(4),
            InfolistComponents\Section::make('Members List')
                ->schema([
                    InfolistComponents\ViewEntry::make('members')
                        ->label('')
                        ->view('infolists.components.conference.members-list')
                        // ->keyLabel('Card ID')
                        // ->valueLabel('Member Name')
                ])
                ->columnSpan('full')
        ];
    }
}





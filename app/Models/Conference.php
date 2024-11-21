<?php

namespace App\Models;

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

    public static function getCheckTimeSchedules($checkStart, $checkEnd)
    {
        $schedules = self::where('status', 'approve')->get();

        if(!$schedules) {
            return false;
        }

        foreach($schedules as $schedule) {
            if($checkStart->between($schedule->start_at_carbon, $schedule->end_at_carbon) || $checkEnd->between($schedule->start_at_carbon, $schedule->end_at_carbon)) {
                return true;
            }
        }

        return false;
    }

    public function additionalCharges() {
        // for time
        $start = $this->start_at_carbon->addMinutes(15);
        $end = $this->end_at_carbon->addMinutes(15);
        $now = \Carbon\Carbon::now();
        $defaultTime = $start->diffInHours($end);
        // $ceil = ceil($start->diffInHours($now));

        if($now->copy()->lte($end)) {
            return 0.00;
        }

        $diffInMinutes = $end->copy()->diffInMinutes($now->copy());
        $exceed = ceil($diffInMinutes / 60);

        $package = \App\Library\Helper::getConferencePackageInfo($this->package_id);
        // $exceed = (int)$ceil - (int)$defaultTime;
        return $exceed * $package['succeeding_hours'];
    }

    public function addCheckInToSalesReport()
    {
        $now = \Carbon\Carbon::now();
        $month = $now->copy()->format('F');
        $year = $now->copy()->format('Y');
        $day = $now->copy()->day;

        $monthlySale = \App\Models\Sale::where('type', 'monthly')->where('month', $month)->where('year', $year)->first();
        if(!$monthlySale) {
            $monthlySale = \App\Models\Sale::create(['type' => 'monthly', 'month' => $month, 'year' => $year]);
        }
        $monthlySale->total_conference_users += 1;
        $monthlySale->total_sales += $this->payment;
        $monthlySale->save();

        $dailySale = \App\Models\Sale::where('type', 'daily')->where('day', $day)->where('month', $month)->where('year', $year)->first();
        if(!$dailySale) {
            $dailySale = \App\Models\Sale::create(['type' => 'daily', 'day' => $day, 'month' => $month, 'year' => $year]);
        }
        $dailySale->total_conference_users += 1;
        $dailySale->total_sales += $this->payment;
        $dailySale->save();
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
            FormComponents\Select::make('duration')
                ->label('Hours of Stay')
                ->options(function($get) {
                    $options = [];

                    if(!$get('package')) {
                        return $options;
                    }

                    $package = \App\Library\Helper::getConferencePackageInfo((int)$get('package'));
                    foreach($package['rates'] as $rate) {
                        $options[$rate['hours']] = $rate['label'];
                    }

                    return $options;
                })
                ->preload()
                ->native(false)
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
                ])
                ->columnSpan('full')
        ];
    }

    public static function getInfolist()
    {
        return [
            InfolistComponents\Section::make('Information')
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
                    InfolistComponents\TextEntry::make('email'),
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

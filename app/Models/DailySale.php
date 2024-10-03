<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUid;
use Filament\Forms\Components as FormComponents;
use Carbon\Carbon;

class DailySale extends Model
{
    use HasFactory, HasUid;

    public static function boot() {
        parent::boot();

        static::created(function ($daily) {
        });
    }

    protected $fillable = [
        'date',
        'time_in_staff_id',
        'time_out_staff_id',
        'card_id',
        'description',
        'time_in',
        'time_out',
        'amount_paid',
        'status',
        'name',
        'discount',
        'apply_discount',
        'total_time',
        'is_monthly',
        'is_flexi',
        'mode_of_payment',
        'default_amount'
    ];

    protected $appends = [
        'time_in_carbon',
        'time_out_carbon'
    ];

    public function staffIn()
    {
        return $this->belongsTo(\App\Models\Staff::class, 'time_in_staff_id');
    }

    public function staffOut()
    {
        return $this->belongsTo(\App\Models\Staff::class, 'time_out_staff_id');
    }

    public function card()
    {
        return $this->belongsTo(\App\Models\Card::class, 'card_id');
    }

    public function getTimeInCarbonAttribute()
    {
        return Carbon::parse($this->time_in);
    }

    public function getTimeOutCarbonAttribute()
    {
        return Carbon::parse($this->time_out);
    }

    public function addCheckInToSalesReport()
    {
        $month = $this->time_in_carbon->format('F');
        $year = $this->time_in_carbon->format('Y');
        $day = $this->time_in_carbon->day;

        if(!$this->is_flexi && !$this->is_monthly) {
            $monthlySale = \App\Models\Sale::where('type', 'monthly')->where('month', $month)->where('year', $year)->first();
            if(!$monthlySale) {
                $monthlySale = \App\Models\Sale::create(['type' => 'monthly', 'month' => $month, 'year' => $year]);
            }
            $monthlySale->total_daily_users = (int)$monthlySale->total_daily_users + 1;
            $monthlySale->save();

            $dailySale = \App\Models\Sale::where('type', 'daily')->where('day', $day)->where('month', $month)->where('year', $year)->first();
            if(!$dailySale) {
                $dailySale = \App\Models\Sale::create(['type' => 'daily', 'day' => $day, 'month' => $month, 'year' => $year]);
            }
            $dailySale->total_daily_users = (int)$dailySale->total_daily_users + 1;
            $dailySale->save();
        }
    }

    public function addPaymenToMonthlySalesReport()
    {
        $month = $this->time_in_carbon->format('F');
        $year = $this->time_in_carbon->format('Y');
        $day = $this->time_in_carbon->day;

        if(!$this->is_flexi && !$this->is_monthly) {
            $monthlySale = \App\Models\Sale::where('type', 'monthly')->where('month', $month)->where('year', $year)->first();
            if(!$monthlySale) {
                $monthlySale = \App\Models\Sale::create(['type' => 'monthly', 'month' => $month, 'year' => $year]);
            }
            $monthlySale->total_sales = (double)$monthlySale->total_sales + (double)$this->amount_paid;
            $monthlySale->save();

            $dailySale = \App\Models\Sale::where('type', 'daily')->where('day', $day)->where('month', $month)->where('year', $year)->first();
            if(!$dailySale) {
                $dailySale = \App\Models\Sale::create(['type' => 'daily', 'day' => $day, 'month' => $month, 'year' => $year]);
            }
            $dailySale->total_sales = (double)$dailySale->total_sales + (double)$this->amount_paid;
            $dailySale->save();
        }
    }

    public function removeSalesReport()
    {
        $month = $this->time_in_carbon->format('F');
        $year = $this->time_in_carbon->format('Y');
        $day = $this->time_in_carbon->day;

        if(!$this->is_flexi && !$this->is_monthly) {
            $monthlySale = \App\Models\Sale::where('type', 'monthly')->where('month', $month)->where('year', $year)->first();
            if(!$monthlySale) {
                $monthlySale = \App\Models\Sale::create(['type' => 'monthly', 'month' => $month, 'year' => $year]);
            }
            $monthlySale->total_daily_users -= 1;
            $monthlySale->save();

            $dailySale = \App\Models\Sale::where('type', 'daily')->where('day', $day)->where('month', $month)->where('year', $year)->first();
            if(!$dailySale) {
                $dailySale = \App\Models\Sale::create(['type' => 'daily', 'day' => $day, 'month' => $month, 'year' => $year]);
            }
            $dailySale->total_daily_users -= 1;
            $dailySale->save();
        }
    }

    public function computeAmount()
    {
        $timeInCarbon = $this->time_in_carbon;
        $timeOutCarbon = $this->time_out_carbon;

        $hours = $timeInCarbon->diffInHours($timeOutCarbon);

        if($hours < round($hours)) {
            $totalHours = round($hours);
        } else {
            $totalHours = round($hours) + 1;
        }

        $amount = 0;
        if($totalHours < 4) {
            $amount = $totalHours * 65;
        } elseif($totalHours == 5 || $totalHours == 4) {
            $amount = 250;
        } elseif($totalHours > 5 && $totalHours < 7) {
            $excess = $totalHours - 5;
            $additional = $excess * 65;
            $amount = 250 + $additional;
        } elseif($totalHours == 7 || $totalHours == 8) {
            $amount = 350;
        } elseif($totalHours > 8 && $totalHours < 11) {
            $excess = $totalHours - 8;
            $additional = $excess * 65;
            $amount = 350 + $additional;
        } else {
            $amount = 500;
        }

        if($this->apply_discount) {
            $percent = $this->discount / 100;
            $discount = (double)$amount * $percent;
            $amount = (double)$amount - (double)$discount;
        }

        if($this->is_flexi || $this->is_monthly) {
            $amount = $this->amount_paid;
        }

        $this->total_time = $totalHours;
        $this->amount_paid = $amount;
        $this->save();
    }

    public function computeShowTime($removeDiscount = false)
    {
        $timeInCarbon = $this->time_in_carbon;
        $timeOutCarbon = \Carbon\Carbon::now();

        $hours = $timeInCarbon->diffInHours($timeOutCarbon);

        if($hours < round($hours)) {
            $totalHours = round($hours);
        } else {
            $totalHours = round($hours) + 1;
        }

        $amount = 0;
        $wholeDay = false;
        if($totalHours > 24) {
            $wholeDay = true;
            $totalHours = $totalHours - 24;
        }

        if($totalHours < 4) {
            $amount = $totalHours * 65;
        } elseif($totalHours == 5 || $totalHours == 4) {
            $amount = 250;
        } elseif($totalHours > 5 && $totalHours < 7) {
            $excess = $totalHours - 5;
            $additional = $excess * 65;
            $amount = 250 + $additional;
        } elseif($totalHours == 7 || $totalHours == 8) {
            $amount = 350;
        } elseif($totalHours > 8 && $totalHours < 11) {
            $excess = $totalHours - 8;
            $additional = $excess * 65;
            $amount = 350 + $additional;
        } else {
            $amount = 500;
        }

        if($wholeDay) {
            $amount = $amount + 500;
        }

        if($this->is_flexi || $this->is_monthly) {
            return [
                'total_hours_accumulated' => $totalHours,
                'amount_to_paid' => $this->amount_paid
            ];
        }

        if($removeDiscount) {
            return [
                'total_hours_accumulated' => $totalHours,
                'amount_to_paid' => $amount
            ];
        }

        if($this->apply_discount) {
            $percent = $this->discount / 100;
            $discount = (double)$amount * $percent;
            $amount = (double)$amount - (double)$discount;
        }

        return [
            'total_hours_accumulated' => $totalHours,
            'amount_to_paid' => $amount
        ];
    }

    public static function getAvarageSalesPerMonth()
    {
        $sales = self::whereMonth('time_in', Carbon::now()->month)
                    ->whereYear('time_in', Carbon::now()->year)
                    ->where('is_flexi', false)
                    ->where('is_monthly', false)
                    ->get();

        if($sales->isEmpty()) {
            return 0;
        }

        $now = Carbon::now();

        $firstRecord = $sales->first();
        $timeIn = Carbon::parse($firstRecord->time_in);
        
        $days = round($timeIn->diffInDays($now)) + 1;

        return round($sales->count() / (int)$days);
    }

    public static function getForm()
    {
        return [
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
                                    $takenIds = self::whereNull('time_out')->pluck('card_id')->toArray();
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
                    FormComponents\Textarea::make('description')
                        ->rows(5),
                    FormComponents\Grid::make(2)
                        ->schema([
                            FormComponents\TextInput::make('amount_paid')
                                ->numeric(),
                            FormComponents\Select::make('mode_of_payment')
                                ->options([
                                    'Cash' => 'Cash',
                                    'GCash' => 'GCash',
                                    'Bank Transfer' => 'Bank Transfer',
                                ])
                                ->native(false)
                                ->required(),
                        ])
                        ->visibleOn('edit')
                ]),
            FormComponents\Fieldset::make('Error Log')
                ->schema([
                    FormComponents\Grid::make(2)
                        ->schema([
                            FormComponents\Select::make('error_staff_id')
                                ->options(function() {
                                    $options = [];

                                    foreach(\App\Models\Staff::where('is_active', true)->get() as $staff) {
                                        $options[$staff->id] = $staff->user->name;
                                    }

                                    return $options;
                                })
                                ->native(false),
                            FormComponents\Textarea::make('reason')
                                ->rows(5),
                        ])
                ])
                ->visibleOn('edit'),
        ];
    }
}

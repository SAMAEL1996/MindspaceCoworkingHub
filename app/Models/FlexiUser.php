<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUid;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Filament\Forms\Components as FormComponents;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class FlexiUser extends Model
{
    use HasFactory, HasUid;

    public static function boot() {
        parent::boot();

        static::created(function ($flexi) {
            $month = $flexi->start_at_carbon->format('F');
            $year = $flexi->start_at_carbon->format('Y');
            $day = $flexi->start_at_carbon->day;

            $monthlySale = \App\Models\Sale::where('type', 'monthly')->where('month', $month)->where('year', $year)->first();
            if(!$monthlySale) {
                $monthlySale = \App\Models\Sale::create(['type' => 'monthly', 'month' => $month, 'year' => $year]);
            }
            $monthlySale->total_flexi_users = (int)$monthlySale->total_flexi_users + 1;
            $monthlySale->total_sales = (double)$monthlySale->total_sales + (double)$flexi->amount_paid;
            $monthlySale->save();

            $dailySale = \App\Models\Sale::where('type', 'daily')->where('day', $day)->where('month', $month)->where('year', $year)->first();
            if(!$dailySale) {
                $dailySale = \App\Models\Sale::create(['type' => 'daily', 'day' => $day, 'month' => $month, 'year' => $year]);
            }
            $dailySale->total_flexi_users = (int)$dailySale->total_flexi_users + 1;
            $dailySale->total_sales = (double)$dailySale->total_sales + (double)$flexi->amount_paid;
            $dailySale->save();
        });
    }

    protected $fillable = [
        'card_id',
        'name',
        'contact_no',
        'facebook',
        'start_at',
        'end_at',
        'is_active',
        'status',
        'paid',
        'amount',
    ];

    protected $appends = [
        'remaining_time',
        'start_at_carbon',
        'end_at_carbon',
    ];

    public function card()
    {
        return $this->hasOne(\App\Models\Card::class, 'card_id');
    }

    public function subject(): MorphOne
    {
        return $this->morphOne(\App\Models\ErrorLog::class, 'subjectable');
    }

    public function getRemainingTimeAttribute()
    {
        $interval = $this->start_at_carbon->diff($this->end_at_carbon);
        $hours = $interval->h;
        $minutes = $interval->i;

        $hours += ($interval->d * 24);

        return $hours . ' hours and ' . $minutes . ' minutes';
    }

    public function getStartAtCarbonAttribute()
    {
        return \Carbon\Carbon::parse($this->start_at);
    }

    public function getEndAtCarbonAttribute()
    {
        return \Carbon\Carbon::parse($this->end_at);
    }

    public function recalculateTimeRemaining($daily_sale_id)
    {
        $dailySale = \App\Models\DailySale::find($daily_sale_id);

        $timeInCarbon = $dailySale->time_in_carbon;
        $timeOutCarbon = $dailySale->time_out_carbon;

        $consumed = $timeInCarbon->diffInMinutes($timeOutCarbon);

        $this->end_at = \Carbon\Carbon::parse($this->end_at)->subMinutes($consumed);
        $this->save();
    }

    public function getRemainingTimeArray()
    {
        if($this->remaining == 0) {
            return [
                'hours' => 0,
                'minutes' => 0,
                'seconds' => 0
            ];
        }
        $interval = $this->start_at_carbon->diff($this->end_at_carbon);
        $hours = $interval->h;
        $minutes = $interval->i;
        $seconds = $interval->s;

        $hours += ($interval->d * 24);

        return [
            'hours' => $hours,
            'minutes' => $minutes,
            'seconds' => $seconds
        ];
    }

    public function checkRemainingTime()
    {
        $startAt = $this->start_at_carbon;
        $endAt = $this->end_at_carbon;

        if ($startAt->diffInMinutes($endAt) === 0 && $startAt->diffInHours($endAt) === 0) {
            $this->status = false;
            $this->save();
        }
    }

    public function checkPassIsExpired($daily_sale_id)
    {
        $dailySale = \App\Models\DailySale::find($daily_sale_id);
        $timeInCarbon = $dailySale->time_in_carbon;
        $timeOutCarbon = \Carbon\Carbon::now();

        $consumed = $timeInCarbon->diffInMinutes($timeOutCarbon);
        $timeNow = $this->end_at_carbon->subMinutes($consumed);

        if($timeNow->lt($this->start_at_carbon)) {
            return true;
        }

        return false;
    }

    public function calculateAdditionalCharge($daily_sale_id, $filter = null)
    {
        if(!$this->checkPassIsExpired($daily_sale_id)) {
            return 0.00;
        }
        
        $dailySale = \App\Models\DailySale::find($daily_sale_id);
        $timeInCarbon = $dailySale->time_in_carbon;
        $timeOutCarbon = \Carbon\Carbon::now();

        $dailyTotalTime = $timeInCarbon->diffInMinutes($timeOutCarbon);

        $startAtCarbon = $this->start_at_carbon;
        $endAtCarbon = $this->end_at_carbon;

        $flexiTotalTime = $startAtCarbon->diffInMinutes($endAtCarbon);

        $totalTime = $dailyTotalTime - $flexiTotalTime;

        $now = \Carbon\Carbon::now();
        $end = $now->copy()->addMinutes($totalTime);

        if($filter == 'minutes') {
            return $now->diffInMinutes($end);
        }

        $hours = $now->diffInHours($end);
        if($hours < round($hours)) {
            $totalHours = round($hours);
        } else {
            $totalHours = round($hours) + 1;
        }

        if($filter == 'hours') {
            return $totalHours;
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

        return $amount;
    }

    public function sendSmsReminder()
    {
        $now = \Carbon\Carbon::now();

        $apikey = config('app.semaphore_key');

        $content = 'You have ' . $this->remaining_time . ' remaining consumable hours on your Flexi Pass. Thank you! ';
        $params = [
            'apikey' => $apikey,
            'number' => $this->contact_no,
            'message' => $content,
        ];

        try {

            $client = new Client();

            $request = new Request('POST', "https://api.semaphore.co/api/v4/messages?" . http_build_query($params));
            $res = $client->sendAsync($request)->wait();

        } catch (\Exception $e) {

            \Log::error($this->name.' send sms error on '.$now->copy()->format(config('app.date_time_carbon')) . ' with message: '. $e->getMessage());

        }

        activity()
            ->inLog('notifications')
            ->performedOn($this)
            ->log('<b>SMS Notification</b> <br>'.$content);
    }

    public static function getForm()
    {
        return [
            FormComponents\Grid::make(1)
                ->schema([
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
                        ->helperText('Flexi Pass Rate: PHP 1,500.00'),
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
                ])
        ];
    }
}

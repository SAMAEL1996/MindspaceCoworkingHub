<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUid;
use Filament\Forms\Components as FormComponents;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class MonthlyUser extends Model
{
    use HasFactory, HasUid, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public static function boot() {
        parent::boot();

        static::created(function ($monthly) {
            $createdAt = Carbon::parse($monthly->created_at);

            $day = $createdAt->copy()->format('d');
            $month = $createdAt->copy()->format('F');
            $year = $createdAt->copy()->format('Y');

            // DAILY SALE
            $dailySale = \App\Models\Sale::where('type', 'daily')->where('day', $day)->where('month', $month)->where('year', $year)->first();
            if(!$dailySale) {
                $dailySale = \App\Models\Sale::create(['type' => 'daily', 'day' => $day, 'month' => $month, 'year' => $year]);
            }
            $dailySale->total_monthly_users += 1;
            $dailySale->total_sales += (double)$monthly->amount;
            $dailySale->save();

            // MONTHLY SALE
            $monthlySale = \App\Models\Sale::where('type', 'monthly')->where('month', $month)->where('year', $year)->first();
            if(!$monthlySale) {
                $monthlySale = \App\Models\Sale::create(['type' => 'monthly', 'month' => $month, 'year' => $year]);
            }
            $monthlySale->total_monthly_users += 1;
            $monthlySale->total_sales += (double)$monthly->amount;
            $monthlySale->save();
        });
    }

    protected $fillable = [
        'rate_id',
        'card_id',
        'name',
        'contact_no',
        'facebook',
        'social_media',
        'date_start',
        'date_finish',
        'is_active',
        'is_expired',
        'paid',
        'amount',
    ];

    protected $appends = [
        'date_start_carbon',
        'date_finish_carbon',
    ];

    public function rate()
    {
        return $this->belongsTo(\App\Models\Rate::class, 'rate_id');
    }

    public function card()
    {
        return $this->belongsTo(\App\Models\Card::class, 'card_id');
    }

    public function getDateStartCarbonAttribute()
    {
        return Carbon::parse($this->date_start);
    }

    public function getDateFinishCarbonAttribute()
    {
        return Carbon::parse($this->date_finish)->addDay();
    }

    public function sendWelcomeMessage()
    {
        $apikey = config('app.semaphore_key');

        $content = 'Thank you for subscribing to Monthly Pass! You can now enjoy unlimited co-working access, valid for 30 days.';
        $params = [
            'apikey' => $apikey,
            'number' => $this->contact_no,
            'message' => $content,
        ];

        try {
            $client = new Client();
            $request = new GuzzleRequest('POST', "https://api.semaphore.co/api/v4/messages?" . http_build_query($params));
            $res = $client->sendAsync($request)->wait();
        } catch (\Exception $e) {
            \Log::error($this->name.' send sms error on '.Carbon::now()->format(config('app.date_time_carbon')) . ' with message: '. $e->getMessage());
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
                    FormComponents\Select::make('card_id')
                        ->options(function() {
                            $options = [];
                            $monthlyIds = self::where('is_expired', false)->pluck('card_id')->toArray();
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

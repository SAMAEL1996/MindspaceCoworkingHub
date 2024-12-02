<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUid;
use Filament\Forms\Components as FormComponents;

class MonthlyUser extends Model
{
    use HasFactory, HasUid;

    public static function boot() {
        parent::boot();

        static::created(function ($monthly) {
            $month = $monthly->date_start_carbon->format('F');
            $year = $monthly->date_start_carbon->format('Y');
            $day = $monthly->date_start_carbon->day;

            $monthlySale = \App\Models\Sale::where('type', 'monthly')->where('month', $month)->where('year', $year)->first();
            if(!$monthlySale) {
                $monthlySale = \App\Models\Sale::create(['type' => 'monthly', 'month' => $month, 'year' => $year]);
            }
            $monthlySale->total_monthly_users = (int)$monthlySale->total_monthly_users + 1;
            $monthlySale->total_sales = (double)$monthlySale->total_sales + (double)$monthly->amount_paid;
            $monthlySale->save();

            $dailySale = \App\Models\Sale::where('type', 'daily')->where('day', $day)->where('month', $month)->where('year', $year)->first();
            if(!$dailySale) {
                $dailySale = \App\Models\Sale::create(['type' => 'daily', 'day' => $day, 'month' => $month, 'year' => $year]);
            }
            $dailySale->total_monthly_users = (int)$dailySale->total_monthly_users + 1;
            $dailySale->total_sales = (double)$dailySale->total_sales + (double)$monthly->amount_paid;
            $dailySale->save();
        });
    }

    protected $fillable = [
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

    public function card()
    {
        return $this->belongsTo(\App\Models\Card::class, 'card_id');
    }

    public function getDateStartCarbonAttribute()
    {
        return \Carbon\Carbon::parse($this->start_at);
    }

    public function getDateFinishCarbonAttribute()
    {
        return \Carbon\Carbon::parse($this->end_at)->addDay();
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

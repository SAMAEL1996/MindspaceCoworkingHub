<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUid;
use Filament\Forms\Components as FormComponents;
use Rap2hpoutre\FastExcel\FastExcel;

class Staff extends Model
{
    use HasFactory, HasUid;

    public static function boot() {
        parent::boot();

        static::created(function ($staff) {
            $staff->profile()->create();
        });
    }

    protected $fillable = [
        'user_id',
        'card_id',
        'personal_email',
        'emergency_contact_person',
        'emergency_relationship',
        'emergency_contact_no',
        'is_active'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function card()
    {
        return $this->belongsTo(\App\Models\Card::class, 'card_id');
    }

    public function dailySales()
    {
        return $this->hasMany(\App\Models\DailySale::class, 'time_in_staff_id', 'id');
    }

    public function saleReports()
    {
        return $this->hasMany(\App\Models\SalesReport::class);
    }

    public function attendances()
    {
        return $this->hasMany(\App\Models\Attendance::class);
    }

    public function errors()
    {
        return $this->hasMany(\App\Models\ErrorLog::class);
    }

    public function profile()
    {
        return $this->hasOne(\App\Models\Profile::class);
    }

    public function createReport($dailySale_id)
    {
        $dailySale = \App\Models\DailySale::find($dailySale_id);

        $staffSales = 0.00;
        $staffCustomer = false;
        if($dailySale->time_in_staff_id == $dailySale->time_out_staff_id) {
            $staffSales = (double)$dailySale->amount_paid;
            $staffCustomer = true;
        }

        $saleReport = \App\Models\SaleReport::create([
            'staff_id' => $this->id,
            'daily_sale_id' => $dailySale->id,
            'date' => \Carbon\Carbon::now(),
            'staff_sales' => $staffSales,
            'total_sales' => $dailySale->amount_paid,
            'staff_customer' => $staffCustomer,
            'status' => true,
        ]);
    }

    public function exportSales($collection, $filename)
    {
        $export = [];
        $totalSales = 0.00;
        $staffTotalSales = 0.00;
        foreach($collection as $item)
        {
            $exportData = [
                'Staff Name' => $this->user->name,
                'Staff Card ID' => $this->card_id . ' - ' . $this->card->code,
                'Date' => \Carbon\Carbon::parse($item->date)->format(config('app.date_format')),
                'Card ID' => $item->card_id,
                'Card ID' => $item->name,
                'Time In' => \Carbon\Carbon::parse($item->date . ' ' . $item->time_in)->format(config('app.time_format')),
                'Time Out' => \Carbon\Carbon::parse($item->date . ' ' . $item->time_in)->format(config('app.time_format')),
                'Total Time' => $item->total_time,
                'Discount' => $item->discount ? 'Yes' : 'No',
                'Self Time Out' => $item->time_out_staff_id == $this->id ? 'Yes' : 'No',
                'Amount Paid' => $item->amount_paid
            ];
            $export[] = $exportData;

            if($item->time_out_staff_id == $this->id) {
                $staffTotalSales += $item->amount_paid;
            }

            $totalSales += $item->amount_paid;
        }
        $staffTotalSalesExportData = [
            'Staff Name' => '',
            'Staff Card ID' => '',
            'Date' => '',
            'Card ID' => '',
            'Card ID' => '',
            'Time In' => '',
            'Time Out' => '',
            'Total Time' => '',
            'Amount Paid' => '',
            'Discount' => 'STAFF TOTAL SALES',
            'Self Time Out' => $staffTotalSales
        ];
        $export[] = $staffTotalSalesExportData;
        $totalSalesExportData = [
            'Staff Name' => '',
            'Staff Card ID' => '',
            'Date' => '',
            'Card ID' => '',
            'Card ID' => '',
            'Time In' => '',
            'Time Out' => '',
            'Total Time' => '',
            'Amount Paid' => '',
            'Discount' => 'TOTAL SALES',
            'Self Time Out' => $totalSales
        ];
        $export[] = $totalSalesExportData;

        return $export;
    }

    public static function getForm()
    {
        return [
            FormComponents\Grid::make(1)
                ->schema([
                    FormComponents\TextInput::make('name')
                        ->required(),
                    FormComponents\TextInput::make('personal_email')
                        ->label('Personal Email')
                        ->required()
                        ->email(),
                    FormComponents\TextInput::make('email')
                        ->label('Work Email')
                        ->email()
                        ->visible(auth()->user()->hasRole('Super Administrator')),
                    FormComponents\TextInput::make('contact_no'),
                    FormComponents\TextInput::make('password')
                        ->default('password')
                        ->password()
                        ->visibleOn('create'),
                    FormComponents\Select::make('card_id')
                        ->label('Set Card ID')
                        ->options(function() {
                            $ids = self::all()->pluck('card_id')->toArray();

                            return \App\Models\Card::where('type', 'Staff')->whereNotIn('id', $ids)->get()->pluck('code', 'id')->toArray();
                        })
                        ->required()
                        ->preload()
                        ->searchable()
                        ->native(false)
                        ->visibleOn('create'),
                    FormCOmponents\Toggle::make('is_active')
                        ->label('Is Active'),
                    FormCOmponents\Section::make('Emergency Contact')
                        ->schema([
                            FormComponents\TextInput::make('emergency_contact_person')
                                ->label('Contact Person')
                                ->required(),
                            FormComponents\TextInput::make('emergency_relationship')
                                ->label('Relationship'),
                            FormComponents\TextInput::make('emergency_contact_no')
                                ->label('Contact No.')
                                ->required(),
                        ])
                ])
        ];
    }
}

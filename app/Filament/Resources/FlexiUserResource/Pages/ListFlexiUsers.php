<?php

namespace App\Filament\Resources\FlexiUserResource\Pages;

use App\Filament\Resources\FlexiUserResource;
use App\Filament\Resources\DailySaleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\FlexiUser;
use App\Models\Rate;
use App\Models\CashLog;
use App\Models\DailySale;
use App\Models\Card;
use Excel;
use Filament\Forms\Components as FormComponents;
use Filament\Support\Enums\MaxWidth;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class ListFlexiUsers extends ListRecords
{
    protected static string $resource = FlexiUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export-creators')
                ->label('Export')
                ->modalWidth(MaxWidth::Large)
                ->modalHeading('Export Creators')
                ->fillForm(function ($data) {
                    $data['user'] = 'all';
                    $data['status'] = 'active';
                    $data['package'] = 'all';

                    return $data;
                })
                ->form([
                    FormComponents\Grid::make(1)
                        ->schema([
                            FormComponents\Select::make('user')
                                ->label('Flexi User')
                                ->options(function() {
                                    $options = ['all' => 'All'];
                                    $names = \DB::table('flexi_users')
                                        ->select('name')
                                        ->distinct()
                                        ->pluck('name');
                                    foreach($names as $name) {
                                        $options[$name] = $name;
                                    }

                                    return $options;
                                })
                                ->native(false)
                                ->required(),
                            FormComponents\Select::make('status')
                                ->label('Status')
                                ->options([
                                    'all' => 'All',
                                    'active' => 'Active',
                                    'expired' => 'Expired'
                                ])
                                ->native(false)
                                ->required(),
                            FormComponents\Select::make('package')
                                ->label('Package')
                                ->options(function() {
                                    $options = ['all' => 'All', 'old' => 'Old Pass'];
                                    $packages = Rate::where('type', 'Flexi')->get();
                                    foreach($packages as $package) {
                                        $options[$package->id] = $package->name;
                                    }

                                    return $options;
                                })
                                ->native(false)
                                ->required(),
                        ])
                        ->columnSpanFull()
                ])
                ->action(function ($data, $livewire) {
                    $coaches = FlexiUser::query()
                        ->with('rate')
                        ->when($data['user'] !== 'all', function ($query) use ($data) {
                            $query->where('name', $data['user']);
                        })
                        ->when($data['status'] !== 'all', function ($query) use ($data) {
                            if ($data['status'] === 'active') {
                                $query->where('status', true);
                            } else {
                                $query->where('status', false);
                            }
                        })
                        ->when($data['package'] !== 'all', function ($query) use ($data) {
                            if ($data['package'] === 'old') {
                                $query->whereNull('rate_id');
                            } else {
                                $query->where('rate_id', $data['package']);
                            }
                        })
                        ->get();

                    $export = [];
                    $headings = [
                        'ID',
                        'Package',
                        'Name',
                        'Contact No',
                        'Status',
                        'Date Start',
                        'Date Expired',
                        'Remaining TIme'
                    ];

                    foreach ($coaches as $item) {
                        $exportData = [
                            'ID' => $item->id,
                            'Package' => $item->rate?->name,
                            'Name' => $item->name,
                            'Contact No' => $item->contact_no,
                            'Status' => $item->status ? 'Active' : 'Expired',
                            'Date Start' => Carbon::parse($item->start_at)->format(config('app.date_time_format')),
                            'Date Expired' => Carbon::parse($item->expired_at)->format(config('app.date_time_format')),
                            'Remaining Time' => $item->remaining_time
                        ];

                        $export[] = $exportData;
                    }
                    $exportFile = new \App\Exports\ExportModule($headings, $export);

                    Notification::make()
                        ->title('Success')
                        ->body('Flexi users successfully exported.')
                        ->success()
                        ->send();

                    return Excel::download($exportFile, 'flexi-users-' . date('Y-m-d') . '.csv');
                })
                ->visible(auth()->user()->hasRole('Super Administrator'))
                ->icon('heroicon-o-arrow-up-on-square'),
            Actions\Action::make('add')
                ->label('Add')
                ->modalWidth(MaxWidth::ThreeExtraLarge)
                ->modalHeading('Add New Flexi')
                ->fillForm(function() {
                    $data['discount'] = 0;

                    return $data;
                })
                ->form([
                    FormComponents\Grid::make(2)
                        ->schema([
                            FormComponents\TextInput::make('name')
                                ->label('Guest Name')
                                ->required()
                                ->extraAttributes([
                                    'x-data' => '{}',
                                    'x-on:input' => "event.target.value = event.target.value.replace(/\b\w/g, c => c.toUpperCase())",
                                ]),
                            FormComponents\Select::make('rate_id')
                                ->label('Type')
                                ->options(fn() => Rate::where('type', 'Flexi')->get()->pluck('name', 'id'))
                                ->preload()
                                ->live()
                                ->afterStateUpdated(function($state, $set) {
                                    $rate = Rate::find($state);
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
                            FormComponents\TextInput::make('discount')
                                ->label('Discount')
                                ->numeric()
                                ->live()
                                ->minValue(0)
                                ->default('0')
                                ->required()
                                ->afterStateUpdated(function($state, $set, $get) {
                                    $originalAmount = Rate::find($get('rate_id'))?->price ?? 0;
                                    $discount = floatval($state);
                                    $discountedAmount = $originalAmount - ($originalAmount * ($discount / 100));

                                    $set('amount', $discountedAmount);
                                })
                                ->helperText('Default value is 0.'),
                            FormComponents\TextInput::make('amount')
                                ->label('Amount Paid')
                                ->numeric()
                                ->minValue(1)
                                ->required()
                                ->disabled()
                                ->dehydrated()
                                ->inputMode('decimal')
                                ->helperText(function($get) {
                                    return $get('amount_helper_text') ?? '';
                                }),
                        ])
                ])
                ->action(function ($data, $action) {
                    // Check contact if exists
                    if(FlexiUser::where('contact_no', $data['contact_no'])->where('status', true)->first()) {
                        Notification::make()
                            ->title('Danger')
                            ->body("Contact no. is already exists. Please insert other number")
                            ->danger()
                            ->send();

                        return $action->halt();
                    }

                    $rate = Rate::find($data['rate_id']);
                    $flexi = FlexiUser::create([
                        'rate_id' => $rate->id,
                        'card_id' => null,
                        'name' => $data['name'],
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

                    $takenIds = DailySale::whereNull('time_out')->pluck('card_id')->toArray();
                    $card = Card::whereNotIn('id', $takenIds)->where('type', 'Daily')->first();

                    $dailyPass = DailySale::create([
                        'date' => Carbon::now(),
                        'card_id' => $card->id,
                        'name' => $data['name'],
                        'description' => 'Flexi',
                        'time_in' => Carbon::now(),
                        'time_in_staff_id' => auth()->user()->staff?->id,
                        'time_out' => Carbon::now(),
                        'time_out_staff_id' => auth()->user()->staff?->id,
                        'default_amount' => true,
                        'amount_paid' => $data['amount'],
                        'apply_discount' => true,
                        'discount' => 100,
                        'is_flexi' => true,
                        'is_monthly' => false,
                        'is_conference' => false,
                        'status' => false,
                        'mode_of_payment' => $data['mode_of_payment'],
                    ]);

                    $flexi->update([
                        'remaining' => $flexi->start_at_carbon->diffInMinutes($flexi->end_at_carbon)
                    ]);

                    Notification::make()
                        ->title('Success')
                        ->body("Flexi successfully created.")
                        ->success()
                        ->send();

                    return $flexi;
                })
                ->visible(function() {
                    $user = auth()->user();
    
                    if($user->hasRole('Super Administrator')) {
                        return true;
                    }
    
                    return CashLog::where('status', true)->where('user_id', $user->id)->exists();
                })
                ->icon('heroicon-o-plus-circle'),
            
        ];
    }

    public function getTabs(): array
    {
        return [
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', true))
                ->badge(FlexiUser::query()->where('status', true)->count()),
            'expired' => Tab::make('Expired')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', false))
                ->badge(FlexiUser::query()->where('status', false)->count()),
            'all' => Tab::make('All')
                ->badge(FlexiUser::query()->count()),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            
        ];
    }
}

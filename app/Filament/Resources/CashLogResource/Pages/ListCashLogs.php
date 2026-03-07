<?php

namespace App\Filament\Resources\CashLogResource\Pages;

use App\Filament\Resources\CashLogResource;
use App\Models\CashLog;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components as FormComponents;
use Illuminate\Support\Facades\Session;

class ListCashLogs extends ListRecords
{
    protected static string $resource = CashLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('cash-in')
                ->modalHeading('Cash In')
                ->form([
                    FormComponents\TextInput::make('amount')
                        ->label('Amount')
                        ->required()
                        ->numeric()
                        ->minValue(0),
                ])
                ->action(function($data) {
                    $user = auth()->user();

                    $cashHistory = $user->cashLogs()->create([
                        'cash_in' => $data['amount'],
                        'date_cash_in' => \Carbon\Carbon::now(),
                        'total_sales' => 0.00
                    ]);

                    return $cashHistory;
                })
                ->modalWidth(MaxWidth::Small)
                ->visible(function() {
                    $user = auth()->user();

                    if(CashLog::hasActiveCashier()) {
                        return false;
                    }

                    if(!$user->staff?->hasActiveAttendance()) {
                        return false;
                    }

                    return true;
                }),
            Actions\Action::make('cash-out')
                ->modalHeading('Cash Out')
                ->form([
                    FormComponents\TextInput::make('amount')
                        ->label('Amount')
                        ->required()
                        ->numeric()
                        ->minValue(0),
                ])
                ->action(function($data) {
                    $user = auth()->user();

                    $latestCashHistory = $user->cashLogs()->latest()->first();
                    $debits = $latestCashHistory->items()->where('in', 0.00)->sum('out');
                    $credts = $latestCashHistory->items()->where('out', 0.00)->sum('in');

                    $total = (double)$latestCashHistory->cash_in + (double)$credts - (double)$debits;

                    $cashHistory = $latestCashHistory->update([
                        'cash_out' => $data['amount'],
                        'date_cash_out' => \Carbon\Carbon::now(),
                        'total_sales' =>  $data['amount'] - $total,
                        'status' => false
                    ]);

                    return $cashHistory;
                })
                ->modalWidth(MaxWidth::Small)
                ->visible(function() {
                    $user = auth()->user();

                    if(CashLog::hasActiveCashier()) {
                        return Cashlog::where('status', true)->where('user_id', $user->id)->latest()->exists();
                    }

                    return false;
                }),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            
        ];
    }
}

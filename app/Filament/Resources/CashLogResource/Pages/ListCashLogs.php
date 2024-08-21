<?php

namespace App\Filament\Resources\CashLogResource\Pages;

use App\Filament\Resources\CashLogResource;
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

                    Session::put('cashier', $user);
                    
                    return $cashHistory;
                })
                ->modalWidth(MaxWidth::Small)
                ->visible(function() {
                    $user = auth()->user();

                    if($user->hasRole('Super Administrator')) {
                        return true;
                    }

                    if(session('cashier') || !session('on_shift')) {
                        return false;
                    }

                    $latestCashHistory = $user->cashLogs()->latest()->first();

                    if(!$latestCashHistory) {
                        return true;
                    }
                    
                    return !$latestCashHistory->status;
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

                    Session::put('cashier', false);
                    
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

                    if($user->hasRole('Super Administrator')) {
                        return true;
                    }

                    $latestCashHistory = $user->cashLogs()->latest()->first();
                    
                    if(!$latestCashHistory) {
                        return false;
                    }
                    
                    return $latestCashHistory->status;
                }),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            
        ];
    }
}

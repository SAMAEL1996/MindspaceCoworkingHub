<?php

namespace App\Filament\Resources\CashLogResource\Pages;

use App\Filament\Resources\CashLogResource;
use App\Models\CashLog;
use App\Models\CashLogMoneyCalculator;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components as FormComponents;

class ListCashLogs extends ListRecords
{
    protected static string $resource = CashLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('cash-in')
                ->modalHeading('Cash In')
                ->form($this->getMoneyCalculatorFormSchema())
                ->action(function (array $data) {
                    $user = auth()->user();
                    $amount = CashLogMoneyCalculator::calculateAmount($data);

                    $cashLog = $user->cashLogs()->create([
                        'cash_in' => $amount,
                        'date_cash_in' => Carbon::now(),
                        'total_sales' => 0.00,
                    ]);

                    CashLogMoneyCalculator::storeForCashLog($cashLog, 'cash_in', $data);

                    return $cashLog;
                })
                ->modalWidth(MaxWidth::Large)
                ->visible(function () {
                    $user = auth()->user();

                    if (CashLog::hasActiveCashier()) {
                        return false;
                    }

                    if (! $user->staff?->hasActiveAttendance()) {
                        return false;
                    }

                    return true;
                }),
            Actions\Action::make('cash-out')
                ->modalHeading('Cash Out')
                ->form($this->getMoneyCalculatorFormSchema())
                ->action(function (array $data) {
                    $user = auth()->user();
                    $amount = CashLogMoneyCalculator::calculateAmount($data);

                    $latestCashHistory = $user->cashLogs()->latest()->first();
                    $debits = $latestCashHistory->items()->where('in', 0.00)->sum('out');
                    $credts = $latestCashHistory->items()->where('out', 0.00)->sum('in');

                    $total = (double) $latestCashHistory->cash_in + (double) $credts - (double) $debits;

                    $latestCashHistory->update([
                        'cash_out' => $amount,
                        'date_cash_out' => Carbon::now(),
                        'total_sales' => $amount - $total,
                        'status' => false,
                    ]);

                    CashLogMoneyCalculator::storeForCashLog($latestCashHistory, 'cash_out', $data);

                    return $latestCashHistory;
                })
                ->modalWidth(MaxWidth::Large)
                ->visible(function () {
                    $user = auth()->user();

                    if (CashLog::hasActiveCashier()) {
                        return CashLog::where('status', true)->where('user_id', $user->id)->latest()->exists();
                    }

                    return false;
                }),
        ];
    }

    protected function getMoneyCalculatorFormSchema(): array
    {
        $sections = [];

        foreach (CashLogMoneyCalculator::formGroups() as $section => $fields) {
            $inputs = [];

            foreach ($fields as $field => $definition) {
                $inputs[] = $this->getMoneyCalculatorInput($field, $definition['label']);
            }

            $sections[] = FormComponents\Section::make($section)
                ->schema([
                    FormComponents\Grid::make(2)
                        ->schema($inputs),
                ]);
        }

        return $sections;
    }

    protected function getMoneyCalculatorInput(string $name, string $label): TextInput
    {
        return FormComponents\TextInput::make($name)
            ->label($label)
            ->required()
            ->numeric()
            ->default(0)
            ->step(1)
            ->minValue(0);
    }

    public function getBreadcrumbs(): array
    {
        return [
            
        ];
    }
}

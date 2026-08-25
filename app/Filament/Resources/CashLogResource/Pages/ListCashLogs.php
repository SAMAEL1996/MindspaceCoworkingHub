<?php

namespace App\Filament\Resources\CashLogResource\Pages;

use App\Filament\Resources\CashLogResource;
use App\Models\CashLog;
use App\Models\CashLogMoneyCalculator;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components as FormComponents;
use Illuminate\Support\HtmlString;

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
                ->modalWidth(MaxWidth::SevenExtraLarge)
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
                ->modalWidth(MaxWidth::SevenExtraLarge)
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
                ->columnSpan(1)
                ->schema([
                    FormComponents\Grid::make(2)
                        ->schema($inputs),
                ]);
        }

        return [
            FormComponents\Hidden::make('calculated_total')
                ->default(0)
                ->dehydrated(false),
            FormComponents\Grid::make([
                'default' => 1,
                'xl' => 2,
            ])
                ->schema($sections),
            FormComponents\Placeholder::make('calculated_total_display')
                ->hiddenLabel()
                ->columnSpanFull()
                ->content(fn (Get $get): HtmlString => new HtmlString(
                    $this->getMoneyCalculatorTotalMarkup((float) ($get('calculated_total') ?? 0))
                ))
                ->extraAttributes([
                    'class' => 'mx-auto w-full max-w-xl pt-2',
                ]),
        ];
    }

    protected function getMoneyCalculatorInput(string $name, string $label): TextInput
    {
        return FormComponents\TextInput::make($name)
            ->label($label)
            ->required()
            ->numeric()
            ->default(0)
            ->live(debounce: 300)
            ->afterStateHydrated(fn (Get $get, Set $set) => $this->syncMoneyCalculatorTotal($get, $set))
            ->afterStateUpdated(fn (Get $get, Set $set) => $this->syncMoneyCalculatorTotal($get, $set))
            ->step(1)
            ->minValue(0);
    }

    protected function calculateMoneyCalculatorTotal(Get $get): float
    {
        $data = [];

        foreach (CashLogMoneyCalculator::formGroups() as $fields) {
            foreach (array_keys($fields) as $field) {
                $data[$field] = $get($field);
            }
        }

        return CashLogMoneyCalculator::calculateAmount($data);
    }

    protected function syncMoneyCalculatorTotal(Get $get, Set $set): void
    {
        $set('calculated_total', $this->calculateMoneyCalculatorTotal($get));
    }

    protected function getMoneyCalculatorTotalMarkup(float $total): string
    {
        $formattedTotal = number_format($total, 2);

        return <<<HTML
            <div class="flex w-full flex-col items-center justify-center rounded-2xl border border-gray-200 bg-gray-50 px-6 py-5 text-center shadow-sm dark:border-white/10 dark:bg-white/5">
                <div class="text-xs font-semibold uppercase tracking-[0.35em] text-gray-500 dark:text-gray-400">Total Amount</div>
                <div class="mt-2 text-4xl font-bold tracking-tight text-gray-950 dark:text-white">PHP {$formattedTotal}</div>
            </div>
        HTML;
    }

    public function getBreadcrumbs(): array
    {
        return [
            
        ];
    }
}

<?php

namespace App\Livewire\DailySalesReport;

use Livewire\Component;
use App\Filament\Resources\ConferenceResource;
use App\Models\Sale;
use App\Models\DailySale;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions as TableActions;
use Filament\Tables\Columns as TableColumns;
use Filament\Tables\Filters as TableFilters;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components as InfolistComponents;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Notifications\Notification;

class Transactions extends Component implements HasForms, HasInfolists, HasTable
{
    use InteractsWithInfolists;
    use InteractsWithTable;
    use InteractsWithForms;

    public Sale $report;
    public $date;

    public function table(Table $table): Table
    {
        return $table
            ->query(DailySale::whereDate('date', $this->date->format('Y-m-d')))
            ->headerActions([
                //
            ])
            ->columns([
                TableColumns\TextColumn::make('card.code')
                    ->label('Guest')
                    ->formatStateUsing(function($state, $record) {
                        return $record->card->code;
                    })
                    ->description(function($state, $record) {
                        return $record->name;
                    })
                    ->searchable(),
                TableColumns\TextColumn::make('time_in')
                    ->label('Time In')
                    ->formatStateUsing(function($state, $record) {
                        return $record->time_in_carbon->format(config('app.time_format'));
                    })
                    ->description(function($state, $record) {
                        return $record->time_in_carbon->format(config('app.date_format'));
                    }),
                TableColumns\TextColumn::make('staffIn.user.name')
                    ->label('Staff In')
                    ->formatStateUsing(function($state, $record) {
                        return $record->staffIn->user->name;
                    })
                    ->searchable(),
                TableColumns\TextColumn::make('mode_of_payment')
                    ->label('M.O.P.'),
                TableColumns\TextColumn::make('amount_paid')
                    ->label('Amount')
                    ->money('PHP'),
                TableColumns\TextColumn::make('amount_paid')
                    ->label('Total Sales')
                    ->summarize(TableColumns\Summarizers\Sum::make()->label(''))
            ])
            ->filters([
                TableFilters\SelectFilter::make('mode_of_payment')
                    ->options([
                        'Cash' => 'Cash',
                        'GCash' => 'GCash',
                        'Bank Transfer' => 'Bank Transfer',
                    ])
            ])
            ->filtersTriggerAction(
                fn (TableActions\Action $action) => $action
                    ->button()
                    ->label('Filter'),
            )
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ])
            ->defaultPaginationPageOption(25);
    }

    public function mount()
    {
        $date = $this->report->day . ' ' . $this->report->month . ' ' . $this->report->year;
        $this->date = \Carbon\Carbon::parse($date);
    }

    public function render()
    {
        return view('livewire.daily-sales-report.transactions');
    }
}

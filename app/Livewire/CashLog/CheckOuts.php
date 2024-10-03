<?php

namespace App\Livewire\CashLog;

use Livewire\Component;
use App\Filament\Resources\ConferenceResource;
use App\Models\CashLog;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions as TableActions;
use Filament\Tables\Columns as TableColumns;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Forms\Components as FormComponents;
use Filament\Notifications\Notification;
use Filament\Tables\Filters;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Query\Builder;

class CheckOuts extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public CashLog $cashLog;

    public function table(Table $table): Table
    {
        return $table
            ->query(function() {
                $cashInDate = \Carbon\Carbon::parse($this->cashLog->date_cash_in);
                $cashOutDate = \Carbon\Carbon::parse($this->cashLog->date_cash_out);

                return \App\Models\DailySale::where('time_out_staff_id', $this->cashLog->user->staff->id)
                                            ->whereBetween('time_out', [$cashInDate, $cashOutDate]);
            })
            ->heading('Check Outs')
            ->headerActions([
                //
            ])
            ->columns([
                TableColumns\TextColumn::make('card_id')
                    ->label('Guest')
                    ->formatStateUsing(function($state, $record) {
                        return $record->card->code;
                    })
                    ->description(function($state, $record) {
                        return $record->name;
                    })
                    ->searchable(['name']),
                TableColumns\TextColumn::make('is_monthly')
                    ->label('Type')
                    ->formatStateUsing(function($record) {
                        if($record->is_flexi) {
                            return 'Flexi';
                        }

                        if($record->is_monthly) {
                            return 'Monthly';
                        }
                        
                        return 'Daily';
                    }),
                TableColumns\TextColumn::make('time_in_staff_id')
                    ->label('Staff In')
                    ->formatStateUsing(function($state, $record) {
                        return $record->staffIn->user->name;
                    }),
                TableColumns\TextColumn::make('time_in')
                    ->label('Time In')
                    ->formatStateUsing(function($state, $record) {
                        return $record->time_in_carbon->format(config('app.time_format'));
                    })
                    ->description(function($state, $record) {
                        return $record->time_in_carbon->format(config('app.date_format'));
                    }),
                TableColumns\TextColumn::make('time_out')
                    ->label('Time Out')
                    ->formatStateUsing(function($state, $record) {
                        return $record->time_out_carbon->format(config('app.time_format'));
                    })
                    ->description(function($state, $record) {
                        return $record->time_out_carbon->format(config('app.date_format'));
                    })
                    ->extraAttributes(function($record) {
                        if($record->time_out) {
                            return ['class' => 'block'];
                        } else {
                            return ['class' => 'hidden'];
                        }
                    }),
                TableColumns\TextColumn::make('created_at')
                    ->label('Total Hours')
                    ->formatStateUsing(function($state, $record) {
                        if($record->total_time) {
                            return $record->total_time . ' Hour/s';
                        } else {
                            return \Carbon\Carbon::parse($record->time_in)->diffForHumans();
                        }
                    }),
                TableColumns\TextColumn::make('discount')
                    ->label('Discount')
                    ->badge()
                    ->color(function($state, $record) {
                        return $state == 0 ? 'gray' : 'success';
                    })
                    ->formatStateUsing(function($state, $record) {
                        return $state == 0 ? 'No' : $state . '%';
                    }),
                TableColumns\TextColumn::make('mode_of_payment')
                    ->label('M.O.P.'),
                TableColumns\TextColumn::make('amount_paid')
                    ->label('Amount')
                    ->money('PHP'),
                TableColumns\TextColumn::make('amount_paid')
                    ->summarize(
                        TableColumns\Summarizers\Sum::make()
                            ->numeric(decimalPlaces: 2,)
                            ->money('PHP')
                            ->label('')
                    ),
            ])
            ->filters([
                Filters\SelectFilter::make('mode_of_payment')
                    ->options([
                        'Cash' => 'Cash',
                        'GCash' => 'GCash',
                        'Bank Transfer' => 'Bank Transfer'
                    ])
            ])
            ->filtersTriggerAction(
                fn (Action $action) => $action
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

    public function render()
    {
        return view('livewire.cash-log.check-outs');
    }
}

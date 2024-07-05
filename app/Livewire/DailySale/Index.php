<?php

namespace App\Livewire\DailySale;

use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use App\Models\DailySale;
use Filament\Tables\Columns as TableColumns;
use Filament\Forms\Components as FormComponents;
use Filament\Tables\Grouping\Group;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables;
use Filament\Resources\Components\Tab;
use Filament\Resources\Concerns\HasTabs;

class Index extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use HasTabs;
    use InteractsWithTable {
        makeTable as makeBaseTable;
    }

    protected function getTableQuery(): Builder
    {
        return DailySale::query();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->defaultGroup(
                Group::make('date')
                    ->date()
                    ->collapsible()
                    ->titlePrefixedWithLabel(false)
                    ->orderQueryUsing(fn (Builder $query, string $direction) => $query->orderBy('date', 'DESC'))
            )
            ->columns([
                TableColumns\TextColumn::make('time_in_staff_id')
                    ->label('Staff')
                    ->formatStateUsing(function($state, $record) {
                        return $record->staffIn->user->name;
                    }),
                TableColumns\TextColumn::make('card_id')
                    ->label('Guest')
                    ->formatStateUsing(function($state, $record) {
                        return $record->card->code . ' (' . $record->name . ')';
                    }),
                TableColumns\TextColumn::make('time_in')
                    ->label('Time In')
                    ->formatStateUsing(function($state, $record) {
                        return $record->time_in_carbon->format(config('app.date_time_format'));
                    }),
                TableColumns\TextColumn::make('time_out')
                    ->label('Time Out')
                    ->formatStateUsing(function($state, $record) {
                        return $record->time_out_carbon->format(config('app.date_time_format'));
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
                TableColumns\TextColumn::make('amount_paid')
                    ->label('Amount')
                    ->money('PHP'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('End Time')
                        ->form([
                            FormComponents\Select::make('mode_of_payment')
                                ->options([
                                    'Cash' => 'Cash',
                                    'GCash' => 'GCash',
                                    'Bank Transfer' => 'Bank Transfer'
                                ])
                                ->required()
                                ->native(false)
                        ])
                        ->action(function($data, $record) {
                            $record->time_out = \Carbon\Carbon::now();
                            $record->time_out_staff_id = auth()->user()->staff ? auth()->user()->staff->id : null;
                            $record->status = false;
                            $record->mode_of_payment = $data['mode_of_payment'];
                            $record->save();

                            if($record->is_monthly) {
                                $monthly = \App\Models\MonthlyUser::where('card_id', $record->card_id)->where('is_expired', false)->latest()->first();
                                
                                $monthly->is_active = false;
                                $monthly->save();
                            }

                            if($record->is_flexi) {
                                $flexi = \App\Models\FlexiUser::where('card_id', $record->card_id)->where('is_active', true)->latest()->first();

                                $flexi->recalculateTimeRemaining($record->id);

                                $flexi->checkRemainingTime();

                                $flexi->is_active = false;
                                $flexi->card_id = null;
                                $flexi->save();
                            }

                            $record->computeAmount();

                            $staff = $record->staffIn;
                            if($staff) {
                                $staff->createReport($record->id);
                            }

                            if($record->time_in_staff_id != $record->time_out_staff_id) {
                                $staffOut = $record->staffOut;
                                if($staffOut) {
                                    $staffOut->createReport($record->id);
                                }
                            }

                            Notification::make()
                                ->title('Daily user time ends.')
                                ->success()
                                ->send();

                            return redirect()->to(DailySaleResource::getUrl('view', ['record' => $record]));
                        })
                        ->modalWidth(MaxWidth::Medium)
                        ->visible(function($record) {
                            return $record->status ? true : false;
                        })
                ])
                ->icon('heroicon-o-ellipsis-horizontal'),
            ])
            ->bulkActions([
                // ...
            ])
            ->defaultPaginationPageOption(25);
    }

    public function getTabs(): array
    {
        return [
            'ongoing' => Tab::make('On-going')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', true))
                ->badge(DailySale::query()->where('status', true)->count()),
            'finished' => Tab::make('Finished')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', false)),
            'all' => Tab::make(),
        ];
    }

    protected function makeTable(): Table
    {
        return $this->makeBaseTable()
            ->query(fn (): Builder => $this->getTableQuery())
            ->modifyQueryUsing($this->modifyQueryWithActiveTab(...));

    }

    public function render()
    {
        return view('livewire.daily-sale.index');
    }
}

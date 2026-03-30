<?php

namespace App\Livewire\Conference;

use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;
use Livewire\Component;
use App\Filament\Resources\ConferenceResource;
use App\Models\Conference;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions as TableActions;
use Filament\Tables\Columns as TableColumns;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class Index extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public $viewList = true;
    public $activeTab = 'upcoming';
    protected $queryString = ['activeTab'];
    protected $conferenceQuery;

    protected $listeners = ['table-updated' => '$refresh'];

    public function changeView($type)
    {
        $this->viewList = $type == 'calendar';
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetTable();
        $this->dispatch('refresh-table');
    }

    protected function getTableQuery()
    {
        $query = Conference::query();

        if ($this->activeTab === 'upcoming') {
            $query->where('status', 'approve');
        } elseif ($this->activeTab === 'pending') {
            $query->where('status', 'pending');
        } elseif ($this->activeTab === 'past') {
            $query->where('status', 'finished');
        }

        return $query;
    }

    protected function applyDefaultSortingToTableQuery(Builder $query): Builder
    {
        if($this->activeTab === 'upcoming') {
            return $query
                ->orderBy('start_at', 'asc');
        }

        return $query
            ->orderBy('status')
            ->orderBy('start_at', 'desc');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TableColumns\TextColumn::make('id')
                    ->label('ID')
                    ->visible(auth()->user()->hasRole('Super Administrator')),
                TableColumns\TextColumn::make('host')
                    ->label('P.O.C'),
                TableColumns\TextColumn::make('members')
                    ->label('Total Guests'),
                TableColumns\TextColumn::make('start_at')
                    ->formatStateUsing(function($state, $record) {
                        return Carbon::parse($record->start_at)->format(config('app.date_format'));
                    })
                    ->description(function($state, $record) {
                        return Carbon::parse($record->start_at)->format(config('app.time_format'));
                    }),
                TableColumns\TextColumn::make('duration')
                    ->label('End at')
                    ->formatStateUsing(function($state, $record) {
                        return Carbon::parse($record->start_at)->addHours($record->duration)->format(config('app.date_format'));
                    })
                    ->description(function($state, $record) {
                        return Carbon::parse($record->start_at)->addHours($record->duration)->format(config('app.time_format'));
                    }),
                TableColumns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(function($state, $record) {
                        return ucfirst($state);
                    })
                    ->color(function($state, $record) {
                        if($state == 'approve') {
                            return 'success';
                        } elseif($state == 'pending') {
                            return 'warning';
                        } elseif($state == 'past') {
                            return 'gray';
                        } elseif($state == 'cancelled') {
                            return 'danger';
                        }
                    }),
            ])
            ->filters([
                // ...
            ])
            ->actions([])
            ->bulkActions([
                // ...
            ])
            ->recordUrl(fn($record) => ConferenceResource::getUrl('view', ['record' => $record]))
            ->defaultPaginationPageOption(25);
    }

    public function render(): View
    {
        return view('livewire.conference.index');
    }
}



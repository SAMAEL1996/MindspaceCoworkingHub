<?php

namespace App\Livewire\Conference;

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
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components as InfolistComponents;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Notifications\Notification;

class MembersList extends Component implements HasForms, HasInfolists, HasTable
{
    use InteractsWithInfolists;
    use InteractsWithTable;
    use InteractsWithForms;

    public Conference $conference;

    public function table(Table $table): Table
    {
        return $table
            ->relationship(fn (): HasMany => $this->conference->conferenceMembers())
            ->headerActions([
                //
            ])
            ->columns([
                TableColumns\TextColumn::make('card_id')
                    ->formatStateUsing(function($record) {
                        return \App\Models\Card::find($record->card_id)->code;
                    }),
                TableColumns\TextColumn::make('guest'),
                TableColumns\TextColumn::make('status')
                    ->badge()
                    ->color(function($record) {
                        return $record->status ? 'success' : 'gray';
                    })
                    ->formatStateUsing(function($record) {
                        return $record->status ? 'Active' : 'Inactive';
                    }),
                TableColumns\TextColumn::make('time_in')
                    ->label('Time In')
                    ->formatStateUsing(function($record) {
                        return \Carbon\Carbon::parse($record->time_in)->format(config('app.time_format'));
                    }),
                TableColumns\TextColumn::make('time_out')
                    ->label('Time Out')
                    ->formatStateUsing(function($record) {
                        return \Carbon\Carbon::parse($record->time_out)->format(config('app.time_format'));
                    }),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                TableActions\BulkAction::make('end-guest')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function($records) {
                        foreach($records as $record) {
                            $record->status = false;
                            $record->time_out = \Carbon\Carbon::now();
                            $record->save();
                        }

                        Notification::make()
                            ->title('Guest/s successfully ended.')
                            ->success()
                            ->send();

                        return redirect()->to(ConferenceResource::getUrl('view', ['record' => $this->conference]));
                    })
            ])
            ->checkIfRecordIsSelectableUsing(function($record) {
                return $record->status;
            });
    }

    public function render()
    {
        return view('livewire.conference.members-list');
    }
}

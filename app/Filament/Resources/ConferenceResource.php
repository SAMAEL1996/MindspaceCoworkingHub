<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConferenceResource\Pages;
use App\Filament\Resources\ConferenceResource\RelationManagers;
use App\Models\Conference;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns as TableColumns;
use Filament\Infolists\Infolist;

class ConferenceResource extends Resource
{
    protected static ?string $model = Conference::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'SALES';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Conference::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TableColumns\TextColumn::make('event'),
                TableColumns\TextColumn::make('date')
                    ->date(),
                TableColumns\TextColumn::make('start_at')
                    ->label('Time')
                    ->formatStateUsing(function($record) {
                        return $record->start_at . ' to ' . $record->end_at;
                    }),
                TableColumns\TextColumn::make('members'),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema(Conference::getInfolist());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\IndexConferences::route('/'),
            // 'index' => Pages\ListConferences::route('/'),
            'create' => Pages\CreateConference::route('/create'),
            'view' => Pages\ViewConference::route('/{record}'),
            'edit' => Pages\EditConference::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view conferences');
    }
}

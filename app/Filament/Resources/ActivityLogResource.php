<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Filament\Resources\ActivityLogResource\RelationManagers;
use App\Models\Activity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns as TableColumns;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationGroup = 'ADMIN';

    public static function getEloquentQuery(): Builder
    {
        return Activity::query()->orderBy('created_at', 'desc');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TableColumns\TextColumn::make('subject_type')
                    ->label('User')
                    ->formatStateUsing(function($record) {
                        return $record->subject->name;
                    }),
                TableColumns\TextColumn::make('description'),
                TableColumns\TextColumn::make('causer_id')
                    ->label('Staff')
                    ->formatStateUsing(function($record) {
                        return $record->causer ? $record->causer->name : '';
                    }),
                TableColumns\TextColumn::make('created_at')
                    ->label('Date')
                    ->formatStateUsing(function($record) {
                        return \Carbon\Carbon::parse($record->created_at)->format(config('app.date_time_format'));
                    }),
            ])
            ->filters([])
            ->actions([])
            ->bulkActions([])
            ->recordUrl(null);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
            'create' => Pages\CreateActivityLog::route('/create'),
            'edit' => Pages\EditActivityLog::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view activities');
    }
}

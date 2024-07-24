<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ErrorLogResource\Pages;
use App\Filament\Resources\ErrorLogResource\RelationManagers;
use App\Models\ErrorLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns as TableColumns;

class ErrorLogResource extends Resource
{
    protected static ?string $model = ErrorLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-x-mark';
    protected static ?string $navigationGroup = 'ADMIN';

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
                TableColumns\TextColumn::make('created_at')
                    ->label('Date')
                    ->formatStateUsing(function($state, $record) {
                        return \Carbon\Carbon::parse($state)->format(config('app.date_time_format'));
                    }),
                TableColumns\TextColumn::make('staff.user.name')
                    ->label('Staff')
                    ->searchable(),
                TableColumns\TextColumn::make('subjectable_type')
                    ->label('Type')
                    ->formatStateUsing(function($state, $record) {
                        return str_replace('App\\Models\\', '', $state);
                    })
                    ->searchable(),
                TableColumns\TextColumn::make('subjectable')
                    ->label('Subject')
                    ->formatStateUsing(function($record) {
                        return $record->subjectable->name;
                    }),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListErrorLogs::route('/'),
            'create' => Pages\CreateErrorLog::route('/create'),
            'view' => Pages\ViewErrorLog::route('/{record}'),
            'edit' => Pages\EditErrorLog::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('Super Administrator');
    }
}

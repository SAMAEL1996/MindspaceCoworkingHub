<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Filament\Resources\ExpenseResource\RelationManagers;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns as TableColumns;
use Filament\Forms\Components as FormComponents;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';
    protected static ?string $navigationGroup = 'ADMIN';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FormComponents\TextInput::make('quantity')
                    ->numeric()
                    ->required(),
                FormComponents\TextInput::make('item')
                    ->required(),
                FormComponents\TextInput::make('amount')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TableColumns\TextColumn::make('quantity'),
                TableColumns\TextColumn::make('item'),
                TableColumns\TextColumn::make('amount')
                    ->money('PHP'),
                TableColumns\TextColumn::make('created_at')
                    ->label('Date')
                    ->formatStateUsing(function($state, $record) {
                        return \Carbon\Carbon::parse($state)->format(config('app.date_time_format'));
                    }),
            ])
            ->filters([])
            ->actions([])
            ->bulkActions([])
            ->recordUrl('');
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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'view' => Pages\ViewExpense::route('/{record}'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }

    public static function getNavigationItems(): array
    {
        return [
            \Filament\Navigation\NavigationItem::make(static::getNavigationLabel())
                ->group(static::$navigationGroup ?? null)
                ->parentItem(static::$navigationParentItem ?? null)
                ->icon(static::$navigationIcon ?? null)
                ->isActiveWhen(fn () => request()->routeIs(static::getRouteBaseName() . '.*'))
                ->sort(static::$navigationSort ?? 0)
                ->url(static::getUrl())
                ->visible(auth()->user()->hasRole('Super Administrator')),
        ];
    }
    
    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('Super Administrator');
    }
}

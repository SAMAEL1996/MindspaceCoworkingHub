<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashLogResource\Pages;
use App\Filament\Resources\CashLogResource\RelationManagers;
use App\Models\CashLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns as TableColumns;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components as InfolistComponents;

class CashLogResource extends Resource
{
    protected static ?string $model = CashLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

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
                TableColumns\TextColumn::make('user_id')
                    ->label('User')
                    ->formatStateUsing(function($state, $record) {
                        return $record->user->name;
                    }),
                TableColumns\TextColumn::make('cash_in')
                    ->label('Cash In')
                    ->money('PHP')
                    ->description(function($record) {
                        return \Carbon\Carbon::parse($record->date_cash_in)->format(config('app.date_time_format'));
                    }),
                TableColumns\TextColumn::make('cash_out')
                    ->label('Cash Out')
                    ->money('PHP')
                    ->description(function($record) {
                        if(!$record->date_cash_out) {
                            return 'NO CASH OUT YET';
                        }
                        return \Carbon\Carbon::parse($record->date_cash_out)->format(config('app.date_time_format'));
                    }),
                TableColumns\TextColumn::make('total_sales')
                    ->label('Total Sales')
                    ->money('PHP'),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ])
            ->defaultPaginationPageOption(25)
            ->defaultSort('created_at', 'desc')
            ->recordUrl(function($record) {
                if(auth()->user()->hasRole('Super Administrator')) {
                    return CashLogResource::getUrl('view', ['record' => $record]);
                }

                if($record->user_id != auth()->user()->id) {
                    return false;
                }

                return $record->status ? CashLogResource::getUrl('view', ['record' => $record]) : false;
            });
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistComponents\Section::make('Information')
                    ->schema([
                        InfolistComponents\TextEntry::make('user.name')
                            ->label('User / Staff'),
                        InfolistComponents\TextEntry::make('user.email')
                            ->label('Email'),
                        InfolistComponents\TextEntry::make('date_cash_in')
                            ->label('Date Cash-In')
                            ->formatStateUsing(function($record) {
                                return \Carbon\Carbon::parse($record->date_cash_in)->format(config('app.date_time_format'));
                            }),
                        InfolistComponents\TextEntry::make('date_cash_out')
                            ->label('Date Cash-Out')
                            ->formatStateUsing(function($record) {
                                return \Carbon\Carbon::parse($record->date_cash_out)->format(config('app.date_time_format'));
                            }),
                        InfolistComponents\TextEntry::make('cash_in')
                            ->label('Amount Cash-In')
                            ->money('PHP'),
                        InfolistComponents\TextEntry::make('cash_out')
                            ->label('Amount Cash-Out')
                            ->money('PHP'),
                        InfolistComponents\TextEntry::make('credit')
                            ->label('Credit')
                            ->money('PHP'),
                        InfolistComponents\TextEntry::make('debit')
                            ->label('Debit')
                            ->money('PHP'),
                    ])
                    ->columns(2)
                    ->columnSpan('full'),
                InfolistComponents\Section::make('Items')
                    ->schema([
                        InfolistComponents\ViewEntry::make('items')
                            ->label('')
                            ->view('infolists.components.cash-log.items')
                    ]),
                InfolistComponents\ViewEntry::make('Check Outs')
                    ->label('')
                    ->view('infolists.components.cash-log.check-outs')
                    ->visible(auth()->user()->hasRole('Super Administrator'))
                    ->columnSpan('full'),
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
            'index' => Pages\ListCashLogs::route('/'),
            'create' => Pages\CreateCashLog::route('/create'),
            'view' => Pages\ViewCashLog::route('/{record}'),
            'edit' => Pages\EditCashLog::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DailySalesReportResource\Pages;
use App\Filament\Resources\DailySalesReportResource\RelationManagers;
use App\Models\Sale;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns as TableColumns;
use Filament\Tables\Grouping\Group;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components as InfolistComponents;

class DailySalesReportResource extends Resource
{
    protected static ?string $model = Sale::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', 'daily')->orderBy('year', 'DESC');
    }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Daily Sales Report';
    protected static ?string $navigationGroup = 'REPORTS';

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
            ->defaultGroup(
                Group::make('month')
                    ->collapsible()
                    ->titlePrefixedWithLabel(false)
                    ->orderQueryUsing(fn (Builder $query, string $direction) =>
                        $query->orderBy(\DB::raw('
                            CASE 
                                WHEN month = "January" THEN 1
                                WHEN month = "February" THEN 2
                                WHEN month = "March" THEN 3
                                WHEN month = "April" THEN 4
                                WHEN month = "May" THEN 5
                                WHEN month = "June" THEN 6
                                WHEN month = "July" THEN 7
                                WHEN month = "August" THEN 8
                                WHEN month = "September" THEN 9
                                WHEN month = "October" THEN 10
                                WHEN month = "November" THEN 11
                                WHEN month = "December" THEN 12
                            END
                        '), 'desc'))
            )
            ->columns([
                TableColumns\TextColumn::make('day')
                    ->alignCenter(),
                TableColumns\TextColumn::make('month')
                    ->alignCenter(),
                TableColumns\TextColumn::make('total_daily_users')
                    ->label('Total Daily Pass')
                    ->alignCenter(),
                TableColumns\TextColumn::make('total_flexi_users')
                    ->label('Total Flexi Pass')
                    ->alignCenter(),
                TableColumns\TextColumn::make('total_monthly_users')
                    ->label('Total Monthly Pass')
                    ->alignCenter(),
                TableColumns\TextColumn::make('total_conference_users')
                    ->label('Total Conference Booked')
                    ->alignCenter(),
                TableColumns\TextColumn::make('total_sales')
                    ->money('PHP')
                    ->alignCenter()
                    ->visible(auth()->user()->hasRole('Super Administrator')),
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
            ->defaultSort(function ($query) {
                return $query->select('*', \DB::raw("STR_TO_DATE(CONCAT(year, '-', LPAD(month, 2, '0'), '-', LPAD(day, 2, '0')), '%Y-%m-%d') as combined_date"))
                            ->orderBy('combined_date', 'asc');
            });
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistComponents\Section::make('Transactions')
                    ->schema([
                        InfolistComponents\ViewEntry::make('id')
                            ->label('')
                            ->view('infolists.components.daily-sales-report.transactions'),
                    ])
                    ->columnSpan('full')
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
            'index' => Pages\ListDailySalesReports::route('/'),
            'create' => Pages\CreateDailySalesReport::route('/create'),
            'view' => Pages\ViewDailySalesReport::route('/{record}'),
            'edit' => Pages\EditDailySalesReport::route('/{record}/edit'),
        ];
    }
}

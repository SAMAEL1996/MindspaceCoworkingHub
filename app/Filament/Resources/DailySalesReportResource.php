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

class DailySalesReportResource extends Resource
{
    protected static ?string $model = Sale::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', 'daily');
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
            })
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
            'index' => Pages\ListDailySalesReports::route('/'),
            'create' => Pages\CreateDailySalesReport::route('/create'),
            'edit' => Pages\EditDailySalesReport::route('/{record}/edit'),
        ];
    }
}

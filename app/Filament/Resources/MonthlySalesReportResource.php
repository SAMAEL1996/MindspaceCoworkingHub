<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MonthlySalesReportResource\Pages;
use App\Filament\Resources\MonthlySalesReportResource\RelationManagers;
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

class MonthlySalesReportResource extends Resource
{
    protected static ?string $model = Sale::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', 'monthly')->orderBy('year', 'DESC');
    }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Monthly Sales Report';
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
                Group::make('year')
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
                TableColumns\TextColumn::make('month'),
                TableColumns\TextColumn::make('total_daily_users')
                    ->label('Total Daily Pass'),
                TableColumns\TextColumn::make('total_flexi_users')
                    ->label('Total Flexi Pass'),
                TableColumns\TextColumn::make('total_monthly_users')
                    ->label('Total Monthly Pass'),
                TableColumns\TextColumn::make('total_conference_users')
                    ->label('Total Conference Booked'),
                TableColumns\TextColumn::make('total_sales')
                    ->money('PHP')
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
            'index' => Pages\ListMonthlySalesReports::route('/'),
            'create' => Pages\CreateMonthlySalesReport::route('/create'),
            'view' => Pages\ViewMonthlySalesReport::route('/{record}'),
            'edit' => Pages\EditMonthlySalesReport::route('/{record}/edit'),
        ];
    }
}

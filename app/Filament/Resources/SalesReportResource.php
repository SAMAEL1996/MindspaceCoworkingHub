<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesReportResource\Pages;
use App\Filament\Resources\SalesReportResource\RelationManagers;
use App\Models\SaleReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns as TableColumns;
use Filament\Notifications\Notification;
use Rap2hpoutre\FastExcel\FastExcel;

class SalesReportResource extends Resource
{
    protected static ?string $model = SaleReport::class;

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = SaleReport::query();

        if($user->hasRole('Staff')) {
            $query = SaleReport::where('staff_id', $user->staff->id);
        }

        return $query;
    }

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'STAFF PROFILE';

    public static function getNavigationLabel(): string
    {
        return auth()->user()->is_staff ? 'My Sales' : 'Sales';
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
                TableColumns\TextColumn::make('id')
                    ->label('ID'),
                TableColumns\TextColumn::make('staff_id')
                    ->label('Staff')
                    ->formatStateUsing(function($record) {
                        return $record->staff->user->name;
                    }),
                TableColumns\TextColumn::make('staff_customer')
                    ->formatStateUsing(function($state, $record) {
                        return $state ? 'Yes' : 'No';
                    }),
                TableColumns\TextColumn::make('date')
                    ->date(),
                TableColumns\TextColumn::make('staff_sales')
                    ->label('Staff Sales'),
                TableColumns\TextColumn::make('total_sales')
                    ->label('Total Sales'),
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
            ->recordUrl(false);
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
            'index' => Pages\ListSalesReports::route('/'),
            'create' => Pages\CreateSalesReport::route('/create'),
            'view' => Pages\ViewSalesReport::route('/{record}'),
            'edit' => Pages\EditSalesReport::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return !auth()->user()->is_staff;
        return auth()->user()->can('view sale-reports');
    }
}

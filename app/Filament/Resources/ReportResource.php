<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Filament\Resources\ReportResource\RelationManagers;
use App\Models\Report;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns as TableColumns;
use Filament\Notifications\Notification;
use Rap2hpoutre\FastExcel\FastExcel;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';
    protected static ?string $navigationGroup = 'STAFF PROFILE';

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
                Actions\ActionGroup::make([
                    Actions\Action::make('export-report')
                        ->requiresConfirmation()
                        ->action(function($record) {
                            $staff = $record->staff;
                            $attendance = $record->attendance;

                            $sales = $staff->dailySales()->whereBetween('created_at', [$attendance->check_in_carbon, $attendance->check_out_carbon])->get();
                            $filename = $attendance->check_in_carbon->format('Y-m-d') . '-' . $attendance->check_out_carbon->format('Y-m-d') . '-staff_' . $attendance->staff_id . '.xlsx';
                            
                            $export = $staff->exportSales($sales, $filename);

                            Notification::make()
                                ->title('Report successfully export.')
                                ->success()
                                ->send();

                            return (new FastExcel($export))->download($filename);
                        })
                        ->visible(auth()->user()->can('export reports'))
                        ->icon('heroicon-o-arrow-down-tray')
                ])
                ->icon('heroicon-o-ellipsis-horizontal'),
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
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'view' => Pages\ViewReport::route('/{record}'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return !auth()->user()->is_staff;
        return auth()->user()->can('view reports');
    }
}

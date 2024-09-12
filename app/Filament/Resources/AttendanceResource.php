<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns as TableColumns;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Rap2hpoutre\FastExcel\FastExcel;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;
use Filament\Tables\Filters;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components as FormComponents;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions as TableActions;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'STAFF PROFILE';

    public static function getNavigationLabel(): string
    {
        return auth()->user()->is_staff ? 'My Attendance' : 'Attendances';
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        if($user->hasRole('Super Administrator')) {
            $query = Attendance::query();
        } else {
            $query = Attendance::where('staff_id', $user->staff->id);
        }

        return $query;
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
                TableColumns\TextColumn::make('staff_id')
                    ->label('Staff')
                    ->formatStateUsing(function($record) {
                        return $record->staff->user->name;
                    })
                    ->visible(auth()->user()->can('export attendances')),
                TableColumns\TextColumn::make('check_in')
                    ->label('Check In')
                    ->formatStateUsing(function($state, $record) {
                        return $record->check_in_carbon->format(config('app.time_format'));
                    })
                    ->description(function($state, $record) {
                        return $record->check_in_carbon->format(config('app.date_format'));
                    }),
                TableColumns\TextColumn::make('check_out')
                    ->label('Check In')
                    ->formatStateUsing(function($state, $record) {
                        return $record->check_out ? $record->check_out_carbon->format(config('app.time_format')) : null;
                    })
                    ->description(function($state, $record) {
                        return $record->check_out ? $record->check_out_carbon->format(config('app.date_format')) : null;
                    })
                    ->placeholder('On-going Shift'),
                TableColumns\TextColumn::make('created_at')
                    ->label('Total Time')
                    ->formatStateUsing(function($record) {
                        if($record->check_out) {
                            return Carbon::parse($record->check_in)->diff(Carbon::parse($record->check_out))->format('%H:%I:%S');
                        } else {
                            return Carbon::parse($record->check_in)->diffForHumans();
                        }
                    }),
                TableColumns\TextColumn::make('approve_overtime')
                    ->label('Approve OT')
                    ->badge()
                    ->formatStateUsing(function($state, $record) {
                        return $state ? 'Yes' : 'No';
                    })
                    ->color(function($state, $record) {
                        return $state ? 'success' : 'gray';
                    }),
                TableColumns\TextColumn::make('total_overtime_hours')
                    ->label('Total OT')
                    ->formatStateUsing(function($record) {
                        return $record->total_overtime_hours . ' hour/s';
                    }),
                TableColumns\TextColumn::make('restday_overtime')
                    ->label('Rest-day OT')
                    ->badge()
                    ->formatStateUsing(function($state, $record) {
                        return $state ? 'Yes' : 'No';
                    })
                    ->color(function($state, $record) {
                        return $state ? 'success' : 'gray';
                    }),
            ])
            ->filters([
                Filters\Filter::make('created_at')
                    ->form([
                        Fieldset::make('Check In')
                            ->schema([
                                DatePicker::make('from'),
                                DatePicker::make('to'),
                            ])
                            ->columns(1),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->filtersTriggerAction(
                fn (TableActions\Action $action) => $action
                    ->button()
                    ->label('Filter'),
            )
            ->headerActions([
                ExportAction::make()
                    ->exports([
                        ExcelExport::make()
                            ->withColumns([
                                Column::make('staff_id')
                                    ->heading('Staff')
                                    ->formatStateUsing(function($record) {
                                        return $record->staff->user->name;
                                    }),
                                Column::make('check_in')
                                    ->heading('Check In')
                                    ->formatStateUsing(function($record) {
                                        return $record->check_in_carbon->format(config('app.date_time_format'));
                                    }),
                                Column::make('check_out')
                                    ->heading('Check Out')
                                    ->formatStateUsing(function($record) {
                                        return $record->check_out_carbon->format(config('app.date_time_format'));
                                    }),
                                Column::make('created_at')
                                    ->heading('Total Time')
                                    ->formatStateUsing(function($record) {
                                        return Carbon::parse($record->check_in)->diff(Carbon::parse($record->check_out))->format('%H:%I:%S');
                                    }),
                                Column::make('approve_overtime')
                                    ->heading('Approve OT')
                                    ->formatStateUsing(function($record) {
                                        return $record->approve_overtime ? 'Yes' : 'No';
                                    }),
                                Column::make('total_overtime_hours')
                                    ->heading('Total OT')
                                    ->formatStateUsing(function($record) {
                                        return $record->total_overtime_hours . ' hour/s';
                                    }),
                                Column::make('restday_overtime')
                                    ->heading('Rest-day OT')
                                    ->formatStateUsing(function($record) {
                                        return $record->restday_overtime ? 'Yes' : 'No';
                                    }),
                            ])
                            ->fromTable()
                            ->withFilename('attendances-'.date('Y-m-d'))
                            ->withWriterType(\Maatwebsite\Excel\Excel::CSV)
                    ])
                    ->visible(function() {
                        return auth()->user()->can('export attendances');
                    })
                    ->icon('heroicon-o-arrow-up-on-square'),
            ])
            ->actions([
                Actions\ActionGroup::make([
                        Actions\Action::make('generate-report')
                            ->requiresConfirmation()
                            ->modalDescription('Are you sure you would like to do this? This cannot be generated again.')
                            ->action(function($record) {
                                $staff = $record->staff;

                                $sales = $staff->dailySales()->whereBetween('created_at', [$record->check_in_carbon, $record->check_out_carbon])->get();
                                $filename = $record->check_in_carbon->format('Y-m-d') . '-' . $record->check_out_carbon->format('Y-m-d') . '-staff_' . $record->staff_id . '.xlsx';
                                
                                $export = $staff->exportSales($sales, $filename);

                                Notification::make()
                                    ->title('Generate report successfully download.')
                                    ->success()
                                    ->send();

                                return (new FastExcel($export))->download($filename);
                            })
                            ->visible(function($record) {
                                return auth()->user()->can('generate daily-sales');
                            })
                            ->icon('heroicon-o-arrow-down-tray'),
                        Actions\Action::make('approve-overtime')
                            ->label('Approve OT')
                            ->form([
                                FormComponents\TextInput::make('total_overtime_hours')
                                    ->label('Total OT Hours')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                            ])
                            ->modalHeading('Approve Over-Time')
                            ->modalWidth(MaxWidth::Medium)
                            ->action(function($data, $record) {
                                $record->approve_overtime = true;
                                $record->total_overtime_hours = (int)$data['total_overtime_hours'];
                                $record->save();

                                Notification::make()
                                    ->title('Staff OT approved.')
                                    ->success()
                                    ->send();

                                return $record;
                            })
                            ->visible(function($record) {
                                return auth()->user()->hasRole('Super Administrator');
                            })
                            ->icon('heroicon-o-check-circle'),
                        Actions\Action::make('restday-overtime')
                            ->label('Staff Rest-day OT')
                            ->requiresConfirmation()
                            ->modalHeading('Staff Rest-day OT')
                            ->action(function($record) {
                                $record->restday_overtime = true;
                                $record->save();

                                Notification::make()
                                    ->title('Staff rest-day OT approved.')
                                    ->success()
                                    ->send();

                                return $record;
                            })
                            ->visible(function($record) {
                                return auth()->user()->hasRole('Super Administrator');
                            })
                            ->icon('heroicon-o-shield-check')
                    ])
                    ->icon('heroicon-o-ellipsis-horizontal'),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(25)
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'view' => Pages\ViewAttendance::route('/{record}'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view attendances');
    }
}

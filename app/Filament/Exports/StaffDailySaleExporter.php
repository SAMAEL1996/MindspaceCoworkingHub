<?php

namespace App\Filament\Exports;

use App\Models\DailySale;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Exports\Enums\ExportFormat;

class StaffDailySaleExporter extends Exporter
{
    protected static ?string $model = DailySale::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('date'),
            ExportColumn::make('card_id')
                ->label('Card ID'),
            ExportColumn::make('name'),
            ExportColumn::make('time_in'),
            ExportColumn::make('time_out'),
            ExportColumn::make('time_in_staff_id')
                ->label('Staff in'),
            ExportColumn::make('time_out_staff_id')
                ->label('Staff out'),
            ExportColumn::make('discount'),
            ExportColumn::make('total_time'),
            ExportColumn::make('amount_paid'),
        ];
    }

    public function getFormats(): array
    {
        return [
            ExportFormat::Csv,
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your staff daily sale export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}

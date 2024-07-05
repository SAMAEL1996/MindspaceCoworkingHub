<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets;

class Calendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.calendar';
    
    protected static bool $isDiscovered = false;

    public static function getWidgets(): array
    {
        return [
            Widgets\CalendarWidget::class,
        ];
    }
}

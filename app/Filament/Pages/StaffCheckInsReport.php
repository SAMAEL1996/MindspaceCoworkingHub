<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class StaffCheckInsReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.staff-check-ins-report';
    
    protected static bool $isDiscovered = false;
}

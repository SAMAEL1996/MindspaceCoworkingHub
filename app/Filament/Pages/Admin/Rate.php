<?php

namespace App\Filament\Pages\Admin;

use Filament\Pages\Page;

class Rate extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.admin.rate';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('Super Administrator');
    }
}

<?php

namespace App\Filament\Pages;

use Filament\Pages\SimplePage;
use Filament\Support\Enums\MaxWidth;

class BookConference extends SimplePage
{
    protected static string $view = 'booking';

    protected static ?string $title = 'Meeting Room Booking Form';

    protected function getLayoutData(): array
    {
        return [
            'hasTopbar' => false,
            'maxWidth' => MaxWidth::ExtraLarge,
        ];
    }
}

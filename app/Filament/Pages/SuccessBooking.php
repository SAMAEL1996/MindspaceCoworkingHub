<?php

namespace App\Filament\Pages;

use Filament\Pages\SimplePage;
use Filament\Support\Enums\MaxWidth;

class SuccessBooking extends SimplePage
{
    protected static string $view = 'success-booking';

    protected static ?string $title = 'Meeting Room Booking Sucessful';

    protected function getLayoutData(): array
    {
        return [
            'hasTopbar' => false,
            'maxWidth' => MaxWidth::ExtraLarge,
        ];
    }
}

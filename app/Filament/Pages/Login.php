<?php

namespace App\Filament\Pages;

use App\Models\UserLocation;
use Filament\Pages\Page;
use Filament\Pages\Auth\Login as BaseLogin;
use Stevebauman\Location\Facades\Location;

class Login extends BaseLogin
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }
}

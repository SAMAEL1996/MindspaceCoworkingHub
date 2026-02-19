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

    public function authenticate(): ?\Filament\Http\Responses\Auth\Contracts\LoginResponse
    {
        $response = parent::authenticate();

        if (auth()->check()) {
            $ip = request()->ip();
            $location = Location::get($ip);

            UserLocation::create([
                'user_id' => auth()->user()->id,
                'country' => $location?->countryName,
                'city' => $location?->cityName,
                'latitude' => $location?->latitude,
                'longitude' => $location?->longitude
            ]);
        }

        return $response;
    }
}

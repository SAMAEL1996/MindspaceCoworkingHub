<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use App\Filament\Pages\Profile;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // FilamentView::registerRenderHook(
        //     PanelsRenderHook::USER_MENU_BEFORE,
        //     fn (): string => 'Logged In As:  '. filament()->auth()->user()->name,
        // );

        Filament::registerNavigationGroups([
            'SALES',
            'STAFF PROFILE',
            'ADMIN',
            'REPORTS',
        ]);

        FilamentAsset::register([
            Js::make('custom-script', __DIR__ . '/../../resources/js/global.js'),
        ]);

        Filament::serving(function () {

            Filament::registerRenderHook(
                PanelsRenderHook::BODY_START,
                function (): ?string {
                    if (! filament()->auth()->check()) {
                        return null;
                    }

                    return Livewire::mount('filament-response');
                }
            );

        });
    }
}

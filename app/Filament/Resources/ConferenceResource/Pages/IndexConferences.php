<?php

namespace App\Filament\Resources\ConferenceResource\Pages;

use App\Filament\Resources\ConferenceResource;
use Filament\Resources\Pages\Page;

class IndexConferences extends Page
{
    protected static string $resource = ConferenceResource::class;

    protected static ?string $title = 'Conferences';

    protected static string $view = 'filament.resources.conference-resource.pages.index-conferences';

    public function getBreadcrumbs(): array
    {
        return [
            
        ];
    }
}

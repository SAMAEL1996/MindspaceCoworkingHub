<?php

namespace App\Filament\Resources\CardResource\Pages;

use App\Filament\Resources\CardResource;
use Filament\Resources\Pages\Page;
use App\Models\Card;

class CustomEditCard extends Page
{
    public Card $record;
    protected static string $resource = CardResource::class;

    protected static string $view = 'filament.resources.card-resource.pages.custom-edit-card';

    public function getTitle(): string
    {
        return 'Edit Card: ' . $this->record->code;
    }

    public function getSubheading(): string
    {
        return 'Type: ' . $this->record->type;
    }
}

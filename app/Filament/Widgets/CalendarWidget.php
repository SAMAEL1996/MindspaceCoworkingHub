<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\Conference;
use App\Filament\Resources\ConferenceResource;

class CalendarWidget extends FullCalendarWidget
{
    protected static bool $isDiscovered = false;
    
    public function fetchEvents(array $fetchInfo): array
    {
        return Conference::query()
                ->whereIn('status', ['approve', 'finished'])
                ->get()
                ->map(
                    fn (Conference $conference) => [
                        'id' => $conference->uid,
                        'title' => $conference->event . ' - ' . $conference->host,
                        'color' => $conference->status == 'approve' ? '#239B56' : '#EB984E',
                        'start' => \Carbon\Carbon::parse($conference->start_at),
                        'end' => \Carbon\Carbon::parse($conference->start_at)->addHours($conference->duration),
                        'url' => ConferenceResource::getUrl(name: 'view', parameters: ['record' => $conference])
                    ]
                )
                ->all();
    }
}

<?php

namespace App\Filament\Resources\ConferenceResource\Pages;

use App\Filament\Resources\ConferenceResource;
use App\Models\Conference;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateConference extends CreateRecord
{
    protected static string $resource = ConferenceResource::class;

    protected static bool $canCreateAnother = false;

    protected static ?string $title = 'Create Booking';

    protected int $createdConferenceCount = 1;

    protected function handleRecordCreation(array $data): Model
    {
        $scheduleStartTimes = Conference::resolveScheduleStartTimes($data);

        if (blank($scheduleStartTimes)) {
            Notification::make()
                ->title('Please select at least one date.')
                ->danger()
                ->send();

            $this->halt();
        }

        $pastDates = collect($scheduleStartTimes)
            ->filter(fn (Carbon $timeOfArrival) => $timeOfArrival->copy()->subHour()->isPast())
            ->map(fn (Carbon $timeOfArrival) => $timeOfArrival->format('M d, Y'))
            ->values();

        if ($pastDates->isNotEmpty()) {
            Notification::make()
                ->title('Some selected dates are already past.')
                ->body($pastDates->join(', '))
                ->danger()
                ->send();

            $this->halt();
        }

        $conflictingDates = collect($scheduleStartTimes)
            ->filter(function (Carbon $timeOfArrival) use ($data) {
                $timeOfLeave = $timeOfArrival->copy()->addHours((int) $data['duration']);
                $checkStart = $timeOfArrival->copy()->subMinutes(30);
                $checkEnd = $timeOfLeave->copy()->addMinutes(30);

                return Conference::getCheckTimeSchedules($checkStart, $checkEnd);
            })
            ->map(fn (Carbon $timeOfArrival) => $timeOfArrival->format('M d, Y'))
            ->values();

        if ($conflictingDates->isNotEmpty()) {
            Notification::make()
                ->title('One or more selected dates and times are already taken.')
                ->body($conflictingDates->join(', '))
                ->danger()
                ->send();

            $this->halt();
        }

        $rate = Conference::getRateAmount((int) $data['package'], (int) $data['duration']);

        $conferences = collect($scheduleStartTimes)
            ->map(function (Carbon $timeOfArrival) use ($data, $rate) {
                return static::getModel()::create([
                    'package_id' => (int) $data['package'],
                    'book_by' => auth()->user()->id,
                    'start_at' => $timeOfArrival->copy(),
                    'duration' => (int) $data['duration'],
                    'event' => $data['event'],
                    'members' => $data['members'],
                    'host' => $data['host'],
                    'contact_no' => $data['contact_no'],
                    'status' => 'approve',
                    'amount' => $rate,
                ]);
            });

        $this->createdConferenceCount = $conferences->count();

        return $conferences->first();
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label('Book'),
            $this->getCancelFormAction(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return $this->createdConferenceCount > 1
            ? 'Recurring conferences successfully booked.'
            : 'Conference successfully booked.';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}

<?php

namespace App\Filament\Resources\ConferenceResource\Pages;

use App\Filament\Resources\ConferenceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Conference;
use Filament\Notifications\Notification;

class CreateConference extends CreateRecord
{
    protected static string $resource = ConferenceResource::class;

    protected static bool $canCreateAnother = false;

    protected static ?string $title = 'Create Booking';

    protected function handleRecordCreation(array $data): Model
    {
        $timeOfArrival = Carbon::parse($data['date'] . ' ' . $data['time']);
        if($timeOfArrival->copy()->subHour()->isPast()) {
            Notification::make()
                ->title('Date is already past.')
                ->danger()
                ->send();

            $this->halt();
        }
        $timeOfLeave = $timeOfArrival->copy()->addHours((int)$data['duration']);
        $checkStart = $timeOfArrival->copy()->subMinutes(30);
        $checkEnd = $timeOfLeave->copy()->addMinutes(30);

        $checkDateTime = Conference::getCheckTimeSchedules($checkStart, $checkEnd);

        if($checkDateTime) {
            Notification::make()
                ->title('Date and time is taken.')
                ->danger()
                ->send();

            $this->halt();
        }

        $package = \App\Library\Helper::getConferencePackageInfo((int)$data['package']);
        $rate = \App\Library\Helper::getConferencePackageRate((int)$data['package'], $data['duration']);

        $conference = static::getModel()::create([
            'package_id' => $package['id'],
            'book_by' => auth()->user()->id,
            'start_at' => $timeOfArrival->copy(),
            'duration' => $data['duration'],
            'event' => $data['event'],
            'members' => $data['members'],
            'host' => $data['host'],
            'contact_no' => $data['contact_no'],
            'status' => 'approve',
            'amount' => $rate['price']
        ]);

        return $conference;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Book'),
            $this->getCancelFormAction(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Conference successfully book.';
    }

    public function getBreadcrumbs(): array
    {
        return [
            
        ];
    }
}

<?php

namespace App\Livewire\PublicPages;

use Livewire\Component;
use App\Models\Conference;
use Filament\Forms\Components as FormComponents;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use App\Filament\Pages\SuccessBooking;

class BookConference extends Component implements HasForms
{
    use InteractsWithForms;
    
    public ?array $data = [];

    public $termsCondition;
    
    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FormComponents\TextInput::make('event')
                    ->required(),
                FormComponents\TextInput::make('host')
                    ->label('Name of POC')
                    ->required(),
                FormComponents\TextInput::make('email')
                    ->email()
                    ->required(),
                FormComponents\TextInput::make('contact_no')
                    ->tel()
                    ->required(),
                FormComponents\TextInput::make('members')
                    ->label('Total # of guests including POC')
                    ->numeric()
                    ->minValue(1)
                    ->required(),
                FormComponents\Fieldset::make('Schedule')
                    ->schema([
                        FormComponents\DatePicker::make('date')
                            ->label('Date')
                            ->required()
                            ->displayFormat('d F Y')
                            ->timezone('Asia/Manila')
                            ->minDate(now())
                            ->native(false),
                        FormComponents\Select::make('time')
                            ->label('Time')
                            ->required()
                            ->options(\App\Library\Helper::get12HourTimeSelectOptions())
                            ->placeholder('Select Time')
                            ->native(false),
                    ])
                    ->columnSpan('full'),
                FormComponents\TextInput::make('duration')
                    ->label('Hours of Stay (in hours)')
                    ->numeric()
                    ->minValue(1)
                    ->default(3)
                    ->required(),
                FormComponents\ViewField::make('terms')
                    ->label('')
                    ->view('forms.components.conference-booking.terms')
            ])
            ->statePath('data');
    }

    public function create()
    {
        $data = $this->form->getState();

        if(!$this->termsCondition) {
            Notification::make()
                ->title('Please check the terms and conditions before submitting.')
                ->danger()
                ->send();

            return;
        }

        $timeOfArrival = Carbon::parse($data['date'] . ' ' . $data['time']);
        $timeOfLeave = $timeOfArrival->copy()->addHours((int)$data['duration']);
        $checkStart = $timeOfArrival->copy()->subHour();
        $checkEnd = $timeOfLeave->copy()->addHour();

        $checkDateTime = Conference::getCheckTimeSchedules($checkStart, $checkEnd);

        if($checkDateTime) {
            Notification::make()
                ->title('Date and time is taken.')
                ->danger()
                ->send();

            return;
        }

        $conference = Conference::create([
            'book_by' => null,
            'start_at' => $timeOfArrival->copy(),
            'duration' => $data['duration'],
            'event' => $data['event'],
            'members' => $data['members'],
            'host' => $data['host'],
            'contact_no' => $data['contact_no'],
            'email' => $data['email'],
            'status' => 'pending',
        ]);

        $this->booked = true;

        Notification::make()
            ->title('Your booking is pending and we will contact you once its okay.')
            ->success()
            ->send();

        return redirect()->route('booking-conference.success');
    }

    public function render(): View
    {
        return view('livewire.public-pages.book-conference');
    }
}

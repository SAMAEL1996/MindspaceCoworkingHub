<?php

namespace App\Filament\Resources\ConferenceResource\Pages;

use App\Filament\Resources\ConferenceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Filament\Forms\Components as FormComponents;

class ViewConference extends ViewRecord
{
    protected static string $resource = ConferenceResource::class;

    public function getBreadcrumbs(): array
    {
        return [
            
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\Action::make('approve')
                    ->requiresConfirmation()
                    ->action(function($record) {
                        $record->status = 'approve';
                        $record->save();

                        Notification::make()
                            ->title('Conference booking successfully approved.')
                            ->success()
                            ->send();
                    })
                    ->visible(function($record) {
                        if(!auth()->user()->can('approve conferences')) {
                            return false;
                        }

                        return $record->status == 'pending' ? true : false;
                    }),
                Actions\Action::make('add-members')
                    ->label('Add Guests')
                    ->modalHeading('Add Guests')
                    ->form([
                        FormComponents\Repeater::make('list_of_members')
                            ->label('')
                            ->schema([
                                FormComponents\TextInput::make('guest_name')
                                    ->required(),
                                FormComponents\Select::make('guest_card_id')
                                    ->label('Card ID')
                                    ->options(function() {
                                        $options = [];
                                        
                                        $takenIds = $this->record->conferenceMembers()->pluck('card_id');
                                        $conferenceCards = \App\Models\Card::whereNotIn('id', $takenIds)->where('type', 'Conference')->get();
                                        foreach($conferenceCards as $guest) {
                                            $options[$guest->id] = $guest->code;
                                        }
            
                                        return $options;
                                    })
                                    ->preload()
                                    ->searchable('code')
                                    ->required()
                                    ->native(false),
                            ])
                            ->addActionLabel('Add guest')
                            ->reorderable(false)
                            ->columns(2),
                    ])
                    ->action(function($data, $record) {
                        $listOfMembers = $data['list_of_members'];
                        $cardId = [];
                        foreach($listOfMembers as $listOfMember) {
                            if(in_array($listOfMember['guest_card_id'], $cardId)) {
                                Notification::make()
                                    ->title('Card ID duplicated.')
                                    ->danger()
                                    ->send();
        
                                $this->halt();
                            }
                            $cardId[] = $listOfMember['guest_card_id'];
                        }

                        foreach($listOfMembers as $item) {
                            $record->conferenceMembers()->create([
                                'card_id' => $item['guest_card_id'],
                                'guest' => $item['guest_name'],
                                'status' => true,
                                'time_in' => \Carbon\Carbon::now()
                            ]);
                        }

                        $record->save();

                        Notification::make()
                            ->title('Guests successfully added.')
                            ->success()
                            ->send();

                        return redirect()->to(ConferenceResource::getUrl('view', ['record' => $record]));
                    })
                    ->visible(function($record) {
                        if(!auth()->user()->can('add conference-guests')) {
                            return false;
                        }

                        if($record->is_paid || !$record->has_reservation_fee) {
                            return false;
                        }

                        return $record->status == 'approve' ? true : false;
                    }),
                Actions\Action::make('add-reservation-fee')
                    ->label('Add Reservation Fee')
                    ->modalHeading('Reservation Fee')
                    ->form([
                        FormComponents\TextInput::make('amount')
                            ->label('Amount')
                            ->required()
                            ->numeric()
                            ->default(function($record) {
                                return round($record->amount/2);
                            })
                            ->helperText(function($record) {
                                return '50% of Conference Rate: PHP ' . number_format($record->amount/2, 2);
                            }),
                        FormComponents\Select::make('mop')
                            ->label('Mode of Payment')
                            ->options([
                                'Cash' => 'Cash',
                                'GCash' => 'GCash',
                                'Bank Transfer' => 'Bank Transfer'
                            ])
                            ->required()
                            ->native(false)
                            ->columnSpan('full')
                    ])
                    ->action(function($data, $record) {
                        $record->payment = $data['amount'];
                        $record->mop_reservation_fee = $data['mop'];
                        $record->has_reservation_fee = true;
                        $record->save();

                        $sale = \App\Models\DailySale::create([
                            'date' => \Carbon\Carbon::now()->format('Y-m-d'),
                            'card_id' => 38,
                            'name' => $record->host,
                            'description' => 'Conference',
                            'time_in' => \Carbon\Carbon::now(),
                            'time_in_staff_id' => auth()->user()->staff->id,
                            'time_out' => \Carbon\Carbon::now(),
                            'time_out_staff_id' => auth()->user()->staff->id,
                            'default_amount' => true,
                            'amount_paid' => $data['amount'],
                            'apply_discount' => false,
                            'discount' => 0,
                            'is_flexi' => false,
                            'is_monthly' => false,
                            'status' => false,
                            'mode_of_payment' => $data['mop']
                        ]);

                        Notification::make()
                            ->title('Reservation fee successfully paid.')
                            ->success()
                            ->send();
                    })
                    ->visible(function($record) {
                        if(!auth()->user()->can('edit conferences')) {
                            return false;
                        }

                        if($record->is_paid) {
                            return false;
                        }
                        
                        return $record->status == 'approve' && !$record->has_reservation_fee ? true : false;
                    }),
                Actions\Action::make('bounce-na')
                    ->label('Bounce na to')
                    ->modalHeading('Bounce na!')
                    ->form([
                        FormComponents\Grid::make(2)
                            ->schema([
                                FormComponents\TextInput::make('amount')
                                    ->label('Amount to Pay')
                                    ->formatStateUsing(function($record) {
                                        return $record->remaining_balance + $record->additionalCharges();
                                    })
                                    ->disabled()
                                    ->dehydrated(),
                                FormComponents\TextInput::make('total-hours')
                                    ->label('Total Hours')
                                    ->formatStateUsing(function($record) {
                                        $start = $record->start_at_carbon->addMinutes(15);
                                        $end = \Carbon\Carbon::now();

                                        $diff = $start->diff($end);
                                        return $diff->h . ' hours and ' . $diff->i . ' minutes';
                                    })
                                    ->disabled()
                                    ->dehydrated(),
                                FormComponents\Select::make('mode_of_payment')
                                    ->label('Mode of Payment')
                                    ->options([
                                        'Cash' => 'Cash',
                                        'GCash' => 'GCash',
                                        'Bank Transfer' => 'Bank Transfer'
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->columnSpan('full')
                            ])
                    ])
                    ->action(function($data, $record) {
                        $record->amount = $record->amount + $record->additionalCharges();
                        $record->payment = $record->payment + $data['amount'];
                        $record->is_paid = true;
                        $record->save();

                        Notification::make()
                            ->title('Conference successfully paid.')
                            ->success()
                            ->send();
                    })
                    ->visible(function($record) {
                        if(!auth()->user()->can('edit conferences')) {
                            return false;
                        }

                        if($record->conferenceMembers->isEmpty()) {
                            return false;
                        }

                        if($record->is_paid) {
                            return false;
                        }

                        foreach($record->conferenceMembers as $guest) {
                            if($guest->status) {
                                return false;
                            }
                        }

                        return true;
                    }),
                Actions\Action::make('mark-as-finished')
                    ->label('Mark as Finished')
                    ->requiresConfirmation()
                    ->action(function($record) {
                        $record->status = 'finished';
                        $record->save();

                        $record->addCheckInToSalesReport();

                        Notification::make()
                            ->title('Conference booking successfully finished.')
                            ->success()
                            ->send();
                    })
                    ->visible(function($record) {
                        if(!auth()->user()->can('edit conferences')) {
                            return false;
                        }

                        if($record->status == 'finished') {
                            return false;
                        }

                        return $record->is_paid;
                    }),
                Actions\Action::make('cancel')
                    ->label('Mark as Cancel')
                    ->requiresConfirmation()
                    ->action(function($record) {
                        $record->status = 'cancelled';
                        $record->save();

                        Notification::make()
                            ->title('Conference booking successfully cancelled.')
                            ->success()
                            ->send();
                    })
                    ->visible(function($record) {
                        if(!auth()->user()->can('edit conferences')) {
                            return false;
                        }

                        if($record->status == 'cancelled') {
                            return false;
                        }

                        return !$record->is_paid;
                    }),
            ])
            ->label('Action')
            ->button(),
        ];
    }
}

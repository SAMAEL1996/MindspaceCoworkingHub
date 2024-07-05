<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components as InfolistComponents;
use App\Models\User;

class Index extends Component implements HasForms, HasInfolists
{
    use InteractsWithInfolists;
    use InteractsWithForms;

    public User $user;

    public function informationInfolist(Infolist $infolist)
    {
        return $infolist
            ->record($this->user)
            ->schema([
                InfolistComponents\Section::make('Information')
                    ->schema([
                        InfolistComponents\TextEntry::make('name'),
                        InfolistComponents\TextEntry::make('staff.personal_email')
                            ->label('Personal Email'),
                        InfolistComponents\TextEntry::make('contact_no'),
                        // InfolistComponents\TextEntry::make('is_staff')
                        //     ->label('Is Staff')
                        //     ->formatStateUsing(function($state, $record) {
                        //         return $state ? 'Yes' : 'No';
                        //     })
                        //     ->badge(),
                    ])
                    ->columns(3),
                InfolistComponents\Section::make('Emergency Contact')
                    ->schema([
                        InfolistComponents\TextEntry::make('staff.emergency_contact_person')
                            ->label('Contact Person'),
                        InfolistComponents\TextEntry::make('staff.emergency_relationship')
                            ->label('Relationship'),
                        InfolistComponents\TextEntry::make('staff.emergency_contact_no')
                            ->label('Contact No.'),
                    ])
                    ->columns(3),
                InfolistComponents\Section::make('Requirements')
                    ->schema([
                        InfolistComponents\TextEntry::make('profile.sss')
                            ->label('SSS E1/ID/Static Information')
                            ->formatStateUsing(function($state, $record) {
                                return $state ? 'Yes' : 'No';
                            })
                            ->color(function($state, $record) {
                                return $state ? 'success' : 'gray';
                            })
                            ->badge()
                            ->default(false),
                        InfolistComponents\TextEntry::make('profile.pagibig')
                            ->label('Pagibig Member\'s Data Form/ Loyalty Card')
                            ->formatStateUsing(function($state, $record) {
                                return $state ? 'Yes' : 'No';
                            })
                            ->color(function($state, $record) {
                                return $state ? 'success' : 'gray';
                            })
                            ->helperText('or any document showing pagibig number')
                            ->badge()
                            ->default(false),
                        InfolistComponents\TextEntry::make('profile.philhealth')
                            ->label('Philhealth Insurance ID/MDR')
                            ->formatStateUsing(function($state, $record) {
                                return $state ? 'Yes' : 'No';
                            })
                            ->color(function($state, $record) {
                                return $state ? 'success' : 'gray';
                            })
                            ->badge()
                            ->default(false),
                        InfolistComponents\TextEntry::make('profile.tin')
                            ->label('TIN ID')
                            ->formatStateUsing(function($state, $record) {
                                return $state ? 'Yes' : 'No';
                            })
                            ->color(function($state, $record) {
                                return $state ? 'success' : 'gray';
                            })
                            ->helperText('If none fillout BIR 1904 form')
                            ->badge()
                            ->default(false),
                        InfolistComponents\TextEntry::make('profile.psa')
                            ->label('PSA Birth Certificate')
                            ->formatStateUsing(function($state, $record) {
                                return $state ? 'Yes' : 'No';
                            })
                            ->color(function($state, $record) {
                                return $state ? 'success' : 'gray';
                            })
                            ->badge()
                            ->default(false),
                        InfolistComponents\TextEntry::make('profile.nbi')
                            ->label('NBI / Police Clearance')
                            ->formatStateUsing(function($state, $record) {
                                return $state ? 'Yes' : 'No';
                            })
                            ->color(function($state, $record) {
                                return $state ? 'success' : 'gray';
                            })
                            ->badge()
                            ->default(false),
                        InfolistComponents\TextEntry::make('profile.brgy_clearance')
                            ->label('Brgy. Clearance')
                            ->formatStateUsing(function($state, $record) {
                                return $state ? 'Yes' : 'No';
                            })
                            ->color(function($state, $record) {
                                return $state ? 'success' : 'gray';
                            })
                            ->badge()
                            ->default(false),
                        InfolistComponents\TextEntry::make('profile.diploma')
                            ->label('High School/College Credentials(TOR, Diploma etc.)')
                            ->formatStateUsing(function($state, $record) {
                                return $state ? 'Yes' : 'No';
                            })
                            ->color(function($state, $record) {
                                return $state ? 'success' : 'gray';
                            })
                            ->badge()
                            ->default(false),
                        InfolistComponents\TextEntry::make('profile.medical')
                            ->label('Medical Result')
                            ->formatStateUsing(function($state, $record) {
                                return $state ? 'Yes' : 'No';
                            })
                            ->color(function($state, $record) {
                                return $state ? 'success' : 'gray';
                            })
                            ->badge()
                            ->default(false),
                        InfolistComponents\TextEntry::make('profile.coe')
                            ->label('COE from previous employers')
                            ->formatStateUsing(function($state, $record) {
                                return $state ? 'Yes' : 'No';
                            })
                            ->color(function($state, $record) {
                                return $state ? 'success' : 'gray';
                            })
                            ->badge()
                            ->default(false),
                        InfolistComponents\TextEntry::make('profile.bir')
                            ->label('BIR 2316 from previous employer')
                            ->formatStateUsing(function($state, $record) {
                                return $state ? 'Yes' : 'No';
                            })
                            ->color(function($state, $record) {
                                return $state ? 'success' : 'gray';
                            })
                            ->badge()
                            ->default(false),
                        InfolistComponents\TextEntry::make('profile.id_picture_1')
                            ->label('1x1 ID Picture-Formal Attire')
                            ->formatStateUsing(function($state, $record) {
                                return $state ? 'Yes' : 'No';
                            })
                            ->color(function($state, $record) {
                                return $state ? 'success' : 'gray';
                            })
                            ->badge()
                            ->default(false),
                        InfolistComponents\TextEntry::make('profile.id_picture_2')
                            ->label('2x2 ID Picture-Formal Attire')
                            ->formatStateUsing(function($state, $record) {
                                return $state ? 'Yes' : 'No';
                            })
                            ->color(function($state, $record) {
                                return $state ? 'success' : 'gray';
                            })
                            ->badge()
                            ->default(false),
                        InfolistComponents\TextEntry::make('profile.status')
                            ->label('Status')
                            ->formatStateUsing(function($state, $record) {
                                return $state ? 'Yes' : 'No';
                            })
                            ->color(function($state, $record) {
                                return $state ? 'success' : 'gray';
                            })
                            ->badge()
                            ->default(false),
                    ])
                    ->columns(3)
            ]);
    }

    public function render()
    {
        return view('livewire.profile.index');
    }
}

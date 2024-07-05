<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfileResource\Pages;
use App\Filament\Resources\ProfileResource\RelationManagers;
use App\Models\Profile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns as TableColumns;
use App\Filament\Pages\Profile as PageProfile;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components as InfolistComponents;

class ProfileResource extends Resource
{
    protected static ?string $model = Profile::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Staff Profiles';
    protected static ?string $navigationGroup = 'ADMIN';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Profile::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TableColumns\TextColumn::make('staff_id')
                    ->label('Staff')
                    ->formatStateUsing(function($state, $record) {
                        return $record->staff->user->name;
                    }),
                TableColumns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(function($state, $record) {
                        return $state ? 'Completed' : 'Incomplete';
                    })
                    ->color(function($state, $record) {
                        return $state ? 'success' : 'gray';
                    }),
                TableColumns\TextColumn::make('total_submitted')
                    ->label('Total Submitted')
                    ->formatStateUsing(function($state, $record) {
                        return $state . ' / ' . $record->requirements;
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProfiles::route('/'),
            'create' => Pages\CreateProfile::route('/create'),
            'view' => Pages\ViewProfile::route('/{record}'),
            'edit' => Pages\EditProfile::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistComponents\Section::make('Information')
                    ->schema([
                        InfolistComponents\TextEntry::make('staff.user.name')
                            ->label('Name'),
                        InfolistComponents\TextEntry::make('staff.personal_email')
                            ->label('Personal Email'),
                        InfolistComponents\TextEntry::make('staff.user.contact_no')
                            ->label('Contact No.'),
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
                        InfolistComponents\TextEntry::make('sss')
                            ->label('SSS E1/ID/Static Information')
                            ->formatStateUsing(function($state, $record) {
                                return $state ? 'Yes' : 'No';
                            })
                            ->color(function($state, $record) {
                                return $state ? 'success' : 'gray';
                            })
                            ->badge()
                            ->default(false),
                        InfolistComponents\TextEntry::make('pagibig')
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
                        InfolistComponents\TextEntry::make('philhealth')
                            ->label('Philhealth Insurance ID/MDR')
                            ->formatStateUsing(function($state, $record) {
                                return $state ? 'Yes' : 'No';
                            })
                            ->color(function($state, $record) {
                                return $state ? 'success' : 'gray';
                            })
                            ->badge()
                            ->default(false),
                        InfolistComponents\TextEntry::make('tin')
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
                        InfolistComponents\TextEntry::make('psa')
                            ->label('PSA Birth Certificate')
                            ->formatStateUsing(function($state, $record) {
                                return $state ? 'Yes' : 'No';
                            })
                            ->color(function($state, $record) {
                                return $state ? 'success' : 'gray';
                            })
                            ->badge()
                            ->default(false),
                        InfolistComponents\TextEntry::make('nbi')
                            ->label('NBI / Police Clearance')
                            ->formatStateUsing(function($state, $record) {
                                return $state ? 'Yes' : 'No';
                            })
                            ->color(function($state, $record) {
                                return $state ? 'success' : 'gray';
                            })
                            ->badge()
                            ->default(false),
                        InfolistComponents\TextEntry::make('brgy_clearance')
                            ->label('Brgy. Clearance')
                            ->formatStateUsing(function($state, $record) {
                                return $state ? 'Yes' : 'No';
                            })
                            ->color(function($state, $record) {
                                return $state ? 'success' : 'gray';
                            })
                            ->badge()
                            ->default(false),
                        InfolistComponents\TextEntry::make('diploma')
                            ->label('High School/College Credentials(TOR, Diploma etc.)')
                            ->formatStateUsing(function($state, $record) {
                                return $state ? 'Yes' : 'No';
                            })
                            ->color(function($state, $record) {
                                return $state ? 'success' : 'gray';
                            })
                            ->badge()
                            ->default(false),
                        InfolistComponents\TextEntry::make('medical')
                            ->label('Medical Result')
                            ->formatStateUsing(function($state, $record) {
                                return $state ? 'Yes' : 'No';
                            })
                            ->color(function($state, $record) {
                                return $state ? 'success' : 'gray';
                            })
                            ->badge()
                            ->default(false),
                        InfolistComponents\TextEntry::make('coe')
                            ->label('COE from previous employers')
                            ->formatStateUsing(function($state, $record) {
                                return $state ? 'Yes' : 'No';
                            })
                            ->color(function($state, $record) {
                                return $state ? 'success' : 'gray';
                            })
                            ->badge()
                            ->default(false),
                        InfolistComponents\TextEntry::make('bir')
                            ->label('BIR 2316 from previous employer')
                            ->formatStateUsing(function($state, $record) {
                                return $state ? 'Yes' : 'No';
                            })
                            ->color(function($state, $record) {
                                return $state ? 'success' : 'gray';
                            })
                            ->badge()
                            ->default(false),
                        InfolistComponents\TextEntry::make('id_picture_1')
                            ->label('1x1 ID Picture-Formal Attire')
                            ->formatStateUsing(function($state, $record) {
                                return $state ? 'Yes' : 'No';
                            })
                            ->color(function($state, $record) {
                                return $state ? 'success' : 'gray';
                            })
                            ->badge()
                            ->default(false),
                        InfolistComponents\TextEntry::make('id_picture_2')
                            ->label('2x2 ID Picture-Formal Attire')
                            ->formatStateUsing(function($state, $record) {
                                return $state ? 'Yes' : 'No';
                            })
                            ->color(function($state, $record) {
                                return $state ? 'success' : 'gray';
                            })
                            ->badge()
                            ->default(false),
                        InfolistComponents\TextEntry::make('status')
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

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view permissions');
    }
}

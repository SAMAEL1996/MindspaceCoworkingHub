<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffResource\Pages;
use App\Filament\Resources\StaffResource\RelationManagers;
use App\Models\Staff;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns as TableColumns;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components as InfolistComponents;
use App\Filament\Pages\Profile;

class StaffResource extends Resource
{
    protected static ?string $model = Staff::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'ADMIN';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Staff::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TableColumns\TextColumn::make('card.code')
                    ->label('Card Code')
                    ->searchable(),
                TableColumns\TextColumn::make('user.name')
                    ->label('Staff Name')
                    ->searchable(),
                TableColumns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([

            ])
            ->bulkActions([

            ])
            ->recordUrl(function($record) {
                return false;
                
                return Profile::getUrl(['record' => $record->staff]);
            });
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
            'index' => Pages\ListStaff::route('/'),
            'create' => Pages\CreateStaff::route('/create'),
            'view' => Pages\ViewStaff::route('/{record}'),
            'edit' => Pages\EditStaff::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistComponents\Section::make('Information')
                    ->schema([
                        InfolistComponents\TextEntry::make('user.name')
                            ->label('Name'),
                        InfolistComponents\TextEntry::make('personal_email'),
                        InfolistComponents\TextEntry::make('user.contact_no')
                            ->label('Contact'),
                        InfolistComponents\TextEntry::make('user.email')
                            ->label('Work Email'),
                        InfolistComponents\TextEntry::make('card.code')
                            ->label('Card ID'),

                    ])
                    ->columns(3),
                InfolistComponents\Section::make('Emergency Contact')
                    ->schema([
                        InfolistComponents\TextEntry::make('emergency_contact_person')
                            ->label('Contact Person'),
                        InfolistComponents\TextEntry::make('emergency_relationship')
                            ->label('Relationship'),
                        InfolistComponents\TextEntry::make('emergency_contact_no')
                            ->label('Contact No.'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view staff');
    }
}

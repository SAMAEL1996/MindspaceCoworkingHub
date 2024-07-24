<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlexiUserResource\Pages;
use App\Filament\Resources\FlexiUserResource\RelationManagers;
use App\Models\FlexiUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns as TableColumns;

class FlexiUserResource extends Resource
{
    protected static ?string $model = FlexiUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationGroup = 'SALES';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(FlexiUser::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TableColumns\TextColumn::make('card_id')
                    ->label('Card'),
                TableColumns\TextColumn::make('name'),
                TableColumns\TextColumn::make('contact_no')
                    ->label('Contact'),
                TableColumns\TextColumn::make('start_at')
                    ->label('Date Start')
                    ->date(),
                TableColumns\TextColumn::make('remaining_time')
                    ->label('Remaining Time'),
                TableColumns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(function($state) {
                        return $state ? 'success' : 'danger';
                    })
                    ->formatStateUsing(function($state) {
                        return $state ? 'Active' : 'Expired';
                    }),
                TableColumns\TextColumn::make('paid')
                    ->label('Paid')
                    ->badge()
                    ->color(function($state) {
                        return $state ? 'success' : 'gray';
                    })
                    ->formatStateUsing(function($state) {
                        return $state ? 'Yes' : 'No';
                    })
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(auth()->user()->hasRole('Super Administrator'))
            ])
            ->bulkActions([
                //
            ])
            ->recordUrl(null);
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
            'index' => Pages\ListFlexiUsers::route('/'),
            'create' => Pages\CreateFlexiUser::route('/create'),
            'view' => Pages\ViewFlexiUser::route('/{record}'),
            'edit' => Pages\EditFlexiUser::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view flexi-users');
    }
}

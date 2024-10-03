<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MonthlyUserResource\Pages;
use App\Filament\Resources\MonthlyUserResource\RelationManagers;
use App\Models\MonthlyUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns as TableColumns;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class MonthlyUserResource extends Resource
{
    protected static ?string $model = MonthlyUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'SALES';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(MonthlyUser::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TableColumns\TextColumn::make('card.code')
                    ->label('Card')
                    ->searchable(),
                TableColumns\TextColumn::make('name')
                    ->searchable(),
                TableColumns\TextColumn::make('contact_no')
                    ->label('Contact'),
                TableColumns\TextColumn::make('date_start')
                    ->label('Date Start')
                    ->date(),
                TableColumns\TextColumn::make('created_at')
                    ->label('Expiry')
                    ->formatStateUsing(function($record) {
                        return \Carbon\Carbon::parse($record->date_finish)->format(config('app.date_format'));
                    }),
                TableColumns\TextColumn::make('is_active')
                    ->label('Active')
                    ->badge()
                    ->color(function($state) {
                        return $state ? 'success' : 'gray';
                    })
                    ->formatStateUsing(function($state) {
                        return $state ? 'Yes' : 'No';
                    }),
                TableColumns\TextColumn::make('date_finish')
                    ->label('Expired In')
                    ->formatStateUsing(function($state, $record) {
                        return \Carbon\Carbon::parse($record->date_finish)->addDay()->diffForHumans();
                    })
                    ->sortable()
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('Set as Expired')
                        ->requiresConfirmation()
                        ->action(function($record) {
                            $record->is_expired = true;
                            $record->card_id = null;
                            $record->save();

                            Notification::make()
                                ->title('Monthly user successfully set to expired.')
                                ->success()
                                ->send();

                            return redirect()->to(MonthlyUserResource::getUrl('index'));
                        })
                        ->visible(function($record) {
                            return $record->is_expired ? false : true;
                        }),
                    Tables\Actions\Action::make('Send Reminder')
                        ->requiresConfirmation()
                        ->action(function($record) {
                            $apikey = config('app.semaphore_key');

                            $expireIn = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($record->date_finish)->addDay());
                            $content = 'You monthly pass subscription will expire in ' . $expireIn . ' day/s. Please renew your subscription to continue unlimited coworking space access. Thank you!';
                            $params = [
                                'apikey' => $apikey,
                                'number' => $record->contact_no,
                                'message' => $content,
                            ];

                            $client = new Client();
                            $request = new Request('POST', "https://api.semaphore.co/api/v4/messages?" . http_build_query($params));
                            $res = $client->sendAsync($request)->wait();

                            Notification::make()
                                ->title('Monthly user successfully send expiry notification.')
                                ->success()
                                ->send();

                            return redirect()->to(MonthlyUserResource::getUrl('index'));
                        })
                        ->visible(function($record) {
                            if(!auth()->user()->hasRole('Super Administrator')) {
                                return false;
                            }

                            return $record->is_expired ? false : true;
                        }),
                    Tables\Actions\EditAction::make()
                        ->visible(auth()->user()->hasRole('Super Administrator')),
                    Tables\Actions\Action::make('Re-new Pass')
                        ->requiresConfirmation()
                        ->action(function($record) {
                            $newRecord = $record->replicate();

                            $record->card_id = null;
                            $record->is_expired = true;
                            $record->save();

                            if($newRecord->date_finish_carbon->isPast()) {
                                $newRecord->date_start = \Carbon\Carbon::now()->format('Y-m-d');
                                $newRecord->date_finish = \Carbon\Carbon::now()->addMonth()->subDay()->format('Y-m-d');
                                $newRecord->save();
                            } else {
                                $startDate = \Carbon\Carbon::parse($record->date_start)->addMonth();

                                $newRecord->date_start = $startDate->copy()->format('Y-m-d');
                                $newRecord->date_finish = $startDate->copy()->addMonth()->subDay()->format('Y-m-d');
                                $newRecord->save();
                            }

                            Notification::make()
                                ->title('Monthly user successfully renew.')
                                ->success()
                                ->send();

                            return redirect()->to(MonthlyUserResource::getUrl('index'));
                        })
                        ->visible(function($record) {
                            if(!auth()->user()->hasRole('Super Administrator')) {
                                return false;
                            }
                            
                            return $record->is_expired ? false : true;
                        }),
                ])
                ->icon('heroicon-o-ellipsis-horizontal')
            ])
            ->bulkActions([

            ])
            ->defaultSort('is_active', 'desc')
            ->defaultPaginationPageOption(25)
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
            'index' => Pages\ListMonthlyUsers::route('/'),
            'create' => Pages\CreateMonthlyUser::route('/create'),
            'view' => Pages\ViewMonthlyUser::route('/{record}'),
            'edit' => Pages\EditMonthlyUser::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view monthly-users');
    }
}

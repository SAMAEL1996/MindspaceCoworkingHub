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
use Filament\Notifications\Notification;
use Filament\Forms\Components as FormComponents;
use Filament\Support\Enums\MaxWidth;
use Carbon\Carbon;
use App\Filament\Resources\DailySaleResource;
use App\Models\DailySale;
use App\Models\Card;
use App\Models\CashLog;

class FlexiUserResource extends Resource
{
    protected static ?string $model = FlexiUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationGroup = 'SALES';

    public static function getEloquentQuery(): Builder
    {
        return FlexiUser::query()->orderBy('is_active', 'desc')->latest();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(FlexiUser::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TableColumns\TextColumn::make('id')
                    ->label('ID')
                    ->visible(auth()->user()->hasRole('Super Administrator')),
                TableColumns\TextColumn::make('rate_id')
                    ->label('Package')
                    ->formatStateUsing(function($record, $state) {
                        return $state ? $record->rate->name : 'Old';
                    })
                    ->placeholder('Old Pass'),
                TableColumns\TextColumn::make('card_id')
                    ->label('Card ID')
                    ->formatStateUsing(function($record, $state) {
                        return $state ? $record->card->id : null;
                    })
                    ->description(function($record, $state) {
                        return $state ? $record->card->code : null;
                    }),
                TableColumns\TextColumn::make('name')
                    ->wrap()
                    ->searchable(),
                TableColumns\TextColumn::make('contact_no')
                    ->label('Contact')
                    ->copyable(),
                TableColumns\TextColumn::make('start_at')
                    ->label('Date Start')
                    ->formatStateUsing(function($record, $state) {
                        return $state ? $record->start_at_carbon->format(config('app.date_format')) : null;
                    })
                    ->description(function($record, $state) {
                        return $state ? $record->start_at_carbon->format(config('app.time_format')) : null;
                    })
                    ->sortable(),
                TableColumns\TextColumn::make('remaining')
                    ->label('Remaining Time')
                    ->formatStateUsing(function($record) {
                        return $record->remaining_time;
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->reorder()
                            ->orderBy('remaining', $direction);
                    }),
                TableColumns\TextColumn::make('expired_at')
                    ->label('Expired At')
                    ->formatStateUsing(function($record, $state) {
                        return $state ? $record->expired_at_carbon->format(config('app.date_format')) : null;
                    })
                    ->description(function($record, $state) {
                        return $state ? $record->expired_at_carbon->format(config('app.time_format')) : null;
                    })
                    ->sortable()
                    ->placeholder('No expiry'),
                TableColumns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->formatStateUsing(function ($record, $state) {
                        return $state ? $record->updated_at->format(config('app.date_format')) : null;
                    })
                    ->description(function ($record, $state) {
                        return $state ? $record->updated_at->format(config('app.time_format')) : null;
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(function($state) {
                        return $state ? 'success' : 'danger';
                    })
                    ->formatStateUsing(function($state) {
                        return $state ? 'Active' : 'Expired';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumns\TextColumn::make('is_active')
                    ->label('Is Active')
                    ->badge()
                    ->color(function($state) {
                        return $state ? 'success' : 'gray';
                    })
                    ->formatStateUsing(function($state) {
                        return $state ? 'Yes' : 'No';
                    })
            ])
            ->filters([
                Tables\Filters\Filter::make('updated_at')
                    ->label('Updated At')
                    ->form([
                        FormComponents\Fieldset::make('Updated At')
                            ->schema([
                                FormComponents\DatePicker::make('from')->label('From'),
                                FormComponents\DatePicker::make('to')->label('To'),
                            ]),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $query, $date): Builder => $query->whereDate('updated_at', '>=', $date))
                        ->when($data['to'] ?? null, fn (Builder $query, $date): Builder => $query->whereDate('updated_at', '<=', $date))),
                Tables\Filters\Filter::make('start_at')
                    ->label('Date Start')
                    ->form([
                        FormComponents\Fieldset::make('Date Start')
                            ->schema([
                                FormComponents\DatePicker::make('from')->label('From'),
                                FormComponents\DatePicker::make('to')->label('To'),
                            ]),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $query, $date): Builder => $query->whereDate('start_at', '>=', $date))
                        ->when($data['to'] ?? null, fn (Builder $query, $date): Builder => $query->whereDate('start_at', '<=', $date))),
                Tables\Filters\Filter::make('expired_at')
                    ->label('Expired At')
                    ->form([
                        FormComponents\Fieldset::make('Expired At')
                            ->schema([
                                FormComponents\DatePicker::make('from')->label('From'),
                                FormComponents\DatePicker::make('to')->label('To'),
                            ]),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $query, $date): Builder => $query->whereDate('expired_at', '>=', $date))
                        ->when($data['to'] ?? null, fn (Builder $query, $date): Builder => $query->whereDate('expired_at', '<=', $date))),
                Tables\Filters\Filter::make('created_at')
                    ->label('Created At')
                    ->form([
                        FormComponents\Fieldset::make('Created At')
                            ->schema([
                                FormComponents\DatePicker::make('from')->label('From'),
                                FormComponents\DatePicker::make('to')->label('To'),
                            ]),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date))
                        ->when($data['to'] ?? null, fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date))),
            ])
            ->filtersTriggerAction(fn(Tables\Actions\Action $action) => $action->button()->label('Filters'))
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->visible(auth()->user()->hasRole('Super Administrator')),
                    Tables\Actions\Action::make('renew-pass')
                        ->label('Renew Pass')
                        ->color('warning')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            FormComponents\Select::make('rate_id')
                                ->label('Package')
                                ->options(\App\Models\Rate::where('type', 'Flexi')->where('status', true)->get()->pluck('name', 'id'))
                                ->required()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(function($state, $set) {
                                    $rate = \App\Models\Rate::find($state);
                                    $set('amount', $rate->price);

                                    $helperText = 'Flexi Pass Rate: PHP ' . number_format($rate->price, 2);
                                    $set('amount_helper_text', $helperText);
                                })
                                ->native(false),
                            FormComponents\TextInput::make('amount')
                                ->label('Amount Paid')
                                ->numeric()
                                ->minValue(1)
                                ->required()
                                ->helperText(function($get) {
                                    return $get('amount_helper_text') ?? '';
                                }),
                            FormComponents\Select::make('mode_of_payment')
                                ->options([
                                    'Cash' => 'Cash',
                                    'GCash' => 'GCash',
                                    'Bank Transfer' => 'Bank Transfer'
                                ])
                                ->required()
                                ->native(false)
                        ])
                        ->modalWidth(MaxWidth::Medium)
                        ->action(function($data, $record) {
                            $rate = \App\Models\Rate::find($data['rate_id']);

                            $newRecord = $record->replicate();

                            $remainingMinutes = $record->start_at_carbon->diffInMinutes($record->end_at_carbon);

                            $record->card_id = null;
                            $record->end_at = $record->start_at_carbon->toDateTimeString();
                            $record->status = false;
                            $record->save();

                            $newRecord->rate_id = $rate->id;
                            $newRecord->amount = $data['amount'];
                            $newRecord->start_at = \Carbon\Carbon::now();
                            $newRecord->end_at = \Carbon\Carbon::now()->addHours((int)$rate->consumable)->addMinutes($remainingMinutes);
                            $newRecord->expired_at = \Carbon\Carbon::now()->addDays((int)$rate->validity);
                            $newRecord->save();

                            $newRecord->sendWelcomeMessage();

                            $saleData = [
                                'date' => \Carbon\Carbon::now(),
                                'time_in' => \Carbon\Carbon::now(),
                                'time_in_staff_id' => auth()->user()->staff->id,
                                'time_out' => \Carbon\Carbon::now(),
                                'time_out_staff_id' => auth()->user()->staff->id,
                                'card_id' => $record->card_id ? $record->card_id : \App\Models\Card::where('type', 'Daily')->latest()->first()->id,
                                'name' => $newRecord->name,
                                'description' => 'Flexi',
                                'apply_discount' => true,
                                'discount' => 100,
                                'status' => false,
                                'is_flxi' => true,
                                'is_monthly' => false,
                                'amount_paid' => $data['amount'],
                                'mode_of_payment' => $data['mode_of_payment']
                            ];
                    
                            $dailyPass = \App\Models\DailySale::create($saleData);

                            Notification::make()
                                ->title('Success')
                                ->body("Flexi user successfully renew.")
                                ->success()
                                ->send();

                            return redirect()->to(FlexiUserResource::getUrl('index'));
                        })
                        ->visible(function($record) {
                            return $record->status ? true : false;
                        }),
                    Tables\Actions\Action::make('check-in')
                        ->label('Guest Check In')
                        ->color('info')
                        ->icon('heroicon-o-check-badge')
                        ->modalHeading('Check In Flexi')
                        ->modalDescription(fn($record) => $record->name)
                        ->modalWidth(MaxWidth::Medium)
                        ->form([
                            FormComponents\Select::make('card_id')
                                ->label('Card ID')
                                ->native(false)
                                ->placeholder('Select Card ID')
                                ->searchable()
                                ->options(function() {
                                    $takenIds = DailySale::whereNull('time_out')->pluck('card_id')->toArray();

                                    return Card::whereNotIn('id', $takenIds)->where('type', 'Daily')->pluck('code', 'id');
                                }),
                        ])
                        ->action(function($record, $data) {
                            $user = auth()->user();
                            $staff = $user->staff;
                            $now = Carbon::now();

                            // daily sale data
                            $saleData = [
                                'date' => $now->copy(),
                                'time_in_staff_id' => $staff->id,
                                'card_id' => $data['card_id'],
                                'name' => $record->name,
                                'description' => 'Flexi',
                                'apply_discount' => true,
                                'discount' => 100,
                                'time_in' => $now->copy()->addMinutes(15),
                                'status' => true,
                                'is_flexi' => true,
                                'is_monthly' => false
                            ];

                            // create daily sale record
                            $dailyPass = \App\Models\DailySale::create($saleData);

                            $record->update([
                                'card_id' => $data['card_id'],
                                'is_active' => true
                            ]);

                            Notification::make()
                                ->title('Success')
                                ->body("Monthly user successfully checked in.")
                                ->success()
                                ->send();

                            return redirect()->to(DailySaleResource::getUrl('index'));
                        })
                        ->visible(function($record) {
                            $user = auth()->user();
            
                            if($user->hasRole('Super Administrator')) {
                                return true;
                            }

                            if($record->is_active) {
                                return false;
                            }
            
                            return CashLog::where('status', true)->where('user_id', $user->id)->exists();
                        }),
                ])
                ->icon('heroicon-o-ellipsis-horizontal')
            ])
            ->bulkActions([
                //
            ])
            ->toggleColumnsTriggerAction(
                fn (Tables\Actions\Action $action) => $action
                    ->button()
                    ->label('Columns'),
            )
            // ->defaultSort('is_active', 'desc')
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

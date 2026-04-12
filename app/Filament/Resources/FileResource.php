<?php

namespace App\Filament\Resources;

use App\Jobs\ProcessUploadedFile;
use App\Filament\Resources\FileResource\Pages;
use App\Models\File;
use Filament\Actions\CreateAction;
use Filament\Forms\Components as FormComponents;
use Filament\Forms\Form;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns as TableColumns;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FileResource extends Resource
{
    protected static ?string $model = File::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'ADMIN';

    protected static ?string $modelLabel = 'File';

    protected static ?string $pluralModelLabel = 'Files';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FormComponents\FileUpload::make('uploaded_files')
                    ->label('Files')
                    ->multiple()
                    ->required()
                    ->disk('local')
                    ->directory('livewire-tmp/files')
                    ->storeFileNamesIn('uploaded_file_names')
                    ->preserveFilenames()
                    ->helperText('Upload one or more original files. Files are queued for background processing, then moved to storage/app/files without resizing or compression.')
                    ->visibleOn('create'),
                FormComponents\TextInput::make('original_name')
                    ->label('Original Name')
                    ->required()
                    ->maxLength(255)
                    ->hiddenOn('create'),
                FormComponents\FileUpload::make('replacement_file')
                    ->label('Replace File')
                    ->disk('local')
                    ->directory('livewire-tmp/files')
                    ->storeFileNamesIn('replacement_file_name')
                    ->preserveFilenames()
                    ->helperText('Leave empty to keep the current file.')
                    ->hiddenOn('create'),
                FormComponents\Placeholder::make('status_display')
                    ->label('Processing Status')
                    ->content(fn (?File $record): string => $record ? str($record->status)->title()->toString() : 'Queued')
                    ->hiddenOn('create'),
                FormComponents\Placeholder::make('error_message_display')
                    ->label('Error')
                    ->content(fn (?File $record): string => $record?->error_message ?: 'None')
                    ->hidden(fn (?File $record): bool => blank($record?->error_message)),
                FormComponents\Placeholder::make('type_display')
                    ->label('Type')
                    ->content(fn (?File $record): string => $record?->type ? strtoupper($record->type) : 'N/A')
                    ->hiddenOn('create'),
                FormComponents\Placeholder::make('size_display')
                    ->label('Size')
                    ->content(fn (?File $record): string => $record ? static::formatBytes($record->size) : 'N/A')
                    ->hiddenOn('create'),
                FormComponents\DateTimePicker::make('uploaded_at')
                    ->label('Date Uploaded')
                    ->seconds(false)
                    ->disabled()
                    ->dehydrated(false)
                    ->hiddenOn('create'),
                FormComponents\Placeholder::make('path_display')
                    ->label('Storage Path')
                    ->content(fn (?File $record): string => $record?->path ?? 'N/A')
                    ->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TableColumns\TextColumn::make('original_name')
                    ->label('Original Name')
                    ->searchable()
                    ->sortable(),
                TableColumns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        File::STATUS_COMPLETED => 'success',
                        File::STATUS_PROCESSING => 'warning',
                        File::STATUS_FAILED => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => str($state)->title()->toString())
                    ->sortable(),
                TableColumns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state ? strtoupper($state) : 'N/A')
                    ->sortable(),
                TableColumns\TextColumn::make('mime_type')
                    ->label('Mime Type')
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumns\TextColumn::make('size')
                    ->label('Size')
                    ->formatStateUsing(fn ($state): string => static::formatBytes((int) $state))
                    ->sortable(),
                TableColumns\TextColumn::make('uploaded_at')
                    ->label('Date Uploaded')
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),
                TableColumns\TextColumn::make('path')
                    ->label('Storage Path')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable()
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(fn (File $record): string => route('files.download', $record))
                    ->visible(fn (File $record): bool => $record->isDownloadable()),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (File $record): void {
                        Storage::disk('local')->delete($record->path);
                        Storage::disk('local')->delete($record->temp_path);
                        Storage::disk('local')->delete($record->pending_delete_path);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Collection $records): void {
                            $records->each(function (File $record): void {
                                Storage::disk('local')->delete($record->path);
                                Storage::disk('local')->delete($record->temp_path);
                                Storage::disk('local')->delete($record->pending_delete_path);
                            });
                        }),
                ]),
            ])
            ->defaultSort('uploaded_at', 'desc')
            ->recordUrl(null)
            ->poll('10s');
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
            'index' => Pages\ListFiles::route('/'),
            'edit' => Pages\EditFile::route('/{record}/edit'),
        ];
    }

    public static function getCreateAction(): CreateAction
    {
        return CreateAction::make()
            ->modalWidth('3xl')
            ->createAnother(false)
            ->using(function (array $data): File {
                $paths = $data['uploaded_files'] ?? [];
                $names = $data['uploaded_file_names'] ?? [];
                $firstRecord = null;

                foreach ($paths as $index => $path) {
                    $record = File::create([
                        'path' => $path,
                        'temp_path' => $path,
                        'original_name' => $names[$index] ?? basename($path),
                        'type' => pathinfo($names[$index] ?? $path, PATHINFO_EXTENSION) ?: null,
                        'size' => Storage::disk('local')->size($path),
                        'status' => File::STATUS_QUEUED,
                        'uploaded_at' => now(),
                    ]);

                    ProcessUploadedFile::dispatch($record->id)->onQueue('uploads');

                    if (! $firstRecord) {
                        $firstRecord = $record;
                    }
                }

                return $firstRecord ?? new File();
            })
            ->successNotificationTitle('Files queued for background processing.');
    }

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make(static::getNavigationLabel())
                ->group(static::$navigationGroup ?? null)
                ->parentItem(static::$navigationParentItem ?? null)
                ->icon(static::$navigationIcon ?? null)
                ->isActiveWhen(fn (): bool => request()->routeIs(static::getRouteBaseName() . '.*'))
                ->sort(static::$navigationSort ?? 0)
                ->url(static::getUrl())
                ->visible(static::userCanAccess()),
        ];
    }

    public static function canViewAny(): bool
    {
        return static::userCanAccess();
    }

    public static function canCreate(): bool
    {
        return static::userCanAccess();
    }

    public static function canEdit(Model $record): bool
    {
        return static::userCanAccess();
    }

    public static function canDelete(Model $record): bool
    {
        return static::userCanAccess();
    }

    public static function canDeleteAny(): bool
    {
        return static::userCanAccess();
    }

    public static function userCanAccess(): bool
    {
        return auth()->check() && auth()->user()->hasRole('Super Administrator');
    }

    public static function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        $units = ['KB', 'MB', 'GB', 'TB'];
        $value = $bytes / 1024;

        foreach ($units as $unit) {
            if ($value < 1024 || $unit === 'TB') {
                return number_format($value, 2) . ' ' . $unit;
            }

            $value /= 1024;
        }

        return number_format($value, 2) . ' TB';
    }
}

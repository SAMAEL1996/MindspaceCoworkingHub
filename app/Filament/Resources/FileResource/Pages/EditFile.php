<?php

namespace App\Filament\Resources\FileResource\Pages;

use App\Filament\Resources\FileResource;
use App\Jobs\ProcessUploadedFile;
use App\Models\File;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EditFile extends EditRecord
{
    protected static string $resource = FileResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $replacementPath = $data['replacement_file'] ?? null;
        $replacementName = $data['replacement_file_name'] ?? null;

        unset($data['replacement_file'], $data['replacement_file_name']);

        if ($replacementPath) {
            $oldPath = $record->path;
            $data = array_merge($data, [
                'path' => $replacementPath,
                'temp_path' => $replacementPath,
                'pending_delete_path' => $oldPath,
                'original_name' => $replacementName ?: $record->original_name,
                'type' => pathinfo($replacementName ?: $replacementPath, PATHINFO_EXTENSION) ?: null,
                'mime_type' => Storage::disk('local')->mimeType($replacementPath),
                'size' => Storage::disk('local')->size($replacementPath),
                'status' => File::STATUS_QUEUED,
                'error_message' => null,
                'processing_started_at' => null,
                'processing_completed_at' => null,
                'uploaded_at' => now(),
            ]);
        }

        $record->update($data);

        if ($replacementPath) {
            ProcessUploadedFile::dispatch($record->id)->onQueue('uploads');
        }

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'File saved. Replacement uploads continue in the background when applicable.';
    }
}

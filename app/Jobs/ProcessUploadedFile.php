<?php

namespace App\Jobs;

use App\Models\File;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessUploadedFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 7200;

    public int $tries = 3;

    public function __construct(public int $fileId)
    {
    }

    public function handle(): void
    {
        $file = File::find($this->fileId);

        if (! $file || blank($file->temp_path)) {
            return;
        }

        $disk = Storage::disk('local');

        if (! $disk->exists($file->temp_path)) {
            $file?->update([
                'status' => File::STATUS_FAILED,
                'error_message' => 'Temporary upload not found.',
                'processing_completed_at' => now(),
            ]);

            return;
        }

        $file->update([
            'status' => File::STATUS_PROCESSING,
            'error_message' => null,
            'processing_started_at' => now(),
        ]);

        $finalPath = File::makeStoredPath($file->original_name);

        try {
            $disk->move($file->temp_path, $finalPath);

            $metadata = File::buildMetadata($finalPath, $file->original_name);

            $file->update(array_merge($metadata, [
                'temp_path' => null,
                'status' => File::STATUS_COMPLETED,
                'error_message' => null,
                'processing_completed_at' => now(),
            ]));

            if (filled($file->pending_delete_path) && $file->pending_delete_path !== $finalPath) {
                $disk->delete($file->pending_delete_path);
            }

            $file->update([
                'pending_delete_path' => null,
            ]);
        } catch (\Throwable $exception) {
            $file->update([
                'status' => File::STATUS_FAILED,
                'error_message' => $exception->getMessage(),
                'processing_completed_at' => now(),
            ]);

            throw $exception;
        }
    }
}

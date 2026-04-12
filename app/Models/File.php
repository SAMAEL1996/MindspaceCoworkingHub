<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class File extends Model
{
    use HasFactory;

    public const STATUS_QUEUED = 'queued';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'path',
        'temp_path',
        'pending_delete_path',
        'original_name',
        'type',
        'mime_type',
        'size',
        'status',
        'error_message',
        'uploaded_at',
        'processing_started_at',
        'processing_completed_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'processing_started_at' => 'datetime',
        'processing_completed_at' => 'datetime',
    ];

    public static function buildMetadata(string $path, ?string $originalName = null, string $disk = 'local'): array
    {
        $storage = Storage::disk($disk);
        $originalName = $originalName ?: basename($path);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);

        return [
            'path' => $path,
            'original_name' => $originalName,
            'type' => $extension !== '' ? strtolower($extension) : $storage->mimeType($path),
            'mime_type' => $storage->mimeType($path),
            'size' => $storage->size($path),
            'uploaded_at' => now(),
        ];
    }

    public static function makeStoredPath(string $originalName): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $filename = (string) Str::uuid();

        if ($extension !== '') {
            $filename .= '.' . strtolower($extension);
        }

        return 'files/' . $filename;
    }

    public function isDownloadable(): bool
    {
        return $this->status === self::STATUS_COMPLETED
            && filled($this->path)
            && Storage::disk('local')->exists($this->path);
    }
}

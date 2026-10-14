<?php

namespace App\Services;

use App\Models\Media;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaUploadService
{
    public function store(UploadedFile $file, User $user, array $meta = []): Media
    {
        $path = $file->store('vantage-media', 'public');
        [$width, $height] = @getimagesize($file->getRealPath()) ?: [null, null];

        return Media::create([
            'uploaded_by' => $user->id,
            'disk' => 'public',
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'size_bytes' => $file->getSize() ?: 0,
            'width' => $width,
            'height' => $height,
            'alt_text' => $meta['alt_text'] ?? $file->getClientOriginalName(),
            'category' => $meta['category'] ?? 'General',
            'source_note' => $meta['source_note'] ?? null,
            'license_note' => $meta['license_note'] ?? null,
            'is_published' => $meta['is_published'] ?? true,
        ]);
    }

    public function delete(Media $media): void
    {
        Storage::disk($media->disk)->delete($media->path);
        $media->delete();
    }
}

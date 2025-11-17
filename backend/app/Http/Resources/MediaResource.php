<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'mediable_type' => $this->mediable_type,
            'mediable_id' => $this->mediable_id,
            'organization_id' => $this->organization_id,
            'file_name' => $this->file_name,
            'file_path' => $this->file_path,
            'file_url' => $this->disk ? Storage::disk($this->disk)->url($this->file_path) : null,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'size_human' => $this->size ? $this->formatBytes($this->size) : null,
            'is_image' => $this->isImage(),
            'is_pdf' => $this->isPdf(),
            'ocr_status' => $this->ocr_status,
            'ocr_data' => $this->ocr_data,
            'ocr_processed_at' => $this->ocr_processed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'uploader' => [
                'id' => $this->uploader->id,
                'name' => $this->uploader->name,
            ] when $this->relationLoaded('uploader'),
        ];
    }

    /**
     * Format bytes to human-readable format.
     */
    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

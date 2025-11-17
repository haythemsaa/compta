<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'mediable_type',
        'mediable_id',
        'organization_id',
        'uploaded_by',
        'file_name',
        'file_path',
        'mime_type',
        'size',
        'disk',
        'ocr_data',
        'ocr_status',
        'ocr_processed_at',
    ];

    protected $casts = [
        'size' => 'integer',
        'ocr_data' => 'array',
        'ocr_processed_at' => 'datetime',
    ];

    // Relationships
    public function mediable()
    {
        return $this->morphTo();
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Helper methods
    public function getUrl(): string
    {
        return \Storage::disk($this->disk)->url($this->file_path);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    public function needsOcr(): bool
    {
        return ($this->isImage() || $this->isPdf()) && $this->ocr_status !== 'completed';
    }
}

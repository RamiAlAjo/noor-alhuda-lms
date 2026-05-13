<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MessageAttachment extends Model
{
    protected $fillable = [
        'message_id',
        'filename',
        'original_filename',
        'mime_type',
        'file_size',
        'file_path',
        'disk',
        'file_type',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'file_size' => 'integer',
    ];

    /**
     * File type constants
     */
    const TYPE_IMAGE = 'image';
    const TYPE_DOCUMENT = 'document';
    const TYPE_VIDEO = 'video';
    const TYPE_AUDIO = 'audio';
    const TYPE_OTHER = 'other';

    /**
     * Get the message this attachment belongs to.
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    /**
     * Get the file URL.
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk ?? 'public')->url($this->file_path);
    }

    /**
     * Get the file size in human readable format.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Check if attachment is an image.
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Check if attachment is a video.
     */
    public function isVideo(): bool
    {
        return str_starts_with($this->mime_type, 'video/');
    }

    /**
     * Check if attachment is audio.
     */
    public function isAudio(): bool
    {
        return str_starts_with($this->mime_type, 'audio/');
    }

    /**
     * Check if attachment is a document.
     */
    public function isDocument(): bool
    {
        $documentTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/csv',
        ];

        return in_array($this->mime_type, $documentTypes);
    }

    /**
     * Get file type based on mime type.
     */
    public function getFileTypeAttribute(): string
    {
        if ($this->isImage()) {
            return self::TYPE_IMAGE;
        } elseif ($this->isVideo()) {
            return self::TYPE_VIDEO;
        } elseif ($this->isAudio()) {
            return self::TYPE_AUDIO;
        } elseif ($this->isDocument()) {
            return self::TYPE_DOCUMENT;
        } else {
            return self::TYPE_OTHER;
        }
    }

    /**
     * Delete the file from storage.
     */
    public function deleteFile(): bool
    {
        if (Storage::disk($this->disk ?? 'public')->exists($this->file_path)) {
            return Storage::disk($this->disk ?? 'public')->delete($this->file_path);
        }

        return true;
    }

    /**
     * Get file icon based on type.
     */
    public function getIconAttribute(): string
    {
        return match ($this->file_type) {
            self::TYPE_IMAGE => 'photo',
            self::TYPE_VIDEO => 'video-camera',
            self::TYPE_AUDIO => 'musical-note',
            self::TYPE_DOCUMENT => 'document',
            default => 'document',
        };
    }

    /**
     * Scope for images.
     */
    public function scopeImages($query)
    {
        return $query->where('file_type', self::TYPE_IMAGE);
    }

    /**
     * Scope for documents.
     */
    public function scopeDocuments($query)
    {
        return $query->where('file_type', self::TYPE_DOCUMENT);
    }

    /**
     * Scope for media files (images, videos, audio).
     */
    public function scopeMedia($query)
    {
        return $query->whereIn('file_type', [self::TYPE_IMAGE, self::TYPE_VIDEO, self::TYPE_AUDIO]);
    }
}

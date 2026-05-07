<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_offering_id',
        'uploaded_by',
        'title',
        'title_ar',
        'description',
        'file_path',
        'file_type',
        'file_size',
        'video_url',
        'material_type',
        'week',
        'is_published',
    ];

    /**
     * Get the offering (main relationship).
     */
    public function offering()
    {
        return $this->belongsTo(CourseOffering::class, 'course_offering_id');
    }

    /**
     * Get the user who uploaded the material.
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get YouTube video ID from URL.
     */
    public function getYouTubeVideoId(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        $pattern = '/(?:youtube\\.com\\/(?:[^\\/]+\\/+\\S*\\/|embed\\/|v\\/|watch\\?v=|\\S*\\?v=)|youtu\\.be\\/)([a-zA-Z0-9_-]{11})/';

        if (preg_match($pattern, $this->video_url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Get YouTube embed URL.
     */
    public function getYouTubeEmbedUrl(): ?string
    {
        $videoId = $this->getYouTubeVideoId();

        return $videoId ? "https://www.youtube.com/embed/{$videoId}" : null;
    }

    /**
     * Check if material has a YouTube video.
     */
    public function hasYouTubeVideo(): bool
    {
        return ! empty($this->getYouTubeVideoId());
    }
}

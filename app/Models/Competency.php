<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Competency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'description',
        'description_ar',
        'code',
        'department_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the localized name.
     */
    public function getLocalizedNameAttribute(): string
    {
        $locale = app()->getLocale();

        return $locale === 'ar' && $this->name_ar ? $this->name_ar : $this->name;
    }

    /**
     * Get the localized description.
     */
    public function getLocalizedDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();

        return $locale === 'ar' && $this->description_ar ? $this->description_ar : $this->description;
    }

    /**
     * Get the department that owns this competency.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the course sections associated with this competency.
     */
    public function courseSections(): BelongsToMany
    {
        return $this->belongsToMany(CourseSection::class, 'course_competencies')
            ->withTimestamps();
    }

    /**
     * Scope to get only active competencies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

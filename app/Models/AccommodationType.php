<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccommodationType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'category',
        'default_settings',
        'requires_documentation',
        'is_active',
    ];

    protected $casts = [
        'default_settings' => 'array',
        'requires_documentation' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Categories for accommodations.
     */
    const CATEGORY_TIMING = 'timing';

    const CATEGORY_FORMAT = 'format';

    const CATEGORY_ENVIRONMENT = 'environment';

    const CATEGORY_MATERIALS = 'materials';

    const CATEGORY_COMMUNICATION = 'communication';

    const CATEGORY_OTHER = 'other';

    /**
     * Get all categories.
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_TIMING => __('lms.timing_accommodations'),
            self::CATEGORY_FORMAT => __('lms.format_accommodations'),
            self::CATEGORY_ENVIRONMENT => __('lms.environment_accommodations'),
            self::CATEGORY_MATERIALS => __('lms.materials_accommodations'),
            self::CATEGORY_COMMUNICATION => __('lms.communication_accommodations'),
            self::CATEGORY_OTHER => __('lms.other_accommodations'),
        ];
    }

    /**
     * Get the student accommodations for this type.
     */
    public function studentAccommodations()
    {
        return $this->hasMany(StudentAccommodation::class);
    }

    /**
     * Scope to get active accommodation types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get the default setting value.
     */
    public function getDefaultSetting(string $key, $default = null)
    {
        return $this->default_settings[$key] ?? $default;
    }
}

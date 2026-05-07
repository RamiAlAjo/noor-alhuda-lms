<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'second_name',
        'third_name',
        'last_name',
        'phone',
        'address',
        'city',
        'country',
        'postal_code',
        'date_of_birth',
        'gender',
        'nationality',
        'photo',
        'bio',
        'personal_email',
        'social_links',
        // Teacher-specific fields
        'cv',
        'department_id',
        'blood_type',
        'years_of_experience',
        'office_hours',
        // Student-specific fields
        'major_id',
        'emergency_phone',
        'emergency_contact_name',
        'emergency_contact_relationship',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'office_hours' => 'array',
        'social_links' => 'array',
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department (for teachers).
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the major (for students).
     */
    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    /**
     * Get the full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->first_name,
            $this->second_name,
            $this->third_name,
            $this->last_name,
        ]);

        return implode(' ', $parts);
    }

    /**
     * Get the full name in Arabic format.
     */
    public function getFullNameArAttribute(): string
    {
        $parts = array_filter([
            $this->first_name,
            $this->second_name,
            $this->third_name,
            $this->last_name,
        ]);

        return implode(' ', array_reverse($parts));
    }

    /**
     * Get the role-specific ID (Student ID or Teacher ID).
     */
    public function getRoleSpecificIdAttribute(): ?string
    {
        // Check if the attribute exists in the model
        return array_key_exists('role_specific_id', $this->attributes) ? $this->attributes['role_specific_id'] : null;
    }

    /**
     * Check if profile belongs to a teacher.
     */
    public function isTeacher(): bool
    {
        return $this->user?->isTeacher() ?? false;
    }

    /**
     * Check if profile belongs to a student.
     */
    public function isStudent(): bool
    {
        return $this->user?->isStudent() ?? false;
    }

    /**
     * Get the display ID based on user role.
     * Returns Student ID (YYYY-NNNNN) for students or Teacher ID (T-YYYY-NNNNN) for teachers.
     */
    public function getDisplayIdAttribute(): ?string
    {
        // Check if the attribute exists and has a value
        if (array_key_exists('role_specific_id', $this->attributes) && $this->attributes['role_specific_id']) {
            return $this->attributes['role_specific_id'];
        }

        return null;
    }

    /**
     * Get office hours formatted for display.
     */
    public function getOfficeHoursFormattedAttribute(): ?string
    {
        if (! $this->office_hours || ! is_array($this->office_hours)) {
            return null;
        }

        $days = [
            'sunday' => 'Sunday',
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
        ];

        $formatted = [];
        foreach ($this->office_hours as $day => $hours) {
            if (! empty($hours['start']) && ! empty($hours['end'])) {
                $formatted[] = $days[$day] ?? $day.': '.$hours['start'].' - '.$hours['end'];
            }
        }

        return implode(', ', $formatted);
    }

    /**
     * Backward compatibility accessor for user_id_unique.
     */
    public function getUserIdUniqueAttribute(): ?string
    {
        return $this->role_specific_id;
    }

    /**
     * Get formatted social links.
     */
    public function getSocialLinksFormattedAttribute(): array
    {
        return $this->social_links ?? [];
    }

    /**
     * Check if a specific social link exists.
     */
    public function hasSocialLink(string $platform): bool
    {
        return ! empty($this->social_links[$platform] ?? null);
    }

    /**
     * Get a specific social link.
     */
    public function getSocialLink(string $platform): ?string
    {
        return $this->social_links[$platform] ?? null;
    }

    /**
     * Get full address formatted.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->country,
            $this->postal_code,
        ]);

        return implode(', ', $parts);
    }
}

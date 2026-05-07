<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'event_type',
        'is_all_day',
        'color',
        'location',
        'course_section_id',
        'reminder_enabled',
        'reminder_minutes',
        'is_recurring',
        'recurrence_rule',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_all_day' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get color based on event type.
     */
    public function getColorAttribute(): string
    {
        return match ($this->event_type) {
            'exam' => '#f97316', // orange
            'assignment' => '#eab308', // yellow
            'class' => '#8b5cf6', // violet
            'meeting' => '#06b6d4', // cyan
            'other' => '#6b7280', // gray
            default => '#3b82f6', // blue
        };
    }

    /**
     * Get event type options.
     */
    public static function getEventTypes(): array
    {
        return [
            'personal' => __('Personal'),
            'exam' => __('Exam'),
            'assignment' => __('Assignment'),
            'class' => __('Class'),
            'meeting' => __('Meeting'),
            'other' => __('Other'),
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'target_type',
        'target_faculty_id',
        'target_department_id',
        'target_offering_id',
        'target_course_id',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function targetFaculty()
    {
        return $this->belongsTo(Faculty::class, 'target_faculty_id');
    }

    public function targetDepartment()
    {
        return $this->belongsTo(Department::class, 'target_department_id');
    }

    public function targetSection()
    {
        return $this->belongsTo(CourseSection::class, 'target_offering_id');
    }

    /**
     * Get the offering (main relationship).
     */
    public function targetOffering()
    {
        return $this->belongsTo(CourseOffering::class, 'target_offering_id');
    }
}

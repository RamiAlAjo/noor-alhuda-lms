<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'faculty_id',
        'name',
        'name_ar',
        'code',
        'head_name',
        'email',
    ];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function majors()
    {
        return $this->hasMany(Major::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Generate a comprehensive report for this department.
     */
    public function generateDepartmentReport(): array
    {
        $courses = $this->courses;
        $totalCourses = $courses->count();

        $totalStudents = 0;
        $activeOfferings = 0;

        foreach ($courses as $course) {
            $offerings = $course->offerings;
            $activeOfferings += $offerings->where('is_active', true)->count();

            foreach ($offerings as $offering) {
                $totalStudents += $offering->enrollments()->count();
            }
        }

        return [
            'total_courses' => $totalCourses,
            'total_students' => $totalStudents,
            'active_course_offerings' => $activeOfferings,
            'average_students_per_course' => $totalCourses > 0 ? round($totalStudents / $totalCourses, 2) : 0,
        ];
    }
}

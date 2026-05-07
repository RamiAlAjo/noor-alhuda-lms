<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'code',
        'dean_name',
        'email',
        'phone',
    ];

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Generate a comprehensive report for this faculty.
     */
    public function generateFacultyReport(): array
    {
        $departments = $this->departments;
        $totalDepartments = $departments->count();

        $totalCourses = 0;
        $totalStudents = 0;

        foreach ($departments as $department) {
            $courses = $department->courses;
            $totalCourses += $courses->count();

            foreach ($courses as $course) {
                foreach ($course->offerings as $offering) {
                    $totalStudents += $offering->enrollments()->count();
                }
            }
        }

        return [
            'total_departments' => $totalDepartments,
            'total_courses' => $totalCourses,
            'total_students' => $totalStudents,
            'average_courses_per_department' => $totalDepartments > 0 ? round($totalCourses / $totalDepartments, 2) : 0,
            'average_students_per_course' => $totalCourses > 0 ? round($totalStudents / $totalCourses, 2) : 0,
        ];
    }
}

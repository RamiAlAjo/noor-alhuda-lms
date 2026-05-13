<?php

namespace App\Policies;

use App\Models\CourseOffering;
use App\Models\User;

class CourseOfferingPolicy
{
    /**
     * Determine whether the user can view the course offering.
     */
    public function view(User $user, CourseOffering $courseOffering): bool
    {
        // Teachers can view their own course offerings
        if ($user->hasRole('teacher') && $courseOffering->teacher_id === $user->id) {
            return true;
        }

        // Admins can view all course offerings
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return true;
        }

        // Students can view course offerings they're enrolled in
        if ($user->hasRole('student')) {
            return $courseOffering->enrollments()->where('student_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can update the course offering.
     */
    public function update(User $user, CourseOffering $courseOffering): bool
    {
        // Only teachers assigned to the course can update it
        return $user->hasRole('teacher') && $courseOffering->teacher_id === $user->id;
    }

    /**
     * Determine whether the user can delete the course offering.
     */
    public function delete(User $user, CourseOffering $courseOffering): bool
    {
        // Only admins can delete course offerings
        return $user->hasRole('admin') || $user->hasRole('super-admin');
    }

    /**
     * Determine whether the user can manage assessments for the course offering.
     */
    public function manageAssessments(User $user, CourseOffering $courseOffering): bool
    {
        return $user->hasRole('teacher') && $courseOffering->teacher_id === $user->id;
    }

    /**
     * Determine whether the user can view students in the course offering.
     */
    public function viewStudents(User $user, CourseOffering $courseOffering): bool
    {
        return $user->hasRole('teacher') && $courseOffering->teacher_id === $user->id;
    }

    /**
     * Determine whether the user can manage attendance for the course offering.
     */
    public function manageAttendance(User $user, CourseOffering $courseOffering): bool
    {
        return $user->hasRole('teacher') && $courseOffering->teacher_id === $user->id;
    }

    /**
     * Determine whether the user can manage materials for the course offering.
     */
    public function manageMaterials(User $user, CourseOffering $courseOffering): bool
    {
        return $user->hasRole('teacher') && $courseOffering->teacher_id === $user->id;
    }

    /**
     * Determine whether the user can view grades for the course offering.
     */
    public function viewGrades(User $user, CourseOffering $courseOffering): bool
    {
        return $user->hasRole('teacher') && $courseOffering->teacher_id === $user->id;
    }
}

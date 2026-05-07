<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\AssessmentType;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\Semester;
use Illuminate\Support\Facades\Cache;

/**
 * Cache Service for static and frequently accessed data.
 * Provides centralized caching with consistent TTLs and cache key management.
 */
class CacheService
{
    /**
     * Cache TTL constants (in minutes)
     */
    const TTL_STATIC = 60;        // 1 hour for static data

    const TTL_COURSES = 5;        // 5 minutes for course listings

    const TTL_PERMISSIONS = 60;   // 1 hour for user permissions

    const TTL_DASHBOARD = 5;      // 5 minutes for dashboard data

    /**
     * Get all academic years with caching.
     */
    public static function getAcademicYears()
    {
        return Cache::remember('academic_years', now()->addMinutes(self::TTL_STATIC), function () {
            return AcademicYear::orderBy('start_year', 'desc')->get();
        });
    }

    /**
     * Get active academic year with caching.
     */
    public static function getActiveAcademicYear()
    {
        return Cache::remember('active_academic_year', now()->addMinutes(self::TTL_STATIC), function () {
            return AcademicYear::where('is_active', true)->first();
        });
    }

    /**
     * Get all semesters with caching.
     */
    public static function getSemesters()
    {
        return Cache::remember('semesters', now()->addMinutes(self::TTL_STATIC), function () {
            return Semester::with('academicYear')
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    /**
     * Get current semester with caching.
     */
    public static function getCurrentSemester()
    {
        return Cache::remember('current_semester', now()->addMinutes(self::TTL_COURSES), function () {
            return Semester::getCurrent();
        });
    }

    /**
     * Get all departments with caching.
     */
    public static function getDepartments()
    {
        return Cache::remember('all_departments', now()->addMinutes(self::TTL_STATIC), function () {
            return Department::with('faculty')->get();
        });
    }

    /**
     * Get all faculties with caching.
     */
    public static function getFaculties()
    {
        return Cache::remember('all_faculties', now()->addMinutes(self::TTL_STATIC), function () {
            return Faculty::with('departments')->get();
        });
    }

    /**
     * Get all majors with caching.
     */
    public static function getMajors()
    {
        return Cache::remember('all_majors', now()->addMinutes(self::TTL_STATIC), function () {
            return Major::with('department.faculty')->get();
        });
    }

    /**
     * Get all assessment types with caching.
     */
    public static function getAssessmentTypes()
    {
        return Cache::remember('all_assessment_types', now()->addMinutes(self::TTL_STATIC), function () {
            return AssessmentType::where('is_active', true)->get();
        });
    }

    /**
     * Get user permissions with caching.
     */
    public static function getUserPermissions(int $userId)
    {
        return Cache::remember("user_permissions_{$userId}", now()->addMinutes(self::TTL_PERMISSIONS), function () use ($userId) {
            $user = \App\Models\User::find($userId);
            if (! $user) {
                return collect();
            }

            return $user->getAllPermissions();
        });
    }

    /**
     * Get user roles with caching.
     */
    public static function getUserRoles(int $userId)
    {
        return Cache::remember("user_roles_{$userId}", now()->addMinutes(self::TTL_PERMISSIONS), function () use ($userId) {
            $user = \App\Models\User::find($userId);
            if (! $user) {
                return collect();
            }

            return $user->roles;
        });
    }

    /**
     * Check if user has specific permission with caching.
     */
    public static function userHasPermission(int $userId, string $permission): bool
    {
        $permissions = self::getUserPermissions($userId);

        return $permissions->contains('name', $permission);
    }

    /**
     * Check if user has specific role with caching.
     */
    public static function userHasRole(int $userId, string $role): bool
    {
        $roles = self::getUserRoles($userId);

        return $roles->contains('name', $role);
    }

    /**
     * Clear all static data caches.
     */
    public static function clearStaticCaches(): void
    {
        Cache::forget('academic_years');
        Cache::forget('active_academic_year');
        Cache::forget('semesters');
        Cache::forget('current_semester');
        Cache::forget('all_departments');
        Cache::forget('all_faculties');
        Cache::forget('all_majors');
        Cache::forget('all_assessment_types');
    }

    /**
     * Clear user-specific caches.
     */
    public static function clearUserCaches(int $userId): void
    {
        Cache::forget("user_permissions_{$userId}");
        Cache::forget("user_roles_{$userId}");
        Cache::forget("student_gpa_{$userId}");
        Cache::forget("student_financial_{$userId}");
        Cache::forget("student_available_courses_{$userId}");
        Cache::forget("teacher_upcoming_assessments_{$userId}");
        Cache::forget("teacher_pending_grades_{$userId}");
        Cache::forget("teacher_announcements_{$userId}");
    }

    /**
     * Clear course-related caches.
     */
    public static function clearCourseCaches(): void
    {
        Cache::forget('admin_course_offerings');
        Cache::forget('student_announcements');
        Cache::forget('admin_dashboard_stats');
    }

    /**
     * Clear dashboard caches.
     */
    public static function clearDashboardCaches(): void
    {
        Cache::forget('admin_dashboard_stats');
    }

    /**
     * Clear all caches (use sparingly).
     */
    public static function clearAllCaches(): void
    {
        self::clearStaticCaches();
        self::clearCourseCaches();
        Cache::flush();
    }
}

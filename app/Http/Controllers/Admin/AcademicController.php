<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\Semester;
use App\Models\User;
use App\Services\CacheService;
use Illuminate\Http\Request;

class AcademicController extends Controller
{
    // ==================== Main Academic Overview ====================

    public function index()
    {
        // Use cached data for static/semi-static content
        $academicYears = CacheService::getAcademicYears();
        $faculties = CacheService::getFaculties();
        $departments = CacheService::getDepartments();
        $majors = CacheService::getMajors();

        // Course offerings change more frequently, cache for shorter time
        $courseOfferings = \Illuminate\Support\Facades\Cache::remember('admin_course_offerings', now()->addMinutes(5), function () {
            return CourseOffering::with([
                'course.department',
                'semester.academicYear',
                'teacher.profile',
            ])->latest()->get();
        });

        // Get enrollment statistics
        $totalEnrollments = \App\Models\Enrollment::count();
        $activeEnrollments = \App\Models\Enrollment::whereHas('offering', function ($q) {
            $q->where('is_active', true);
        })->count();

        // Get active semester
        $activeSemester = \App\Models\Semester::whereHas('academicYear', function ($q) {
            $q->where('is_active', true);
        })->where('is_active', true)->first();

        return view('pages.admin.academic.index', compact(
            'academicYears',
            'faculties',
            'departments',
            'majors',
            'courseOfferings',
            'totalEnrollments',
            'activeEnrollments',
            'activeSemester'
        ));
    }

    // ==================== Academic Years ====================

    public function academicYears()
    {
        $years = CacheService::getAcademicYears();

        return view('pages.admin.academic.years.index', compact('years'));
    }

    public function storeAcademicYear(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_year' => 'required|integer|min:2000|max:2100',
            'end_year' => 'required|integer|min:2000|max:2100|gte:start_year',
            'is_active' => 'boolean',
        ]);

        AcademicYear::create($request->all());

        // Clear static caches
        CacheService::clearStaticCaches();

        return back()->with('success', __('lms::messages.academic_year_created'));
    }

    public function updateAcademicYear(Request $request, AcademicYear $year)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_year' => 'required|integer|min:2000|max:2100',
            'end_year' => 'required|integer|min:2000|max:2100|gte:start_year',
            'is_active' => 'boolean',
        ]);

        $year->update($request->all());

        // Clear static caches
        CacheService::clearStaticCaches();

        return back()->with('success', __('lms::messages.academic_year_updated'));
    }

    public function destroyAcademicYear(AcademicYear $year)
    {
        $year->delete();

        // Clear static caches
        CacheService::clearStaticCaches();

        return back()->with('success', __('lms::messages.academic_year_deleted'));
    }

    // ==================== Semesters ====================

    public function storeSemester(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'enrollment_start_date' => 'nullable|date',
            'enrollment_end_date' => 'nullable|date|after_or_equal:enrollment_start_date',
            'drop_start_date' => 'nullable|date',
            'drop_end_date' => 'nullable|date|after_or_equal:drop_start_date',
        ]);

        Semester::create($request->all());

        // Clear static caches
        CacheService::clearStaticCaches();

        return back()->with('success', __('lms::messages.semester_created'));
    }

    /**
     * Update a semester.
     */
    public function updateSemester(Request $request, Semester $semester)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'enrollment_start_date' => 'nullable|date',
            'enrollment_end_date' => 'nullable|date|after_or_equal:enrollment_start_date',
            'drop_start_date' => 'nullable|date',
            'drop_end_date' => 'nullable|date|after_or_equal:drop_start_date',
        ]);

        $semester->update($request->all());

        // Clear static caches
        CacheService::clearStaticCaches();

        return back()->with('success', __('lms::messages.semester_updated'));
    }

    public function destroySemester(Semester $semester)
    {
        if ($semester->offerings()->count() > 0) {
            return back()->with('error', __('Cannot delete semester with course offerings'));
        }
        $semester->delete();

        // Clear static caches
        CacheService::clearStaticCaches();

        return back()->with('success', __('lms::messages.semester_deleted'));
    }

    // ==================== Faculties ====================

    public function faculties()
    {
        $faculties = CacheService::getFaculties();

        return view('pages.admin.academic.faculties.index', compact('faculties'));
    }

    public function storeFaculty(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'required|string|max:10|unique:faculties',
            'dean_name' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
        ]);

        Faculty::create($request->all());

        // Clear static caches
        CacheService::clearStaticCaches();

        return back()->with('success', __('lms::messages.faculty_created'));
    }

    public function updateFaculty(Request $request, Faculty $faculty)
    {
        $faculty->update($request->all());

        // Clear static caches
        CacheService::clearStaticCaches();

        return back()->with('success', __('lms::messages.faculty_updated'));
    }

    public function destroyFaculty(Faculty $faculty)
    {
        if ($faculty->departments()->count() > 0) {
            return back()->with('error', __('lms::messages.faculty_has_departments'));
        }
        $faculty->delete();

        // Clear static caches
        CacheService::clearStaticCaches();

        return back()->with('success', __('lms::messages.faculty_deleted'));
    }

    // ==================== Departments ====================

    public function departments()
    {
        $departments = CacheService::getDepartments();
        $faculties = CacheService::getFaculties();

        return view('pages.admin.academic.departments.index', compact('departments', 'faculties'));
    }

    public function storeDepartment(Request $request)
    {
        $request->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'required|string|max:10',
            'head_name' => 'nullable|string|max:255',
            'email' => 'nullable|email',
        ]);

        Department::create($request->all());

        // Clear static caches
        CacheService::clearStaticCaches();

        return back()->with('success', __('lms::messages.department_created'));
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $department->update($request->all());

        // Clear static caches
        CacheService::clearStaticCaches();

        return back()->with('success', __('lms::messages.department_updated'));
    }

    public function destroyDepartment(Department $department)
    {
        if ($department->majors()->count() > 0 || $department->courses()->count() > 0) {
            return back()->with('error', __('lms::messages.department_has_records'));
        }
        $department->delete();

        // Clear static caches
        CacheService::clearStaticCaches();

        return back()->with('success', __('lms::messages.department_deleted'));
    }

    // ==================== Majors ====================

    public function majors()
    {
        $majors = CacheService::getMajors();
        $departments = CacheService::getDepartments();

        return view('pages.admin.academic.majors.index', compact('majors', 'departments'));
    }

    public function storeMajor(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'required|string|max:10',
            'degree' => 'required|in:bachelor,master,phd',
            'years' => 'required|integer|min:1|max:10',
        ]);

        Major::create($request->all());

        // Clear static caches
        CacheService::clearStaticCaches();

        return back()->with('success', __('lms::messages.major_created'));
    }

    public function updateMajor(Request $request, Major $major)
    {
        $major->update($request->all());

        // Clear static caches
        CacheService::clearStaticCaches();

        return back()->with('success', __('lms::messages.major_updated'));
    }

    public function destroyMajor(Major $major)
    {
        if ($major->students()->count() > 0) {
            return back()->with('error', __('lms::messages.major_has_students'));
        }
        $major->delete();

        // Clear static caches
        CacheService::clearStaticCaches();

        return back()->with('success', __('lms::messages.major_deleted'));
    }

    // ==================== Course Offerings ====================

    public function offerings()
    {
        $semesters = Semester::with([
            'academicYear',
            'offerings.course.department',
            'offerings.teacher.profile',
            'offerings.enrollments',
        ])
            ->orderBy('start_date', 'desc')
            ->get();

        $teachers = \Illuminate\Support\Facades\Cache::remember('all_teachers', now()->addHour(), function () {
            return User::role('teacher')->with('profile')->get();
        });

        $courses = \Illuminate\Support\Facades\Cache::remember('all_courses', now()->addMinutes(5), function () {
            return Course::with('department')->get();
        });

        // Get statistics
        $totalOfferings = CourseOffering::count();
        $activeOfferings = CourseOffering::where('is_active', true)->count();
        $totalEnrolled = \App\Models\Enrollment::count();
        $totalCapacity = CourseOffering::sum('max_students');

        return view('pages.admin.academic.offerings.index', compact('semesters', 'teachers', 'courses', 'totalOfferings', 'activeOfferings', 'totalEnrolled', 'totalCapacity'));
    }

    public function storeOffering(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester_id' => 'required|exists:semesters,id',
            'teacher_id' => 'required|exists:users,id',
            'section_name' => 'required|string|max:10',
            'schedule' => 'nullable|string',
            'room' => 'nullable|string|max:50',
            'capacity' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        CourseOffering::create([
            'course_id' => $request->course_id,
            'semester_id' => $request->semester_id,
            'teacher_id' => $request->teacher_id,
            'section_name' => $request->section_name,
            'schedule' => $request->schedule,
            'room' => $request->room,
            'capacity' => $request->capacity,
            'is_active' => $request->is_active ?? true,
            'enrolled_count' => 0,
        ]);

        // Clear course caches and teacher/course caches
        CacheService::clearCourseCaches();
        \Illuminate\Support\Facades\Cache::forget('all_teachers');
        \Illuminate\Support\Facades\Cache::forget('all_courses');

        return back()->with('success', __('Course offering created successfully'));
    }

    public function updateOffering(Request $request, CourseOffering $offering)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester_id' => 'required|exists:semesters,id',
            'teacher_id' => 'required|exists:users,id',
            'section_name' => 'required|string|max:10',
            'schedule' => 'nullable|string',
            'room' => 'nullable|string|max:50',
            'capacity' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $offering->update([
            'teacher_id' => $request->teacher_id,
            'section_name' => $request->section_name,
            'schedule' => $request->schedule,
            'room' => $request->room,
            'capacity' => $request->capacity,
            'is_active' => $request->is_active ?? false,
        ]);

        // Clear course caches and teacher/course caches
        CacheService::clearCourseCaches();
        \Illuminate\Support\Facades\Cache::forget('all_teachers');
        \Illuminate\Support\Facades\Cache::forget('all_courses');

        return back()->with('success', __('Course offering updated successfully'));
    }

    public function destroyOffering(CourseOffering $offering)
    {
        if ($offering->enrollments()->count() > 0) {
            return back()->with('error', __('Cannot delete course offering with enrollments'));
        }
        $offering->delete();

        // Clear course caches
        CacheService::clearCourseCaches();

        return back()->with('success', __('Course offering deleted successfully'));
    }
}

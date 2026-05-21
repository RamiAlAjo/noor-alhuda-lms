<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\Major;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CourseController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function index(Request $request)
    {
        $query = Course::with(['department.faculty', 'majors']);

        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        $courses = $query->orderBy('code')->paginate(20);

        // Cache departments for 1 hour (static data)
        $departments = Cache::remember('all_departments', now()->addHour(), function () {
            return Department::with('faculty')->get();
        });

        // Get enrollment statistics using optimized queries
        $totalEnrolled = Enrollment::where('status', 'approved')->count();

        $activeEnrolled = Enrollment::whereHas('courseSection.semester', function ($q) {
            $q->where('is_current', true);
        })->where('status', 'approved')->count();

        return view('pages.admin.courses.index', compact('courses', 'departments', 'totalEnrolled', 'activeEnrolled'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create()
    {
        // Cache static data for 1 hour
        $departments = Cache::remember('all_departments', now()->addHour(), function () {
            return Department::with('faculty')->get();
        });

        $majors = Cache::remember('all_majors', now()->addHour(), function () {
            return Major::with('department')->get();
        });

        return view('pages.admin.courses.create', compact('departments', 'majors'));
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Course creation attempt', [
            'input' => $request->all(),
            'user_id' => auth()->id(),
        ]);

        // Clean empty strings for numeric/optional fields from form selects
        $request->merge([
            'year_level' => $request->year_level ?: null,
            'theory_hours' => $request->theory_hours ?: null,
            'lab_hours' => $request->lab_hours ?: null,
        ]);

        $request->validate([
            'code' => 'required|string|max:20|unique:courses',
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'credits' => 'required|integer|min:1|max:10',
            'theory_hours' => 'nullable|integer|min:0|max:20',
            'lab_hours' => 'nullable|integer|min:0|max:20',
            'year_level' => 'nullable|integer|between:1,5',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'major_ids' => 'array',
            'major_ids.*' => 'exists:majors,id',
        ]);

        // Laravel's boolean validation automatically converts 'on' to true
        // and null (when checkbox unchecked) to false
        $courseData = $request->except(['major_ids', '_token']);

        // Handle checkbox
        $courseData['is_active'] = $request->boolean('is_active');

        // Map legacy form fields if needed (defensive)
        if (isset($courseData['hours'])) {
            $courseData['theory_hours'] = $courseData['hours'];
            unset($courseData['hours']);
        }
        if (isset($courseData['level'])) {
            $courseData['year_level'] = $courseData['level'];
            unset($courseData['level']);
        }

        // Only keep fields that are actually fillable in the model (prevents mass assignment issues)
        $fillable = (new \App\Models\Course)->getFillable();
        $courseData = array_intersect_key($courseData, array_flip($fillable));

        // Set sensible defaults for missing optional fields
        $courseData['semester_available'] = $courseData['semester_available'] ?? 'both';
        $courseData['theory_hours'] = $courseData['theory_hours'] ?? 3;
        $courseData['lab_hours'] = $courseData['lab_hours'] ?? 0;
        $courseData['year_level'] = $courseData['year_level'] ?? 1;
        $courseData['is_active'] = $courseData['is_active'] ?? true;

        try {
            $course = Course::create($courseData);

            if ($request->filled('major_ids')) {
                $course->majors()->sync($request->major_ids);
            }

            Cache::forget('admin_course_offerings');

            \Illuminate\Support\Facades\Log::info('Course created successfully', ['course_id' => $course->id, 'code' => $course->code]);

            return redirect()->route('admin.courses.index')
                ->with('success', __('Course created successfully.'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Course creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to create course: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course)
    {
        $course->load([
            'department.faculty',
            'majors',
            'prerequisites.prerequisiteCourse',
        ]);

        // Get offerings with eager loading
        $offerings = CourseOffering::where('course_id', $course->id)
            ->with([
                'semester.academicYear',
                'teacher.profile',
                'enrollments' => function ($query) {
                    $query->where('status', 'approved');
                },
            ])
            ->get();

        return view('pages.admin.courses.show', compact('course', 'offerings'));
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course)
    {
        $course->load('majors');

        // Cache static data for 1 hour
        $departments = Cache::remember('all_departments', now()->addHour(), function () {
            return Department::with('faculty')->get();
        });

        $majors = Cache::remember('all_majors', now()->addHour(), function () {
            return Major::with('department')->get();
        });

        return view('pages.admin.courses.edit', compact('course', 'departments', 'majors'));
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, Course $course)
    {
        \Illuminate\Support\Facades\Log::info('Course update attempt', [
            'course_id' => $course->id,
            'input' => $request->all(),
            'user_id' => auth()->id(),
        ]);

        // Clean empty strings for numeric/optional fields
        $request->merge([
            'year_level' => $request->year_level ?: null,
            'theory_hours' => $request->theory_hours ?: null,
            'lab_hours' => $request->lab_hours ?: null,
        ]);

        $request->validate([
            'code' => 'required|string|max:20|unique:courses,code,'.$course->id,
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'credits' => 'required|integer|min:1|max:10',
            'theory_hours' => 'nullable|integer|min:0|max:20',
            'lab_hours' => 'nullable|integer|min:0|max:20',
            'year_level' => 'nullable|integer|between:1,5',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'major_ids' => 'array',
            'major_ids.*' => 'exists:majors,id',
        ]);

        $courseData = $request->except(['major_ids', '_token', '_method']);

        // Handle checkbox reliably
        $courseData['is_active'] = $request->boolean('is_active');

        // Map legacy fields
        if (isset($courseData['hours'])) {
            $courseData['theory_hours'] = $courseData['hours'];
            unset($courseData['hours']);
        }
        if (isset($courseData['level'])) {
            $courseData['year_level'] = $courseData['level'];
            unset($courseData['level']);
        }

        // Only keep fillable fields
        $fillable = (new \App\Models\Course)->getFillable();
        $courseData = array_intersect_key($courseData, array_flip($fillable));

        // Set defaults
        $courseData['semester_available'] = $courseData['semester_available'] ?? $course->semester_available ?? 'both';
        $courseData['theory_hours'] = $courseData['theory_hours'] ?? $course->theory_hours ?? 3;
        $courseData['lab_hours'] = $courseData['lab_hours'] ?? $course->lab_hours ?? 0;
        $courseData['year_level'] = $courseData['year_level'] ?? $course->year_level ?? 1;
        $courseData['is_active'] = $courseData['is_active'] ?? $course->is_active ?? true;

        try {
            $course->update($courseData);

            if ($request->has('major_ids')) {
                $course->majors()->sync($request->major_ids);
            } else {
                $course->majors()->detach();
            }

            Cache::forget('admin_course_offerings');

            \Illuminate\Support\Facades\Log::info('Course updated successfully', ['course_id' => $course->id]);

            return redirect()->route('admin.courses.index')
                ->with('success', __('Course updated successfully.'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Course update failed', [
                'course_id' => $course->id,
                'error' => $e->getMessage(),
                'input' => $request->all(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to update course: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Course $course)
    {
        if ($course->offerings()->count() > 0) {
            return back()->with('error', __('lms::messages.course_has_sections'));
        }
        $course->delete();

        // Clear course listings cache
        Cache::forget('admin_course_offerings');

        return back()->with('success', __('lms::messages.course_deleted'));
    }

    // ==================== Course Sections ====================

    public function createSection(Course $course)
    {
        // Cache teachers for 1 hour
        $teachers = Cache::remember('all_teachers', now()->addHour(), function () {
            return User::role('teacher')->with('profile')->get();
        });

        return view('pages.admin.courses.sections.create', compact('course', 'teachers'));
    }

    public function storeSection(Request $request, Course $course)
    {
        $request->validate([
            'section_name' => 'required|string|max:10',
            'semester_id' => 'required|exists:semesters,id',
            'teacher_id' => 'required|exists:users,id',
            'capacity' => 'required|integer|min:1|max:500',
            'schedule' => 'nullable|string|max:255',
            'room' => 'nullable|string|max:50',
        ]);

        $course->sections()->create([
            'section_name' => $request->section_name,
            'semester_id' => $request->semester_id,
            'teacher_id' => $request->teacher_id,
            'capacity' => $request->capacity,
            'schedule' => $request->schedule,
            'room' => $request->room,
            'enrolled_count' => 0,
            'is_active' => true,
            'is_visible_to_students' => true,
        ]);

        // Clear course offerings cache
        Cache::forget('admin_course_offerings');

        return redirect()->route('admin.courses.show', $course)
            ->with('success', __('lms::messages.section_created'));
    }

    public function destroySection(CourseOffering $section)
    {
        if ($section->enrollments()->count() > 0) {
            return back()->with('error', __('lms::messages.section_has_students'));
        }
        $section->delete();

        // Clear course offerings cache
        Cache::forget('admin_course_offerings');

        return back()->with('success', __('lms::messages.section_deleted'));
    }
}

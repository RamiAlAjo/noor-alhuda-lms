<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Attendance;
use App\Models\CourseMaterial;
use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Semester;
use App\Models\StudentFee;
use App\Models\StudentGrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Browse available courses.
     */
    public function browse(): View
    {
        $student = auth()->user();

        // Get available offerings (not enrolled yet) with caching
        $enrolledOfferingIds = Enrollment::where('student_id', $student->id)
            ->pluck('course_offering_id');

        // Cache available courses for 5 minutes
        $sections = Cache::remember("student_available_courses_{$student->id}", now()->addMinutes(5), function () use ($enrolledOfferingIds) {
            return CourseOffering::whereNotIn('id', $enrolledOfferingIds)
                ->with([
                    'course.department',
                    'course.major',
                    'semester.academicYear',
                    'teacher.profile',
                ])
                ->where('is_active', true)
                ->where('is_visible_to_students', true)
                ->get();
        });

        // Get current semester for enrollment status with caching
        $currentSemester = Cache::remember('current_semester', now()->addMinutes(5), function () {
            return Semester::getCurrent();
        });

        return view('pages.student.courses.browse', compact('sections', 'currentSemester'));
    }

    /**
     * Enroll in a course.
     */
    public function enroll(Request $request)
    {
        $request->validate([
            'offering_id' => 'required|exists:course_offerings,id',
        ]);

        $student = auth()->user();
        $offering = CourseOffering::findOrFail($request->offering_id);

        // Check if enrollment period is open
        $semester = $offering->semester;
        if ($semester && ! $semester->isEnrollmentOpen()) {
            $status = $semester->getEnrollmentStatus();
            if ($status === 'not_configured') {
                return back()->with('error', __('Enrollment period has not been configured for this semester.'));
            } elseif ($status === 'upcoming') {
                return back()->with('error', __('Enrollment period has not started yet.'));
            } else {
                return back()->with('error', __('Enrollment period has ended.'));
            }
        }

        // Check if already enrolled or has pending request
        $existingEnrollment = Enrollment::where('student_id', $student->id)
            ->where('course_offering_id', $offering->id)
            ->first();

        if ($existingEnrollment) {
            if ($existingEnrollment->status === 'pending') {
                return back()->with('error', __('lms::messages.enrollment_pending'));
            }

            return back()->with('error', __('lms::messages.already_enrolled'));
        }

        // Check capacity
        if ($offering->enrolled_count >= $offering->capacity) {
            return back()->with('error', __('lms::messages.section_full'));
        }

        // Create enrollment request with pending status
        Enrollment::create([
            'student_id' => $student->id,
            'course_offering_id' => $offering->id,
            'status' => 'pending',
        ]);

        // Clear the cache for available courses
        Cache::forget("student_available_courses_{$student->id}");

        return back()->with('success', __('lms::messages.enrollment_request_submitted'));
    }

    /**
     * Drop a course.
     */
    public function drop(Enrollment $enrollment)
    {
        $student = auth()->user();

        if ($enrollment->student_id !== $student->id) {
            abort(403);
        }

        // Check if drop period is open
        $offering = $enrollment->offering;
        $semester = $offering?->semester;

        if ($semester && ! $semester->isDropOpen()) {
            $status = $semester->getDropStatus();
            if ($status === 'not_configured') {
                return back()->with('error', __('Drop period has not been configured for this semester.'));
            } elseif ($status === 'upcoming') {
                return back()->with('error', __('Drop period has not started yet.'));
            } else {
                return back()->with('error', __('Drop period has ended.'));
            }
        }

        $enrollment->drop();

        // Clear the cache for available courses
        Cache::forget("student_available_courses_{$student->id}");

        return back()->with('success', __('lms::messages.drop_success'));
    }

    /**
     * View enrolled courses.
     */
    public function myCourses(Request $request): View
    {
        $student = auth()->user();

        $enrollments = Enrollment::with([
            'offering.course.department',
            'offering.course.major',
            'offering.semester.academicYear',
            'offering.teacher.profile',
        ])
            ->where('student_id', $student->id)
            ->where('status', 'approved')
            ->orderBy('enrolled_at', 'desc')
            ->paginate(12);

        // Calculate GPA with caching
        $gpa = Cache::remember("student_gpa_{$student->id}", now()->addMinutes(5), function () use ($student) {
            return $this->calculateGpa($student->id);
        });

        // Get pending fees count
        $pendingFees = StudentFee::where('student_id', $student->id)
            ->where('status', 'pending')
            ->count();

        return view('pages.student.courses.index', compact('enrollments', 'gpa', 'pendingFees'));
    }

    /**
     * Calculate student GPA.
     */
    private function calculateGpa(int $studentId): float
    {
        $grades = StudentGrade::where('student_id', $studentId)
            ->whereNotNull('grade')
            ->with('assessment.offering.course')
            ->get();

        if ($grades->isEmpty()) {
            return 0.0;
        }

        $totalPoints = 0;
        $totalCredits = 0;

        foreach ($grades as $grade) {
            // Skip if assessment or offering is null
            if (! $grade->assessment || ! $grade->assessment->offering) {
                continue;
            }
            $percentage = $grade->percentage;
            $credits = $grade->assessment->offering->course->credits ?? 3;

            $points = $this->getGradePoints($percentage);
            $totalPoints += $points * $credits;
            $totalCredits += $credits;
        }

        return $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.0;
    }

    /**
     * Convert percentage to grade points.
     */
    private function getGradePoints(float $percentage): float
    {
        return match (true) {
            $percentage >= 90 => 4.0,
            $percentage >= 80 => 3.7,
            $percentage >= 75 => 3.3,
            $percentage >= 70 => 3.0,
            $percentage >= 65 => 2.7,
            $percentage >= 60 => 2.3,
            $percentage >= 55 => 2.0,
            $percentage >= 50 => 1.7,
            default => 0.0,
        };
    }

    /**
     * Show course details.
     */
    public function show(CourseOffering $offering): View
    {
        $student = auth()->user();

        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('course_offering_id', $offering->id)
            ->first();

        if (! $enrollment || $enrollment->status !== 'approved') {
            abort(403, 'You are not enrolled in this course.');
        }

        // Eager load materials with uploader relationship
        $materials = $offering->materials()
            ->with('uploadedBy.profile')
            ->orderBy('created_at', 'desc')
            ->get();

        // Group materials by week
        $materialsByWeek = $materials->groupBy('week');

        // Eager load assessments with assessment type
        $assessments = $offering->assessments()
            ->with('assessmentType')
            ->orderBy('due_date', 'asc')
            ->get();

        // Get upcoming assessments
        $upcomingAssessments = $assessments->filter(function ($assessment) {
            return $assessment->due_date && $assessment->due_date->isFuture();
        });

        $attendance = Attendance::where('student_id', $student->id)
            ->where('course_offering_id', $offering->id)
            ->get();

        return view('pages.student.courses.show', compact('offering', 'enrollment', 'materials', 'materialsByWeek', 'assessments', 'upcomingAssessments', 'attendance'));
    }

    /**
     * View course material.
     */
    public function viewMaterial(CourseMaterial $material): View
    {
        $student = auth()->user();

        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('course_offering_id', $material->course_offering_id)
            ->first();

        if (! $enrollment || $enrollment->status !== 'approved') {
            abort(403, 'You are not enrolled in this course.');
        }

        $material->increment('view_count');

        return view('pages.student.courses.material', compact('material'));
    }

    /**
     * Get course progress.
     */
    public function progress(Enrollment $enrollment): array
    {
        $student = auth()->user();

        if ($enrollment->student_id !== $student->id) {
            abort(403);
        }

        return [
            'completed_activities' => $enrollment->completed_activities,
            'total_activities' => $enrollment->total_activities,
            'progress_percentage' => $enrollment->progress_percentage,
        ];
    }

    /**
     * View course grades.
     */
    public function grades(CourseOffering $offering): View
    {
        $student = auth()->user();

        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('course_offering_id', $offering->id)
            ->first();

        if (! $enrollment || $enrollment->status !== 'approved') {
            abort(403, 'You are not enrolled in this course.');
        }

        $grades = StudentGrade::where('student_id', $student->id)
            ->whereHas('assessment', function ($query) use ($offering) {
                $query->where('course_offering_id', $offering->id);
            })
            ->with(['assessment.assessmentType', 'gradedBy.profile'])
            ->get();

        // Calculate current grade
        $currentGrade = 0;
        $totalWeight = 0;
        foreach ($grades as $grade) {
            if ($grade->grade && $grade->assessment->weight) {
                $percentage = ($grade->grade / $grade->assessment->max_grade) * 100;
                $currentGrade += $percentage * ($grade->assessment->weight / 100);
                $totalWeight += $grade->assessment->weight;
            }
        }

        // Calculate letter grade
        $letterGrade = match (true) {
            $currentGrade >= 90 => 'A',
            $currentGrade >= 80 => 'B',
            $currentGrade >= 70 => 'C',
            $currentGrade >= 60 => 'D',
            default => 'F',
        };

        // Calculate GPA points
        $gpaPoints = match (true) {
            $currentGrade >= 90 => 4.0,
            $currentGrade >= 80 => 3.0,
            $currentGrade >= 70 => 2.0,
            $currentGrade >= 60 => 1.0,
            default => 0.0,
        };

        return view('pages.student.courses.grades', compact('offering', 'enrollment', 'grades', 'currentGrade', 'letterGrade', 'gpaPoints'));
    }

    /**
     * View course attendance.
     */
    public function attendance(CourseOffering $offering): View
    {
        $student = auth()->user();

        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('course_offering_id', $offering->id)
            ->first();

        if (! $enrollment || $enrollment->status !== 'approved') {
            abort(403, 'You are not enrolled in this course.');
        }

        $attendance = Attendance::where('student_id', $student->id)
            ->where('course_offering_id', $offering->id)
            ->orderBy('date', 'desc')
            ->get();

        // Calculate attendance statistics
        $totalSessions = $attendance->count();
        $presentCount = $attendance->where('status', 'present')->count();
        $absentCount = $attendance->where('status', 'absent')->count();
        $lateCount = $attendance->where('status', 'late')->count();
        $excusedCount = $attendance->where('status', 'excused')->count();

        return view('pages.student.courses.attendance', compact(
            'offering', 'enrollment', 'attendance',
            'totalSessions', 'presentCount', 'absentCount', 'lateCount', 'excusedCount'
        ));
    }

    /**
     * View course participants.
     */
    public function participants(CourseOffering $offering): View
    {
        $student = auth()->user();

        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('course_offering_id', $offering->id)
            ->first();

        if (! $enrollment || $enrollment->status !== 'approved') {
            abort(403, 'You are not enrolled in this course.');
        }

        $participants = Enrollment::where('course_offering_id', $offering->id)
            ->where('status', 'approved')
            ->with(['student.profile', 'student.major'])
            ->get();

        return view('pages.student.courses.participants', compact('offering', 'enrollment', 'participants'));
    }

    /**
     * View course materials.
     */
    public function materials(CourseOffering $offering): View
    {
        $student = auth()->user();

        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('course_offering_id', $offering->id)
            ->first();

        if (! $enrollment || $enrollment->status !== 'approved') {
            abort(403, 'You are not enrolled in this course.');
        }

        $materials = $offering->materials()
            ->with('uploadedBy.profile')
            ->orderBy('week')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.student.courses.materials', compact('offering', 'enrollment', 'materials'));
    }

    /**
     * Bulk export course data.
     */
    public function bulkExport(Request $request)
    {
        $request->validate([
            'courses' => 'required|array|min:1',
            'courses.*' => 'integer|exists:enrollments,id',
        ]);

        $student = auth()->user();
        $courseIds = $request->courses;

        // Verify ownership
        $enrollments = Enrollment::where('student_id', $student->id)
            ->whereIn('id', $courseIds)
            ->with(['offering.course', 'offering.semester'])
            ->get();

        if ($enrollments->isEmpty()) {
            return back()->with('error', __('No valid courses selected'));
        }

        // Generate CSV content
        $csvContent = $this->generateCoursesCsv($enrollments);

        // Return CSV download
        $filename = 'student_courses_'.now()->format('Y-m-d_H-i-s').'.csv';

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    /**
     * Generate CSV content for courses.
     */
    private function generateCoursesCsv($enrollments): string
    {
        $csv = fopen('php://temp', 'r+');

        // Headers
        fputcsv($csv, [
            'Course Code',
            'Course Name',
            'Section',
            'Semester',
            'Status',
            'Credits',
            'Teacher',
        ]);

        // Data
        foreach ($enrollments as $enrollment) {
            fputcsv($csv, [
                $enrollment->offering->course->code ?? '',
                $enrollment->offering->course->name ?? '',
                $enrollment->offering->section_name ?? '',
                $enrollment->offering->semester->name ?? '',
                $enrollment->status,
                $enrollment->offering->course->credits ?? 0,
                $enrollment->offering->teacher->full_name ?? '',
            ]);
        }

        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        return $content;
    }
}

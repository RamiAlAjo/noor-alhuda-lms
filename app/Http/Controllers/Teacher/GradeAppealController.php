<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\GradeAppeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeAppealController extends Controller
{
    /**
     * Display a listing of grade appeals for teacher's courses.
     */
    public function index(Request $request)
    {
        $teacherId = Auth::id();

        $query = GradeAppeal::with(['student', 'grade', 'assessment', 'enrollment.offering.course'])
            ->whereHas('enrollment.offering', function ($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })
            ->orWhereHas('assessment.offering', function ($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            });

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by course
        if ($request->filled('course')) {
            $query->whereHas('enrollment.offering.course', function ($q) use ($request) {
                $q->where('id', $request->course);
            });
        }

        $appeals = $query->latest()->paginate(10);

        // Get teacher's courses for filter
        $courses = Auth::user()->taughtCourses()
            ->with('course')
            ->get()
            ->pluck('course', 'course.id')
            ->unique();

        return view('pages.teacher.appeals.index', compact('appeals', 'courses'));
    }

    /**
     * Display the specified grade appeal.
     */
    public function show(GradeAppeal $appeal)
    {
        // Verify teacher has access to this appeal
        $this->authorizeTeacher($appeal);

        $appeal->load(['student.profile', 'grade', 'assessment', 'enrollment.offering.course', 'reviewer', 'escalatedTo']);

        return view('pages.teacher.appeals.show', compact('appeal'));
    }

    /**
     * Update the appeal status to under review.
     */
    public function review(GradeAppeal $appeal)
    {
        // Verify teacher has access to this appeal
        $this->authorizeTeacher($appeal);

        if (! $appeal->isPending()) {
            return back()->with('error', __('lms.appeal_not_pending'));
        }

        $appeal->markAsUnderReview();

        return back()->with('success', __('lms.appeal_under_review'));
    }

    /**
     * Approve the grade appeal.
     */
    public function approve(Request $request, GradeAppeal $appeal)
    {
        // Verify teacher has access to this appeal
        $this->authorizeTeacher($appeal);

        if (! in_array($appeal->status, [GradeAppeal::STATUS_PENDING, GradeAppeal::STATUS_UNDER_REVIEW])) {
            return back()->with('error', __('lms.appeal_cannot_be_processed'));
        }

        $validated = $request->validate([
            'teacher_response' => 'required|string|min:10',
            'new_grade' => 'nullable|numeric|min:0|max:100',
        ]);

        // Update the grade if a new grade is provided
        if (! empty($validated['new_grade']) && $appeal->grade) {
            $grade = $appeal->grade;
            $grade->grade = $validated['new_grade'];
            $grade->letter_grade = $this->calculateLetterGrade($validated['new_grade']);
            $grade->grade_points = $this->calculateGradePoints($validated['new_grade']);
            $grade->save();
        }

        $appeal->approve(Auth::id(), $validated['teacher_response']);

        return redirect()->route('teacher.appeals.index')
            ->with('success', __('lms.appeal_approved_successfully'));
    }

    /**
     * Reject the grade appeal.
     */
    public function reject(Request $request, GradeAppeal $appeal)
    {
        // Verify teacher has access to this appeal
        $this->authorizeTeacher($appeal);

        if (! in_array($appeal->status, [GradeAppeal::STATUS_PENDING, GradeAppeal::STATUS_UNDER_REVIEW])) {
            return back()->with('error', __('lms.appeal_cannot_be_processed'));
        }

        $validated = $request->validate([
            'teacher_response' => 'required|string|min:10',
        ]);

        $appeal->reject(Auth::id(), $validated['teacher_response']);

        return redirect()->route('teacher.appeals.index')
            ->with('success', __('lms.appeal_rejected_successfully'));
    }

    /**
     * Escalate the appeal to admin.
     */
    public function escalate(Request $request, GradeAppeal $appeal)
    {
        // Verify teacher has access to this appeal
        $this->authorizeTeacher($appeal);

        if (! in_array($appeal->status, [GradeAppeal::STATUS_PENDING, GradeAppeal::STATUS_UNDER_REVIEW])) {
            return back()->with('error', __('lms.appeal_cannot_be_escalated'));
        }

        $validated = $request->validate([
            'escalation_reason' => 'required|string|min:10',
        ]);

        // Find an admin to escalate to
        $admin = \App\Models\User::role('admin')->first();

        if (! $admin) {
            return back()->with('error', __('lms.no_admin_available'));
        }

        $appeal->escalate($admin->id);
        $appeal->update(['admin_notes' => $validated['escalation_reason']]);

        return redirect()->route('teacher.appeals.index')
            ->with('success', __('lms.appeal_escalated_successfully'));
    }

    /**
     * Verify the teacher has access to the appeal.
     */
    private function authorizeTeacher(GradeAppeal $appeal): void
    {
        $teacherId = Auth::id();
        $hasAccess = false;

        if ($appeal->enrollment && $appeal->enrollment->offering) {
            $hasAccess = $appeal->enrollment->offering->teacher_id == $teacherId;
        }

        if (! $hasAccess && $appeal->assessment && $appeal->assessment->offering) {
            $hasAccess = $appeal->assessment->offering->teacher_id == $teacherId;
        }

        if (! $hasAccess) {
            abort(403, __('lms.unauthorized_access'));
        }
    }

    /**
     * Calculate letter grade from numeric grade.
     */
    private function calculateLetterGrade(float $grade): string
    {
        if ($grade >= 90) {
            return 'A';
        }
        if ($grade >= 80) {
            return 'B';
        }
        if ($grade >= 70) {
            return 'C';
        }
        if ($grade >= 60) {
            return 'D';
        }

        return 'F';
    }

    /**
     * Calculate grade points from numeric grade.
     */
    private function calculateGradePoints(float $grade): float
    {
        if ($grade >= 90) {
            return 4.0;
        }
        if ($grade >= 85) {
            return 3.7;
        }
        if ($grade >= 80) {
            return 3.3;
        }
        if ($grade >= 75) {
            return 3.0;
        }
        if ($grade >= 70) {
            return 2.7;
        }
        if ($grade >= 65) {
            return 2.3;
        }
        if ($grade >= 60) {
            return 2.0;
        }
        if ($grade >= 55) {
            return 1.7;
        }
        if ($grade >= 50) {
            return 1.0;
        }

        return 0.0;
    }
}

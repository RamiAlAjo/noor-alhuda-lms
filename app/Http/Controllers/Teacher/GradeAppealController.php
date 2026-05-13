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
    /**
     * Bulk approve grade appeals.
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'appeal_ids' => 'required|array',
            'appeal_ids.*' => 'exists:grade_appeals,id',
        ]);

        $appeals = GradeAppeal::whereIn('id', $request->appeal_ids)->get();
        $approved = 0;

        foreach ($appeals as $appeal) {
            try {
                $this->authorizeTeacher($appeal);

                if (! in_array($appeal->status, [GradeAppeal::STATUS_PENDING, GradeAppeal::STATUS_UNDER_REVIEW])) {
                    continue;
                }

                $this->approveAppeal($appeal, $request);
                $approved++;
            } catch (\Exception $e) {
                // Continue with other appeals
            }
        }

        return redirect()->back()->with('success', "Successfully approved {$approved} appeal(s).");
    }

    /**
     * Bulk reject grade appeals.
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'appeal_ids' => 'required|array',
            'appeal_ids.*' => 'exists:grade_appeals,id',
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $appeals = GradeAppeal::whereIn('id', $request->appeal_ids)->get();
        $rejected = 0;

        foreach ($appeals as $appeal) {
            try {
                $this->authorizeTeacher($appeal);

                if (! in_array($appeal->status, [GradeAppeal::STATUS_PENDING, GradeAppeal::STATUS_UNDER_REVIEW])) {
                    continue;
                }

                $this->rejectAppeal($appeal, $request);
                $rejected++;
            } catch (\Exception $e) {
                // Continue with other appeals
            }
        }

        return redirect()->back()->with('success', "Successfully rejected {$rejected} appeal(s).");
    }

    /**
     * Bulk escalate grade appeals.
     */
    public function bulkEscalate(Request $request)
    {
        $request->validate([
            'appeal_ids' => 'required|array',
            'appeal_ids.*' => 'exists:grade_appeals,id',
            'escalation_reason' => 'required|string|max:1000',
        ]);

        $appeals = GradeAppeal::whereIn('id', $request->appeal_ids)->get();
        $escalated = 0;

        foreach ($appeals as $appeal) {
            try {
                $this->authorizeTeacher($appeal);

                if (! in_array($appeal->status, [GradeAppeal::STATUS_PENDING, GradeAppeal::STATUS_UNDER_REVIEW])) {
                    continue;
                }

                $this->escalateAppeal($appeal, $request);
                $escalated++;
            } catch (\Exception $e) {
                // Continue with other appeals
            }
        }

        return redirect()->back()->with('success', "Successfully escalated {$escalated} appeal(s).");
    }

    private function authorizeTeacher(GradeAppeal $appeal): void
    {
        $teacher = auth()->user();

        if (! $appeal->enrollment || $appeal->enrollment->offering->teacher_id !== $teacher->id) {
            if (! $appeal->assessment || $appeal->assessment->courseOffering->teacher_id !== $teacher->id) {
                abort(403, 'You are not authorized to manage this appeal.');
            }
        }
    }

    private function approveAppeal(GradeAppeal $appeal, Request $request)
    {
        $validated = $request->validate([
            'new_grade' => 'nullable|numeric|min:0|max:100',
            'approval_reason' => 'nullable|string|max:1000',
        ]);

        $appeal->update([
            'status' => GradeAppeal::STATUS_APPROVED,
            'reviewer_id' => auth()->id(),
            'reviewed_at' => now(),
            'reviewer_notes' => $validated['approval_reason'] ?? null,
        ]);

        // Update grade if new grade is provided
        if (! empty($validated['new_grade']) && $appeal->grade) {
            $grade = $appeal->grade;
            $grade->update([
                'grade' => $validated['new_grade'],
                'percentage' => $validated['new_grade'],
                'feedback' => ($grade->feedback ? $grade->feedback."\n\n" : '')."Grade updated due to appeal approval: {$validated['approval_reason']}",
                'graded_at' => now(),
                'graded_by' => auth()->id(),
            ]);
        }

        // Log the action
        \App\Services\AuditLogService::logGradeAppeal($appeal, 'approved');
    }

    private function rejectAppeal(GradeAppeal $appeal, Request $request)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $appeal->update([
            'status' => GradeAppeal::STATUS_REJECTED,
            'reviewer_id' => auth()->id(),
            'reviewed_at' => now(),
            'reviewer_notes' => $validated['rejection_reason'],
        ]);

        // Log the action
        \App\Services\AuditLogService::logGradeAppeal($appeal, 'rejected');
    }

    private function escalateAppeal(GradeAppeal $appeal, Request $request)
    {
        $validated = $request->validate([
            'escalation_reason' => 'required|string|max:1000',
        ]);

        $appeal->update([
            'status' => GradeAppeal::STATUS_ESCALATED,
            'reviewer_id' => auth()->id(),
            'reviewed_at' => now(),
            'reviewer_notes' => $validated['escalation_reason'],
            'escalated_to' => 1, // Assuming admin ID is 1, you might want to make this configurable
        ]);

        // Log the action
        \App\Services\AuditLogService::logGradeAppeal($appeal, 'escalated');
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

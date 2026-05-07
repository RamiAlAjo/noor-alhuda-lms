<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\GradeAppeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeAppealController extends Controller
{
    /**
     * Display a listing of all grade appeals.
     */
    public function index(Request $request)
    {
        $query = GradeAppeal::with(['student', 'grade', 'assessment', 'enrollment.offering.course', 'reviewer', 'escalatedTo']);

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

        // Filter by student
        if ($request->filled('student')) {
            $query->where('student_id', $request->student);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $appeals = $query->latest()->paginate(15);

        // Get statuses for filter
        $statuses = GradeAppeal::getStatuses();

        return view('pages.admin.appeals.index', compact('appeals', 'statuses'));
    }

    /**
     * Display the specified grade appeal.
     */
    public function show(GradeAppeal $appeal)
    {
        $appeal->load(['student.profile', 'grade', 'assessment', 'enrollment.offering.course', 'reviewer', 'escalatedTo']);

        return view('pages.admin.appeals.show', compact('appeal'));
    }

    /**
     * Update the appeal status.
     */
    public function updateStatus(Request $request, GradeAppeal $appeal)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,under_review,approved,rejected,escalated',
            'admin_notes' => 'nullable|string',
        ]);

        $appeal->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? $appeal->admin_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', __('lms.appeal_status_updated'));
    }

    /**
     * Approve the grade appeal (admin override).
     */
    public function approve(Request $request, GradeAppeal $appeal)
    {
        $validated = $request->validate([
            'teacher_response' => 'required|string|min:10',
            'new_grade' => 'nullable|numeric|min:0|max:100',
            'admin_notes' => 'nullable|string',
        ]);

        // Update the grade if a new grade is provided
        if (! empty($validated['new_grade']) && $appeal->grade) {
            $grade = $appeal->grade;
            $grade->grade = $validated['new_grade'];
            $grade->letter_grade = $this->calculateLetterGrade($validated['new_grade']);
            $grade->grade_points = $this->calculateGradePoints($validated['new_grade']);
            $grade->save();
        }

        $appeal->update([
            'status' => GradeAppeal::STATUS_APPROVED,
            'teacher_response' => $validated['teacher_response'],
            'admin_notes' => $validated['admin_notes'] ?? $appeal->admin_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.appeals.index')
            ->with('success', __('lms.appeal_approved_successfully'));
    }

    /**
     * Reject the grade appeal (admin override).
     */
    public function reject(Request $request, GradeAppeal $appeal)
    {
        $validated = $request->validate([
            'teacher_response' => 'required|string|min:10',
            'admin_notes' => 'nullable|string',
        ]);

        $appeal->update([
            'status' => GradeAppeal::STATUS_REJECTED,
            'teacher_response' => $validated['teacher_response'],
            'admin_notes' => $validated['admin_notes'] ?? $appeal->admin_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.appeals.index')
            ->with('success', __('lms.appeal_rejected_successfully'));
    }

    /**
     * Get escalated appeals.
     */
    public function escalated(Request $request)
    {
        $query = GradeAppeal::with(['student', 'grade', 'assessment', 'enrollment.offering.course', 'escalatedTo'])
            ->escalated();

        $appeals = $query->latest()->paginate(15);

        return view('pages.admin.appeals.escalated', compact('appeals'));
    }

    /**
     * Export appeals to CSV.
     */
    public function export(Request $request)
    {
        $query = GradeAppeal::with(['student', 'grade', 'assessment', 'enrollment.offering.course', 'reviewer']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $appeals = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="grade_appeals.csv"',
        ];

        $callback = function () use ($appeals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID',
                'Student',
                'Subject',
                'Current Grade',
                'Requested Grade',
                'Status',
                'Submitted At',
                'Reviewed By',
                'Reviewed At',
            ]);

            foreach ($appeals as $appeal) {
                fputcsv($file, [
                    $appeal->id,
                    $appeal->student->name,
                    $appeal->subject,
                    $appeal->current_grade,
                    $appeal->requested_grade,
                    $appeal->status,
                    $appeal->created_at->format('Y-m-d H:i'),
                    $appeal->reviewer?->name,
                    $appeal->reviewed_at?->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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

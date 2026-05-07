<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ExcusedAbsence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExcusedAbsenceController extends Controller
{
    /**
     * Display a listing of excused absence requests for teacher's courses.
     */
    public function index(Request $request)
    {
        $teacherId = Auth::id();

        // Get teacher's course offerings - specify table to avoid ambiguous column
        $offeringIds = Auth::user()->taughtCourses()->pluck('course_offerings.id');

        $query = ExcusedAbsence::with(['student', 'courseOffering.course'])
            ->whereIn('course_offering_id', $offeringIds);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by course
        if ($request->filled('course')) {
            $query->where('course_offering_id', $request->course);
        }

        $absences = $query->latest()->paginate(15);

        $statuses = ExcusedAbsence::getStatuses();

        return view('pages.teacher.excused-absences.index', compact('absences', 'statuses'));
    }

    /**
     * Display the specified excused absence request.
     */
    public function show(ExcusedAbsence $excusedAbsence)
    {
        $this->authorizeAccess($excusedAbsence);

        $excusedAbsence->load(['student.profile', 'courseOffering.course', 'reviewer']);

        return view('pages.teacher.excused-absences.show', compact('excusedAbsence'));
    }

    /**
     * Approve the excused absence request.
     */
    public function approve(Request $request, ExcusedAbsence $excusedAbsence)
    {
        $this->authorizeAccess($excusedAbsence);

        if (! $excusedAbsence->isPending()) {
            return back()->with('error', __('lms.request_not_pending'));
        }

        $validated = $request->validate([
            'review_notes' => 'nullable|string',
        ]);

        $excusedAbsence->approve(Auth::id(), $validated['review_notes'] ?? null);

        // Update attendance records
        $this->updateAttendance($excusedAbsence);

        return redirect()->route('teacher.excused-absences.index')
            ->with('success', __('lms.excused_absence_approved'));
    }

    /**
     * Reject the excused absence request.
     */
    public function reject(Request $request, ExcusedAbsence $excusedAbsence)
    {
        $this->authorizeAccess($excusedAbsence);

        if (! $excusedAbsence->isPending()) {
            return back()->with('error', __('lms.request_not_pending'));
        }

        $validated = $request->validate([
            'review_notes' => 'required|string|min:5',
        ]);

        $excusedAbsence->reject(Auth::id(), $validated['review_notes']);

        return redirect()->route('teacher.excused-absences.index')
            ->with('success', __('lms.excused_absence_rejected'));
    }

    /**
     * Authorize access to the excused absence request.
     */
    private function authorizeAccess(ExcusedAbsence $excusedAbsence): void
    {
        $teacherId = Auth::id();
        $hasAccess = Auth::user()->taughtCourses()
            ->where('course_offerings.id', $excusedAbsence->course_offering_id)
            ->exists();

        if (! $hasAccess) {
            abort(403, __('lms.unauthorized_access'));
        }
    }

    /**
     * Update attendance records for approved excused absence.
     */
    private function updateAttendance(ExcusedAbsence $absence): void
    {
        $startDate = $absence->absence_date->copy();
        $endDate = $absence->end_date ?? $absence->absence_date;

        while ($startDate->lte($endDate)) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $absence->student_id,
                    'course_offering_id' => $absence->course_offering_id,
                    'date' => $startDate->format('Y-m-d'),
                ],
                [
                    'status' => 'excused',
                    'notes' => "Excused Absence: {$absence->reason_type}",
                    'recorded_by' => Auth::id(),
                ]
            );

            $startDate->addDay();
        }

        $absence->update(['attendance_updated' => true]);
    }
}

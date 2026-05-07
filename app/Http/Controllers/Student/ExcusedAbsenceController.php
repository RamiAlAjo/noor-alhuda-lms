<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\ExcusedAbsence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExcusedAbsenceController extends Controller
{
    /**
     * Display a listing of the student's excused absence requests.
     */
    public function index(Request $request)
    {
        $query = ExcusedAbsence::with(['courseOffering.course', 'reviewer'])
            ->where('student_id', Auth::id());

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by course
        if ($request->filled('course')) {
            $query->where('course_offering_id', $request->course);
        }

        $absences = $query->latest()->paginate(10);

        // Get student's courses for filter
        $courses = Enrollment::where('student_id', Auth::id())
            ->with('offering.course')
            ->approved()
            ->get()
            ->pluck('offering.course', 'offering.course.id')
            ->unique();

        return view('pages.student.excused-absences.index', compact('absences', 'courses'));
    }

    /**
     * Show the form for creating a new excused absence request.
     */
    public function create(Request $request)
    {
        $absenceTypes = ExcusedAbsence::getAbsenceTypes();
        $reasonTypes = ExcusedAbsence::getReasonTypes();

        // Get student's enrolled courses
        $enrollments = Enrollment::where('student_id', Auth::id())
            ->with(['offering.course'])
            ->approved()
            ->get();

        $preselectedCourse = $request->get('course');

        return view('pages.student.excused-absences.create', compact('absenceTypes', 'reasonTypes', 'enrollments', 'preselectedCourse'));
    }

    /**
     * Store a newly created excused absence request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
            'absence_date' => 'required|date',
            'absence_type' => 'required|in:single_day,multiple_days,late_arrival,early_departure',
            'end_date' => 'nullable|date|after_or_equal:absence_date',
            'reason_type' => 'required|in:personal,family_emergency,religious,medical_appointment,other',
            'reason' => 'required|string|min:10',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx',
        ]);

        // Verify student is enrolled in the course
        $enrollment = Enrollment::where('student_id', Auth::id())
            ->where('course_offering_id', $validated['course_offering_id'])
            ->approved()
            ->first();

        if (! $enrollment) {
            return back()->with('error', __('lms.not_enrolled_in_course'));
        }

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('excused-absences', 'public');
                $attachments[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'type' => $file->getClientMimeType(),
                ];
            }
            $validated['attachments'] = $attachments;
        }

        $validated['student_id'] = Auth::id();
        $validated['enrollment_id'] = $enrollment->id;
        $validated['status'] = ExcusedAbsence::STATUS_PENDING;

        $absence = ExcusedAbsence::create($validated);

        return redirect()->route('student.excused-absences.show', $absence)
            ->with('success', __('lms.excused_absence_submitted'));
    }

    /**
     * Display the specified excused absence request.
     */
    public function show(ExcusedAbsence $excusedAbsence)
    {
        // Ensure the student owns this request
        if ($excusedAbsence->student_id !== Auth::id()) {
            abort(403);
        }

        $excusedAbsence->load(['courseOffering.course', 'reviewer']);

        return view('pages.student.excused-absences.show', compact('excusedAbsence'));
    }

    /**
     * Cancel the excused absence request.
     */
    public function destroy(ExcusedAbsence $excusedAbsence)
    {
        // Ensure the student owns this request and it's still pending
        if ($excusedAbsence->student_id !== Auth::id() || ! $excusedAbsence->isPending()) {
            abort(403);
        }

        $excusedAbsence->delete();

        return redirect()->route('student.excused-absences.index')
            ->with('success', __('lms.excused_absence_cancelled'));
    }
}

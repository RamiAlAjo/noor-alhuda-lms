<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradeAppeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GradeAppealController extends Controller
{
    /**
     * Display a listing of the student's grade appeals.
     */
    public function index(Request $request)
    {
        $query = GradeAppeal::with(['grade', 'assessment', 'enrollment.courseOffering.course', 'reviewer'])
            ->where('student_id', Auth::id());

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by course
        if ($request->filled('course')) {
            $query->whereHas('enrollment.courseOffering.course', function ($q) use ($request) {
                $q->where('id', $request->course);
            });
        }

        $appeals = $query->latest()->paginate(10);

        // Get student's courses for filter
        $courses = Enrollment::where('student_id', Auth::id())
            ->with('offering.course')
            ->approved()
            ->get()
            ->pluck('offering.course', 'offering.course.id')
            ->unique();

        return view('pages.student.appeals.index', compact('appeals', 'courses'));
    }

    /**
     * Show the form for creating a new grade appeal.
     */
    public function create(Request $request)
    {
        $gradeId = $request->get('grade_id');
        $assessmentId = $request->get('assessment_id');

        $grade = null;
        $assessment = null;

        if ($gradeId) {
            $grade = Grade::with(['assessment', 'enrollment.courseOffering.course'])
                ->where('student_id', Auth::id())
                ->findOrFail($gradeId);
        }

        if ($assessmentId) {
            $assessment = Assessment::with(['courseOffering.course'])
                ->whereHas('grades', function ($q) {
                    $q->where('student_id', Auth::id());
                })
                ->findOrFail($assessmentId);
        }

        // Get student's enrollments for selection
        $enrollments = Enrollment::where('student_id', Auth::id())
            ->with(['offering.course', 'grades.assessment'])
            ->approved()
            ->get();

        return view('pages.student.appeals.create', compact('grade', 'assessment', 'enrollments'));
    }

    /**
     * Store a newly created grade appeal.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'grade_id' => 'nullable|exists:student_grades,id',
            'enrollment_id' => 'nullable|exists:enrollments,id',
            'assessment_id' => 'nullable|exists:assessments,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'student_justification' => 'required|string|min:20',
            'current_grade' => 'nullable|numeric|min:0|max:100',
            'requested_grade' => 'nullable|numeric|min:0|max:100',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        // Verify the student owns the grade/enrollment
        if (! empty($validated['grade_id'])) {
            $grade = Grade::where('student_id', Auth::id())
                ->findOrFail($validated['grade_id']);
            $validated['current_grade'] = $grade->grade;
        }

        if (! empty($validated['enrollment_id'])) {
            Enrollment::where('student_id', Auth::id())
                ->findOrFail($validated['enrollment_id']);
        }

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('appeals', 'public');
                $attachments[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'type' => $file->getClientMimeType(),
                ];
            }
            $validated['attachments'] = $attachments;
        }

        $validated['student_id'] = Auth::id();
        $validated['status'] = GradeAppeal::STATUS_PENDING;

        $appeal = GradeAppeal::create($validated);

        return redirect()->route('student.appeals.show', $appeal)
            ->with('success', __('lms.appeal_submitted_successfully'));
    }

    /**
     * Display the specified grade appeal.
     */
    public function show(GradeAppeal $appeal)
    {
        // Ensure the student owns this appeal
        if ($appeal->student_id !== Auth::id()) {
            abort(403);
        }

        $appeal->load(['grade', 'assessment', 'enrollment.offering.course', 'reviewer', 'escalatedTo']);

        return view('pages.student.appeals.show', compact('appeal'));
    }

    /**
     * Show the form for editing the grade appeal.
     */
    public function edit(GradeAppeal $appeal)
    {
        // Ensure the student owns this appeal and it's still pending
        if ($appeal->student_id !== Auth::id() || ! $appeal->isPending()) {
            abort(403);
        }

        return view('pages.student.appeals.edit', compact('appeal'));
    }

    /**
     * Update the grade appeal.
     */
    public function update(Request $request, GradeAppeal $appeal)
    {
        // Ensure the student owns this appeal and it's still pending
        if ($appeal->student_id !== Auth::id() || ! $appeal->isPending()) {
            abort(403);
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'student_justification' => 'required|string|min:20',
            'requested_grade' => 'nullable|numeric|min:0|max:100',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            $attachments = $appeal->attachments ?? [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('appeals', 'public');
                $attachments[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'type' => $file->getClientMimeType(),
                ];
            }
            $validated['attachments'] = $attachments;
        }

        $appeal->update($validated);

        return redirect()->route('student.appeals.show', $appeal)
            ->with('success', __('lms.appeal_updated_successfully'));
    }

    /**
     * Remove the grade appeal.
     */
    public function destroy(GradeAppeal $appeal)
    {
        // Ensure the student owns this appeal and it's still pending
        if ($appeal->student_id !== Auth::id() || ! $appeal->isPending()) {
            abort(403);
        }

        // Delete attachments
        if ($appeal->attachments) {
            foreach ($appeal->attachments as $attachment) {
                Storage::disk('public')->delete($attachment['path']);
            }
        }

        $appeal->delete();

        return redirect()->route('student.appeals.index')
            ->with('success', __('lms.appeal_deleted_successfully'));
    }

    /**
     * Withdraw the grade appeal.
     */
    public function withdraw(GradeAppeal $appeal)
    {
        // Ensure the student owns this appeal and it's still pending or under review
        if ($appeal->student_id !== Auth::id() || ! in_array($appeal->status, [GradeAppeal::STATUS_PENDING, GradeAppeal::STATUS_UNDER_REVIEW])) {
            abort(403);
        }

        $appeal->delete();

        return redirect()->route('student.appeals.index')
            ->with('success', __('lms.appeal_withdrawn_successfully'));
    }
}

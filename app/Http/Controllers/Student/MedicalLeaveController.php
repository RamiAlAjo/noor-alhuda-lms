<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\MedicalLeave;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalLeaveController extends Controller
{
    /**
     * Display a listing of the student's medical leave requests.
     */
    public function index(Request $request)
    {
        $query = MedicalLeave::with(['semester', 'reviewer'])
            ->where('student_id', Auth::id());

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by semester
        if ($request->filled('semester')) {
            $query->where('semester_id', $request->semester);
        }

        $leaves = $query->latest()->paginate(10);

        // Get semesters for filter
        $semesters = Semester::orderByDesc('start_date')->get();

        return view('pages.student.medical-leaves.index', compact('leaves', 'semesters'));
    }

    /**
     * Show the form for creating a new medical leave request.
     */
    public function create()
    {
        $leaveTypes = MedicalLeave::getLeaveTypes();
        $semesters = Semester::where('end_date', '>=', now())->orderByDesc('start_date')->get();

        return view('pages.student.medical-leaves.create', compact('leaveTypes', 'semesters'));
    }

    /**
     * Store a newly created medical leave request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'semester_id' => 'nullable|exists:semesters,id',
            'leave_type' => 'required|in:sick,emergency,hospitalization,chronic',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|min:10',
            'medical_notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx',
        ]);

        // Calculate duration
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = \Carbon\Carbon::parse($validated['end_date']);
        $validated['duration_days'] = $startDate->diffInDays($endDate) + 1;

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('medical-leaves', 'public');
                $attachments[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'type' => $file->getClientMimeType(),
                ];
            }
            $validated['attachments'] = $attachments;
        }

        $validated['student_id'] = Auth::id();
        $validated['status'] = MedicalLeave::STATUS_PENDING;

        $leave = MedicalLeave::create($validated);

        return redirect()->route('student.medical-leaves.show', $leave)
            ->with('success', __('lms.medical_leave_submitted'));
    }

    /**
     * Display the specified medical leave request.
     */
    public function show(MedicalLeave $medicalLeave)
    {
        // Ensure the student owns this leave request
        if ($medicalLeave->student_id !== Auth::id()) {
            abort(403);
        }

        $medicalLeave->load(['semester', 'reviewer']);

        return view('pages.student.medical-leaves.show', compact('medicalLeave'));
    }

    /**
     * Show the form for editing the medical leave request.
     */
    public function edit(MedicalLeave $medicalLeave)
    {
        // Ensure the student owns this leave request and it's still pending
        if ($medicalLeave->student_id !== Auth::id() || ! $medicalLeave->isPending()) {
            abort(403);
        }

        $leaveTypes = MedicalLeave::getLeaveTypes();
        $semesters = Semester::orderByDesc('start_date')->get();

        return view('pages.student.medical-leaves.edit', compact('medicalLeave', 'leaveTypes', 'semesters'));
    }

    /**
     * Update the medical leave request.
     */
    public function update(Request $request, MedicalLeave $medicalLeave)
    {
        // Ensure the student owns this leave request and it's still pending
        if ($medicalLeave->student_id !== Auth::id() || ! $medicalLeave->isPending()) {
            abort(403);
        }

        $validated = $request->validate([
            'semester_id' => 'nullable|exists:semesters,id',
            'leave_type' => 'required|in:sick,emergency,hospitalization,chronic',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|min:10',
            'medical_notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx',
        ]);

        // Calculate duration
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = \Carbon\Carbon::parse($validated['end_date']);
        $validated['duration_days'] = $startDate->diffInDays($endDate) + 1;

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            $attachments = $medicalLeave->attachments ?? [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('medical-leaves', 'public');
                $attachments[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'type' => $file->getClientMimeType(),
                ];
            }
            $validated['attachments'] = $attachments;
        }

        $medicalLeave->update($validated);

        return redirect()->route('student.medical-leaves.show', $medicalLeave)
            ->with('success', __('lms.medical_leave_updated'));
    }

    /**
     * Cancel the medical leave request.
     */
    public function destroy(MedicalLeave $medicalLeave)
    {
        // Ensure the student owns this leave request and it's still pending
        if ($medicalLeave->student_id !== Auth::id() || ! $medicalLeave->isPending()) {
            abort(403);
        }

        $medicalLeave->cancel();

        return redirect()->route('student.medical-leaves.index')
            ->with('success', __('lms.medical_leave_cancelled'));
    }
}

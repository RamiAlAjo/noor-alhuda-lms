<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\MedicalLeave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalLeaveController extends Controller
{
    /**
     * Display a listing of all medical leave requests.
     */
    public function index(Request $request)
    {
        $query = MedicalLeave::with(['student', 'semester', 'reviewer']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by leave type
        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('start_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('end_date', '<=', $request->to_date);
        }

        // Filter by student
        if ($request->filled('student')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->student.'%')
                    ->orWhere('user_id', 'like', '%'.$request->student.'%');
            });
        }

        $leaves = $query->latest()->paginate(15);

        $statuses = MedicalLeave::getStatuses();
        $leaveTypes = MedicalLeave::getLeaveTypes();

        return view('pages.admin.medical-leaves.index', compact('leaves', 'statuses', 'leaveTypes'));
    }

    /**
     * Display the specified medical leave request.
     */
    public function show(MedicalLeave $medicalLeave)
    {
        $medicalLeave->load(['student.profile', 'semester', 'reviewer']);

        return view('pages.admin.medical-leaves.show', compact('medicalLeave'));
    }

    /**
     * Approve the medical leave request.
     */
    public function approve(Request $request, MedicalLeave $medicalLeave)
    {
        if (! $medicalLeave->isPending()) {
            return back()->with('error', __('lms.leave_not_pending'));
        }

        $validated = $request->validate([
            'review_notes' => 'nullable|string',
            'affects_attendance' => 'boolean',
            'requires_makeup' => 'boolean',
            'makeup_instructions' => 'nullable|string',
        ]);

        $medicalLeave->approve(Auth::id(), $validated['review_notes'] ?? null);
        $medicalLeave->update([
            'affects_attendance' => $validated['affects_attendance'] ?? true,
            'requires_makeup' => $validated['requires_makeup'] ?? false,
            'makeup_instructions' => $validated['makeup_instructions'] ?? null,
        ]);

        // Update attendance records if affects_attendance is true
        if ($validated['affects_attendance'] ?? true) {
            $this->updateAttendanceForLeave($medicalLeave);
        }

        return redirect()->route('admin.medical-leaves.index')
            ->with('success', __('lms.medical_leave_approved'));
    }

    /**
     * Reject the medical leave request.
     */
    public function reject(Request $request, MedicalLeave $medicalLeave)
    {
        if (! $medicalLeave->isPending()) {
            return back()->with('error', __('lms.leave_not_pending'));
        }

        $validated = $request->validate([
            'review_notes' => 'required|string|min:5',
        ]);

        $medicalLeave->reject(Auth::id(), $validated['review_notes']);

        return redirect()->route('admin.medical-leaves.index')
            ->with('success', __('lms.medical_leave_rejected'));
    }

    /**
     * Export medical leaves to CSV.
     */
    public function export(Request $request)
    {
        $query = MedicalLeave::with(['student', 'semester', 'reviewer']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('start_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('end_date', '<=', $request->to_date);
        }

        $leaves = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="medical_leaves.csv"',
        ];

        $callback = function () use ($leaves) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID',
                'Student',
                'Student ID',
                'Leave Type',
                'Start Date',
                'End Date',
                'Duration (Days)',
                'Status',
                'Submitted At',
                'Reviewed By',
                'Reviewed At',
            ]);

            foreach ($leaves as $leave) {
                fputcsv($file, [
                    $leave->id,
                    $leave->student->name,
                    $leave->student->user_id,
                    $leave->leave_type,
                    $leave->start_date->format('Y-m-d'),
                    $leave->end_date->format('Y-m-d'),
                    $leave->duration_days,
                    $leave->status,
                    $leave->created_at->format('Y-m-d H:i'),
                    $leave->reviewer?->name,
                    $leave->reviewed_at?->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get pending leaves count for dashboard.
     */
    public static function getPendingCount(): int
    {
        return MedicalLeave::pending()->count();
    }

    /**
     * Update attendance records for approved medical leave.
     */
    private function updateAttendanceForLeave(MedicalLeave $leave): void
    {
        // Get all dates within the leave period
        $startDate = $leave->start_date->copy();
        $endDate = $leave->end_date->copy();

        while ($startDate->lte($endDate)) {
            // Skip weekends if needed (optional)
            // if ($startDate->isWeekend()) {
            //     $startDate->addDay();
            //     continue;
            // }

            // Find or create attendance record for this date
            Attendance::updateOrCreate(
                [
                    'student_id' => $leave->student_id,
                    'date' => $startDate->format('Y-m-d'),
                ],
                [
                    'status' => 'excused',
                    'notes' => "Medical Leave: {$leave->leave_type}",
                    'recorded_by' => Auth::id(),
                ]
            );

            $startDate->addDay();
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Semester;
use App\Models\StudentFee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    /**
     * Display all enrollments.
     */
    public function index(Request $request): View
    {
        // Get counts first (cached)
        $stats = Cache::remember('enrollment_stats', now()->addMinutes(5), function () {
            return [
                'total' => Enrollment::count(),
                'approved' => Enrollment::where('status', 'approved')->count(),
                'pending' => Enrollment::where('status', 'pending')->count(),
                'rejected' => Enrollment::where('status', 'rejected')->count(),
            ];
        });

        // Load enrollments with relationships for display
        $query = Enrollment::with([
            'student.profile',
            'offering.course',
            'offering.semester',
        ]);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('pages.admin.enrollments.index', compact('enrollments', 'stats'));
    }

    /**
     * Display enrollment requests.
     */
    public function requests(): View
    {
        $enrollments = Enrollment::with([
            'student.profile',
            'student.major.department',
            'offering.course.department',
            'offering.semester.academicYear',
            'offering.teacher.profile',
        ])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('pages.admin.enrollments.requests', compact('enrollments'));
    }

    /**
     * Approve an enrollment.
     */
    public function approve(Enrollment $enrollment)
    {
        $offering = $enrollment->offering;
        $course = $offering->course;
        $student = $enrollment->student;

        // Check prerequisites
        $prerequisitesMet = $course->hasCompletedPrerequisites($student->id);
        if (! $prerequisitesMet) {
            $prerequisites = $course->prerequisites()->with('prerequisiteCourse')->get();
            $prerequisiteNames = $prerequisites->pluck('prerequisiteCourse.name')->implode(', ');

            return back()->with('error', __('lms::messages.prerequisites_not_met', ['prerequisites' => $prerequisiteNames]));
        }

        // Check if fees are paid
        $unpaidFees = StudentFee::where('student_id', $student->id)
            ->where('status', '!=', 'paid')
            ->whereHas('fee', function ($q) use ($offering) {
                $q->where('semester_id', $offering->semester_id);
            })
            ->count();

        if ($unpaidFees > 0) {
            return back()->with('error', __('lms::messages.unpaid_fees'));
        }

        // Check capacity
        if ($offering->enrolled_count >= $offering->capacity) {
            return back()->with('error', __('lms::messages.section_full'));
        }

        $enrollment->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        // Increment enrollment count
        $offering->increment('enrolled_count');

        // Clear relevant caches
        Cache::forget('admin_dashboard_stats');
        Cache::forget('admin_course_offerings');

        return back()->with('success', __('lms::messages.enrollment_approved'));
    }

    /**
     * Reject an enrollment.
     */
    public function reject(Request $request, Enrollment $enrollment)
    {
        $enrollment->update([
            'status' => 'rejected',
            'admin_notes' => $request->input('reason', ''),
        ]);

        // Clear dashboard cache
        Cache::forget('admin_dashboard_stats');

        return back()->with('success', __('lms::messages.enrollment_rejected'));
    }

    /**
     * Drop an enrollment.
     */
    public function drop(Enrollment $enrollment)
    {
        $enrollment->drop();

        // Clear relevant caches
        Cache::forget('admin_dashboard_stats');
        Cache::forget('admin_course_offerings');

        return back()->with('success', __('lms::messages.enrollment_dropped'));
    }

    /**
     * Bulk approve enrollments.
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'enrollment_ids' => 'required|array',
        ]);

        $enrollments = Enrollment::with(['offering.course', 'student'])->whereIn('id', $request->enrollment_ids)->get();
        $approved = 0;
        $failed = 0;

        foreach ($enrollments as $enrollment) {
            if ($enrollment->status === 'pending') {
                $offering = $enrollment->offering;
                $course = $offering->course;
                $student = $enrollment->student;

                // Check prerequisites
                if (! $course->hasCompletedPrerequisites($student->id)) {
                    $failed++;

                    continue;
                }

                // Check if fees are paid
                $unpaidFees = StudentFee::where('student_id', $student->id)
                    ->where('status', '!=', 'paid')
                    ->whereHas('fee', function ($q) use ($offering) {
                        $q->where('semester_id', $offering->semester_id);
                    })
                    ->count();

                if ($unpaidFees > 0) {
                    $failed++;

                    continue;
                }

                // Check capacity
                if ($offering->enrolled_count >= $offering->capacity) {
                    $failed++;

                    continue;
                }

                $enrollment->update([
                    'status' => 'approved',
                    'approved_at' => now(),
                ]);
                $offering->increment('enrolled_count');
                $approved++;
            }
        }

        // Clear relevant caches
        Cache::forget('admin_dashboard_stats');
        Cache::forget('admin_course_offerings');

        if ($failed > 0) {
            return back()->with('warning', __('lms::messages.enrollments_partial_approved', ['approved' => $approved, 'failed' => $failed]));
        }

        return back()->with('success', __('lms::messages.enrollments_bulk_approved'));
    }

    /**
     * Bulk reject enrollments.
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'enrollment_ids' => 'required|array',
        ]);

        Enrollment::whereIn('id', $request->enrollment_ids)->update(['status' => 'rejected']);

        // Clear dashboard cache
        Cache::forget('admin_dashboard_stats');

        return back()->with('success', __('lms::messages.enrollments_bulk_rejected'));
    }

    /**
     * Check enrollment requirements.
     */
    public function checkRequirements(Enrollment $enrollment): array
    {
        $offering = $enrollment->offering;
        $course = $offering->course;
        $student = $enrollment->student;

        // Check prerequisites
        $prerequisitesMet = $course->hasCompletedPrerequisites($student->id);
        $prerequisites = $course->prerequisites()->with('prerequisiteCourse')->get();

        // Check fees
        $unpaidFees = StudentFee::where('student_id', $student->id)
            ->where('status', '!=', 'paid')
            ->whereHas('fee', function ($q) use ($offering) {
                $q->where('semester_id', $offering->semester_id);
            })
            ->with('fee')
            ->get();

        // Check capacity
        $hasCapacity = $offering->enrolled_count < $offering->capacity;

        return [
            'prerequisites_met' => $prerequisitesMet,
            'prerequisites' => $prerequisites,
            'unpaid_fees' => $unpaidFees,
            'has_capacity' => $hasCapacity,
            'can_approve' => $prerequisitesMet && $unpaidFees->isEmpty() && $hasCapacity,
        ];
    }

    /**
     * Show bulk enrollment creation form.
     */
    public function createBulk(): View
    {
        $students = User::role('student')
            ->with('profile')
            ->orderBy('name')
            ->get();

        $offerings = CourseOffering::with(['course.department', 'semester.academicYear', 'teacher'])
            ->whereHas('semester', function ($q) {
                $q->where('is_current', true);
            })
            ->orderBy('id')
            ->get();

        $semesters = Semester::with('academicYear')->orderBy('start_date', 'desc')->get();

        return view('pages.admin.enrollments.bulk-create', compact('students', 'offerings', 'semesters'));
    }

    /**
     * Store bulk enrollments.
     */
    public function storeBulk(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id',
            'offering_ids' => 'required|array',
            'offering_ids.*' => 'exists:course_offerings,id',
            'status' => 'required|in:pending,approved',
            'skip_prerequisites' => 'boolean',
            'skip_capacity_check' => 'boolean',
        ]);

        $studentIds = $validated['student_ids'];
        $offeringIds = $validated['offering_ids'];
        $status = $validated['status'];
        $skipPrerequisites = $request->has('skip_prerequisites');
        $skipCapacityCheck = $request->has('skip_capacity_check');

        $created = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($offeringIds as $offeringId) {
                $offering = CourseOffering::with('course')->find($offeringId);

                foreach ($studentIds as $studentId) {
                    // Check if already enrolled
                    $existingEnrollment = Enrollment::where('student_id', $studentId)
                        ->where('course_offering_id', $offeringId)
                        ->first();

                    if ($existingEnrollment) {
                        $skipped++;

                        continue;
                    }

                    // Check prerequisites
                    if (! $skipPrerequisites) {
                        $course = $offering->course;
                        if (! $course->hasCompletedPrerequisites($studentId)) {
                            $errors[] = __('lms.prerequisites_not_met_for_student', [
                                'student' => User::find($studentId)->name,
                                'course' => $course->name,
                            ]);
                            $skipped++;

                            continue;
                        }
                    }

                    // Check capacity
                    if (! $skipCapacityCheck && $offering->enrolled_count >= $offering->capacity) {
                        $errors[] = __('lms.section_full_for_course', ['course' => $offering->course->name]);

                        continue;
                    }

                    // Create enrollment
                    Enrollment::create([
                        'student_id' => $studentId,
                        'course_offering_id' => $offeringId,
                        'status' => $status,
                        'enrolled_at' => now(),
                        'approved_at' => $status === 'approved' ? now() : null,
                    ]);

                    if ($status === 'approved') {
                        $offering->increment('enrolled_count');
                    }

                    $created++;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', __('lms.bulk_enrollment_failed').': '.$e->getMessage());
        }

        // Clear caches
        Cache::forget('admin_dashboard_stats');
        Cache::forget('admin_course_offerings');

        $message = __('lms.bulk_enrollment_complete', ['created' => $created, 'skipped' => $skipped]);

        if (! empty($errors)) {
            return back()->with('warning', $message)->with('errors', $errors);
        }

        return redirect()->route('admin.enrollments.index')
            ->with('success', $message);
    }

    /**
     * Show CSV import form.
     */
    public function importCsv(): View
    {
        $semesters = Semester::with('academicYear')->orderBy('start_date', 'desc')->get();

        return view('pages.admin.enrollments.import-csv', compact('semesters'));
    }

    /**
     * Process CSV import.
     */
    public function processCsvImport(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
            'semester_id' => 'required|exists:semesters,id',
            'status' => 'required|in:pending,approved',
            'skip_prerequisites' => 'boolean',
            'skip_capacity_check' => 'boolean',
        ]);

        $file = $request->file('csv_file');
        $semesterId = $request->semester_id;
        $status = $request->status;
        $skipPrerequisites = $request->has('skip_prerequisites');
        $skipCapacityCheck = $request->has('skip_capacity_check');

        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));

        if (empty($data)) {
            return back()->with('error', __('lms.csv_empty'));
        }

        // Get header row
        $header = array_map('trim', array_shift($data));

        // Validate header
        $requiredHeaders = ['student_id', 'course_code'];
        $missingHeaders = array_diff($requiredHeaders, $header);

        if (! empty($missingHeaders)) {
            return back()->with('error', __('lms.csv_missing_headers', ['headers' => implode(', ', $missingHeaders)]));
        }

        $created = 0;
        $skipped = 0;
        $errors = [];
        $rowNumber = 1;

        DB::beginTransaction();
        try {
            foreach ($data as $row) {
                $rowNumber++;

                if (count($row) < count($requiredHeaders)) {
                    $errors[] = __('lms.csv_row_invalid', ['row' => $rowNumber]);

                    continue;
                }

                $rowData = array_combine($header, $row);

                // Find student
                $student = User::role('student')
                    ->where('id', $rowData['student_id'])
                    ->orWhere('email', $rowData['student_id'] ?? '')
                    ->first();

                if (! $student) {
                    $errors[] = __('lms.csv_student_not_found', ['row' => $rowNumber, 'student' => $rowData['student_id']]);
                    $skipped++;

                    continue;
                }

                // Find course offering
                $offering = CourseOffering::whereHas('course', function ($q) use ($rowData) {
                    $q->where('code', $rowData['course_code']);
                })->where('semester_id', $semesterId)->first();

                if (! $offering) {
                    $errors[] = __('lms.csv_offering_not_found', ['row' => $rowNumber, 'course' => $rowData['course_code']]);
                    $skipped++;

                    continue;
                }

                // Check if already enrolled
                $existingEnrollment = Enrollment::where('student_id', $student->id)
                    ->where('course_offering_id', $offering->id)
                    ->first();

                if ($existingEnrollment) {
                    $skipped++;

                    continue;
                }

                // Check prerequisites
                if (! $skipPrerequisites) {
                    if (! $offering->course->hasCompletedPrerequisites($student->id)) {
                        $errors[] = __('lms.csv_prerequisites_not_met', ['row' => $rowNumber, 'student' => $student->name]);
                        $skipped++;

                        continue;
                    }
                }

                // Check capacity
                if (! $skipCapacityCheck && $offering->enrolled_count >= $offering->capacity) {
                    $errors[] = __('lms.csv_section_full', ['row' => $rowNumber, 'course' => $offering->course->name]);
                    $skipped++;

                    continue;
                }

                // Create enrollment
                Enrollment::create([
                    'student_id' => $student->id,
                    'course_offering_id' => $offering->id,
                    'status' => $status,
                    'enrolled_at' => now(),
                    'approved_at' => $status === 'approved' ? now() : null,
                ]);

                if ($status === 'approved') {
                    $offering->increment('enrolled_count');
                }

                $created++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', __('lms.csv_import_failed').': '.$e->getMessage());
        }

        // Clear caches
        Cache::forget('admin_dashboard_stats');
        Cache::forget('admin_course_offerings');

        $message = __('lms.csv_import_complete', ['created' => $created, 'skipped' => $skipped]);

        if (! empty($errors)) {
            return back()->with('warning', $message)->with('import_errors', $errors);
        }

        return redirect()->route('admin.enrollments.index')
            ->with('success', $message);
    }

    /**
     * Download CSV template.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="enrollment_template.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, ['student_id', 'course_code']);

            // Example rows
            fputcsv($file, ['12345', 'CS101']);
            fputcsv($file, ['student@example.com', 'MATH201']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export enrollments to CSV.
     */
    public function export(Request $request)
    {
        $query = Enrollment::with([
            'student.profile',
            'offering.course',
            'offering.semester',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('semester_id')) {
            $query->whereHas('offering', function ($q) use ($request) {
                $q->where('semester_id', $request->semester_id);
            });
        }

        $enrollments = $query->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="enrollments_'.date('Y-m-d').'.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($enrollments) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Student ID',
                'Student Name',
                'Student Email',
                'Course Code',
                'Course Name',
                'Semester',
                'Status',
                'Enrolled At',
                'Approved At',
            ]);

            foreach ($enrollments as $enrollment) {
                fputcsv($file, [
                    $enrollment->student->id,
                    $enrollment->student->name,
                    $enrollment->student->email,
                    $enrollment->offering->course->code ?? '',
                    $enrollment->offering->course->name ?? '',
                    $enrollment->offering->semester->name ?? '',
                    $enrollment->status,
                    $enrollment->enrolled_at?->format('Y-m-d H:i:s'),
                    $enrollment->approved_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show the form for creating a new enrollment.
     */
    public function create(): View
    {
        $students = User::role('student')->active()->get();
        $courseOfferings = CourseOffering::with(['course', 'semester'])->active()->get();
        $semesters = Semester::active()->get();

        return view('pages.admin.enrollments.create', compact('students', 'courseOfferings', 'semesters'));
    }

    /**
     * Store a newly created enrollment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'course_offering_id' => 'required|exists:course_offerings,id',
            'semester_id' => 'required|exists:semesters,id',
            'enrollment_date' => 'required|date',
            'status' => 'required|in:pending,approved,rejected,dropped',
            'notes' => 'nullable|string',
        ]);

        // Convert enrollment_date to enrolled_at
        $validated['enrolled_at'] = $validated['enrollment_date'];
        unset($validated['enrollment_date']);

        // Check if enrollment already exists
        $existing = Enrollment::where('student_id', $validated['student_id'])
            ->where('course_offering_id', $validated['course_offering_id'])
            ->where('semester_id', $validated['semester_id'])
            ->first();

        if ($existing) {
            return back()->withErrors(['student_id' => 'Student is already enrolled in this course offering.']);
        }

        Enrollment::create($validated);

        return redirect()->route('admin.enrollments.index')->with('success', 'Enrollment created successfully.');
    }

    /**
     * Show the form for editing an enrollment.
     */
    public function edit(Enrollment $enrollment): View
    {
        $enrollment->load(['student.profile', 'offering.course', 'offering.semester']);
        $students = User::role('student')->active()->get();
        $courseOfferings = CourseOffering::with(['course', 'semester'])->active()->get();
        $semesters = Semester::active()->get();

        return view('pages.admin.enrollments.edit', compact('enrollment', 'students', 'courseOfferings', 'semesters'));
    }

    /**
     * Update the specified enrollment.
     */
    public function update(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'student_id' => 'sometimes|exists:users,id',
            'course_offering_id' => 'sometimes|exists:course_offerings,id',
            'semester_id' => 'sometimes|exists:semesters,id',
            'enrollment_date' => 'nullable|date',
            'status' => 'sometimes|in:pending,approved,rejected,dropped',
            'notes' => 'nullable|string',
            'admin_notes' => 'nullable|string',
        ]);

        // Convert enrollment_date to enrolled_at if provided
        if (isset($validated['enrollment_date'])) {
            $validated['enrolled_at'] = $validated['enrollment_date'];
            unset($validated['enrollment_date']);
        }

        // Check if changing would create duplicate (only if student_id, course_offering_id, or semester_id changed)
        $checkDuplicate = false;
        if (isset($validated['student_id']) && $enrollment->student_id != $validated['student_id']) {
            $checkDuplicate = true;
        }
        if (isset($validated['course_offering_id']) && $enrollment->course_offering_id != $validated['course_offering_id']) {
            $checkDuplicate = true;
        }
        if (isset($validated['semester_id']) && $enrollment->semester_id != $validated['semester_id']) {
            $checkDuplicate = true;
        }

        if ($checkDuplicate) {
            $studentId = $validated['student_id'] ?? $enrollment->student_id;
            $courseOfferingId = $validated['course_offering_id'] ?? $enrollment->course_offering_id;
            $semesterId = $validated['semester_id'] ?? $enrollment->semester_id;

            $existing = Enrollment::where('student_id', $studentId)
                ->where('course_offering_id', $courseOfferingId)
                ->where('semester_id', $semesterId)
                ->where('id', '!=', $enrollment->id)
                ->first();

            if ($existing) {
                return back()->withErrors(['student_id' => 'Student is already enrolled in this course offering.']);
            }
        }

        $enrollment->update($validated);

        return redirect()->route('admin.enrollments.index')->with('success', 'Enrollment updated successfully.');
    }

    /**
     * Remove the specified enrollment.
     */
    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();

        return redirect()->route('admin.enrollments.index')->with('success', 'Enrollment deleted successfully.');
    }
}

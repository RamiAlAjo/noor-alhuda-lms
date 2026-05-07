<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicStanding;
use App\Models\Grade;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AcademicStandingController extends Controller
{
    /**
     * Display a listing of academic standings.
     */
    public function index(Request $request)
    {
        $query = AcademicStanding::with(['student', 'semester', 'setter']);

        // Filter by standing
        if ($request->filled('standing')) {
            $query->where('standing', $request->standing);
        }

        // Filter by active status
        if ($request->filled('active')) {
            $query->where('is_active', $request->active === 'yes');
        }

        // Filter by student
        if ($request->filled('student')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->student.'%')
                    ->orWhere('user_id', 'like', '%'.$request->student.'%');
            });
        }

        $standings = $query->latest()->paginate(15);

        $standingOptions = AcademicStanding::getStandings();

        return view('pages.admin.academic-standings.index', compact('standings', 'standingOptions'));
    }

    /**
     * Show the form for creating a new academic standing.
     */
    public function create(Request $request)
    {
        $studentId = $request->get('student');
        $student = $studentId ? User::findOrFail($studentId) : null;

        $standingOptions = AcademicStanding::getStandings();
        $standingTypes = AcademicStanding::getStandingTypes();
        $semesters = Semester::orderByDesc('start_date')->get();

        return view('pages.admin.academic-standings.create', compact('student', 'standingOptions', 'standingTypes', 'semesters'));
    }

    /**
     * Store a newly created academic standing.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'semester_id' => 'nullable|exists:semesters,id',
            'standing' => 'required|in:good_standing,probation,suspension,dismissal',
            'standing_type' => 'nullable|in:academic,disciplinary',
            'reason' => 'required|string|min:10',
            'notes' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'requirements' => 'nullable|array',
            'requirements.*' => 'string',
        ]);

        // Get student's current GPA
        $student = User::findOrFail($validated['student_id']);
        $gpa = $this->calculateStudentGpa($student);

        // Deactivate any existing active standing
        AcademicStanding::where('student_id', $validated['student_id'])
            ->where('is_active', true)
            ->update(['is_active' => false, 'end_date' => now()]);

        // Create new standing
        $standing = AcademicStanding::create([
            'student_id' => $validated['student_id'],
            'semester_id' => $validated['semester_id'] ?? null,
            'standing' => $validated['standing'],
            'standing_type' => $validated['standing_type'] ?? AcademicStanding::TYPE_ACADEMIC,
            'gpa_at_time' => $gpa,
            'cumulative_gpa' => $gpa,
            'reason' => $validated['reason'],
            'notes' => $validated['notes'] ?? null,
            'is_active' => true,
            'start_date' => $validated['start_date'] ?? now(),
            'end_date' => $validated['end_date'] ?? null,
            'set_by' => Auth::id(),
            'set_at' => now(),
            'requirements' => $validated['requirements'] ?? null,
        ]);

        // Update user's academic standing
        $student->update(['academic_standing' => $validated['standing']]);

        return redirect()->route('admin.academic-standings.show', $standing)
            ->with('success', __('lms.academic_standing_set'));
    }

    /**
     * Display the specified academic standing.
     */
    public function show(AcademicStanding $academicStanding)
    {
        $academicStanding->load(['student.profile', 'semester', 'setter']);

        return view('pages.admin.academic-standings.show', compact('academicStanding'));
    }

    /**
     * Show the form for editing the academic standing.
     */
    public function edit(AcademicStanding $academicStanding)
    {
        $standingOptions = AcademicStanding::getStandings();
        $standingTypes = AcademicStanding::getStandingTypes();
        $semesters = Semester::orderByDesc('start_date')->get();

        return view('pages.admin.academic-standings.edit', compact('academicStanding', 'standingOptions', 'standingTypes', 'semesters'));
    }

    /**
     * Update the academic standing.
     */
    public function update(Request $request, AcademicStanding $academicStanding)
    {
        $validated = $request->validate([
            'standing' => 'required|in:good_standing,probation,suspension,dismissal',
            'standing_type' => 'nullable|in:academic,disciplinary',
            'reason' => 'required|string|min:10',
            'notes' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'requirements' => 'nullable|array',
            'requirements.*' => 'string',
        ]);

        $academicStanding->update($validated);

        // Update user's academic standing if this is active
        if ($academicStanding->is_active) {
            $academicStanding->student->update(['academic_standing' => $validated['standing']]);
        }

        return redirect()->route('admin.academic-standings.show', $academicStanding)
            ->with('success', __('lms.academic_standing_updated'));
    }

    /**
     * Deactivate the academic standing.
     */
    public function deactivate(AcademicStanding $academicStanding)
    {
        $academicStanding->deactivate();

        // Set student back to good standing
        $academicStanding->student->update(['academic_standing' => AcademicStanding::STANDING_GOOD]);

        return redirect()->route('admin.academic-standings.index')
            ->with('success', __('lms.academic_standing_deactivated'));
    }

    /**
     * Calculate GPA for all students.
     */
    public function calculateAll()
    {
        $students = User::role('student')->get();

        foreach ($students as $student) {
            $this->updateStudentStanding($student);
        }

        return back()->with('success', __('lms.academic_standing_calculated'));
    }

    /**
     * Calculate student's GPA.
     */
    private function calculateStudentGpa(User $student): float
    {
        $grades = Grade::where('student_id', $student->id)
            ->whereNotNull('grade_points')
            ->get();

        if ($grades->isEmpty()) {
            return 0.0;
        }

        $totalPoints = $grades->sum('grade_points');
        $totalCredits = $grades->sum('course_credits');

        return $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.0;
    }

    /**
     * Update student's academic standing based on GPA.
     */
    private function updateStudentStanding(User $student): void
    {
        $gpa = $this->calculateStudentGpa($student);
        $standing = AcademicStanding::calculateStandingFromGpa($gpa);

        // Deactivate existing active standing
        AcademicStanding::where('student_id', $student->id)
            ->where('is_active', true)
            ->update(['is_active' => false, 'end_date' => now()]);

        // Create new standing if not good standing
        if ($standing !== AcademicStanding::STANDING_GOOD) {
            AcademicStanding::create([
                'student_id' => $student->id,
                'standing' => $standing,
                'standing_type' => AcademicStanding::TYPE_ACADEMIC,
                'gpa_at_time' => $gpa,
                'cumulative_gpa' => $gpa,
                'reason' => "Automatic calculation based on GPA: {$gpa}",
                'is_active' => true,
                'start_date' => now(),
                'set_by' => Auth::id(),
                'set_at' => now(),
            ]);
        }

        // Update user's academic standing
        $student->update([
            'academic_standing' => $standing,
            'cumulative_gpa' => $gpa,
        ]);
    }

    /**
     * Export academic standings to CSV.
     */
    public function export(Request $request)
    {
        $query = AcademicStanding::with(['student', 'semester']);

        if ($request->filled('standing')) {
            $query->where('standing', $request->standing);
        }

        $standings = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="academic_standings.csv"',
        ];

        $callback = function () use ($standings) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Student ID',
                'Student Name',
                'Standing',
                'GPA',
                'Type',
                'Start Date',
                'End Date',
                'Is Active',
                'Set By',
                'Set At',
            ]);

            foreach ($standings as $standing) {
                fputcsv($file, [
                    $standing->student->user_id,
                    $standing->student->name,
                    $standing->standing,
                    $standing->gpa_at_time,
                    $standing->standing_type,
                    $standing->start_date?->format('Y-m-d'),
                    $standing->end_date?->format('Y-m-d'),
                    $standing->is_active ? 'Yes' : 'No',
                    $standing->setter?->name,
                    $standing->set_at?->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\CourseOffering;
use App\Models\QuizAccommodation;
use App\Models\StudentAccommodation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccommodationController extends Controller
{
    /**
     * Display accommodations for students in teacher's courses.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $offeringId = $request->get('offering_id');

        // Get teacher's course offerings (via teacher_id or course_teachers pivot table)
        $offerings = CourseOffering::where('teacher_id', $user->id)
            ->orWhereHas('teachers', function ($q) use ($user) {
                $q->where('course_teachers.teacher_id', $user->id);
            })
            ->with('course')
            ->get();

        $accommodations = collect();

        if ($offeringId) {
            // Get students enrolled in this offering with active accommodations
            $offering = CourseOffering::findOrFail($offeringId);

            if ($offering->teacher_id !== $user->id && ! $offering->teachers()->where('course_teachers.teacher_id', $user->id)->exists()) {
                abort(403, __('lms.unauthorized'));
            }

            $studentIds = $offering->enrollments()->pluck('student_id');

            $accommodations = StudentAccommodation::with(['student', 'accommodationType'])
                ->whereIn('student_id', $studentIds)
                ->active()
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('student_id');
        }

        return view('pages.teacher.accommodations.index', compact('offerings', 'accommodations', 'offeringId'));
    }

    /**
     * Show accommodations for a specific student.
     */
    public function showStudent(User $student, Request $request)
    {
        $user = Auth::user();

        // Verify teacher has this student in one of their courses
        $hasStudent = CourseOffering::where(function ($q) use ($user) {
            $q->where('teacher_id', $user->id);
        })
            ->orWhereHas('teachers', function ($q) use ($user) {
                $q->where('course_teachers.teacher_id', $user->id);
            })
            ->whereHas('enrollments', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->exists();

        if (! $hasStudent) {
            abort(403, __('lms.unauthorized'));
        }

        $accommodations = StudentAccommodation::with(['accommodationType', 'quizAccommodations.assessment'])
            ->where('student_id', $student->id)
            ->active()
            ->get();

        return view('pages.teacher.accommodations.student', compact('student', 'accommodations'));
    }

    /**
     * Show accommodations for a specific quiz.
     */
    public function showQuiz(Assessment $assessment, Request $request)
    {
        $user = Auth::user();

        // Verify teacher owns this assessment
        $offering = $assessment->courseOffering;
        if ($offering->teacher_id !== $user->id && ! $offering->teachers()->where('course_teachers.teacher_id', $user->id)->exists()) {
            abort(403, __('lms.unauthorized'));
        }
        $studentIds = $offering->enrollments()->pluck('student_id');

        $studentAccommodations = StudentAccommodation::with(['student', 'accommodationType'])
            ->whereIn('student_id', $studentIds)
            ->active()
            ->get();

        // Get existing quiz accommodations
        $existingQuizAccommodations = QuizAccommodation::where('assessment_id', $assessment->id)
            ->with('studentAccommodation.student', 'studentAccommodation.accommodationType')
            ->get()
            ->keyBy('student_accommodation_id');

        return view('pages.teacher.accommodations.quiz', compact(
            'assessment',
            'studentAccommodations',
            'existingQuizAccommodations'
        ));
    }

    /**
     * Apply accommodation to a quiz.
     */
    public function applyToQuiz(Request $request, Assessment $assessment)
    {
        $user = Auth::user();

        // Verify teacher owns this assessment
        $offering = $assessment->courseOffering;
        if ($offering->teacher_id !== $user->id && ! $offering->teachers()->where('course_teachers.teacher_id', $user->id)->exists()) {
            abort(403, __('lms.unauthorized'));
        }

        $validated = $request->validate([
            'student_accommodation_id' => 'required|exists:student_accommodations,id',
            'extended_time_minutes' => 'nullable|integer|min:0',
            'extended_time_percentage' => 'nullable|numeric|min:0|max:200',
            'additional_attempts' => 'nullable|integer|min:0',
            'allow_breaks' => 'boolean',
            'special_instructions' => 'nullable|string|max:1000',
        ]);

        $studentAccommodation = StudentAccommodation::findOrFail($validated['student_accommodation_id']);

        // Verify student is enrolled in this course
        $isEnrolled = $offering->enrollments()
            ->where('student_id', $studentAccommodation->student_id)
            ->exists();

        if (! $isEnrolled) {
            return redirect()->back()
                ->with('error', __('lms.student_not_enrolled'));
        }

        // Create or update quiz accommodation
        $quizAccommodation = QuizAccommodation::updateOrCreate(
            [
                'student_accommodation_id' => $validated['student_accommodation_id'],
                'assessment_id' => $assessment->id,
            ],
            [
                'extended_time_minutes' => $validated['extended_time_minutes'] ?? null,
                'extended_time_percentage' => $validated['extended_time_percentage'] ?? null,
                'additional_attempts' => $validated['additional_attempts'] ?? 0,
                'allow_breaks' => $request->has('allow_breaks'),
                'special_instructions' => $validated['special_instructions'] ?? null,
                'is_applied' => true,
                'applied_at' => now(),
                'applied_by' => $user->id,
            ]
        );

        return redirect()->back()
            ->with('success', __('lms.accommodation_applied_to_quiz'));
    }

    /**
     * Remove accommodation from a quiz.
     */
    public function removeFromQuiz(QuizAccommodation $quizAccommodation)
    {
        $user = Auth::user();

        // Verify teacher owns this assessment
        $assessment = $quizAccommodation->assessment;
        $offering = $assessment->courseOffering;

        if ($offering->teacher_id !== $user->id && ! $offering->teachers()->where('course_teachers.teacher_id', $user->id)->exists()) {
            abort(403, __('lms.unauthorized'));
        }

        $quizAccommodation->delete();

        return redirect()->back()
            ->with('success', __('lms.accommodation_removed_from_quiz'));
    }

    /**
     * Bulk apply accommodations to a quiz.
     */
    public function bulkApplyToQuiz(Request $request, Assessment $assessment)
    {
        $user = Auth::user();

        // Verify teacher owns this assessment
        $offering = $assessment->courseOffering;
        if ($offering->teacher_id !== $user->id && ! $offering->teachers()->where('course_teachers.teacher_id', $user->id)->exists()) {
            abort(403, __('lms.unauthorized'));
        }

        $validated = $request->validate([
            'accommodations' => 'required|array',
            'accommodations.*.student_accommodation_id' => 'required|exists:student_accommodations,id',
            'accommodations.*.extended_time_minutes' => 'nullable|integer|min:0',
            'accommodations.*.extended_time_percentage' => 'nullable|numeric|min:0|max:200',
            'accommodations.*.additional_attempts' => 'nullable|integer|min:0',
            'accommodations.*.allow_breaks' => 'boolean',
        ]);

        foreach ($validated['accommodations'] as $accommodationData) {
            $studentAccommodation = StudentAccommodation::find($accommodationData['student_accommodation_id']);

            if (! $studentAccommodation) {
                continue;
            }

            // Verify student is enrolled
            $isEnrolled = $offering->enrollments()
                ->where('student_id', $studentAccommodation->student_id)
                ->exists();

            if (! $isEnrolled) {
                continue;
            }

            QuizAccommodation::updateOrCreate(
                [
                    'student_accommodation_id' => $accommodationData['student_accommodation_id'],
                    'assessment_id' => $assessment->id,
                ],
                [
                    'extended_time_minutes' => $accommodationData['extended_time_minutes'] ?? null,
                    'extended_time_percentage' => $accommodationData['extended_time_percentage'] ?? null,
                    'additional_attempts' => $accommodationData['additional_attempts'] ?? 0,
                    'allow_breaks' => $accommodationData['allow_breaks'] ?? false,
                    'is_applied' => true,
                    'applied_at' => now(),
                    'applied_by' => $user->id,
                ]
            );
        }

        return redirect()->back()
            ->with('success', __('lms.accommodations_bulk_applied'));
    }
}

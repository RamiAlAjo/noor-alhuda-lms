<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseFeedback;
use App\Models\Enrollment;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseFeedbackController extends Controller
{
    /**
     * Display a listing of feedback forms available to the student.
     */
    public function index()
    {
        $user = Auth::user();
        $currentSemester = Semester::where('is_current', true)->first();

        // Get enrollments that can receive feedback
        $enrollments = Enrollment::where('student_id', $user->id)
            ->with(['courseOffering.course', 'courseOffering.semester'])
            ->whereHas('courseOffering', function ($query) use ($currentSemester) {
                $query->where('semester_id', $currentSemester?->id);
            })
            ->get();

        // Get existing feedback for these enrollments (table may not exist)
        try {
            $existingFeedback = CourseFeedback::where('student_id', $user->id)
                ->whereIn('course_offering_id', $enrollments->pluck('course_offering_id'))
                ->get()
                ->keyBy('course_offering_id');
        } catch (\Exception $e) {
            $existingFeedback = collect();
        }

        return view('pages.student.feedback.index', compact('enrollments', 'existingFeedback', 'currentSemester'));
    }

    /**
     * Show the form for creating new feedback.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $courseOfferingId = $request->get('course_offering_id');

        // Verify enrollment
        $enrollment = Enrollment::where('student_id', $user->id)
            ->where('course_offering_id', $courseOfferingId)
            ->with(['courseOffering.course', 'courseOffering.semester'])
            ->firstOrFail();

        // Check if feedback already exists
        $existingFeedback = CourseFeedback::where('student_id', $user->id)
            ->where('course_offering_id', $courseOfferingId)
            ->first();

        if ($existingFeedback && $existingFeedback->is_submitted) {
            return redirect()->route('student.feedback.index')
                ->with('error', __('lms.feedback_already_submitted'));
        }

        $feedback = $existingFeedback;
        $ratingCategories = CourseFeedback::getRatingCategories();

        return view('pages.student.feedback.create', compact('enrollment', 'feedback', 'ratingCategories'));
    }

    /**
     * Store a newly created feedback in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $courseOfferingId = $request->get('course_offering_id');

        // Verify enrollment
        $enrollment = Enrollment::where('student_id', $user->id)
            ->where('course_offering_id', $courseOfferingId)
            ->firstOrFail();

        // Check if feedback already submitted
        $existingFeedback = CourseFeedback::where('student_id', $user->id)
            ->where('course_offering_id', $courseOfferingId)
            ->where('is_submitted', true)
            ->first();

        if ($existingFeedback) {
            return redirect()->route('student.feedback.index')
                ->with('error', __('lms.feedback_already_submitted'));
        }

        $validated = $request->validate([
            'overall_rating' => 'required|integer|min:1|max:5',
            'content_quality' => 'nullable|integer|min:1|max:5',
            'instructor_knowledge' => 'nullable|integer|min:1|max:5',
            'instructor_communication' => 'nullable|integer|min:1|max:5',
            'course_organization' => 'nullable|integer|min:1|max:5',
            'materials_quality' => 'nullable|integer|min:1|max:5',
            'workload_appropriateness' => 'nullable|integer|min:1|max:5',
            'strengths' => 'nullable|string|max:1000',
            'improvements' => 'nullable|string|max:1000',
            'additional_comments' => 'nullable|string|max:2000',
            'is_anonymous' => 'boolean',
            'save_draft' => 'boolean',
        ]);

        $isDraft = $request->has('save_draft');

        $feedback = CourseFeedback::updateOrCreate(
            [
                'student_id' => $user->id,
                'course_offering_id' => $courseOfferingId,
            ],
            [
                'semester_id' => $enrollment->courseOffering->semester_id,
                'overall_rating' => $validated['overall_rating'],
                'content_quality' => $validated['content_quality'] ?? null,
                'instructor_knowledge' => $validated['instructor_knowledge'] ?? null,
                'instructor_communication' => $validated['instructor_communication'] ?? null,
                'course_organization' => $validated['course_organization'] ?? null,
                'materials_quality' => $validated['materials_quality'] ?? null,
                'workload_appropriateness' => $validated['workload_appropriateness'] ?? null,
                'strengths' => $validated['strengths'] ?? null,
                'improvements' => $validated['improvements'] ?? null,
                'additional_comments' => $validated['additional_comments'] ?? null,
                'is_anonymous' => $request->has('is_anonymous'),
                'is_submitted' => ! $isDraft,
                'submitted_at' => ! $isDraft ? now() : null,
            ]
        );

        $message = $isDraft
            ? __('lms.feedback_saved_draft')
            : __('lms.feedback_submitted_successfully');

        return redirect()->route('student.feedback.index')
            ->with('success', $message);
    }

    /**
     * Display the specified feedback.
     */
    public function show(CourseFeedback $feedback)
    {
        $user = Auth::user();

        // Verify ownership
        if ($feedback->student_id !== $user->id) {
            abort(403, __('lms.unauthorized'));
        }

        $feedback->load(['courseOffering.course', 'courseOffering.semester']);
        $ratingCategories = CourseFeedback::getRatingCategories();

        return view('pages.student.feedback.show', compact('feedback', 'ratingCategories'));
    }

    /**
     * Show the form for editing the specified feedback.
     */
    public function edit(CourseFeedback $feedback)
    {
        $user = Auth::user();

        // Verify ownership and not submitted
        if ($feedback->student_id !== $user->id) {
            abort(403, __('lms.unauthorized'));
        }

        if ($feedback->is_submitted) {
            return redirect()->route('student.feedback.index')
                ->with('error', __('lms.cannot_edit_submitted_feedback'));
        }

        $feedback->load(['courseOffering.course', 'courseOffering.semester']);
        $ratingCategories = CourseFeedback::getRatingCategories();

        return view('pages.student.feedback.edit', compact('feedback', 'ratingCategories'));
    }

    /**
     * Update the specified feedback in storage.
     */
    public function update(Request $request, CourseFeedback $feedback)
    {
        $user = Auth::user();

        // Verify ownership
        if ($feedback->student_id !== $user->id) {
            abort(403, __('lms.unauthorized'));
        }

        if ($feedback->is_submitted) {
            return redirect()->route('student.feedback.index')
                ->with('error', __('lms.cannot_edit_submitted_feedback'));
        }

        $validated = $request->validate([
            'overall_rating' => 'required|integer|min:1|max:5',
            'content_quality' => 'nullable|integer|min:1|max:5',
            'instructor_knowledge' => 'nullable|integer|min:1|max:5',
            'instructor_communication' => 'nullable|integer|min:1|max:5',
            'course_organization' => 'nullable|integer|min:1|max:5',
            'materials_quality' => 'nullable|integer|min:1|max:5',
            'workload_appropriateness' => 'nullable|integer|min:1|max:5',
            'strengths' => 'nullable|string|max:1000',
            'improvements' => 'nullable|string|max:1000',
            'additional_comments' => 'nullable|string|max:2000',
            'is_anonymous' => 'boolean',
            'save_draft' => 'boolean',
        ]);

        $isDraft = $request->has('save_draft');

        $feedback->update([
            'overall_rating' => $validated['overall_rating'],
            'content_quality' => $validated['content_quality'] ?? null,
            'instructor_knowledge' => $validated['instructor_knowledge'] ?? null,
            'instructor_communication' => $validated['instructor_communication'] ?? null,
            'course_organization' => $validated['course_organization'] ?? null,
            'materials_quality' => $validated['materials_quality'] ?? null,
            'workload_appropriateness' => $validated['workload_appropriateness'] ?? null,
            'strengths' => $validated['strengths'] ?? null,
            'improvements' => $validated['improvements'] ?? null,
            'additional_comments' => $validated['additional_comments'] ?? null,
            'is_anonymous' => $request->has('is_anonymous'),
            'is_submitted' => ! $isDraft,
            'submitted_at' => ! $isDraft ? now() : null,
        ]);

        $message = $isDraft
            ? __('lms.feedback_saved_draft')
            : __('lms.feedback_submitted_successfully');

        return redirect()->route('student.feedback.index')
            ->with('success', $message);
    }
}

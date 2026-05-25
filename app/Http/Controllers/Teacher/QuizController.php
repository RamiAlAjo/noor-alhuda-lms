<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentType;
use App\Models\CourseOffering;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\StudentAnswer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuizController extends Controller
{
    /**
     * Display all quizzes across all offerings.
     */
    public function allQuizzes(): View
    {
        $offerings = CourseOffering::whereHas('teacher', function ($q) {
            $q->where('teacher_id', Auth::id());
        })->with(['course', 'semester'])->get();

        $quizzes = Assessment::whereHas('section', function ($q) {
            $q->whereHas('teacher', function ($q2) {
                $q2->where('teacher_id', Auth::id());
            });
        })->where(function ($q) {
            $q->where('quiz_type', '!=', 'none')->orWhereNotNull('time_limit_minutes');
        })->with(['section.course', 'studentGrades' => function ($q) {
            $q->selectRaw('assessment_id, COUNT(*) as attempts_count, AVG(percentage) as avg_score, MAX(percentage) as max_score, MIN(percentage) as min_score')
                ->groupBy('assessment_id');
        }])->orderBy('created_at', 'desc')->paginate(20);

        // Calculate analytics for each quiz
        foreach ($quizzes as $quiz) {
            $studentGrades = $quiz->studentGrades ?? collect();
            $quiz->analytics = [
                'total_attempts' => $studentGrades->sum('attempts_count'),
                'avg_score' => $studentGrades->avg('avg_score'),
                'highest_score' => $studentGrades->max('max_score'),
                'lowest_score' => $studentGrades->min('min_score'),
                'completion_rate' => $quiz->section ? ($studentGrades->count() / $quiz->section->enrollments->where('status', 'approved')->count()) * 100 : 0,
            ];
        }

        return view('pages.teacher.quizzes.all', compact('quizzes', 'offerings'));
    }

    /**
     * Display a listing of quizzes for a course offering.
     */
    public function index(CourseOffering $offering): View
    {
        $offering->load(['course', 'assessments.questions']);

        $quizzes = $offering->assessments()
            ->where(function ($query) {
                $query->where('quiz_type', '!=', 'none')
                      ->orWhereNotNull('time_limit_minutes');
            })
            ->withCount('questions')
            ->withCount(['attempts as completed_attempts_count' => function ($q) {
                $q->whereNotNull('submitted_at');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.teacher.quizzes.index', compact('offering', 'quizzes'));
    }

    /**
     * Show the form for creating a new quiz.
     */
    public function create(CourseOffering $offering): View
    {
        $offering->load(['course']);

        return view('pages.teacher.quizzes.create', compact('offering'));
    }

    /**
     * Store a newly created quiz.
     */
    public function store(Request $request, CourseOffering $offering): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'max_grade' => 'required|numeric|min:1',
            'weight' => 'nullable|numeric|min:0|max:100',
            'quiz_type' => 'required|in:quiz,pre_quiz,post_quiz',
            'time_limit_minutes' => 'nullable|integer|min:1|max:300',
            'time_limit_seconds' => 'nullable|integer|min:0|max:59',
            'attempts_allowed' => 'nullable|integer|min:1',
            'passing_score' => 'nullable|numeric|min:0|max:100',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after_or_equal:available_from',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_options' => 'nullable|boolean',
            'show_results_immediately' => 'nullable|boolean',
            'show_correct_answers' => 'nullable|boolean',
            'show_feedback' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
        ]);

        // Get or create quiz assessment type
        $assessmentType = AssessmentType::firstOrCreate(
            ['code' => 'quiz'],
            ['name' => 'Quiz', 'is_active' => true]
        );

        $quiz = Assessment::create([
            'course_offering_id' => $offering->id,
            'assessment_type_id' => $assessmentType->id,
            'title' => $validated['title'],
            'title_ar' => $validated['title_ar'] ?? null,
            'description' => $validated['description'] ?? null,
            'max_grade' => $validated['max_grade'],
            'weight' => $validated['weight'] ?? 0,
            'quiz_type' => $validated['quiz_type'],
            'time_limit_minutes' => $validated['time_limit_minutes'] ?? null,
            'time_limit_seconds' => $validated['time_limit_seconds'] ?? 0,
            'attempts_allowed' => $validated['attempts_allowed'] ?? null,
            'passing_score' => $validated['passing_score'] ?? null,
            'available_from' => $validated['available_from'] ?? null,
            'available_until' => $validated['available_until'] ?? null,
            'shuffle_questions' => $request->has('shuffle_questions'),
            'shuffle_options' => $request->has('shuffle_options'),
            'show_results_immediately' => $request->has('show_results_immediately'),
            'show_correct_answers' => $request->has('show_correct_answers'),
            'show_feedback' => $request->has('show_feedback'),
            'is_published' => $request->has('is_published'),
        ]);

        return redirect()->route('teacher.quizzes.questions', [$offering, $quiz])
            ->with('success', __('Quiz created successfully. Now add your questions.'));
    }

    /**
     * Show the form for editing a quiz.
     */
    public function edit(CourseOffering $offering, Assessment $quiz): View
    {
        $quiz->load(['questions']);

        return view('pages.teacher.quizzes.edit', compact('offering', 'quiz'));
    }

    /**
     * Update the specified quiz.
     */
    public function update(Request $request, CourseOffering $offering, Assessment $quiz): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'max_grade' => 'required|numeric|min:1',
            'weight' => 'nullable|numeric|min:0|max:100',
            'quiz_type' => 'required|in:quiz,pre_quiz,post_quiz',
            'time_limit_minutes' => 'nullable|integer|min:1|max:300',
            'time_limit_seconds' => 'nullable|integer|min:0|max:59',
            'attempts_allowed' => 'nullable|integer|min:1',
            'passing_score' => 'nullable|numeric|min:0|max:100',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after_or_equal:available_from',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_options' => 'nullable|boolean',
            'show_results_immediately' => 'nullable|boolean',
            'show_correct_answers' => 'nullable|boolean',
            'show_feedback' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
        ]);

        $quiz->update([
            'title' => $validated['title'],
            'title_ar' => $validated['title_ar'] ?? null,
            'description' => $validated['description'] ?? null,
            'max_grade' => $validated['max_grade'],
            'weight' => $validated['weight'] ?? 0,
            'quiz_type' => $validated['quiz_type'],
            'time_limit_minutes' => $validated['time_limit_minutes'] ?? null,
            'time_limit_seconds' => $validated['time_limit_seconds'] ?? 0,
            'attempts_allowed' => $validated['attempts_allowed'] ?? null,
            'passing_score' => $validated['passing_score'] ?? null,
            'available_from' => $validated['available_from'] ?? null,
            'available_until' => $validated['available_until'] ?? null,
            'shuffle_questions' => $request->has('shuffle_questions'),
            'shuffle_options' => $request->has('shuffle_options'),
            'show_results_immediately' => $request->has('show_results_immediately'),
            'show_correct_answers' => $request->has('show_correct_answers'),
            'show_feedback' => $request->has('show_feedback'),
            'is_published' => $request->has('is_published'),
        ]);

        return redirect()->route('teacher.quizzes.index', $offering)
            ->with('success', __('Quiz updated successfully.'));
    }

    /**
     * Remove the specified quiz.
     */
    public function destroy(CourseOffering $offering, Assessment $quiz): RedirectResponse
    {
        // Check if there are any attempts
        if ($quiz->attempts()->exists()) {
            return back()->with('error', __('Cannot delete quiz with existing attempts.'));
        }

        $quiz->questions()->delete();
        $quiz->delete();

        return redirect()->route('teacher.quizzes.index', $offering)
            ->with('success', __('Quiz deleted successfully.'));
    }

    /**
     * Show the questions management page.
     */
    public function questions(CourseOffering $offering, Assessment $quiz): View
    {
        $quiz->load(['questions']);

        return view('pages.teacher.quizzes.questions', compact('offering', 'quiz'));
    }

    /**
     * Store a new question.
     */
    public function storeQuestion(Request $request, CourseOffering $offering, Assessment $quiz): RedirectResponse
    {
        $rules = [
            'question_text' => 'required|string',
            'question_text_ar' => 'nullable|string',
            'question_type' => 'required|in:multiple_choice,true_false,short_answer,essay',
            'points' => 'required|integer|min:1',
            'correct_answer' => 'nullable|string',
            'options' => 'nullable|array',
            'options.*.option_text' => 'nullable|string',
            'correct_option' => 'nullable|integer',
        ];

        $validated = $request->validate($rules);

        // Additional validation for multiple choice
        if ($request->question_type === 'multiple_choice') {
            $options = $request->input('options', []);
            $filled = collect($options)->filter(fn($o) => !empty($o['option_text'] ?? ''))->count();

            if ($filled < 2) {
                return back()
                    ->withErrors(['options' => __('At least 2 options with text are required for multiple choice questions.')])
                    ->withInput();
            }

            if (! isset($validated['correct_option']) || $validated['correct_option'] === null) {
                return back()
                    ->withErrors(['correct_option' => __('Please select the correct answer.')])
                    ->withInput();
            }
        }

        if ($request->question_type === 'true_false') {
            if (empty($validated['correct_answer'])) {
                return back()
                    ->withErrors(['correct_answer' => __('Please select the correct answer.')])
                    ->withInput();
            }
        }

        $questionData = [
            'assessment_id' => $quiz->id,
            'question_text' => $validated['question_text'],
            'question_text_ar' => $validated['question_text_ar'] ?? null,
            'question_type' => $validated['question_type'],
            'points' => $validated['points'],
            'order' => $quiz->questions()->count() + 1,
        ];

        // Handle different question types
        if ($validated['question_type'] === 'multiple_choice') {
            $options = [];
            $correctAnswerText = '';

            if (! empty($validated['options'])) {
                foreach ($validated['options'] as $index => $option) {
                    if (! empty($option['option_text'])) {
                        $isCorrect = (int) ($validated['correct_option'] ?? -1) === $index;
                        $options[] = [
                            'id' => $index + 1,
                            'option_text' => $option['option_text'],
                            'is_correct' => $isCorrect,
                        ];
                        if ($isCorrect) {
                            $correctAnswerText = $option['option_text'];
                        }
                    }
                }
            }

            $questionData['options'] = $options;
            $questionData['correct_answer'] = $correctAnswerText;
        } elseif ($validated['question_type'] === 'true_false') {
            $correctAnswer = $validated['correct_answer'] ?? 'true';
            $questionData['options'] = [
                ['id' => 1, 'option_text' => 'True', 'is_correct' => $correctAnswer === 'true'],
                ['id' => 2, 'option_text' => 'False', 'is_correct' => $correctAnswer === 'false'],
            ];
            $questionData['correct_answer'] = $correctAnswer === 'true' ? 'True' : 'False';
        } else {
            // For short_answer and essay
            $questionData['correct_answer'] = $validated['correct_answer'] ?? null;
            $questionData['options'] = null;
        }

        Question::create($questionData);

        return back()->with('success', __('Question added successfully.'));
    }

    /**
     * Update a question.
     */
    public function updateQuestion(Request $request, CourseOffering $offering, Assessment $quiz, Question $question): RedirectResponse
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_text_ar' => 'nullable|string',
            'points' => 'required|integer|min:1',
            'options' => 'nullable|array',
            'options.*.option_text' => 'required_with:options|string',
            'correct_option' => 'nullable|integer',
            'correct_answer' => 'nullable|string',
        ]);

        $questionData = [
            'question_text' => $validated['question_text'],
            'question_text_ar' => $validated['question_text_ar'] ?? null,
            'points' => $validated['points'],
        ];

        // Handle different question types
        if ($question->question_type === 'multiple_choice') {
            $options = [];
            $correctAnswerText = '';

            if (! empty($validated['options'])) {
                foreach ($validated['options'] as $index => $option) {
                    if (! empty($option['option_text'])) {
                        $isCorrect = (int) ($validated['correct_option'] ?? -1) === $index;
                        $options[] = [
                            'id' => $index + 1,
                            'option_text' => $option['option_text'],
                            'is_correct' => $isCorrect,
                        ];
                        if ($isCorrect) {
                            $correctAnswerText = $option['option_text'];
                        }
                    }
                }
            }

            $questionData['options'] = $options;
            $questionData['correct_answer'] = $correctAnswerText;
        } elseif ($question->question_type === 'true_false') {
            $correctAnswer = $validated['correct_answer'] ?? 'true';
            $questionData['options'] = [
                ['id' => 1, 'option_text' => 'True', 'is_correct' => $correctAnswer === 'true'],
                ['id' => 2, 'option_text' => 'False', 'is_correct' => $correctAnswer === 'false'],
            ];
            $questionData['correct_answer'] = $correctAnswer === 'true' ? 'True' : 'False';
        } else {
            $questionData['correct_answer'] = $validated['correct_answer'] ?? null;
        }

        $question->update($questionData);

        return back()->with('success', __('Question updated successfully.'));
    }

    /**
     * Delete a question.
     */
    public function destroyQuestion(CourseOffering $offering, Assessment $quiz, Question $question): RedirectResponse
    {
        $question->delete();

        // Reorder remaining questions
        $quiz->questions()->orderBy('order')->get()->each(function ($q, $index) {
            $q->update(['order' => $index + 1]);
        });

        return back()->with('success', __('Question deleted successfully.'));
    }

    /**
     * Reorder questions.
     */
    public function reorderQuestions(Request $request, CourseOffering $offering, Assessment $quiz): RedirectResponse
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:questions,id',
        ]);

        foreach ($validated['order'] as $index => $questionId) {
            Question::where('id', $questionId)->update(['order' => $index + 1]);
        }

        return back()->with('success', __('Questions reordered successfully.'));
    }

    /**
     * Show quiz analytics.
     */
    public function analytics(CourseOffering $offering, Assessment $quiz): View
    {
        $quiz->load(['questions']);

        // Get overall statistics
        $stats = [
            'total_attempts' => $quiz->attempts()->completed()->count(),
            'unique_students' => $quiz->attempts()->completed()->distinct('student_id')->count(),
            'average_score' => $quiz->attempts()->completed()->avg('percentage') ?? 0,
            'highest_score' => $quiz->attempts()->completed()->max('percentage') ?? 0,
            'lowest_score' => $quiz->attempts()->completed()->min('percentage') ?? 0,
            'pass_rate' => $quiz->attempts()->completed()->where('passed', true)->count() /
                max($quiz->attempts()->completed()->count(), 1) * 100,
        ];

        // Get question analysis
        $questionAnalysis = $quiz->questions->map(function ($question) {
            $totalAnswers = StudentAnswer::where('question_id', $question->id)
                ->whereHas('attempt', function ($q) {
                    $q->completed();
                })
                ->count();

            $correctAnswers = StudentAnswer::where('question_id', $question->id)
                ->where('is_correct', true)
                ->whereHas('attempt', function ($q) {
                    $q->completed();
                })
                ->count();

            return [
                'question' => $question,
                'total_answers' => $totalAnswers,
                'correct_answers' => $correctAnswers,
                'accuracy' => $totalAnswers > 0 ? round(($correctAnswers / $totalAnswers) * 100, 1) : 0,
            ];
        });

        // Get student results
        $results = $quiz->attempts()
            ->completed()
            ->with(['student.profile'])
            ->orderBy('percentage', 'desc')
            ->paginate(20);

        // Score distribution
        $scoreDistribution = [
            'A' => $quiz->attempts()->completed()->where('percentage', '>=', 90)->count(),
            'B' => $quiz->attempts()->completed()->whereBetween('percentage', [80, 89.99])->count(),
            'C' => $quiz->attempts()->completed()->whereBetween('percentage', [70, 79.99])->count(),
            'D' => $quiz->attempts()->completed()->whereBetween('percentage', [60, 69.99])->count(),
            'F' => $quiz->attempts()->completed()->where('percentage', '<', 60)->count(),
        ];

        return view('pages.teacher.quizzes.analytics', compact(
            'offering', 'quiz', 'stats', 'questionAnalysis', 'results', 'scoreDistribution'
        ));
    }

    /**
     * Show a specific attempt for grading.
     */
    public function showAttempt(CourseOffering $offering, Assessment $quiz, QuizAttempt $attempt): View
    {
        $attempt->load(['student.profile', 'answers.question']);

        return view('pages.teacher.quizzes.attempt', compact('offering', 'quiz', 'attempt'));
    }

    /**
     * Grade an attempt (for manual grading of essay/short answer).
     */
    public function gradeAttempt(Request $request, CourseOffering $offering, Assessment $quiz, QuizAttempt $attempt): RedirectResponse
    {
        $validated = $request->validate([
            'grades' => 'required|array',
            'grades.*.points_earned' => 'required|numeric|min:0',
            'grades.*.feedback' => 'nullable|string',
            'overall_feedback' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $attempt, $quiz) {
            $totalPoints = 0;
            $maxPoints = 0;

            foreach ($validated['grades'] as $questionId => $gradeData) {
                $question = Question::find($questionId);
                if (! $question) {
                    continue;
                }

                $maxPoints += $question->points;
                $pointsEarned = min($gradeData['points_earned'], $question->points);
                $totalPoints += $pointsEarned;

                // Update the student answer
                StudentAnswer::where('attempt_id', $attempt->id)
                    ->where('question_id', $questionId)
                    ->update([
                        'points_earned' => $pointsEarned,
                        'is_correct' => $pointsEarned >= ($question->points / 2),
                        'feedback' => $gradeData['feedback'] ?? null,
                    ]);
            }

            // Calculate percentage
            $percentage = $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100, 2) : 0;
            $passed = $quiz->passing_score ? $percentage >= $quiz->passing_score : true;

            // Update the attempt
            $attempt->update([
                'score' => $totalPoints,
                'max_score' => $maxPoints,
                'percentage' => $percentage,
                'passed' => $passed,
                'status' => 'graded',
                'feedback' => $validated['overall_feedback'] ?? null,
                'graded_by' => Auth::id(),
                'graded_at' => now(),
            ]);
        });

        return redirect()->route('teacher.quizzes.analytics', [$offering, $quiz])
            ->with('success', __('Attempt graded successfully.'));
    }

    /**
     * Publish/unpublish a quiz.
     */
    public function togglePublish(CourseOffering $offering, Assessment $quiz): RedirectResponse
    {
        $quiz->update([
            'is_published' => ! $quiz->is_published,
        ]);

        $message = $quiz->is_published ? __('Quiz published successfully.') : __('Quiz unpublished.');

        return back()->with('success', $message);
    }

    /**
     * Preview the quiz as a teacher.
     */
    public function preview(CourseOffering $offering, Assessment $quiz): View
    {
        $quiz->load(['questions']);

        return view('pages.teacher.quizzes.preview', compact('offering', 'quiz'));
    }

    /**
     * Duplicate a quiz.
     */
    public function duplicate(CourseOffering $offering, Assessment $quiz): RedirectResponse
    {
        $newQuiz = DB::transaction(function () use ($quiz) {
            $newQuiz = $quiz->replicate();
            $newQuiz->title = $quiz->title.' (Copy)';
            $newQuiz->is_published = false;
            $newQuiz->save();

            foreach ($quiz->questions as $question) {
                $newQuestion = $question->replicate();
                $newQuestion->assessment_id = $newQuiz->id;
                $newQuestion->save();
            }

            return $newQuiz;
        });

        return redirect()->route('teacher.quizzes.edit', [$offering, $newQuiz])
            ->with('success', __('Quiz duplicated successfully.'));
    }
}

<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use App\Models\StudentAnswer;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuizController extends Controller
{
    /**
     * Display quiz list for student.
     */
    public function index(): View
    {
        $student = auth()->user();

        // Get quizzes from enrolled courses
        $enrollments = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->with(['offering.course', 'offering.assessments' => function ($q) {
                $q->where('quiz_type', '!=', 'none')
                    ->where('is_published', true);
            }])
            ->get();

        $quizzes = $enrollments->flatMap(function ($enrollment) use ($student) {
            // Skip if offering is null
            if (! $enrollment->offering) {
                return collect();
            }

            return $enrollment->offering->assessments->map(function ($assessment) use ($enrollment, $student) {
                $attempts = QuizAttempt::where('student_id', $student->id)
                    ->where('assessment_id', $assessment->id)
                    ->completed()
                    ->get();

                $assessment->best_score = $attempts->max('percentage') ?? 0;
                $assessment->attempts_count = $attempts->count();
                $assessment->offering = $enrollment->offering;
                $assessment->can_take = $assessment->attempts_allowed === null ||
                    $attempts->count() < $assessment->attempts_allowed;

                return $assessment;
            });
        })->filter();

        return view('pages.student.quizzes.index', compact('quizzes'));
    }

    /**
     * Show quiz start page.
     */
    public function show(Assessment $assessment): View
    {
        $student = auth()->user();

        // Check enrollment
        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('course_offering_id', $assessment->course_offering_id)
            ->where('status', 'approved')
            ->first();

        if (! $enrollment) {
            abort(403, 'You are not enrolled in this course.');
        }

        // Check availability
        if ($assessment->available_from && now()->lt($assessment->available_from)) {
            abort(403, 'This quiz is not available yet.');
        }

        if ($assessment->available_until && now()->gt($assessment->available_until)) {
            abort(403, 'This quiz is no longer available.');
        }

        // Check attempts
        $attemptCount = QuizAttempt::where('student_id', $student->id)
            ->where('assessment_id', $assessment->id)
            ->completed()
            ->count();

        if ($assessment->attempts_allowed && $attemptCount >= $assessment->attempts_allowed) {
            abort(403, 'You have used all your attempts for this quiz.');
        }

        // Check for in-progress attempt
        $inProgress = QuizAttempt::where('student_id', $student->id)
            ->where('assessment_id', $assessment->id)
            ->where('status', 'in_progress')
            ->first();

        $assessment->load(['questions', 'offering.course']);

        return view('pages.student.quizzes.show', compact('assessment', 'attemptCount', 'inProgress'));
    }

    /**
     * Start quiz - create a new attempt.
     */
    public function start(Assessment $assessment): RedirectResponse
    {
        $student = auth()->user();

        // Check enrollment
        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('course_offering_id', $assessment->course_offering_id)
            ->where('status', 'approved')
            ->first();

        if (! $enrollment) {
            abort(403, 'You are not enrolled in this course.');
        }

        // Check for existing in-progress attempt
        $existingAttempt = QuizAttempt::where('student_id', $student->id)
            ->where('assessment_id', $assessment->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existingAttempt) {
            return redirect()->route('student.quizzes.take', $assessment);
        }

        // Check attempts
        $attemptCount = QuizAttempt::where('student_id', $student->id)
            ->where('assessment_id', $assessment->id)
            ->completed()
            ->count();

        if ($assessment->attempts_allowed && $attemptCount >= $assessment->attempts_allowed) {
            return back()->with('error', __('You have used all your attempts for this quiz.'));
        }

        // Create new attempt
        $attempt = QuizAttempt::create([
            'student_id' => $student->id,
            'assessment_id' => $assessment->id,
            'attempt_number' => $attemptCount + 1,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        return redirect()->route('student.quizzes.take', $assessment);
    }

    /**
     * Take quiz - display questions.
     */
    public function take(Assessment $assessment): View
    {
        $student = auth()->user();

        // Check enrollment
        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('course_offering_id', $assessment->course_offering_id)
            ->where('status', 'approved')
            ->first();

        if (! $enrollment) {
            abort(403, 'You are not enrolled in this course.');
        }

        // Get current in-progress attempt
        $attempt = QuizAttempt::where('student_id', $student->id)
            ->where('assessment_id', $assessment->id)
            ->where('status', 'in_progress')
            ->first();

        if (! $attempt) {
            return redirect()->route('student.quizzes.show', $assessment);
        }

        $assessment->load(['questions']);

        // Calculate time remaining
        $timeRemaining = ['hours' => 0, 'minutes' => 0, 'seconds' => 0, 'total' => 0];

        if ($assessment->time_limit_minutes > 0) {
            $startTime = Carbon::parse($attempt->started_at);
            $totalSeconds = $assessment->getTotalTimeLimitInSeconds();
            $elapsed = now()->diffInSeconds($startTime);
            $remaining = max(0, $totalSeconds - $elapsed);

            $timeRemaining['hours'] = floor($remaining / 3600);
            $timeRemaining['minutes'] = floor(($remaining % 3600) / 60);
            $timeRemaining['seconds'] = $remaining % 60;
            $timeRemaining['total'] = $remaining;

            // Auto-submit if time expired
            if ($remaining <= 0) {
                return redirect()->route('student.quizzes.submit', $assessment);
            }
        }

        // Get saved answers
        $savedAnswers = StudentAnswer::where('student_id', $student->id)
            ->where('attempt_id', $attempt->id)
            ->get()
            ->keyBy('question_id');

        // Shuffle questions if enabled
        $questions = $assessment->questions;
        if ($assessment->shuffle_questions) {
            $questions = $questions->shuffle();
        }

        return view('pages.student.quizzes.take', compact('assessment', 'questions', 'savedAnswers', 'attempt', 'timeRemaining'));
    }

    /**
     * Auto-save answer.
     */
    public function saveAnswer(Request $request, Assessment $assessment): array
    {
        $student = auth()->user();

        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer' => 'nullable',
        ]);

        $attempt = QuizAttempt::where('student_id', $student->id)
            ->where('assessment_id', $assessment->id)
            ->where('status', 'in_progress')
            ->first();

        if (! $attempt) {
            return ['success' => false, 'message' => 'No active attempt found.'];
        }

        $question = $assessment->questions()->findOrFail($request->question_id);

        // Handle different question types
        if (in_array($question->question_type, ['multiple_choice', 'true_false'])) {
            $selectedOptions = $request->answer;
            if (! is_array($selectedOptions)) {
                $selectedOptions = [$selectedOptions];
            }

            // Delete previous answers for this question
            StudentAnswer::where('attempt_id', $attempt->id)
                ->where('question_id', $question->id)
                ->delete();

            // Get options - handle both string (JSON) and array formats
            $questionOptions = $question->options;
            if (is_string($questionOptions)) {
                $questionOptions = json_decode($questionOptions, true);
            }
            // Convert associative array format if needed
            if (is_array($questionOptions) && ! empty($questionOptions) && isset($questionOptions[array_key_first($questionOptions)]) && is_string($questionOptions[array_key_first($questionOptions)])) {
                $convertedOptions = [];
                foreach ($questionOptions as $key => $value) {
                    $convertedOptions[] = [
                        'id' => $key,
                        'option_text' => $value,
                        'is_correct' => false,
                    ];
                }
                $questionOptions = $convertedOptions;
            }

            // Save new answers
            foreach ($selectedOptions as $optionId) {
                $option = collect($questionOptions)->firstWhere('id', (int) $optionId);
                if ($option) {
                    StudentAnswer::create([
                        'student_id' => $student->id,
                        'question_id' => $question->id,
                        'assessment_id' => $assessment->id,
                        'attempt_id' => $attempt->id,
                        'option_id' => $optionId,
                        'answer' => $option['option_text'],
                        'is_correct' => $option['is_correct'] ?? false,
                        'submitted_at' => now(),
                    ]);
                }
            }
        } else {
            // Short answer or essay
            StudentAnswer::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'question_id' => $question->id,
                    'attempt_id' => $attempt->id,
                ],
                [
                    'assessment_id' => $assessment->id,
                    'text_answer' => $request->answer,
                    'submitted_at' => now(),
                ]
            );
        }

        return ['success' => true, 'message' => 'Answer saved.'];
    }

    /**
     * Submit quiz answers.
     */
    public function submit(Request $request, Assessment $assessment): RedirectResponse
    {
        $student = auth()->user();

        $attempt = QuizAttempt::where('student_id', $student->id)
            ->where('assessment_id', $assessment->id)
            ->where('status', 'in_progress')
            ->first();

        if (! $attempt) {
            return redirect()->route('student.quizzes.index');
        }

        $answers = $request->input('answers', []);

        DB::transaction(function () use ($assessment, $attempt, $student, $answers) {
            $score = 0;
            $totalPoints = 0;
            $correctCount = 0;

            foreach ($assessment->questions as $question) {
                $totalPoints += $question->points;

                $selectedOptions = $answers[$question->id] ?? [];

                if (! is_array($selectedOptions)) {
                    $selectedOptions = [$selectedOptions];
                }

                $isCorrect = false;
                $pointsEarned = 0;

                // Handle different question types
                if (in_array($question->question_type, ['multiple_choice', 'true_false'])) {
                    // Get options - handle both string (JSON) and array formats
                    $questionOptions = $question->options;
                    if (is_string($questionOptions)) {
                        $questionOptions = json_decode($questionOptions, true);
                    }
                    // Convert associative array format if needed
                    if (is_array($questionOptions) && ! empty($questionOptions) && isset($questionOptions[array_key_first($questionOptions)]) && is_string($questionOptions[array_key_first($questionOptions)])) {
                        $convertedOptions = [];
                        foreach ($questionOptions as $key => $value) {
                            $convertedOptions[] = [
                                'id' => $key,
                                'option_text' => $value,
                                'is_correct' => false,
                            ];
                        }
                        $questionOptions = $convertedOptions;
                    }

                    // Get correct option IDs
                    $correctOptionIds = collect($questionOptions)
                        ->where('is_correct', true)
                        ->pluck('id')
                        ->map(fn ($id) => (int) $id)
                        ->sort()
                        ->values()
                        ->toArray();

                    $selectedOptionIds = collect($selectedOptions)
                        ->map(fn ($id) => (int) $id)
                        ->sort()
                        ->values()
                        ->toArray();

                    // Check if answer is correct
                    $isCorrect = $correctOptionIds === $selectedOptionIds && ! empty($selectedOptionIds);

                    if ($isCorrect) {
                        $pointsEarned = $question->points;
                        $score += $question->points;
                        $correctCount++;
                    }

                    // Delete previous answers and save new ones
                    StudentAnswer::where('attempt_id', $attempt->id)
                        ->where('question_id', $question->id)
                        ->delete();

                    foreach ($selectedOptionIds as $optionId) {
                        $option = collect($questionOptions)->firstWhere('id', $optionId);
                        if ($option) {
                            StudentAnswer::create([
                                'student_id' => $student->id,
                                'question_id' => $question->id,
                                'assessment_id' => $assessment->id,
                                'attempt_id' => $attempt->id,
                                'option_id' => $optionId,
                                'answer' => $option['option_text'],
                                'is_correct' => $option['is_correct'] ?? false,
                                'points_earned' => ($option['is_correct'] ?? false) ? $question->points / max(count($correctOptionIds), 1) : 0,
                                'submitted_at' => now(),
                            ]);
                        }
                    }
                } else {
                    // Short answer or essay
                    $textAnswer = is_array($selectedOptions) ? ($selectedOptions[0] ?? '') : $selectedOptions;

                    // Auto-grade short answer if model answer exists
                    if ($question->question_type === 'short_answer' && ! empty($question->correct_answer)) {
                        $isCorrect = strtolower(trim($textAnswer)) === strtolower(trim($question->correct_answer));
                        if ($isCorrect) {
                            $pointsEarned = $question->points;
                            $score += $question->points;
                            $correctCount++;
                        }
                    }

                    StudentAnswer::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'question_id' => $question->id,
                            'attempt_id' => $attempt->id,
                        ],
                        [
                            'assessment_id' => $assessment->id,
                            'answer' => $textAnswer,
                            'is_correct' => $isCorrect,
                            'points_earned' => $pointsEarned,
                            'submitted_at' => now(),
                        ]
                    );
                }
            }

            // Calculate percentage
            $percentage = $totalPoints > 0 ? round(($score / $totalPoints) * 100, 2) : 0;
            $passed = $assessment->passing_score ? $percentage >= $assessment->passing_score : true;

            // Calculate time spent
            $timeSpent = now()->diffInSeconds(Carbon::parse($attempt->started_at));

            // Update attempt
            $attempt->update([
                'score' => $score,
                'max_score' => $totalPoints,
                'percentage' => $percentage,
                'passed' => $passed,
                'status' => 'submitted',
                'submitted_at' => now(),
                'time_spent_seconds' => $timeSpent,
            ]);

            // Auto-grade if no essay questions
            $hasEssay = $assessment->questions()->where('question_type', 'essay')->exists();
            if (! $hasEssay) {
                $attempt->update(['status' => 'graded']);
            }
        });

        return redirect()->route('student.quizzes.result', [$assessment, $attempt])
            ->with('success', __('Quiz submitted successfully!'));
    }

    /**
     * Show quiz results.
     */
    public function result(Assessment $assessment, QuizAttempt $attempt): View
    {
        $student = auth()->user();

        if ($attempt->student_id !== $student->id) {
            abort(403);
        }

        $assessment->load(['questions']);
        $attempt->load(['answers.question']);

        // Check if can retake
        $completedAttempts = QuizAttempt::where('student_id', $student->id)
            ->where('assessment_id', $assessment->id)
            ->completed()
            ->count();

        $canRetake = $assessment->attempts_allowed === null ||
            $completedAttempts < $assessment->attempts_allowed;

        // Calculate correct count
        $correctCount = $attempt->answers->where('is_correct', true)->count();
        $totalQuestions = $assessment->questions->count();

        return view('pages.student.quizzes.result', compact(
            'assessment', 'attempt', 'canRetake', 'correctCount', 'totalQuestions'
        ));
    }

    /**
     * Show all attempts for a quiz.
     */
    public function attempts(Assessment $assessment): View
    {
        $student = auth()->user();

        $attempts = QuizAttempt::where('student_id', $student->id)
            ->where('assessment_id', $assessment->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.student.quizzes.attempts', compact('assessment', 'attempts'));
    }
}

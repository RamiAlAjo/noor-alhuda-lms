<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Attendance;
use App\Models\CourseMaterial;
use App\Models\CourseSection;
use App\Models\Question;
use App\Models\StudentAnswer;
use App\Models\StudentGrade;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Display teacher's courses.
     */
    public function index(): View
    {
        $teacher = auth()->user();

        $sections = CourseSection::where('teacher_id', $teacher->id)
            ->with(['course', 'semester', 'enrollments', 'assessments'])
            ->get();

        return view('pages.teacher.courses.index', compact('sections'));
    }

    /**
     * Show a specific section with students.
     */
    public function show(CourseSection $section): View
    {
        $this->authorize('view', $section);

        $section->load(['course', 'semester.academicYear', 'enrollments.student', 'materials', 'assessments', 'enrollments.grades']);

        // Calculate average grade for the section
        $allGrades = $section->enrollments->flatMap->grades;
        $averageGrade = $allGrades->count() > 0 ? round($allGrades->avg('percentage'), 1) : null;

        return view('pages.teacher.courses.show', compact('section', 'averageGrade'));
    }

    /**
     * Show attendance form for a section.
     */
    public function attendance(CourseSection $section): View
    {
        $this->authorize('view', $section);

        $section->load(['course', 'enrollments.student', 'enrollments.attendance']);

        // Calculate attendance statistics
        $enrollments = $section->enrollments->where('status', 'approved');
        $allAttendance = $enrollments->flatMap->attendances;

        $presentCount = $allAttendance->where('status', 'present')->count();
        $absentCount = $allAttendance->where('status', 'absent')->count();
        $excusedCount = $allAttendance->where('status', 'excused')->count();
        $lateCount = $allAttendance->where('status', 'late')->count();
        $totalRecords = $allAttendance->count();

        $attendanceRate = $totalRecords > 0 ? round(($presentCount / $totalRecords) * 100, 1) : null;

        return view('pages.teacher.courses.attendance', compact('section', 'attendanceRate', 'presentCount', 'absentCount', 'excusedCount', 'lateCount', 'totalRecords'));
    }

    /**
     * Store attendance.
     */
    public function storeAttendance(Request $request, CourseSection $section)
    {
        $this->authorize('view', $section);

        $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:present,absent,excused,late',
        ]);

        foreach ($request->attendance as $enrollmentId => $status) {
            // Get enrollment to find student_id
            $enrollment = \App\Models\Enrollment::find($enrollmentId);
            if (! $enrollment) {
                continue;
            }

            Attendance::updateOrCreate(
                [
                    'enrollment_id' => $enrollmentId,
                    'date' => $request->date,
                ],
                [
                    'student_id' => $enrollment->student_id,
                    'course_offering_id' => $section->id,
                    'status' => $status,
                    'notes' => $request->input('notes.'.$enrollmentId) ?? null,
                ]
            );
        }

        return back()->with('success', __('lms::messages.attendance_saved'));
    }

    /**
     * Show students enrolled in a section.
     */
    public function students(CourseSection $section): View
    {
        $this->authorize('view', $section);

        $section->load(['course', 'enrollments.student']);

        $search = request('search');
        $enrollments = $section->enrollments()->where('status', 'approved');

        if ($search) {
            $enrollments = $enrollments->whereHas('student', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('user_id', 'like', "%{$search}%")
                    ->orWhereHas('profile', function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        $enrollments = $enrollments->get();

        return view('pages.teacher.courses.students', compact('section', 'enrollments'));
    }

    /**
     * Show materials for a section.
     */
    public function materials(CourseSection $section): View
    {
        $this->authorize('view', $section);

        $section->load(['course', 'materials']);

        return view('pages.teacher.courses.materials', compact('section'));
    }

    /**
     * Store a new material.
     */
    public function storeMaterial(Request $request, CourseSection $section)
    {
        $this->authorize('view', $section);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'week' => 'nullable|integer|min:1|max:20',
            'file' => 'nullable|file|max:10240',
            'video_url' => 'nullable|string|url',
        ]);

        $materialData = [
            'course_offering_id' => $section->id,
            'uploaded_by' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'week' => $request->week ?? 1,
            'material_type' => $request->type ?? 'lecture',
            'is_published' => true,
        ];

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('materials', 'public');
            $materialData['file_path'] = $path;
            $materialData['file_type'] = $request->file('file')->getMimeType();
            $materialData['file_size'] = $request->file('file')->getSize();
        }

        if ($request->filled('video_url')) {
            $materialData['video_url'] = $request->video_url;
        }

        CourseMaterial::create($materialData);

        return back()->with('success', __('lms::messages.material_uploaded'));
    }

    /**
     * Delete a material.
     */
    public function destroyMaterial(CourseMaterial $material)
    {
        $this->authorize('view', $material->section);

        $material->delete();

        return back()->with('success', __('lms::messages.material_deleted'));
    }

    /**
     * Show assessments for a section.
     */
    public function assessments(CourseSection $section): View
    {
        $this->authorize('view', $section);

        $section->load(['course', 'assessments', 'enrollments']);

        return view('pages.teacher.courses.assessments', compact('section'));
    }

    /**
     * Store a new assessment.
     */
    public function storeAssessment(Request $request, CourseSection $section)
    {
        $this->authorize('view', $section);

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'max_score' => 'required|numeric|min:1',
            'weight' => 'nullable|numeric|min:0|max:100',
            'due_date' => 'nullable|date',
            // Quiz specific fields
            'quiz_type' => 'nullable|in:none,quiz,pre_quiz,post_quiz',
            'time_limit_minutes' => 'nullable|integer|min:0',
            'attempts_allowed' => 'nullable|integer|min:1',
            'passing_score' => 'nullable|numeric|min:0|max:100',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_options' => 'nullable|boolean',
        ]);

        // Get or create assessment type
        $typeName = $request->type;
        $assessmentType = \App\Models\AssessmentType::firstOrCreate(
            ['code' => $typeName],
            ['name' => ucfirst($typeName), 'is_active' => true]
        );

        Assessment::create([
            'course_offering_id' => $section->id,
            'assessment_type_id' => $assessmentType->id,
            'title' => $request->title,
            'max_grade' => $request->max_score,
            'weight' => $request->weight,
            'due_date' => $request->due_date,
            'is_published' => true,
            // Quiz specific fields
            'quiz_type' => $request->quiz_type ?? 'none',
            'time_limit_minutes' => $request->time_limit_minutes ?? 0,
            'attempts_allowed' => $request->attempts_allowed,
            'passing_score' => $request->passing_score,
            'available_from' => $request->available_from,
            'available_until' => $request->available_until,
            'shuffle_questions' => $request->has('shuffle_questions'),
            'shuffle_options' => $request->has('shuffle_options'),
        ]);

        return back()->with('success', __('lms::messages.assessment_created'));
    }

    /**
     * Show grades for a section.
     */
    public function grades(CourseSection $section): View
    {
        $this->authorize('view', $section);

        $section->load(['course', 'enrollments.student', 'assessments', 'enrollments.grades']);

        // Calculate average grade and pass rate
        $allGrades = $section->enrollments->flatMap->grades;
        $averageGrade = $allGrades->count() > 0 ? round($allGrades->avg('percentage'), 1) : null;
        $passedCount = $allGrades->where('passed', true)->count();
        $totalGraded = $allGrades->count();
        $passRate = $totalGraded > 0 ? round(($passedCount / $totalGraded) * 100, 1) : null;

        return view('pages.teacher.courses.grades', compact('section', 'averageGrade', 'passRate'));
    }

    /**
     * View grades for a specific assessment (quiz).
     */
    public function viewGrades(CourseSection $section, Assessment $assessment): View
    {
        $this->authorize('view', $section);

        $section->load(['course', 'enrollments.student']);
        $assessment->load(['questions.options', 'studentGrades.student']);

        return view('pages.teacher.courses.grades.view', compact('section', 'assessment'));
    }

    /**
     * Show questions for an assessment.
     */
    public function questions(CourseSection $section, Assessment $assessment): View
    {
        $assessment->load(['questions.options']);

        return view('pages.teacher.courses.questions', compact('section', 'assessment'));
    }

    /**
     * Store a new question for an assessment.
     */
    public function storeQuestion(Request $request, CourseSection $section, Assessment $assessment)
    {
        $this->authorize('view', $section);

        $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,short_answer,essay',
            'points' => 'required|integer|min:1',
            'correct_answer' => 'required|string',
        ]);

        $questionData = [
            'assessment_id' => $assessment->id,
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'points' => $request->points,
            'order' => $assessment->questions()->count() + 1,
        ];

        // Handle options for multiple choice and true/false
        if ($request->question_type === 'multiple_choice') {
            $options = [];
            $correctAnswerText = '';

            if ($request->has('options')) {
                foreach ($request->options as $index => $option) {
                    if (! empty($option['option_text'])) {
                        $isCorrect = (string) $request->correct_option === (string) $index;
                        $options[] = [
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
        } elseif ($request->question_type === 'true_false') {
            $questionData['options'] = [
                ['option_text' => 'True', 'is_correct' => $request->correct_answer === 'true'],
                ['option_text' => 'False', 'is_correct' => $request->correct_answer === 'false'],
            ];
            $questionData['correct_answer'] = $request->correct_answer === 'true' ? 'True' : 'False';
        } else {
            // For short_answer and essay, correct_answer is used for manual grading reference
            $questionData['correct_answer'] = $request->correct_answer;
            $questionData['options'] = null;
        }

        Question::create($questionData);

        return back()->with('success', __('Question added successfully'));
    }

    /**
     * Delete a question.
     */
    public function destroyQuestion(Question $question)
    {
        $question->delete();

        return back()->with('success', __('Question deleted successfully'));
    }

    /**
     * Show grading form for a student's quiz attempt.
     */
    public function gradeStudent(CourseSection $section, Assessment $assessment, StudentGrade $studentGrade)
    {
        $this->authorize('view', $section);

        $studentGrade->load(['student', 'assessment.questions.options', 'assessment.questions.studentAnswers' => function ($query) use ($studentGrade) {
            $query->where('student_id', $studentGrade->student_id);
        }]);

        return view('pages.teacher.courses.grades.grade', compact('section', 'assessment', 'studentGrade'));
    }

    /**
     * Store grades for a student's quiz attempt.
     */
    public function storeGrade(Request $request, CourseSection $section, Assessment $assessment, StudentGrade $studentGrade)
    {
        $this->authorize('view', $section);

        $request->validate([
            'feedback' => 'nullable|string',
            'grades' => 'required|array',
            'grades.*.points' => 'required|numeric|min:0',
        ]);

        $totalPoints = 0;
        $maxPoints = 0;

        foreach ($request->grades as $questionId => $gradeData) {
            $question = Question::find($questionId);
            if (! $question) {
                continue;
            }

            $maxPoints += $question->points;
            $pointsEarned = min($gradeData['points'], $question->points);
            $totalPoints += $pointsEarned;

            // Update or create the student answer
            StudentAnswer::updateOrCreate(
                [
                    'student_id' => $studentGrade->student_id,
                    'question_id' => $questionId,
                    'assessment_id' => $assessment->id,
                ],
                [
                    'points_earned' => $pointsEarned,
                    'is_correct' => $pointsEarned >= ($question->points / 2), // Consider correct if >= 50%
                    'feedback' => $gradeData['feedback'] ?? null,
                ]
            );
        }

        // Calculate percentage
        $percentage = $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100, 2) : 0;
        $passed = $assessment->passing_score ? $percentage >= $assessment->passing_score : true;

        // Update the student grade
        $studentGrade->update([
            'grade' => $totalPoints,
            'max_grade' => $maxPoints,
            'percentage' => $percentage,
            'passed' => $passed,
            'feedback' => $request->feedback,
            'graded_by' => auth()->id(),
            'graded_at' => now(),
        ]);

        return redirect()->route('teacher.courses.grades.view', [$section, $assessment])
            ->with('success', __('Grades saved successfully'));
    }

    /**
     * Preview an assessment as a teacher.
     */
    public function previewAssessment(CourseSection $section, Assessment $assessment): View
    {
        $this->authorize('view', $section);

        $assessment->load(['questions.options']);

        return view('pages.teacher.courses.assessments.preview', compact('section', 'assessment'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Assessment;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * Global search API endpoint
     */
    public function globalSearch(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $limit = $request->get('limit', 10);

        if (strlen($query) < 2) {
            return response()->json([
                'results' => [],
                'total' => 0,
            ]);
        }

        $results = [];

        // Search users (students, teachers, admins)
        $users = User::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('user_id', 'like', "%{$query}%")
                ->orWhereHas('profile', function ($pq) use ($query) {
                    $pq->where('first_name', 'like', "%{$query}%")
                        ->orWhere('last_name', 'like', "%{$query}%");
                });
        })
            ->with(['roles', 'profile'])
            ->limit(6)
            ->get()
            ->map(function ($user) {
                $role = $user->roles->first()?->name ?? 'user';

                return [
                    'id' => $user->id,
                    'type' => 'user',
                    'title' => $user->name,
                    'subtitle' => ucfirst($role),
                    'url' => $this->getUserUrl($user),
                    'icon' => $this->getUserIcon($role),
                ];
            });

        $results = array_merge($results, $users->toArray());

        // Search courses
        $courses = Course::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('code', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%");
        })
            ->with(['department', 'major'])
            ->limit(6)
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'type' => 'course',
                    'title' => $course->code.' - '.$course->name,
                    'subtitle' => $course->department->name ?? '',
                    'url' => $this->getCourseUrl($course),
                    'icon' => 'book-open',
                ];
            });

        $results = array_merge($results, $courses->toArray());

        // Search Assessments / Quizzes early (so they have good visibility)
        if (Auth::check()) {
            $user = Auth::user();
            $assessmentsQuery = Assessment::query()
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                })
                ->with(['courseOffering.course']);

            if ($user->hasRole('student')) {
                $assessmentsQuery->whereHas('courseOffering.enrollments', function ($e) use ($user) {
                    $e->where('student_id', $user->id)->where('status', 'approved');
                });
            } elseif ($user->hasRole('teacher')) {
                $assessmentsQuery->whereHas('courseOffering', function ($o) use ($user) {
                    $o->where('teacher_id', $user->id);
                });
            }

            $assessments = $assessmentsQuery
                ->limit(6)
                ->get()
                ->map(function ($assessment) {
                    $course = $assessment->courseOffering?->course;
                    $courseLabel = $course ? ($course->code . ' - ' . $course->name) : 'General';
                    $isQuiz = !empty($assessment->quiz_type) && $assessment->quiz_type !== 'none';
                    $typeLabel = $isQuiz ? ucfirst(str_replace('_', ' ', $assessment->quiz_type)) : 'Assignment';

                    return [
                        'id' => $assessment->id,
                        'type' => 'assessment',
                        'title' => $assessment->title,
                        'subtitle' => $typeLabel . ' • ' . $courseLabel,
                        'url' => $this->getAssessmentUrl($assessment),
                        'icon' => $isQuiz ? 'clipboard-list' : 'document-text',
                    ];
                });

            $results = array_merge($results, $assessments->toArray());
        }

        // Search course offerings (for students/teachers)
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->hasRole('student')) {
                $offerings = CourseOffering::whereHas('enrollments', function ($q) use ($user) {
                    $q->where('student_id', $user->id)->where('status', 'approved');
                })
                    ->whereHas('course', function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%")
                            ->orWhere('code', 'like', "%{$query}%");
                    })
                    ->with(['course', 'teacher'])
                    ->limit(5)
                    ->get()
                    ->map(function ($offering) {
                        return [
                            'id' => $offering->id,
                            'type' => 'offering',
                            'title' => $offering->course->code.' - '.$offering->course->name,
                            'subtitle' => 'Section '.$offering->section_name.' | '.($offering->teacher->name ?? 'No teacher'),
                            'url' => route('student.courses.show', $offering),
                            'icon' => 'academic-cap',
                        ];
                    });

                $results = array_merge($results, $offerings->toArray());
            } elseif ($user->hasRole('teacher')) {
                $offerings = CourseOffering::where('teacher_id', $user->id)
                    ->whereHas('course', function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%")
                            ->orWhere('code', 'like', "%{$query}%");
                    })
                    ->with(['course'])
                    ->limit(5)
                    ->get()
                    ->map(function ($offering) {
                        return [
                            'id' => $offering->id,
                            'type' => 'offering',
                            'title' => $offering->course->code.' - '.$offering->course->name,
                            'subtitle' => 'Section '.$offering->section_name,
                            'url' => route('teacher.courses.show', $offering),
                            'icon' => 'academic-cap',
                        ];
                    });

                $results = array_merge($results, $offerings->toArray());
            }

            // Search user's own Tasks (productivity)
            $tasks = Task::where('user_id', $user->id)
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                })
                ->limit(5)
                ->get()
                ->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'type' => 'task',
                        'title' => $task->title,
                        'subtitle' => ($task->due_date ? 'Due ' . $task->due_date->diffForHumans() : 'Task') . ($task->completed ? ' • Done' : ''),
                        'url' => route('tasks.index') . '#task-' . $task->id,
                        'icon' => 'check-circle',
                    ];
                });

            $results = array_merge($results, $tasks->toArray());
        }

        // Search announcements
        $announcements = Announcement::where('is_published', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%");
            })
            ->with('author')
            ->limit(4)
            ->get()
            ->map(function ($announcement) {
                return [
                    'id' => $announcement->id,
                    'type' => 'announcement',
                    'title' => $announcement->title,
                    'subtitle' => 'By '.($announcement->author->name ?? 'System').' • '.$announcement->created_at->diffForHumans(),
                    'url' => route('dashboard'), // Best central place for now
                    'icon' => 'megaphone',
                ];
            });

        $results = array_merge($results, $announcements->toArray());

        // Limit total results
        $results = array_slice($results, 0, $limit);

        return response()->json([
            'results' => $results,
            'total' => count($results),
            'query' => $query,
        ]);
    }

    /**
     * Get URL for user based on current user's role
     */
    private function getUserUrl(User $user): string
    {
        if (! Auth::check()) {
            return '#';
        }

        $currentUser = Auth::user();

        if ($currentUser->hasRole('admin')) {
            return route('admin.users.show', $user);
        } elseif ($currentUser->hasRole('teacher') && $user->hasRole('student')) {
            // Teachers: link to their first course's students page (with search hint)
            $firstOffering = $currentUser->taughtCourses()->first();
            if ($firstOffering) {
                return route('teacher.courses.students', $firstOffering) . '?search=' . urlencode($user->name);
            }
            return route('teacher.courses.index');
        }

        return '#';
    }

    /**
     * Get icon for user based on role
     */
    private function getUserIcon(string $role): string
    {
        return match ($role) {
            'admin' => 'shield-check',
            'teacher' => 'academic-cap',
            'student' => 'user',
            default => 'user'
        };
    }

    /**
     * Get URL for course based on current user's role
     */
    private function getCourseUrl(Course $course): string
    {
        if (! Auth::check()) {
            return '#';
        }

        $currentUser = Auth::user();

        if ($currentUser->hasRole('admin')) {
            return route('admin.courses.show', $course);
        }

        return '#';
    }

    /**
     * Get URL for assessment based on current user's role
     */
    private function getAssessmentUrl(Assessment $assessment): string
    {
        if (! Auth::check()) {
            return '#';
        }

        $currentUser = Auth::user();
        $offering = $assessment->courseOffering;

        if (!$offering) {
            return '#';
        }

        if ($currentUser->hasRole('admin')) {
            return route('admin.academic.offerings.show', $offering); // fallback
        } elseif ($currentUser->hasRole('teacher')) {
            if (!empty($assessment->quiz_type) && $assessment->quiz_type !== 'none') {
                return route('teacher.quizzes.questions', [$offering, $assessment]);
            }
            return route('teacher.courses.assessments', $offering);
        } elseif ($currentUser->hasRole('student')) {
            if (!empty($assessment->quiz_type) && $assessment->quiz_type !== 'none') {
                return route('student.quizzes.show', $assessment);
            }
            return route('student.courses.show', $offering) . '#assessments';
        }

        return '#';
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Course;
use App\Models\CourseOffering;
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
                'total' => 0
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
            ->limit($limit)
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
            ->limit($limit)
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'type' => 'course',
                    'title' => $course->code . ' - ' . $course->name,
                    'subtitle' => $course->department->name ?? '',
                    'url' => $this->getCourseUrl($course),
                    'icon' => 'book-open',
                ];
            });

        $results = array_merge($results, $courses->toArray());

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
                    ->limit($limit)
                    ->get()
                    ->map(function ($offering) {
                        return [
                            'id' => $offering->id,
                            'type' => 'offering',
                            'title' => $offering->course->code . ' - ' . $offering->course->name,
                            'subtitle' => 'Section ' . $offering->section_name . ' | ' . ($offering->teacher->name ?? 'No teacher'),
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
                    ->limit($limit)
                    ->get()
                    ->map(function ($offering) {
                        return [
                            'id' => $offering->id,
                            'type' => 'offering',
                            'title' => $offering->course->code . ' - ' . $offering->course->name,
                            'subtitle' => 'Section ' . $offering->section_name,
                            'url' => route('teacher.courses.show', $offering),
                            'icon' => 'academic-cap',
                        ];
                    });

                $results = array_merge($results, $offerings->toArray());
            }
        }

        // Search announcements
        $announcements = Announcement::where('is_published', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->with('author')
            ->limit($limit)
            ->get()
            ->map(function ($announcement) {
                return [
                    'id' => $announcement->id,
                    'type' => 'announcement',
                    'title' => $announcement->title,
                    'subtitle' => 'By ' . ($announcement->author->name ?? 'System') . ' • ' . $announcement->created_at->diffForHumans(),
                    'url' => '#', // Announcements might not have direct links
                    'icon' => 'megaphone',
                ];
            });

        $results = array_merge($results, $announcements->toArray());

        // Limit total results
        $results = array_slice($results, 0, $limit);

        return response()->json([
            'results' => $results,
            'total' => count($results),
            'query' => $query
        ]);
    }

    /**
     * Get URL for user based on current user's role
     */
    private function getUserUrl(User $user): string
    {
        if (!Auth::check()) {
            return '#';
        }

        $currentUser = Auth::user();

        if ($currentUser->hasRole('admin')) {
            return route('admin.users.show', $user);
        } elseif ($currentUser->hasRole('teacher') && $user->hasRole('student')) {
            // Teachers can view their students
            return route('teacher.courses.students', ['section' => 'search', 'student' => $user->id]);
        }

        return '#';
    }

    /**
     * Get icon for user based on role
     */
    private function getUserIcon(string $role): string
    {
        return match($role) {
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
        if (!Auth::check()) {
            return '#';
        }

        $currentUser = Auth::user();

        if ($currentUser->hasRole('admin')) {
            return route('admin.courses.show', $course);
        }

        return '#';
    }
}
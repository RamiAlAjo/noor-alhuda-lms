<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use App\Models\Semester;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BulkNotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display bulk notification form.
     */
    public function index(): View
    {
        $templates = NotificationTemplate::where('is_active', true)->get();
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $courses = \App\Models\Course::where('is_active', true)->get();

        return view('pages.admin.notifications.bulk', compact('templates', 'semesters', 'courses'));
    }

    /**
     * Send bulk notifications.
     */
    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'recipient_type' => 'required|in:all,students,teachers,admins,course,semester',
            'course_id' => 'required_if:recipient_type,course|exists:courses,id',
            'semester_id' => 'required_if:recipient_type,semester|exists:semesters,id',
            'notification_type' => 'required|string',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'send_email' => 'boolean',
            'template_id' => 'nullable|exists:notification_templates,id',
        ]);

        try {
            $recipients = $this->getRecipients($request);
            $count = 0;

            foreach ($recipients as $user) {
                $this->notificationService->sendToUser(
                    $user,
                    $request->notification_type,
                    $request->title,
                    $request->content,
                    null, // No link for bulk notifications
                    [],
                    $request->boolean('send_email', false)
                );
                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => "Bulk notification sent to {$count} recipients",
                'count' => $count,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send bulk notifications: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get preview of recipients.
     */
    public function preview(Request $request): JsonResponse
    {
        $request->validate([
            'recipient_type' => 'required|in:all,students,teachers,admins,course,semester',
            'course_id' => 'required_if:recipient_type,course',
            'semester_id' => 'required_if:recipient_type,semester',
        ]);

        try {
            $recipients = $this->getRecipients($request);

            return response()->json([
                'success' => true,
                'count' => $recipients->count(),
                'preview' => $recipients->take(5)->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->getRoleNames()->first(),
                    ];
                }),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get recipient preview: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get recipients based on request parameters.
     */
    private function getRecipients(Request $request)
    {
        return match ($request->recipient_type) {
            'all' => User::all(),
            'students' => User::role('student')->get(),
            'teachers' => User::role('teacher')->get(),
            'admins' => User::role('admin')->get(),
            'course' => $this->getCourseRecipients($request->course_id),
            'semester' => $this->getSemesterRecipients($request->semester_id),
            default => collect(),
        };
    }

    /**
     * Get recipients for a specific course.
     */
    private function getCourseRecipients(int $courseId): \Illuminate\Database\Eloquent\Collection
    {
        return User::whereHas('enrollments', function ($query) use ($courseId) {
            $query->whereHas('courseOffering', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            })->where('status', 'approved');
        })->get();
    }

    /**
     * Get recipients for a specific semester.
     */
    private function getSemesterRecipients(int $semesterId): \Illuminate\Database\Eloquent\Collection
    {
        return User::whereHas('enrollments', function ($query) use ($semesterId) {
            $query->where('semester_id', $semesterId)
                ->where('status', 'approved');
        })->get();
    }

    /**
     * Get notification analytics.
     */
    public function analytics(): View
    {
        $analytics = $this->notificationService->getAnalytics();

        return view('pages.admin.notifications.analytics', compact('analytics'));
    }

    /**
     * Get notification templates for AJAX.
     */
    public function getTemplates(): JsonResponse
    {
        $templates = NotificationTemplate::where('is_active', true)
            ->select('id', 'name', 'type', 'category', 'subject', 'content')
            ->get();

        return response()->json([
            'success' => true,
            'templates' => $templates,
        ]);
    }
}

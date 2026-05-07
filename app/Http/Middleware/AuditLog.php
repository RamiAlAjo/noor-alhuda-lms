<?php

namespace App\Http\Middleware;

use App\Services\AuditLogService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $action = null): Response
    {
        $response = $next($request);

        // Log the request if it's a write operation
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $actionName = $action ?? $this->determineAction($request);

            if ($actionName) {
                AuditLogService::log($actionName, $this->getDescription($request), [
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                    'status_code' => $response->getStatusCode(),
                ]);
            }
        }

        return $response;
    }

    /**
     * Determine the action based on the request.
     */
    protected function determineAction(Request $request): ?string
    {
        $path = $request->path();
        $method = $request->method();

        // Map common routes to actions
        $actionMap = [
            'admin/users' => 'user_management',
            'admin/courses' => 'course_management',
            'admin/enrollments' => 'enrollment_management',
            'admin/grades' => 'grade_management',
            'admin/payments' => 'payment_management',
            'admin/settings' => 'settings_management',
            'admin/announcements' => 'announcement_management',
            'admin/medical-leaves' => 'medical_leave_management',
            'admin/grade-appeals' => 'grade_appeal_management',
            'teacher/quizzes' => 'quiz_management',
            'teacher/assignments' => 'assignment_management',
            'teacher/grades' => 'grade_management',
            'teacher/attendance' => 'attendance_management',
            'student/quizzes' => 'quiz_submission',
            'student/assignments' => 'assignment_submission',
            'student/grades' => 'grade_view',
            'student/payments' => 'payment_submission',
            'student/medical-leaves' => 'medical_leave_submission',
            'student/grade-appeals' => 'grade_appeal_submission',
        ];

        foreach ($actionMap as $route => $action) {
            if (str_contains($path, $route)) {
                return $action;
            }
        }

        return null;
    }

    /**
     * Get the description for the audit log.
     */
    protected function getDescription(Request $request): string
    {
        $method = $request->method();
        $path = $request->path();

        return strtoupper($method).' request to '.$path;
    }
}

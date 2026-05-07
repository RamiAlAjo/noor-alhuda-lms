<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get all notifications for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $perPage = $request->input('per_page', 15);

        $notifications = $this->notificationService->getAll($user, $perPage);

        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    /**
     * Get unread notifications for the authenticated user.
     */
    public function unread(): JsonResponse
    {
        $user = Auth::user();
        $notifications = $this->notificationService->getUnread($user);

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'unread_count' => $notifications->count(),
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => __('Unauthorized'),
            ], 403);
        }

        $this->notificationService->markAsRead($notification);

        return response()->json([
            'success' => true,
            'message' => __('Notification marked as read'),
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = Auth::user();
        $this->notificationService->markAllAsRead($user);

        return response()->json([
            'success' => true,
            'message' => __('All notifications marked as read'),
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => __('Unauthorized'),
            ], 403);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => __('Notification deleted'),
        ]);
    }

    /**
     * Get unread count for the authenticated user.
     */
    public function unreadCount(): JsonResponse
    {
        $user = Auth::user();
        $count = $user->notifications()->unread()->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    /**
     * Toggle notification sound preference.
     */
    public function toggleSound(Request $request): JsonResponse
    {
        $user = Auth::user();
        $enabled = $request->boolean('enabled', true);

        if ($user->settings) {
            $user->settings->update(['notification_sound' => $enabled]);
        }

        return response()->json([
            'success' => true,
            'sound_enabled' => $enabled,
        ]);
    }
}

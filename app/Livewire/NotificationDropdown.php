<?php

namespace App\Livewire;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationDropdown extends Component
{
    public $notifications = [];

    public $unreadCount = 0;

    public $isOpen = false;

    public $soundEnabled = true;

    public $pushEnabled = false;

    public $searchTerm = '';

    public $filterType = 'all';

    public $showUnreadOnly = false;

    public $lastChecked;

    protected $listeners = [
        'refreshNotifications' => 'loadNotifications',
    ];

    public function mount()
    {
        $this->loadNotifications();
        $this->soundEnabled = Auth::user()->settings?->notification_sound ?? true;
        $this->pushEnabled = Auth::user()->settings?->notification_push ?? false;
        $this->lastChecked = now();
    }

    public function getUserIdProperty()
    {
        return Auth::id();
    }

    public function loadNotifications()
    {
        $user = Auth::user();

        $query = $user->notifications()->orderBy('created_at', 'desc');

        // Apply filters
        if ($this->showUnreadOnly) {
            $query->unread();
        }

        if ($this->filterType !== 'all') {
            $query->where('type', $this->filterType);
        }

        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('content', 'like', '%' . $this->searchTerm . '%');
            });
        }

        $this->notifications = $query->limit(20)
            ->get()
            ->map(function ($notification) {
                // Get type-specific icon and color from the notification model
                $typeConfig = \App\Models\Notification::getTypeConfig($notification->type);

                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'type_label' => $typeConfig['label'],
                    'title' => $notification->title,
                    'content' => $notification->content,
                    'link' => $notification->link,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'icon' => $notification->data['icon'] ?? $typeConfig['icon'],
                    'color' => $notification->data['color'] ?? $typeConfig['color'],
                ];
            })->toArray();

        $this->unreadCount = $user->notifications()->unread()->count();
    }

    public function updatedSearchTerm()
    {
        $this->loadNotifications();
    }

    public function updatedFilterType()
    {
        $this->loadNotifications();
    }

    public function updatedShowUnreadOnly()
    {
        $this->loadNotifications();
    }

    public function checkForNewNotifications()
    {
        $user = Auth::user();

        // Get new notifications since last check
        $newNotifications = $user->notifications()
            ->where('created_at', '>', $this->lastChecked)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($newNotifications->isNotEmpty()) {
            \Log::info('Found ' . $newNotifications->count() . ' new notifications for user ' . $user->id);

            foreach ($newNotifications as $notification) {
                // Add to the beginning of the list
                array_unshift($this->notifications, [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'type_label' => $notification->type_label,
                    'title' => $notification->title,
                    'content' => $notification->content,
                    'link' => $notification->link,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->time_ago,
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                ]);

                // Increment unread count if not read
                if (!$notification->is_read) {
                    $this->unreadCount++;
                }

                // Play sound for new notifications
                if ($this->soundEnabled && !$notification->is_read) {
                    $this->dispatch('notification-received', [
                        'soundEnabled' => $this->soundEnabled,
                        'pushEnabled' => $this->pushEnabled,
                        'title' => $notification->title,
                        'type' => $notification->type,
                    ]);
                }
            }

            // Keep only 10 notifications
            $this->notifications = array_slice($this->notifications, 0, 10);
        }

        $this->lastChecked = now();
    }

    public function toggleDropdown()
    {
        $this->isOpen = ! $this->isOpen;
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);

        if ($notification && $notification->user_id === Auth::id()) {
            $notification->markAsRead();
            $this->unreadCount = max(0, $this->unreadCount - 1);

            // Update the notification in the list
            foreach ($this->notifications as $key => $notif) {
                if ($notif['id'] == $notificationId) {
                    $this->notifications[$key]['is_read'] = true;
                    break;
                }
            }
        }
    }

    public function markAsUnread($notificationId)
    {
        $notification = Notification::find($notificationId);

        if ($notification && $notification->user_id === Auth::id()) {
            $notification->markAsUnread();
            $this->unreadCount++;

            // Update the notification in the list
            foreach ($this->notifications as $key => $notif) {
                if ($notif['id'] == $notificationId) {
                    $this->notifications[$key]['is_read'] = false;
                    break;
                }
            }
        }
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        $this->unreadCount = 0;

        // Mark all as read in the list
        foreach ($this->notifications as $key => $notif) {
            $this->notifications[$key]['is_read'] = true;
        }

        $this->dispatch('notifications-marked-read');
    }

    public function deleteNotification($notificationId)
    {
        $notification = Notification::find($notificationId);

        if ($notification && $notification->user_id === Auth::id()) {
            if (! $notification->is_read) {
                $this->unreadCount = max(0, $this->unreadCount - 1);
            }

            $notification->delete();

            // Remove from the list
            $this->notifications = array_filter(
                $this->notifications,
                fn ($n) => $n['id'] != $notificationId
            );
        }
    }

    public function toggleSound()
    {
        $this->soundEnabled = ! $this->soundEnabled;

        // Save to user settings
        $user = Auth::user();
        if ($user->settings) {
            $user->settings->update(['notification_sound' => $this->soundEnabled]);
        }

        $this->dispatch('sound-toggled', ['enabled' => $this->soundEnabled]);
    }

    public function togglePush()
    {
        $this->pushEnabled = ! $this->pushEnabled;

        // Save to user settings
        $user = Auth::user();
        if ($user->settings) {
            $user->settings->update(['notification_push' => $this->pushEnabled]);
        }

        $this->dispatch('push-toggled', ['enabled' => $this->pushEnabled]);
    }

    public function getNotificationStats()
    {
        $user = Auth::user();

        return [
            'total' => $user->notifications()->count(),
            'unread' => $user->notifications()->unread()->count(),
            'read' => $user->notifications()->where('is_read', true)->count(),
            'by_type' => $user->notifications()
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
            'this_week' => $user->notifications()
                ->where('created_at', '>=', now()->startOfWeek())
                ->count(),
            'avg_per_day' => round($user->notifications()
                ->where('created_at', '>=', now()->subDays(30))
                ->count() / 30, 1),
        ];
    }



    public function render()
    {
        return view('livewire.notification-dropdown');
    }
}

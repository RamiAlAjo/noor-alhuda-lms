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

    protected $listeners = [
        'echo-private:user.{userId},notification.sent' => 'handleNewNotification',
        'refreshNotifications' => 'loadNotifications',
    ];

    public function mount()
    {
        $this->loadNotifications();
        $this->soundEnabled = Auth::user()->settings?->notification_sound ?? true;
    }

    public function getUserIdProperty()
    {
        return Auth::id();
    }

    public function loadNotifications()
    {
        $user = Auth::user();

        $this->notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(10)
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

    public function handleNewNotification($data)
    {
        // Add new notification to the beginning of the list
        array_unshift($this->notifications, [
            'id' => $data['id'],
            'type' => $data['type'],
            'title' => $data['title'],
            'content' => $data['content'],
            'link' => $data['link'] ?? null,
            'is_read' => false,
            'created_at' => $data['created_at'],
            'icon' => $data['data']['icon'] ?? 'bell',
            'color' => $data['data']['color'] ?? 'blue',
        ]);

        // Keep only 10 notifications
        $this->notifications = array_slice($this->notifications, 0, 10);

        // Increment unread count
        $this->unreadCount++;

        // Dispatch browser event for sound notification
        $this->dispatch('notification-received', [
            'soundEnabled' => $this->soundEnabled,
            'title' => $data['title'],
        ]);
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

    public function render()
    {
        return view('livewire.notification-dropdown');
    }
}

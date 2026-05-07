<?php

namespace App\Livewire;

use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Notifications extends Component
{
    public $notifications = [];

    public $unreadCount = 0;

    protected $listeners = ['refreshNotifications' => 'loadNotifications'];

    public function loadNotifications()
    {
        $user = Auth::user();

        // Get announcements visible to the user
        $query = Announcement::where('is_active', true)
            ->where(function ($q) use ($user) {
                $q->where('target_type', 'all')
                    ->orWhere(function ($q2) use ($user) {
                        $q2->where('target_type', 'role')
                            ->where('target_id', $user->role_id);
                    });
            })
            ->orderBy('created_at', 'desc')
            ->limit(10);

        $this->notifications = $query->get();
        $this->unreadCount = $this->notifications->count();
    }

    public function render()
    {
        $this->loadNotifications();

        return view('livewire.notifications');
    }
}

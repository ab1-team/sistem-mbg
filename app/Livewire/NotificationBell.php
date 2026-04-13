<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
{
    public function getUnreadCountProperty()
    {
        return Auth::user()?->unreadNotifications->count() ?? 0;
    }

    public function getNotificationsProperty()
    {
        return Auth::user()?->notifications()->latest()->take(5)->get() ?? collect();
    }

    public function markAllAsRead()
    {
        Auth::user()?->unreadNotifications->markAsRead();
        $this->dispatch('notifications-read');
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
